<?php

namespace App\Http\Controllers\v1\admin\brand\designer\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\HttpService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\IntegralLogDesigner;
use App\Models\OnlineClassBrand;
use App\Models\OnlineClassDesigner;
use App\Models\StatisticDesigner;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\site\DesignerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;

class BrandDesignerController extends ApiController
{
    private $getNameServices;
    private $authService;
    public function __construct(GetNameServices $getNameServices,
                                AuthService $authService
    )
    {
        $this->getNameServices = $getNameServices;
        $this->authService  = $authService;
    }

    public function account_index(Request $request)
    {
        $user = $this->authService->getAuthUser();
        $input = $request->all();

        $login_name = $request->input('ln',null);
        $realname = $request->input('rn',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        //查询设计师列表
        $entry = Designer::join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->OrganizationId($user->brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            //->where('self_designer_level', '>','-1')
            ;

        if($login_name!==null){
            $entry = $entry->where(function($query)use($login_name){
                $query->where('designers.login_username','like','%'.$login_name.'%');
                $query->orWhere('designers.designer_account','like','%'.$login_name.'%');
                $query->orWhere('designers.login_mobile','like','%'.$login_name.'%');
            });
        }

        if($realname!==null){
            $entry = $entry->where('detail.realname','like',"%".$realname."%");
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('designers.created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('designers.id','desc');

        $datas = $entry->select(
            'designers.id',
            'designers.designer_account',
            'detail.nickname',
            'detail.realname',
            'designers.login_mobile',
            'detail.gender',
            'detail.area_belong_province',
            'detail.area_belong_city',
            'detail.area_belong_district',
            'detail.self_designer_type',
            'detail.self_organization',
            'designers.created_at',
            'designers.status',
            'detail.approve_time',
            'detail.approve_realname',
            'detail.self_designer_level'
        )->paginate($limit);

        $datas->transform(function($v){
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->area_belong = '';
            $province =  Area::where('id',$v->area_belong_province)->first();
            $city =  Area::where('id',$v->area_belong_city)->first();
            $district =  Area::where('id',$v->area_belong_district)->first();
            if($province){$v->area_belong.= $province->name;}
            if($city){$v->area_belong.= $city->name;}
            if($district){$v->area_belong.= $district->name;}

            //方案数（待开发）
            $v->album_count = 0;
            $v->status_text = Designer::statusGroup($v->status);
            $v->approve_info = $v->approve_realname == DesignerDetail::APPROVE_REALNAME_YES?$v->approve_time:'未认证';
            $v->isOn = $v->status==Designer::STATUS_ON;
            $v->changeStatusApiUrl = url('admin/brand/brand_designer/api/account/'.$v->id.'/status');
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?:'');
            $self_organization_text = DesignerDetail::getSelfOrganization($v->id);
            $v->self_organization = $self_organization_text;
            $v->level_text = Designer::designerTitleCn($v->self_designer_level);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //账号保存
    public function account_store(Request $request,EasySms $easySms)
    {
        $user = $this->authService->getAuthUser();

        $brand = $user->brand;

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'login_mobile' => 'required|unique:designers',
            'self_designer_type' => 'required'
        ]);

        if ($validator->fails()) {
            $this->respFail($validator->messages()->first());
        }

        //判断是否超过品牌可授权的品牌设计师账号数量
        $designer_count = OrganizationBrandService::countBrandDesignerOnAndVerifying($brand->id);
        if($designer_count >= $brand->quota_designer_brand){
            die('品牌设计师数量已达限额');
        }

        DB::beginTransaction();

        try{

            $designer_account = $this->getNameServices->getDesignerAccount(OrganizationService::ORGANIZATION_TYPE_BRAND,$brand->id,$input_data['self_designer_type']);
            $designer_id_code = $this->getNameServices->getDesignerIdCode();
            $member = new Designer();
            if(!$member){$this->respFail('系统错误');}
            $member->designer_account = $designer_account;
            $member->sys_code = DesignerService::get_sys_code();
            $member->login_username = $designer_account;
            $member->login_mobile = $input_data['login_mobile'];
            $member->login_password = bcrypt($input_data['login_mobile']);
            $member->organization_type = Designer::ORGANIZATION_TYPE_BRAND;
            $member->organization_id = $brand->id;
            $member->designer_id_code = $designer_id_code;
            $member->status = Designer::STATUS_VERIFYING;
            $member->save();

            $member_detail = new DesignerDetail();
            $member_detail->designer_id = $member->id;
            $member_detail->nickname = $designer_account;
            $member_detail->brand_id = $brand->id;
            $member_detail->dealer_id = 0;
            $member_detail->self_designer_type = $input_data['self_designer_type'];
            $member_detail->self_designer_type = $input_data['self_designer_type'];
            $member_detail->save();

            //建品牌统计信息
            $stat = new StatisticDesigner();
            $stat->designer_id = $member->id;
            $stat->belong_brand_id = $brand->id;
            $stat->save();
            if (!$stat){$this->respFail('系统错误');}


            if (!$member_detail){
                DB::rollback();
                $this->respFail('系统错误');
            }

            DesignerService::addToSearch();


            DB::commit();

            //发送手机短信
            /*$msgService = new GetVerifiCodeService();
            $msg_content = '您的设计师账号为：'.$member->login_username.'，初始密码为账号，请尽快登陆修改，谢谢。';
            $msgService->sendMobile($input_data['login_telephone'],$msg_content);*/

            //发送手机验证码
            $result = $easySms->send($member->login_mobile, [
                'content' => "您的账号已创建成功，用户名{login_username}，初始密码{login_password}，请尽快登陆修改，谢谢。",
                'template' => 'SMS_190266682',
                'data'=>[
                    'login_username'=>$member->login_mobile,
                    'login_password'=>$member->login_mobile,
                ]
            ]);

            //如果品牌有在线课堂权限，则创建设计师
            $class_brand = OnlineClassBrand::query()->where('brand_id',$brand->id)->first();
            if($class_brand){
                //调用远程接口，新建课堂设计师账号
                $request_params = [
                    'brandNo'=>$class_brand->class_client_id,
                    'mobile'=>$member->login_mobile,
                    'password'=>$member->login_mobile,
                    'repassword'=>$member->login_mobile,
                    'brandUserType'=>"0",
                    'ip'=>$request->getClientIp()
                ];
                $result = HttpService::post_json("http://39.98.131.113:5840/user/api/user/register",$request_params);


                if($result['code'] == '200'){
                    $account = new OnlineClassDesigner();
                    $account->brand_id = $brand->id;
                    $account->class_brand_id = $class_brand->id;
                    $account->login_account = $member->login_mobile;
                    $account->login_password = $member->login_mobile;
                    $account->class_user_no = $result['data']['userNo'];
                    $account->class_token = $result['data']['token'];
                    $account->type = $input_data['self_designer_type'];
                    $account->save();
                }else{

                    SystemLogService::simple('新增品牌设计师，创建设计师课堂账号接口调用出错！',[
                        'request'.json_encode($request_params),
                        'resp'.json_encode($result)
                    ]);

                    $this->respData([],"设计师账号新增成功！但设计师的课堂账号创建失败，请移步在线课堂菜单手动创建");
                }
            }


            $this->respData([],"设计师账号、设计师在线课堂账号均新增成功！");

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail($e->getMessage());
        }


        $this->respData();


    }

    //修改密码
    public function modify_pwd(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $data = Designer::query()
            ->OrganizationId($brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->find($input_data['id']);
        if(!$data){
            $this->respFail('信息不存在！');
        }

        //其他校验
        //品牌修改品牌设计师密码，不用提供旧密码，直接修改

        if($input_data['new_password'] != $input_data['confirm_password']){
            $this->respFail('两次新密码输入不一致！');
        }

        DB::beginTransaction();

        try{
            $data->login_password = bcrypt($input_data['new_password']);
            $result = $data->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }

    //修改等级
    public function modify_level(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $new_level = $input_data['level'];

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $data = Designer::query()
            ->OrganizationId($brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->find($input_data['id']);
        if(!$data){
            $this->respFail('信息不存在！');
        }

        $detail = $data->detail;
        if(!$detail){
            $this->respFail('信息不存在！');
        }

        //其他校验
        $exist = false;
        $level_group = DesignerDetail::designerLevelIdNameGroup(true);
        for($i=0;$i<count($level_group);$i++){
            if($level_group[$i]['id'] == $new_level){
                $exist = true;
            }
        }
        if(!$exist){
            $this->respFail('参数错误！');
        }

        DB::beginTransaction();

        try{
            $detail->self_designer_level = $input_data['level'];
            $result = $detail->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }


    //调整积分
    public function modify_integral(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'value' => 'required',
            'remark' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $value = intval($input_data['value']);
        if($value==0 || $value<>$input_data['value']){
            $this->respFail('请填写非零整数！');
        }

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $data = Designer::query()
            ->OrganizationId($brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->find($input_data['id']);
        if(!$data){
            $this->respFail('信息不存在！');
        }

        $detail = $data->detail;
        if(!$detail){
            $this->respFail('信息不存在！');
        }

        $original = $detail->point_money;
        $after = $original+$value;
        if($after<0){
            $this->respFail('调整后积分不可以为负值！');
        }

        DB::beginTransaction();

        try{
            $detail->point_money = $input_data['value'];
            $result = $detail->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            //增加后台操作记录
            $log = new IntegralLogDesigner();
            $log->designer_id = $input_data['id'];
            $log->type = $value>0?IntegralLogDesigner::TYPE_ADMIN_ADD:IntegralLogDesigner::TYPE_ADMIN_MINUS;
            $log->integral = $value;
            $log->available_integral = $after;
            $log->remark = $input_data['remark'];
            $result = $log->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }


    //账号更新
    public function account_update($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        if($loginAdmin->id == $id){
            //修改自己
            $handleAdmin = $loginAdmin;
        }else{
            //修改下级管理员
            $handleAdmin = AdministratorBrand::where('created_by_administrator_id',$loginAdmin->id)->find($id);
            if (!$handleAdmin){
                $this->respFail('权限不足');
            }
        }

        $loginAdminIsSuper = $loginAdmin->is_super_admin;

        if($loginAdminIsSuper == 1){
            $data = [
                'id' => 'required',
                'realname' => 'required',
                'self_department' => 'required',
                'self_position' => 'required',
            ];
        }else{
            $data = [
                'id' => 'required',
                'realname' => 'required',
                'self_department' => 'required',
                'self_position' => 'required',
                'role_id' => 'required',
            ];
        }

        $input_data = $request->all();
        $validator = Validator::make($input_data, $data);
        if ($validator->fails()) {
            $this->respFail('请正确填写信息后再提交！');
        }

        //其他校验
        $role = RoleBrand::query()->find($input_data['role_id']);
        if(!$role){$this->respFail('角色错误！');}

        //判断登录用户名是否存在
        $exist = AdministratorBrand::query()
            ->where('login_username',$input_data['login_username'])
            ->where('id','<>',$handleAdmin->id)
            ->first();
        if($exist){$this->respFail('登录用户名已存在！');}



        DB::beginTransaction();

        try{

            $path = [0];
            $level = 0;
            $parentId = $handleAdmin->created_by_administrator_id;
            if($parentId){
                $parent = AdministratorBrand::find($handleAdmin->created_by_administrator_id);
                if($parent){
                    $path = explode(',',$parent->path);
                    array_push($path,$parent->id);
                    $level = $parent->level+1;
                }
            }
            $path = implode(',',$path);

            $handleAdmin->login_username = $input_data['login_username'];
            $handleAdmin->self_department = $input_data['self_department'];
            $handleAdmin->self_position = $input_data['self_position'];
            $handleAdmin->realname = $input_data['realname'];
            $handleAdmin->path = $path;
            $handleAdmin->level = $level;
            $handleAdmin->save();

            //需要修改角色时
            if (isset($input_data['role_id'])&&$handleAdmin->id!=$loginAdmin->id){
                //自己不能修改自己的角色
                if (!$role){
                    $this->respFail('角色不存在');
                }
                $handleAdmin->syncRoles([$role]);
            }
            DB::commit();

            $this->respData();

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误');
        }

        $this->respFail();

    }

    //账号删除
    public function account_destroy($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        if($loginAdmin->id == $id){
            //删除自己
            $handleAdmin = $loginAdmin;
            $this->respFail('权限不足');
        }else{
            //删除下级管理员
            $handleAdmin = AdministratorBrand::where('created_by_administrator_id',$loginAdmin->id)->find($id);
            if (!$handleAdmin){
                $this->respFail('权限不足');
            }
        }

        if($handleAdmin->is_super_admin){
            $this->respFail('不允许删除！');
        }

        //删除角色与独立权限
        $handleAdmin->syncRoles([]); // Delete relationship data
        $handleAdmin->permissions()->sync([]);

        //删除用户
        $handleAdmin->delete();

        $this->respData([]);

    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        //修改品牌设计师
        $designer = Designer::query()
            ->with(['detail'=>function($query){
                $query->where('approve_realname','=',DesignerDetail::APPROVE_REALNAME_YES);
            }])
            ->OrganizationId($loginAdmin->brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->find($id);
        if (!$designer){
            $this->respFail('权限不足');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($designer->status==Designer::STATUS_OFF){
                $designer->status = Designer::STATUS_ON;
            }else{
                $designer->status = Designer::STATUS_OFF;
            }

            $designer->save();

            DesignerService::addToSearch();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }
}
