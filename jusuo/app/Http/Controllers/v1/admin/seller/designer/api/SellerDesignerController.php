<?php

namespace App\Http\Controllers\v1\admin\seller\designer\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\HttpService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\IntegralRechargeLog;
use App\Models\OnlineClassBrand;
use App\Models\OnlineClassDesigner;
use App\Services\v1\admin\IntegralLogBuyService;
use App\LogisticsCompany;
use App\Models\AdministratorBrand;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\IntegralGood;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\StatisticDesigner;
use App\Services\v1\admin\OrganizationDealerService;
use App\Services\v1\site\DesignerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;

class SellerDesignerController extends ApiController
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
        $entry = Designer::leftJoin('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->OrganizationId($user->dealer->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            //->where('self_designer_level', '>','-1')
            ->orderBy('id','DESC');

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
            'detail.self_designer_level',
            'detail.self_birth_time',
            'detail.point_focus',
            'detail.point_experience',
            'detail.point_money',
            'detail.count_album'
        )->paginate($limit);

        $datas->transform(function($v){
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->area_belong = '';
            $v->self_birth_time = date('Y年m月d日',strtotime($v->self_birth_time));
            $province =  Area::where('id',$v->area_belong_province)->first();
            $city =  Area::where('id',$v->area_belong_city)->first();
            $district =  Area::where('id',$v->area_belong_district)->first();
            if($province){$v->area_belong.= $province->name;}
            if($city){$v->area_belong.= $city->name;}
            if($district){$v->area_belong.= $district->name;}

            //方案数（待开发）
            $v->album_count = $v->count_album;
            $v->status_text = Designer::statusGroup($v->status);
            $v->approve_info = $v->approve_realname == DesignerDetail::APPROVE_REALNAME_YES?$v->approve_time:'未认证';
            $v->isOn = $v->status==Designer::STATUS_ON;
            $v->changeStatusApiUrl = url('admin/seller/seller_designer/api/account/'.$v->id.'/status');
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?:'');
            $self_organization_text = DesignerDetail::getSelfOrganization($v->id);
            $v->self_organization = $self_organization_text;
            $v->level_text = Designer::designerTitleCn($v->self_designer_level);
            //兑换礼品数
            $v->exchange_count = IntegralLogBuyService::get_designer_exchange_count($v->id);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }


    //积分变动明细表
    public function integral_log($id,Request $request)
    {
        $limit = $request->input('limit',10);

        $entry = IntegralLogDesigner::query()
            ->where('designer_id',$id)
            ->select(['id','type','integral','available_integral','remark']);

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            $v->type_text = IntegralLogDesigner::typeGroup($v->type);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //积分充值明细表
    public function charge_log($id,Request $request)
    {
        $limit = $request->input('limit',10);

        $entry = IntegralRechargeLog::query()
            ->where('buyer_type',OrganizationService::ORGANIZATION_TYPE_DESIGNER)
            ->where('buyer_id',$id)
            ->where('pay_status',IntegralRechargeLog::PAY_STATUS_YES)
            ->select(['id','order_no','realname','mobile','money','integral','pay_time','pay_no']);

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    public function exchange_log($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $dealer = $loginAdmin->seller;
        $export = $request->input('export',null);

        $limit = $request->input('limit',20);
        $designer_id = $id;

        //得到最终的品牌/销售商设计师ids
        $designer_ids = [$designer_id];
        $all_designers = Designer::query()
            ->whereIn('id',$designer_ids)
            ->get();


        //获取本次兑换列表
        $entry = IntegralLogBuy::query()
            ->select([
                'id','designer_id','goods_id','count','total','receiver_name','receiver_tel',
                'receiver_address','full_address','status','created_at','sent_at','logistics_company',
                'logistics_code'
            ])
            ->whereIn('designer_id',$designer_ids);

        if($designer_id){
            //指定设计师id时，只需要获取已兑换或已发货的
            //设计师列表-》点击兑换礼品数
            $entry->whereIn('status',[
                IntegralLogBuy::STATUS_TO_BE_SENT,IntegralLogBuy::STATUS_SENT
            ]);
        }

        $datas =$entry->paginate(intval($limit));

        //获取本次查询的设计师详情信息
        $data_designer_ids = collect($datas->items())->pluck('designer_id')->toArray();
        $data_designer_details = DesignerDetail::query()
            ->whereIn('designer_id',$data_designer_ids)
            ->get();

        //获取本次查询的积分商品信息
        $data_good_ids = collect($datas->items())->pluck('goods_id')->toArray();
        $data_goods = IntegralGood::query()
            ->whereIn('id',$data_good_ids)
            ->get();

        //获取本次查询的物流公司信息
        $data_logistics_ids = collect($datas->items())->pluck('logistics_company')->toArray();
        $data_logistics = LogisticsCompany::query()
            ->whereIn('id',$data_logistics_ids)
            ->get();

        $datas->transform(function($v) use($data_designer_details,$data_goods,$data_logistics,$all_designers){


            $v->nickname = '';
            $v->realname = '';
            $designer_detail = $data_designer_details->where('designer_id',$v->designer_id)->first();
            if($designer_detail){
                $v->nickname = $designer_detail->nickname;
                $v->realname = $designer_detail->realname;
            }

            $v->mobile = '';
            $designer = $all_designers->where('id',$v->designer_id)->first();
            if($designer){
                $v->mobile = $designer->login_mobile;
            }

            $v->logistics_company_name = '';
            $logistics_company = $data_logistics->where('id',$v->logistics_company)->first();
            if($logistics_company){
                $v->logistics_company_name = $logistics_company->name;
            }

            //是否可处理
            $v->can_handle = false;
            $v->good_name = '';
            $v->rejectApiUrl = '';
            $v->sendApiUrl = '';
            $good = $data_goods->where('id',$v->goods_id)->first();
            if($good){
                $v->good_name = $good->name;
            }

            $v->status_text = IntegralLogBuy::statusGroup($v->status);

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

        $seller = $user->dealer;

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'login_mobile' => 'required|unique:designers',
            'self_designer_type' => 'required'
        ]);

        if ($validator->fails()) {
            $this->respFail($validator->messages()->first());
        }

        $designer_count = OrganizationDealerService::countSellerDesignerOnAndVerifying($seller->id);
        if($designer_count >= $seller->quota_designer){
            $this->respFail('已达设计师授权限额！');
        }


        DB::beginTransaction();

        try{
            $designer_account = $this->getNameServices->getDesignerAccount(OrganizationService::ORGANIZATION_TYPE_SELLER,$seller->id,$input_data['self_designer_type']);
            $designer_id_code = $this->getNameServices->getDesignerIdCode();
            $member = new Designer();
            if(!$member){$this->respFail('系统错误');}
            $member->sys_code = DesignerService::get_sys_code();
            $member->login_username = $designer_account;
            $member->designer_account = $designer_account;
            $member->login_mobile = $input_data['login_mobile'];
            $member->login_password = bcrypt(trim($input_data['login_mobile']));
            $member->organization_type = Designer::ORGANIZATION_TYPE_SELLER;
            $member->organization_id = $seller->id;
            $member->designer_id_code = $designer_id_code;
            $member->status = Designer::STATUS_VERIFYING;
            $member->save();

            $member_detail = new DesignerDetail();
            $member_detail->designer_id = $member->id;
            $member_detail->nickname = $designer_account;
            $member_detail->brand_id = 0;
            $member_detail->dealer_id = $seller->id;
            $member_detail->self_designer_type = $input_data['self_designer_type'];
            $member_detail->save();

            $stat = new StatisticDesigner();
            $stat->designer_id = $member->id;
            $stat->belong_dealer_id = $seller->id;
            $stat->save();
            if (!$stat){$this->respFail('系统错误');}

            if (!$member_detail){
                DB::rollback();
                $this->respFail('系统错误');
            }

            //累加品牌已授权设计师账号数字段
            $brand = $seller->brand;
            $quota_column = "quota_designer_dealer_used";
            $brand->increment($quota_column);

            $quota_column1 = "quota_designer_used";
            $seller->increment($quota_column1);

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


            $this->respData([],'设计师账号、设计师在线课堂账号均新增成功！');

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail($e);
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
        $seller = $loginAdmin->dealer;

        $data = Designer::query()
            ->OrganizationId($seller->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($input_data['id']);
        if(!$data){
            $this->respFail('信息不存在！');
        }

        //其他校验
        //品牌修改设计师密码，不用提供旧密码，直接修改

        if($input_data['new_password'] != $input_data['confirm_password']){
            $this->respFail('两次新密码输入不一致！');
        }

        DB::beginTransaction();

        try{
            $data->login_password = bcrypt(trim($input_data['new_password']));
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

    //调整积分
    public function modify_integral(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;

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
        if($value<=0 || $value<>$input_data['value']){
            $this->respFail('请填写正整数！');
        }

        if($value>$seller->point_money){
            $this->respFail('积分数值超过您账号的积分余额（'.$seller->point_money.'）！');
        }

        $data = Designer::query()
            ->OrganizationId($seller->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
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
            //设计师增加积分
            $detail->point_money = $input_data['value'];
            $result = $detail->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            //销售商扣减积分
            $seller->decrement('point_money',$value);

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

        //修改设计师
        $designer = Designer::query()
            ->with(['detail'=>function($query){
                $query->where('approve_realname','=',DesignerDetail::APPROVE_REALNAME_YES);
            }])
            ->OrganizationId($loginAdmin->dealer->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
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

            DesignerService::addToSearch();

            $designer->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }
}
