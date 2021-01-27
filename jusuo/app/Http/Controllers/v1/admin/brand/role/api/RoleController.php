<?php

namespace App\Http\Controllers\v1\admin\brand\role\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorRoleBrand;
use App\Models\MsgAccountBrand;
use App\Models\PrivilegeBrand;
use App\Models\RoleBrand;
use App\Models\RolePlatform;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\v1\admin\MsgAccountBrandMultiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends ApiController
{
    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //表格异步获取数据
    public function index(Request $request)
    {

        $keyword = $request->input('keyword',null);
        $isSuperAdmin = $request->input('is_super_admin',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;
        if(!$brand){
            $datas = LayuiTableService::getTableResponse([],1,'品牌不存在');
            return json_encode($datas);
        }

        $entry = RoleBrand::query();

        $entry->where('brand_id',$brand->id);

        //获取当前登录用户所创建的权限
        $entry->where('created_by_administrator_id',$loginAdmin->id);

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('display_name','like','%'.$keyword.'%');
            });
        }

        if($isSuperAdmin!==null){
            $entry->where('is_super_admin',$isSuperAdmin);
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            //是否超级管理员专用
            $v->is_super_admin_text = RoleBrand::isSuperAdminGroup($v->is_super_admin);
            //创建此角色的管理员
            $v->created_by_administrator = '系统创建';
            if($v->created_by_administrator_id>0){
                $created_by_administrator = AdministratorBrand::find($v->created_by_administrator_id);
                if($created_by_administrator){
                    $v->created_by_administrator = $created_by_administrator->realname;
                }
            }
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //获取供角色编辑用的权限树（仅提供当前登录管理员所拥有的权限）
    public function get_role_privilege_options(Request $request)
    {
        $role_id = $request->input('rid',0);

        $loginAdmin = $this->authService->getAuthUser();

        $selected_privileges = [];
        $role = null;
        if($role_id){
            //如果有具体的role_id
            $role = RoleBrand::find($role_id);
            if(!$role){$this->respFail('权限不足');}
            $selected_privileges = $role->permissions()->get()->pluck('id');
        }else{
            //获取当前登录用户的role
            $role = $this->authService->getAuthUser()->roles()->first();
            if(!$role){$this->respFail('权限不足');}
        }

        $privilege_options = $loginAdmin->getAllPermissions();

        $sorted = collect($privilege_options)->sortBy(function ($privilege, $key) {
            $path = $privilege['path'];
            $path_num = $this->findNum($path);
            return $path_num.$privilege['sort'];
        });

        $privilege_options = $sorted->values()
            //20200519增加，排序后，将不显示的去除
            ->where('shown',PrivilegeBrand::SHOWN_YES)
            ->all();

        $data['list'] = $privilege_options;
        $data['checkedId'] = $selected_privileges;

        $this->respData($data);
    }

    private function findNum($str=''){

        $str=trim($str);
        if(empty($str)){
            return '';
        }
        $temp=array('1','2','3','4','5','6','7','8','9','0');
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(in_array($str[$i],$temp)){
                $result.=$str[$i];
            }
        }
        return $result;
    }

    //新增角色
    public function store(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'display_name' => 'required',
            'description' => 'present',
        ]);

        if(!isset($inputData['privileges']) || !is_array($inputData['privileges'])){
            $inputData['privileges'] = [];
        }

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌信息不存在！');

        }

        //处理数据

        DB::beginTransaction();

        try{

            //新建
            $data = new RoleBrand();
            $data->name = StrService::strRandom();
            $data->display_name = $inputData['display_name'];
            $data->description = $inputData['description'];
            $data->created_by_administrator_id = $loginAdmin->id;
            $data->brand_id = $brand->id;
            $saveResult = $data->save();

            //分发角色权限
            $data->givePermissionTo($inputData['privileges']);

            if(!$saveResult){
                $this->respFail('操作错误！');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！'.json_encode($e->getMessage()));
        }

    }

    //更新数据提交示例
    public function update(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'id' => 'required',
            'display_name' => 'required',
            'description' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //处理数据
        if(!isset($inputData['privileges']) || !is_array($inputData['privileges'])){
            $inputData['privileges'] = [];
        }

        $role = RoleBrand::find($inputData['id']);

        if(!$role){
            $this->respFail('权限不足！');
        }

        if($role->is_super_admin && $this->authService->getAuthUser()->is_super_admin){
            $this->respFail('不允许编辑！');
        }

        $exist_name = RoleBrand::query()
            ->OfDisplayName($inputData['display_name'])
            ->where('id','<>',$role->id)
            ->first();
        if($exist_name){$this->respFail('角色名称已存在！');}


        DB::beginTransaction();

        try{

            //更新
            $role->display_name = $inputData['display_name'];
            $role->description = $inputData['description'];
            $saveResult = $role->save();

            if(!$saveResult){
                $this->respFail('操作错误！');
            }

            //如果修改了角色权限
            $oldPrivileges = $role->permissions()->get()->pluck('id')->toArray();
            $newPrivileges = $inputData['privileges'];


            //以下是新逻辑（20200715 1104 斌）
            if($oldPrivileges!=$newPrivileges){
                //如果取消了本角色的某些权限，则
                //1、将本角色绑定的管理员找出来
                //2、再找出1中的管理员的下级管理员
                //3、将1中的管理员及2中的下级管理员其所创建的角色的这些权限将全部删除
                $deletedPrivilegeIds = array_diff($oldPrivileges,$newPrivileges); //本次被取消的权限
                $addedPrivilegeIds = array_diff($newPrivileges,$oldPrivileges); //本次被增加的权限

                $relatedFinalIds = [];

                //获取与角色直接关联的管理员作为父管理员
                $relatedParentAdminIds = $role->admins()->get()->pluck('id')->toArray();

                //将父管理员放进处理id中
                $relatedFinalIds = array_merge($relatedFinalIds,$relatedParentAdminIds);

                //获取各父管理员的子孙管理员
                for($i=0;$i<count($relatedParentAdminIds);$i++){
                    $childrenAdminsIds = AdministratorBrand::query()
                        ->whereRaw(" find_in_set('".$relatedParentAdminIds[$i]."',path) ")
                        ->get()->pluck('id')->toArray();
                    if(count($childrenAdminsIds)>0){
                        $relatedFinalIds = array_merge($relatedFinalIds,$childrenAdminsIds);
                    }
                }



                //最终的管理员ids
                if(count($relatedFinalIds)>0){
                    $relatedRoleIds = RoleBrand::query()
                        ->whereIn('created_by_administrator_id',$relatedFinalIds)
                        ->get()->pluck('id')->toArray();

                    if($relatedRoleIds){
                        if($deletedPrivilegeIds>0){
                            //品牌相关管理员的角色删掉被取消的相关权限
                            RolePrivilegeBrand::query()
                                ->whereIn('privilege_id',$deletedPrivilegeIds)
                                ->whereIn('role_id',$relatedRoleIds)
                                ->delete();
                        }
                        if($addedPrivilegeIds>0){
                            //新分配给管理员的权限，这里不需要操作，等下级管理员自己分配给角色
                        }
                    }

                    //品牌管理员被上级管理员修改其权限树时，要写入msg_account_brands表
                    $multipleData = [];
                    $now_time = Carbon::now();
                    foreach($relatedFinalIds as $administrator_id){
                        $admin = AdministratorBrand::find($administrator_id);
                        $multipleData[] = [
                            'administrator_id'=>$administrator_id,
                            'brand_id' =>$admin->brand->id,
                            'content' =>'你的账号权限于'.$now_time.'被上级管理员修改了。',
                            'type' => MsgAccountBrand::TYPE_UPDATE_PRIVILEGE
                        ];
                    }

                    $result = MsgAccountBrandMultiService::add($multipleData);
                    if(!$result){
                        DB::rollback();
                        $this->respFail('账号通知失败，请重新操作');
                    }

                }

                //同步角色权限
                //为何不用laravel-permission的syncPermission，因为这个方法是detach所有再进行sync的，数据多会很慢

                if($deletedPrivilegeIds && count($deletedPrivilegeIds)>0){
                    $role->permissions()->detach($deletedPrivilegeIds);
                    $role->forgetCachedPermissions();
                    $role->load('permissions');
                }
                if($addedPrivilegeIds && count($addedPrivilegeIds)>0){
                    $role->permissions()->attach($addedPrivilegeIds);
                    $role->forgetCachedPermissions();
                    $role->load('permissions');
                }

            }

            //以下是旧逻辑
            //函数array_diff()返回出现在第一个数组中但其他输入数组中没有的值
            /*$need_delete_ids = array_diff($oldPrivileges,$newPrivileges);
            $need_add_ids = array_diff($newPrivileges,$oldPrivileges);
            if(array_diff($oldPrivileges,$newPrivileges) || array_diff($newPrivileges,$oldPrivileges)){
                //本角色所涉及的管理员（包含子孙），其所创建的角色的权限将全部清零，需通过这些管理员重新分配

                //获取与角色直接关联的管理员作为父管理员
                $relatedParentAdminIds = $role->admins()->get()->pluck('id')->toArray();
                $relatedFinalIds = [];
                //获取各父管理员的子孙管理员
                for($i=0;$i<count($relatedParentAdminIds);$i++){
                    $childrenAdminsIds = AdministratorBrand::query()
                        ->whereRaw(" find_in_set('".$relatedParentAdminIds[$i]."',path) ")
                        ->get()->pluck('id')->toArray();
                    if(count($childrenAdminsIds)>0){
                        $relatedFinalIds = array_merge($relatedFinalIds,$childrenAdminsIds);
                    }
                }
                //最终的管理员ids
                $relatedFinalIds = array_merge($relatedFinalIds,$relatedParentAdminIds);
                if(count($relatedFinalIds)>0){
                    $relatedRoleIds = RoleBrand::query()
                        ->whereIn('created_by_administrator_id',$relatedFinalIds)
                        ->get()->pluck('id')->toArray();
                    if($relatedRoleIds){
                        RolePrivilegeBrand::whereIn('role_id',$relatedRoleIds)->delete();
                    }

                    //品牌管理员被平台管理员修改其权限树时，要写入msg_account_brands表
                    $multipleData = [];
                    $now_time = Carbon::now();
                    foreach($relatedFinalIds as $administrator_id){
                        $admin = AdministratorBrand::find($administrator_id);
                        $multipleData[] = [
                            'administrator_id'=>$administrator_id,
                            'brand_id' =>$admin->brand->id,
                            'content' =>'你的账号权限于'.$now_time.'被上级管理员修改了。',
                            'type' => MsgAccountBrand::TYPE_UPDATE_PRIVILEGE
                        ];
                    }

                    $result = MsgAccountBrandMultiService::add($multipleData);
                    if(!$result){
                        DB::rollback();
                        $this->respFail('账号通知失败，请重新操作');
                    }
                }

                //同步角色权限
                //为何不用laravel-permission的syncPermission，因为这个方法是detach所有再进行sync的，数据多会很慢

                if($need_delete_ids && count($need_delete_ids)>0){
                    $role->permissions()->detach($need_delete_ids);
                    $role->forgetCachedPermissions();
                    $role->load('permissions');
                }
                if($need_add_ids && count($need_add_ids)>0){
                    $role->permissions()->attach($need_add_ids);
                    $role->forgetCachedPermissions();
                    $role->load('permissions');
                }
            }*/


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            SystemLogService::simple('',array(
                $e->getMessage(),
                $e->getTraceAsString()
            ));

            $this->respFail('系统错误！');
        }

    }

    //删除数据提交示例
    public function destroy(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('参数错误！');
        }

        DB::beginTransaction();

        try{

            //删除
            $data = RoleBrand::find($inputData['id']);
            if(!$data){
                $this->respFail('信息不存在！');
            }

            //若此角色有账号则无法删除
            $adminCount = $data->admins()->count();
            if($adminCount>0){
                $this->respFail('此角色已绑定管理员，无法删除！');
            }

            $result = $data->delete();

            if(!$result){
                $this->respFail('操作错误！');
            }

            //删除角色权限
            $data->admins()->sync([]); // Delete relationship data
            $data->permissions()->sync([]); // Delete relationship data

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

}