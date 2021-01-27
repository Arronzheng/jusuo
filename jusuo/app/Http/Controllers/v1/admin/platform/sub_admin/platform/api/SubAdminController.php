<?php

namespace App\Http\Controllers\v1\admin\platform\sub_admin\platform\api;

use App\Http\Controllers\Controller;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorPlatform;

use App\Models\AdministratorRolePlatform;
use App\Models\MsgAccountBrand;
use App\Models\MsgAccountPlatform;
use App\Models\RolePlatform;
use App\Models\RolePrivilegeBrand;
use App\Models\RolePrivilegePlatform;
use App\Services\v1\admin\MsgAccountBrandMultiService;
use App\Services\v1\admin\MsgAccountPlatformMultiService;
use App\Services\v1\admin\MsgAccountPlatformService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class SubAdminController extends ApiController
{
    private $globalService;
    private $roleOrganizationRepository;
    private $authService;
    private $infiniteTreeService;

    public function __construct(
        GlobalService $globalService,
        AuthService $authService,
        InfiniteTreeService $infiniteTreeService
    ){
        $this->globalService = $globalService;
        $this->authService = $authService;
        $this->infiniteTreeService = $infiniteTreeService;
    }


    //账号列表数据
    public function account_index(Request $request)
    {
        $requestData = $request->all();

        $loginAdmin = $this->authService->getAuthUser();

        $entry = AdministratorPlatform::query();

        $is_filter = false;

        //筛选角色
        if(isset($requestData['r']) && $requestData['r']!==''){
            $is_filter = true;
            $entry = $entry->whereHas('roles',function($query) use($requestData){
                $query->where('role_platforms.id',$requestData['r']);
            });
        }

        //筛选登录用户名/账号
        if(isset($requestData['ln']) && $requestData['ln']!==''){
            $is_filter = true;
            $entry = $entry->where(function ($query) use($requestData){
                $query->where('login_username','like',"%".$requestData['ln']."%");
                $query->orWhere('login_account','like',"%".$requestData['ln']."%");
            });
        }

        //筛选真实姓名
        if(isset($requestData['rn']) && $requestData['rn']!==''){
            $is_filter = true;
            $entry = $entry->where('realname','like',"%".$requestData['rn']."%");
        }

        //筛选时间
        if(isset($requestData['date_start']) && isset($requestData['date_end']) && $requestData['date_start']!=='' && $requestData['date_end']!==''){
            $is_filter = true;
            $entry->whereBetween('created_at', array($requestData['date_start'].' 00:00:00', $requestData['date_end'].' 23:59:59'));
        }

        $entry->with('roles')
            ->select('administrator_platforms.*')
            ->where(function($query)use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                    $query->orWhere('id',$loginAdmin->id);
            });

        $datas = $entry->get();

        $datas->transform(function($v)use($loginAdmin){
            $role_name = '';
            if(count($v->roles)>0){
                $role_names = $v->roles->pluck('display_name')->toArray();
                $role_name = implode(',',$role_names);
            }
            $v->role_name = $role_name;
            $levelFormat = '';
            for($i=0;$i<$v->level-$loginAdmin->level;$i++){
                $levelFormat.=" -- ";
            }
            $v->login_account = $levelFormat.$v->login_account;
            $v->isOn = $v->status==AdministratorPlatform::STATUS_ON;
            $v->canEdit = 0;
            if($v->created_by_administrator_id == $loginAdmin->id){
                $v->canEdit = 1;
            }
            $v->canModifyPwd = 0;
            if($loginAdmin->id == $v->id || $v->created_by_administrator_id == $loginAdmin->id){
                $v->canModifyPwd = 1;
            }
            $v->changeStatusApiUrl = url('admin/platform/sub_admin/api/account/'.$v->id.'/status');
            return $v;
        });

        if($is_filter != true){
            $datas = $this->infiniteTreeService->getFlatTree($datas,'created_by_administrator_id',$loginAdmin->created_by_administrator_id,$loginAdmin->level);
        }

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponseNoPage($datas);

        return json_encode($datas);
    }

    //获取管理员权限树
    public function get_admin_privilege($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $handleAdmin = AdministratorPlatform::query()
            ->where(function($query) use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                $query->orWhere('id',$loginAdmin->id);
            })
            ->where('level','>=',$loginAdmin->level)
            ->find($id);

        $selected_privileges = [5];

        $privilege_options = $handleAdmin->getAllPermissions()->toArray();

        $data['list'] = $privilege_options;
        $data['checkedId'] = $selected_privileges;

        $this->respData($data);
    }

    //账号保存
    public function account_store(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'login_account' => 'required',
            'login_username' => 'required',
            'login_mobile' => 'required',
            'realname' => 'required',
            'self_department' => 'required',
            'self_position' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //添加账号接收手机
        if ($input_data['login_mobile']!=''){
            $validator = Validator::make($input_data, [
                'login_mobile' => 'phone'
            ]);

            if ($validator->fails()) {
                $this->respFail('请填写正确的手机号！');
            }
        }

        //其他校验
        $role = RolePlatform::query()->find($input_data['role_id']);
        if(!$role){$this->respFail('角色错误！');}

        //判断登录用户名是否存在
        $exist = AdministratorPlatform::query()->where('login_username',$input_data['login_username'])->first();
        if($exist){$this->respFail('登录用户名已存在！');}

        $exist = AdministratorPlatform::query()->where('login_mobile',$input_data['login_mobile'])->first();
        if($exist){$this->respFail('手机号码已存在！');}

        DB::beginTransaction();

        $loginAdmin = $this->authService->getAuthUser();

        //新建账号
        try{
            $path = [0];
            $level = 0;
            $parent = $loginAdmin;
            if($parent){
                $path = explode(',',$parent->path);
                array_push($path,$parent->id);
                $level = $parent->level+1;
            }
            $path = implode(',',$path);

            $admin = new AdministratorPlatform();
            $admin->login_account = $input_data['login_account'];
            $admin->login_username = $input_data['login_username'];
            $admin->login_mobile = $input_data['login_mobile'];
            $admin->login_password = bcrypt($input_data['login_mobile']);
            $admin->self_department = $input_data['self_department'];
            $admin->self_position = $input_data['self_position'];
            $admin->realname = $input_data['realname'];
            $admin->created_by_administrator_id = $loginAdmin->id;
            $admin->path = $path;
            $admin->level= $level;

            $admin->save();

            //同步角色
            $admin->assignRole($role);

            DB::commit();

            //发送手机短信
            /*if ($input_data['telephone']){
                $msgService = new GetVerifiCodeService();
                $msg_content = '您的管理账号用户名为：'.$admin->login_username.'，初始密码为手机号码，请尽快登录修改，谢谢。';
                $msgService->sendMobile($input_data['telephone'],$msg_content);
            }*/

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
            $handleAdmin = AdministratorPlatform::where('created_by_administrator_id',$loginAdmin->id)->find($id);
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
        $role = RolePlatform::query()->find($input_data['role_id']);
        if(!$role){$this->respFail('角色错误！');}

        //判断登录用户名是否存在
        $exist = AdministratorPlatform::query()
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
                $parent = AdministratorPlatform::find($handleAdmin->created_by_administrator_id);
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
            if (isset($input_data['role_id']) && $handleAdmin->id!=$loginAdmin->id){
                //判断修改的管理员的角色是否被修改
                $current_admin_roles = $handleAdmin->roles;
                if(count($current_admin_roles)>0){
                    $current_admin_role = $current_admin_roles[0];
                    if($input_data['role_id'] != $current_admin_role->id){
                        //角色被修改了
                        //该管理员及其子孙所创建的角色的权限将全部清零，需通过这些管理员重新分配
                        $relatedFinalIds = [];
                        $childrenAdminsIds = AdministratorPlatform::query()
                            ->whereRaw(" find_in_set('".$handleAdmin->id."',path) ")
                            ->get()->pluck('id')->toArray();
                        if(count($childrenAdminsIds)>0){
                            $relatedFinalIds = array_merge($relatedFinalIds,$childrenAdminsIds);
                        }
                        $relatedFinalIds = array_merge($relatedFinalIds,[$handleAdmin->id]);
                        if(count($relatedFinalIds)>0){
                            $relatedRoleIds = RolePlatform::query()
                                ->whereIn('created_by_administrator_id',$relatedFinalIds)
                                ->get()->pluck('id')->toArray();
                            if($relatedRoleIds){
                                RolePrivilegePlatform::whereIn('role_id',$relatedRoleIds)->delete();
                            }

                            $multipleData = [];
                            $now_time = Carbon::now();
                            foreach($relatedFinalIds as $administrator_id){
                                $multipleData[] = [
                                    'administrator_id'=>$administrator_id,
                                    'content' =>'你的账号权限于'.$now_time.'被上级管理员修改了。请重新设置你所创建的角色的权限。',
                                    'type' => MsgAccountPlatform::TYPE_UPDATE_PRIVILEGE
                                ];
                            }

                            $result = MsgAccountPlatformMultiService::add($multipleData);
                            if(!$result){
                                DB::rollback();
                                $this->respFail('账号通知失败，请重新操作');
                            }
                        }

                        $handleAdmin->syncRoles([$role]);
                    }
                }
            }

            DB::commit();

            $this->respData();

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误');
        }

        $this->respFail();

    }

    //修改密码
    public function modify_pwd(Request $request)
    {
        $input_data = $request->all();

        $loginAdmin = $this->authService->getAuthUser();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);


        if($loginAdmin->id==$input_data['id']){
            //所有管理员修改自己的密码时
            $validateRules['old_password'] = 'required';
        }else{
            //修改下级管理员
            $handleAdmin = AdministratorPlatform::where('created_by_administrator_id',$loginAdmin->id)->find($input_data['id']);
            if (!$handleAdmin){
                $this->respFail('权限不足');
            }
        }

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $loginAdmin = $this->authService->getAuthUser();

        $admin = AdministratorPlatform::query()
            ->where(function($query) use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                $query->orWhere('id',$loginAdmin->id);
            })
            ->where('level','>=',$loginAdmin->level)
            ->find($input_data['id']);
        if(!$admin){
            $this->respFail('信息不存在！');
        }

        //其他校验
        if($loginAdmin->id==$input_data['id']){
            if (!Hash::check($input_data['old_password'], $admin->login_password)){
                $this->respFail('旧密码错误！');
            }
        }


        if($input_data['new_password'] != $input_data['confirm_password']){
            $this->respFail('两次新密码输入不一致！');
        }

        DB::beginTransaction();

        try{
            $admin->login_password = bcrypt($input_data['new_password']);
            $result = $admin->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            //写入账号通知
            $result1 = true;
            if($admin->created_by_administrator_id==$loginAdmin->id){
                $msg = new MsgAccountPlatformService();
                $msg->setAdministratorId($admin->id);
                $now_time = Carbon::now();
                $msg->setContent('您的密码于'.$now_time.'被上级管理员修改');
                $msg->setType(MsgAccountPlatform::TYPE_MODIFY_PWD);
                $result1= $msg->add_msg();
            }

            if(!$result ||!$result1){
                DB::rollback();
                $this->respFail('账号通知失败，请重新操作');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

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
            $handleAdmin = AdministratorPlatform::where('created_by_administrator_id',$loginAdmin->id)->find($id);
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

        //删除管理员及其所有下级管理员
        $handleAdmin->delete();

        $this->respData([]);

    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        if($loginAdmin->id == $id){
            //修改自己
            $handleAdmin = $loginAdmin;
            $this->respFail('权限不足');
        }else{
            //修改下级管理员
            $handleAdmin = AdministratorPlatform::where('created_by_administrator_id',$loginAdmin->id)->find($id);
            if (!$handleAdmin){
                $this->respFail('权限不足');
            }
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($handleAdmin->status==AdministratorPlatform::STATUS_OFF){
                $handleAdmin->status = AdministratorPlatform::STATUS_ON;
            }else{
                $handleAdmin->status = AdministratorPlatform::STATUS_OFF;
            }

            $handleAdmin->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

}
