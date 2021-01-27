<?php

namespace App\Http\Controllers\v1\admin\brand\sub_admin\brand;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorBrand;
use App\Models\OrganizationBrand;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class SubAdminController extends VersionController
{
    private $globalService;
    private $subAdminService;
    private $getNameServices;
    private $authService;

    public function __construct(GlobalService $globalService,
                                SubAdminService $subAdminService,
                                GetNameServices $getNameServices,
                                AuthService $authService
    ){

        $this->globalService = $globalService;
        $this->subAdminService = $subAdminService;
        $this->getNameServices = $getNameServices;
        $this->authService = $authService;

    }

    //账号列表
    public function account_index(\Illuminate\Http\Request $request)
    {


        return $this->get_view('v1.admin_brand.sub_admin.brand.account_index');
    }

    //账号创建
    public function account_create(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        //获取可选角色
        $roles = RoleBrand::query()
            ->where('brand_id',$brand->id)
            ->where('is_super_admin', 0)
            ->where('created_by_administrator_id',$loginAdmin->id)
            ->get()->toArray();

        //分配登录账号
        $account_name = $this->getNameServices->getBrandAdminAccountName($brand->id,$loginAdmin->level+1,$brand->product_category);

        return $this->get_view('v1.admin_brand.sub_admin.brand.account_edit',compact(
            'roles','account_name'
        ));

    }

    //修改密码
    public function modify_pwd($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $data = AdministratorBrand::query()
            ->where('brand_id',$brand->id)
            ->where('level','>=',$loginAdmin->level)
            ->where(function($query) use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                $query->orWhere('id',$loginAdmin->id);
            })            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_brand.sub_admin.brand.account_modify_pwd',compact(
            'data','loginAdmin'
        ));

    }

    //查看详情
    public function account_detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $data = AdministratorBrand::query()
            ->where('brand_id',$brand->id)
            ->where('level','>=',$loginAdmin->level)
            ->where(function($query) use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                $query->orWhere('id',$loginAdmin->id);
            })
            ->find($id);
        if(!$data){
            die('数据不存在');
        }

        return $this->get_view('v1.admin_brand.sub_admin.brand.account_detail',compact(
            'data'
        ));

    }

    //账号编辑
    public function account_edit($id)
    {
        $loginAdmin = $this->authService->getAuthUser();

        //不能修改自己的账号信息
        if($loginAdmin->id == $id){
            die('权限不足');
        }

        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $is_super_admin = $loginAdmin->is_super_admin;
        //获取可选角色
        $roles = RoleBrand::query()
            ->where('is_super_admin', 0)
            ->where('created_by_administrator_id',$loginAdmin->id)
            ->get()->toArray();

        $data = AdministratorBrand::query()
            ->with('roles')
            ->where('brand_id',$brand->id)
            ->where('level','>=',$loginAdmin->level)
            ->where(function($query) use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                $query->orWhere('id',$loginAdmin->id);
            })
            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_brand.sub_admin.brand.account_edit',compact(
            'roles','data','is_super_admin'
        ));

    }

    //查看账号权限树
    public function account_privilege($id)
    {
        $data = AdministratorBrand::query()
            ->find($id);
        if(!$data){
            return '';
        }

        return $this->get_view('v1.admin_brand.sub_admin.brand.account_privilege',compact(
            'data'
        ));

    }


}
