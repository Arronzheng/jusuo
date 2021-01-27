<?php
namespace App\Http\Services\common;

use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\AdministratorPlatform;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;

class OrganizationService{


    const ORGANIZATION_TYPE_DESIGNER = 0;
    const ORGANIZATION_TYPE_PLATFORM = 1;
    const ORGANIZATION_TYPE_BRAND = 2;
    const ORGANIZATION_TYPE_SELLER = 3;
    const ORGANIZATION_TYPE_DESIGN_COMPANY = 4;

    const ORGANIZATION_TYPE_NAME_PLATFORM = 'platform';
    const ORGANIZATION_TYPE_NAME_BRAND = 'brand';
    const ORGANIZATION_TYPE_NAME_SELLER = 'seller';
    const ORGANIZATION_TYPE_NAME_DESIGN_COMPANY = 'designerCompany';

    //根据组织标识名获取数值
    public static function get_type_value_by_name($typeName)
    {
        switch($typeName){
            case self::ORGANIZATION_TYPE_NAME_PLATFORM:
                return self::ORGANIZATION_TYPE_PLATFORM;
                break;
            case self::ORGANIZATION_TYPE_NAME_BRAND:
                return self::ORGANIZATION_TYPE_BRAND;
                break;
            case self::ORGANIZATION_TYPE_NAME_SELLER:
                return self::ORGANIZATION_TYPE_SELLER;
                break;
            case self::ORGANIZATION_TYPE_NAME_DESIGN_COMPANY:
                return self::ORGANIZATION_TYPE_DESIGN_COMPANY;
                break;
        }
    }

    //根据组织标识名获取数值
    public static function organizationTypeGroup($key){
        $group=[
            self::ORGANIZATION_TYPE_DESIGNER=>'设计师',
            self::ORGANIZATION_TYPE_PLATFORM=>'平台',
            self::ORGANIZATION_TYPE_BRAND=>'品牌',
            self::ORGANIZATION_TYPE_SELLER=>'销售商',
            self::ORGANIZATION_TYPE_DESIGN_COMPANY=>'装饰公司'
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //根据管理员id获取组织
    public static function get_organization_by_admin_id($admin_id)
    {
        $value = null;
        $authService = new AuthService();
        $guardName = $authService->getAuthUserGuardName();
        $admin = null;
        switch($guardName){
            case self::ORGANIZATION_TYPE_NAME_PLATFORM:
                $admin = AdministratorPlatform::find($admin_id);
                break;
            case self::ORGANIZATION_TYPE_NAME_BRAND:
                $admin = AdministratorBrand::find($admin_id);
                break;
            case self::ORGANIZATION_TYPE_NAME_SELLER:
                $admin = AdministratorDealer::find($admin_id);
                break;
            default:break;
        }
        $organization = null;
        if(isset($admin)){
            switch($guardName){
                case self::ORGANIZATION_TYPE_NAME_BRAND:
                    $organization = OrganizationBrand::find($admin->brand_id);
                    break;
                case self::ORGANIZATION_TYPE_NAME_SELLER:
                    $organization = OrganizationDealer::find($admin->dealer_id);
                    break;
                default:break;
            }
            $value = $organization;
        }
        return $value;
    }

}