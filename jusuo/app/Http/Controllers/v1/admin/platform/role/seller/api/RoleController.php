<?php

namespace App\Http\Controllers\v1\admin\platform\role\seller\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorDealer;
use App\Models\MsgAccountDealer;
use App\Models\PrivilegeDealer;
use App\Models\RoleDealer;
use App\Models\RolePrivilegeDealer;
use App\Services\v1\admin\MsgAccountSellerMultiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        $entry = RoleDealer::query();

        $entry->where('is_super_admin',RoleDealer::IS_SUPER_ADMIN_YES);

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
            $v->is_super_admin_text = RoleDealer::isSuperAdminGroup($v->is_super_admin);
            //创建此角色的管理员
            $v->created_by_administrator = '系统创建';
            if($v->created_by_administrator_id>0){
                $created_by_administrator = AdministratorDealer::find($v->created_by_administrator_id);
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

    //获取供销售商角色编辑用的权限树
    public function get_role_privilege_options(Request $request)
    {
        $role_id = $request->input('rid',0);

        $loginAdmin = $this->authService->getAuthUser();

        $selected_privileges = [];
        $role = null;
        if($role_id){
            //如果有具体的role_id
            $role = RoleDealer::find($role_id);
            if(!$role){$this->respFail('权限不足');}
            $selected_privileges = $role->permissions()->get()->pluck('id');
        }else{
            //获取当前登录用户的role
            $role = $this->authService->getAuthUser()->roles()->first();
            if(!$role){$this->respFail('权限不足');}
        }

        $privilege_options = PrivilegeDealer::where('shown',PrivilegeDealer::SHOWN_YES)->get();

        $sorted = collect($privilege_options)->sortBy(function ($privilege, $key) {
            $path = $privilege['path'];
            $path_num = $this->findNum($path);
            return $path_num.$privilege['sort'];
        });

        $privilege_options = $sorted->values()->all();

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

        $role = RoleDealer::find($inputData['id']);

        if(!$role){
            $this->respFail('权限不足！');
        }

        if(!$this->authService->getAuthUser()->is_super_admin){
            $this->respFail('不允许编辑！');
        }

        $exist_name = RoleDealer::query()
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

            //函数array_diff()返回出现在第一个数组中但其他输入数组中没有的值
            $need_delete_ids = array_diff($oldPrivileges,$newPrivileges);
            $need_add_ids = array_diff($newPrivileges,$oldPrivileges);

            if(array_diff($oldPrivileges,$newPrivileges) || array_diff($newPrivileges,$oldPrivileges)){

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
            }

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
            $data = RoleDealer::find($inputData['id']);
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