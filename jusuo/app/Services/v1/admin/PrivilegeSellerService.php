<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\PrivilegeDealer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrivilegeSellerService{

    public static function sync_brand_seller_privilege($brand_id)
    {
        /*----------根据品牌的新一级权限，更新品牌旗下销售商的一级权限及其子孙权限--------*/
        $finalSellerPrivileges = [];
        $administrator_brand = AdministratorBrand::where('brand_id',$brand_id)
            ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)->first();
        if(!$administrator_brand){
            return false;
        }
        $brandPrivilegeL1Names = $administrator_brand
            ->permissions()
            ->where('level',0)
            ->pluck('name')->toArray();
        //先查询所有销售商权限（下面用集合方法筛选，避免等下多次查询数据库）
        $sellerPrivileges = PrivilegeDealer::get();
        //根据上级品牌的一级菜单，根据相同的name找到关联的销售商的一级菜单
        $sellerPrivilegeL1 = $sellerPrivileges
            ->where('is_menu',1)
            ->whereIn('level',[0])
            ->whereIn('name',$brandPrivilegeL1Names)
            ->sortBy('path')
            ->sortBy('sort')
            ->sortBy('id')
            ->pluck('id')->toArray();
        //最终的销售商权限
        $finalSellerPrivileges = $sellerPrivileges
            ->filter(function ($item) use($sellerPrivilegeL1) {
                //与上面得出的销售商一级菜单id相同或者path包含id的，则通过
                $flag = false;
                if(in_array($item->id,$sellerPrivilegeL1)){
                    $flag = true;
                }else{
                    $path_ids = explode(',',$item->path);
                    if(array_intersect($sellerPrivilegeL1,$path_ids)){
                        $flag = true;
                    }
                }
                return $flag;
            })->pluck('id')->toArray();
        //只同步已通过审核的销售商
        $seller_ids = OrganizationDealer::where('p_brand_id',$brand_id)
            ->where('status',OrganizationDealer::STATUS_ON)
            ->get()->pluck('id')->toArray();
        $sellerAdmins = AdministratorDealer::whereIn('dealer_id',$seller_ids)->get();
        foreach($sellerAdmins as $admin){
            //同步销售商管理员权限
            //$admin->syncPermissions($finalSellerPrivileges);  //用权限包的这个方法会很慢
            $admin->permissions()->sync($finalSellerPrivileges);
        }
    }

    public static function sync_seller_privilege_by_new_privilege($privilege_id)
    {
        //获取权限信息
        $new_privilege = PrivilegeDealer::find($privilege_id);
        $new_privilege_name = $new_privilege->name;

        //查询有哪些品牌超级管理员赋予了同名的品牌权限
        $brand_super_admins = AdministratorBrand::query()
            ->select(['id','brand_id'])
            ->whereHas('permissions',function($permission) use($new_privilege_name){
                $permission->where('name',$new_privilege_name);
            })
            ->get()->pluck('brand_id')->toArray();

        //筛选出这些品牌下的销售商
        $seller_ids = OrganizationDealer::whereIn('p_brand_id',$brand_super_admins)
            ->where('status',OrganizationDealer::STATUS_ON)
            ->get()->pluck('id')->toArray();
        $sellerAdmins = AdministratorDealer::whereIn('dealer_id',$seller_ids)->get();
        foreach($sellerAdmins as $admin){
            //同步销售商管理员权限
            $admin->givePermissionTo($new_privilege_name);
        }


    }

}