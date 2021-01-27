<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/9/16
 * Time: 17:10
 */

namespace App\Services\common;


use App\Models\PrivilegeBrand;
use App\Models\PrivilegeDealer;
use App\Models\PrivilegePlatform;
use App\Models\RoleBrand;
use App\Models\RoleDealer;
use App\Models\RolePlatform;

class GuardRBACService
{

    //根据guard_name获取名称
    public static function getCNNameByGuard($guardName)
    {
        switch($guardName){
            case 'platform':
                return '平台';
                break;
            case 'brand':
                return '品牌';
                break;
            case 'seller':
                return '销售商';
                break;
            default:
                return '';
                break;
        }
    }

    //根据guard_name获取Role Model
    public static function getRoleModelByGuard($guardName)
    {
        switch($guardName){
            case 'platform':
                return RolePlatform::query();
                break;
            case 'brand':
                return RoleBrand::query();
                break;
            case 'seller':
                return RoleDealer::query();
                break;
            default:
                return null;
                break;
        }
    }

    //根据guard_name获取Role Model
    public static function getPrivilegeModelByGuard($guardName)
    {
        switch($guardName){
            case 'platform':
                return PrivilegePlatform::query();
                break;
            case 'brand':
                return PrivilegeBrand::query();
                break;
            case 'seller':
                return PrivilegeDealer::query();
                break;
            default:
                return null;
                break;
        }
    }

}