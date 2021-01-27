<?php
/**
 * 判断销售商相关页面访问可见性
 */

namespace App\Services\v1\site;


use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\OrganizationDealer;

class BsDealerPageAccessService
{

    /**
     * 调用者：销售商列表页面访问
     */
    public static function dealerIndex($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $loginDesigner = $params['loginDesigner'];
        $loginBrandId = $params['loginBrandId'];

        $pageBelongBrandId = 0;

        //记录目标页面内容所属品牌id
        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $pageBelongBrandId = $loginDesigner->organization_id;
            session()->put('pageBelongBrandId',$pageBelongBrandId);
        }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
            $pageBelongBrandId = $loginDealer->p_brand_id;
            session()->put('pageBelongBrandId',$pageBelongBrandId);
        }

        //非品牌内设计师禁止访问
        if($loginBrandId != $pageBelongBrandId){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoAuthority;
            return $result;
        }

        return $result;
    }

    /**
     * 调用者：销售商详情页面访问
     */
    public static function dealerDetail($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $loginDesigner = $params['loginDesigner'];
        $targetDealerId = $params['targetDealerId'];
        $loginDealerId = $params['loginDealerId'];
        $loginBrandId = $params['loginBrandId'];

        $targetDealer = OrganizationDealer::find($targetDealerId);


        //记录目标页面内容所属品牌id
        $pageBelongBrandId = 0;
        $pageBelongBrandId = $targetDealer->p_brand_id;
        session()->put('pageBelongBrandId',$pageBelongBrandId);

        //非品牌内设计师禁止访问
        if($loginBrandId != $pageBelongBrandId){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoAuthority;
            return $result;
        }


        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //品牌设计师可见性
            //可打开所有销售商
            //所以直接放行

        }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //销售商设计师可见性
            //可打开所在地可见的销售商+所属销售商

            //所属销售商
            if($loginDealerId == $targetDealerId){
                return $result;
            }

            //所在地
            $locationInfo = LocationService::getClientCity($request);
            $cityId = 0;
            if(isset($locationInfo) && $locationInfo['city_id']){
                $cityId = $locationInfo['city_id'];
            }

            //符合条件的销售商id合集
            $legalDealerIds =  [];

            //获取所在地可见的销售商ids
            if($cityId>0){
                $areaVisibleDealerIds = DetailDealer::query()
                    ->whereHas('dealer',function($dealer) use($loginBrandId){
                        $dealer->where('p_brand_id',$loginBrandId);
                    })//所在地可见的销售商需要在本品牌内
                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();

                $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);
            }

            if(!in_array($targetDealerId,$legalDealerIds)){
                $result['status'] = 0;
                $result['code'] = PageService::ErrorNoService;
                return $result;
            }

        }

        return $result;
    }

}