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
use App\Models\OrganizationDealer;

class OrganizationDealerService
{
    //地区字段使用“服务城市”字段

    //统计销售商设计师已开通和待审核数量
    public static function countSellerDesignerOnAndVerifying($seller_id)
    {
        $count_on = self::getSellerDesignerOnEntry($seller_id)->count();
        $count_verifying = self::getSellerDesignerVerifyingEntry($seller_id)->count();
        return $count_on+$count_verifying;
    }

    //获取品牌下的已开通品牌设计师
    public static function getSellerDesignerOnEntry($seller_id)
    {
        return Designer::where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->where('organization_id',$seller_id)
            ->where('status',Designer::STATUS_ON);
    }

    //获取品牌下的审核中品牌设计师
    public static function getSellerDesignerVerifyingEntry($seller_id)
    {
        return Designer::where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->where('organization_id',$seller_id)
            ->where('status',Designer::STATUS_VERIFYING);
    }

    //获取品牌下所有的一、二销售商
    public static function getBrandAllSellerEntry($brand_id)
    {
        return OrganizationDealer::query()
            ->where('p_brand_id',$brand_id);
    }

    //根据等级获取品牌下审核中的销售商
    public static function getBrandVerifyingSellerEntry($brand_id,$level=null)
    {
        $entry = OrganizationDealer::query()
            ->where('status',OrganizationDealer::STATUS_WAIT_VERIFY)
            ->where('p_brand_id',$brand_id);

        if($level!=null){
            $entry->where('level',$level);
        }

        return $entry;
    }

    public static function countBrandOnAndVerifySellerByLevel($brand_id,$level)
    {
        $seller_on_count = OrganizationDealerService::getBrandLegalSellerEntry($brand_id,$level)->count();
        $seller_verifying_count = OrganizationDealerService::getBrandVerifyingSellerEntry($brand_id,$level)->count();
        return $seller_on_count+$seller_verifying_count;
    }

    //根据等级获取品牌下合法的销售商
    public static function getBrandLegalSellerEntry($brand_id,$level=null)
    {
        $entry = OrganizationDealer::query()
            ->where('status',OrganizationDealer::STATUS_ON)
            ->where('p_brand_id',$brand_id);

        if($level!=null){
            $entry->where('level',$level);
        }

        return $entry;
    }

    //获取品牌下所有合法的销售商
    public static function getBrandAllLegalSellerEntry($brand_id)
    {
        return self::getBrandLegalSellerEntry($brand_id);
    }

    //获取品牌下合法的一级销售商
    public static function getBrandLegalSeller1Entry($brand_id)
    {
        return self::getBrandLegalSellerEntry($brand_id,1);
    }

    //获取品牌下合法的销售商管理员
    public static function getBrandLegalSellerAdminEntry($brand_id)
    {
        $seller_ids = OrganizationDealer::where('p_brand_id',$brand_id)
            ->where('status',OrganizationDealer::STATUS_ON)
            ->get()->pluck('id');

        return AdministratorDealer::query()->whereIn('id',$seller_ids);
    }

    //获取品牌下未审核的销售商管理员
    public static function getBrandVerifyingSellerAdminEntry($brand_id)
    {
        $seller_ids = OrganizationDealer::where('p_brand_id',$brand_id)
            ->where('status',OrganizationDealer::STATUS_WAIT_VERIFY)
            ->get()->pluck('id');

        return AdministratorDealer::query()->whereIn('id',$seller_ids);
    }



    /*-------------------以下方法弃用，一二级销售商已与服务城市无关20191107-------------------*/
    //获取品牌在某地区下合法的一级销售商
    public static function getBrandLegalSeller1InCityEntry($seller_id,$city_id)
    {
        return OrganizationDealer::query()
            ->whereHas('detail',function($query) use($city_id){
                $query->where('area_serving_id',$city_id);
            })
            ->where('status',OrganizationDealer::STATUS_ON)
            ->where('p_brand_id',$seller_id)
            ->where('level',1);
    }

    //获取品牌在某地区下的一级销售商（无论已通过审核或不通过审核）
    public static function getBrandAllSeller1InCityEntry($seller_id,$city_id)
    {
        return OrganizationDealer::query()
            ->whereHas('detail',function($query) use($city_id){
                $query->where('area_serving_id',$city_id);
            })
            ->where('p_brand_id',$seller_id)
            ->where('level',1);
    }



}