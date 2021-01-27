<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorOrganization;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class SubAdminService{

    //分配组织管理员
    public function get_account($organization_type)
    {
        $str = $this->get_account_name($organization_type);
        while(AdministratorOrganization::where('login_username',$str)->first()){
            $str = $this->get_account_name($organization_type);
        }

        return $str;

    }

    private function get_account_name($organization_type){
        //首字母是账号类别，品牌，经销商……
        //然后是注册年、月、随机串
        $account_name = '';

        switch($organization_type){
            case Organization::ORGANIZATION_TYPE_BRAND:
                $account_name.='bd';
                break;
            case Organization::ORGANIZATION_TYPE_SELLER:
                $account_name.='sr';
                break;
            case Organization::ORGANIZATION_TYPE_DESIGN_COMPANY:
                $account_name.='dc';
                break;
        }

        $account_name.=date('y');
        $account_name.=dechex(date('m'));
        $account_name.='_'.StrService::str_random(6);
        return $account_name;
    }

    //判断品牌登录用户是否有相关权限
    public static function check_brand_menu_privilege($privilege_name)
    {
//        if(Auth::guard('brand_sub_admin')->user()){
//            //如果是子管理员登录，则需要判断是否有相关权限
//            return \Illuminate\Support\Facades\Auth::guard('brand_sub_admin')->user()->hasPermission($privilege_name);
//        }else{
//            //如果是品牌商账号登录，则不需判断
//            return true;
//        }
        return true;
    }

    public static function getBrandAdminName($admin_id){
        $admin = AdministratorBrand::find($admin_id);
        //dd($admin);
        if(!$admin){
            return [
                'name'=>'',
                'department'=>'',
                'position'=>'',
            ];
        }
        return [
            'name'=>$admin->realname,
            'department'=>$admin->self_department,
            'position'=>$admin->self_position,
        ];
    }

}