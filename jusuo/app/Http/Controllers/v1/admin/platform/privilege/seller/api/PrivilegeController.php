<?php

namespace App\Http\Controllers\v1\admin\platform\privilege\seller\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\PrivilegeSellerService;
use App\Models\AdministratorDealer;
use App\Models\PrivilegeDealer;
use App\Models\RoleDealer;
use App\Models\RolePrivilegeDealer;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrivilegeController extends ApiController
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

        $entry = PrivilegeDealer::query();

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('display_name','like','%'.$keyword.'%');
                $query->orWhere('description','like','%'.$keyword.'%');
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
            $parent_id = $v->parent_id;
            $v->parent_name = '';
            $parent_privilege = PrivilegeDealer::find($parent_id);
            if($parent_privilege){
                $v->parent_name = $parent_privilege->display_name;
            }
            $v->shown_text = PrivilegeDealer::shownGroup($v->shown);
            $v->is_menu_text = PrivilegeDealer::isMenuGroup($v->is_menu);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //异步获取父级权限列表
    public function ajax_parent_privilege(Request $request)
    {
        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = PrivilegeDealer::select(['id','display_name','name','description'])
            ->orderBy('path','asc')
            ->orderBy('sort','asc')
            ->orderBy('created_at','desc');
            //->where('is_menu',PrivilegeDealer::IS_SHOW_MENU_YES) //默认只显示菜单权限

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->where('display_name','like',"%".$keyword."%");
                $query->orWhere('name','like',"%".$keyword."%");
                $query->orWhere('description','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'data'  =>$datas->items()
        ]);
    }


    //权限保存
    public function store(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'display_name' => 'required',
            'name' => 'required',
            'parent_id' => 'required',
            'url' => 'present',
            'shown' => 'required',
            'is_menu' => 'required',
            'is_super_admin' => 'required',
            'description' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $exist_name = PrivilegeDealer::OfName($input_data['name'])
            ->first();
        if($exist_name){$this->respFail('权限名称已存在！');}

        $parent_privilege = null;
        if($input_data['parent_id']){
            $parent_privilege = PrivilegeDealer::find($input_data['parent_id']);
            if(!$parent_privilege){
                if($exist_name){$this->respFail('上级权限不存在！');}
            }
        }


        DB::beginTransaction();

        try{
            $path = [0];
            $level = 0;
            if($parent_privilege){
                $path = explode(',',$parent_privilege->path);
                array_push($path,$parent_privilege->id);
                $level = $parent_privilege->level+1;
            }
            $path = implode(',',$path);

            //新建权限
            $privilege = new PrivilegeDealer();
            $privilege->display_name = $input_data['display_name'];
            $privilege->name = $input_data['name'];
            $privilege->parent_id = $input_data['parent_id'];
            $privilege->url = $input_data['url'];
            $privilege->shown = $input_data['shown'];
            $privilege->is_menu = $input_data['is_menu'];
            $privilege->is_super_admin = $input_data['is_super_admin'];
            $privilege->description = $input_data['description'];
            $privilege->path = $path;
            $privilege->level = $level;
            $privilege->guard_name = 'seller';
            $privilege->save();

            //更新超级管理员角色权限（20191125 超级管理员权限由上级决定）
            //PrivilegeService::sync_super_admin_role_privileges('seller');

            //如果增加了销售商权限，则要同步给赋予了本权限的一级父权限的销售商超级管理员(20200819)
            $path_array = explode(',',$path);
            if(isset($path_array[1])){
                $lv0_privilege_id = $path_array[1];
                $lv0_privilege = PrivilegeDealer::find($lv0_privilege_id);
                if($lv0_privilege){
                    $related_admins = $lv0_privilege->admins()->get();
                    foreach($related_admins as $admin){
                        $admin->givePermissionTo($privilege);
                    }
                }
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            SystemLogService::simple('平台新增销售商权限',array(
                $e->getTraceAsString()
            ));
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

    //权限更新
    public function update(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'display_name' => 'required',
            'name' => 'required',
            'parent_id' => 'required',
            'url' => 'present',
            'shown' => 'required',
            'is_menu' => 'required',
            'is_super_admin' => 'required',
            'description' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $privilege = PrivilegeDealer::find($input_data['id']);

        if(!$privilege){
            $this->respFail('权限不足！');
        }

        if($privilege->id == $input_data['parent_id']){
            $this->respFail('不能选自己为上级！');
        }

        $exist_name = PrivilegeDealer::OfName($input_data['name'])
            ->where('id','<>',$privilege->id)
            ->first();
        if($exist_name){$this->respFail('权限名称已存在！');}

        $parent_privilege = null;
        if($input_data['parent_id']){
            $parent_privilege = PrivilegeDealer::find($input_data['parent_id']);
            if(!$parent_privilege){
                if($exist_name){$this->respFail('上级权限不存在！');}
            }
        }

        //判断是否修改了parent_id
        $origin_parent_id = $privilege->parent_id;
        $new_parent_id = $input_data['parent_id'];

        //判断是否修改了是否超级管理员专用
        $origin_is_super_admin = $privilege->is_super_admin;
        $new_is_super_admin = $input_data['is_super_admin'];

        DB::beginTransaction();

        try{

            $path = [0];
            $level = 0;
            if($parent_privilege){
                $path = explode(',',$parent_privilege->path);
                array_push($path,$parent_privilege->id);
                $level = $parent_privilege->level+1;
            }
            $path = implode(',',$path);

            //更新权限
            $privilege->display_name = $input_data['display_name'];
            $privilege->name = $input_data['name'];
            $privilege->parent_id = $input_data['parent_id'];
            $privilege->url = $input_data['url'];
            $privilege->shown = $input_data['shown'];
            $privilege->is_menu = $input_data['is_menu'];
            $privilege->is_super_admin = $input_data['is_super_admin'];
            $privilege->description = $input_data['description'];
            $privilege->path = $path;
            $privilege->level = $level;
            $privilege->save();

            if($origin_parent_id != $new_parent_id){
                //如果修改过上级从属关系，则要更新本权限下级权限的path和level
                $child_privileges = PrivilegeDealer::where('path','like','%'.$privilege->id.'%')
                    ->orderBy('level','asc')
                    ->get();

                foreach($child_privileges as $child_privilege){
                    $parent_privilege = PrivilegeDealer::find($child_privilege->parent_id);
                    $path = [0];
                    $level = 0;
                    if($parent_privilege){
                        $path = explode(',',$parent_privilege->path);
                        array_push($path,$parent_privilege->id);
                        $level = $parent_privilege->level+1;
                    }
                    $path = implode(',',$path);
                    $child_privilege->path = $path;
                    $child_privilege->level = $level;
                    $child_privilege->save();
                }
            }


            //去除因修改超级管理员专用而失效的角色权限绑定记录
            //待开发
            if($origin_is_super_admin == 0 && $new_is_super_admin == 1){
                //如果将超级管理员专用从否改为是，则需要将之前与本权限绑定过的非超级管理员角色的绑定关系去除
                $invalid_privileges = DB::table('role_privilege_dealers as rpo')
                    ->select(['rpo.id'])
                    ->join('role_dealers as ro','ro.id','=','rpo.role_id')
                    ->where('ro.is_super_admin',0)  //找出不是超级管理员的角色
                    ->where('rpo.privilege_id',$privilege->id)  //并绑定了此权限
                    ->get();  //的角色权限绑定记录

                if($invalid_privileges->count()>0){
                    $ids = $invalid_privileges->pluck('id');
                    RolePrivilegeDealer::whereIn('id',$ids)->delete();
                }
            }

            //更新超级管理员角色权限（20191125 超级管理员权限由上级决定）
            //PrivilegeService::sync_super_admin_role_privileges('seller');


            DB::commit();

            $this->respData([]);
        }catch(\Exception $e){
            DB::rollback();
            SystemLogService::simple('修改销售商权限信息',array(json_encode($e)));
            $this->respFail('系统错误！');

        }



    }

    //权限删除
    public function destroy(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = PrivilegeDealer::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        //删除使用了此权限的角色权限绑定
        $data->admins()->sync([]);
        $data->roles()->sync([]);

        //删除权限
        $data->delete();

        $this->respData([]);

    }

}