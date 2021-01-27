<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/9/24
 * Time: 15:59
 */

namespace App\Services\v1\admin;


use App\Models\AdministratorDealer;
use App\Models\Designer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;

class OrganizationBrandService{

    //获取正常状态的品牌
    public static function getBrandStatusOnEntry()
    {
        $entry = OrganizationBrand::query()->where('status',OrganizationBrand::STATUS_ON);

        return $entry;
    }

    //获取品牌下的已开通品牌设计师
    public static function getBrandDesignerOnEntry($brand_id)
    {
        return Designer::where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id)
            ->where('status',Designer::STATUS_ON);
    }

    //获取品牌下的审核中品牌设计师
    public static function getBrandDesignerVerifyingEntry($brand_id)
    {
        return Designer::where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id)
            ->where('status',Designer::STATUS_VERIFYING);
    }

    public static function countBrandDesignerOnAndVerifying($brand_id)
    {
        $count_on = self::getBrandDesignerOnEntry($brand_id)->count();
        $count_verifying = self::getBrandDesignerVerifyingEntry($brand_id)->count();
        return $count_on+$count_verifying;
    }

}