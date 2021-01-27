<?php
/**
 * 判断产品相关页面访问可见性
 */

namespace App\Services\v1\site;


use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;

class BsProductPageAccessService
{
    /**
     * 调用者：产品列表面访问
     */
    public static function productIndex($params,$request)
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
     * 调用者：产品详情页面访问
     */
    public static function productDetail($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $loginDesigner = $params['loginDesigner'];
        $targetProductId = $params['targetProductId'];
        $loginDealerId = $params['loginDealerId'];
        $loginBrandId = $params['loginBrandId'];

        $targetProduct = ProductCeramic::find($targetProductId);

        //记录目标页面内容所属品牌id
        $pageBelongBrandId = 0;
        $pageBelongBrandId = $targetProduct->brand_id;
        session()->put('pageBelongBrandId',$pageBelongBrandId);

        //非品牌内设计师禁止访问
        if($loginBrandId != $pageBelongBrandId){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoAuthority;
            return $result;
        }

        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //品牌设计师可见性
            //品牌的所有产品
            if($targetProduct->brand_id == $loginBrandId){
                //通过
                return $result;
            }


        }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //销售商设计师可见性
            //所属销售商的产品+所在地可见销售商的产品

            $locationInfo = LocationService::getClientCity($request);
            $cityId = 0;
            if(isset($locationInfo) && $locationInfo['city_id']){
                $cityId = $locationInfo['city_id'];
            }

            //符合条件的销售商id合集（默认放进所属销售商）
            $legalDealerIds =  [$loginDealerId];

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

            $legalProductIds = ProductCeramic::query()
                ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                    $query->whereIn('dealer_id',$legalDealerIds);
                })
                ->get(['id'])->pluck('id')->toArray();

            SystemLogService::simple('销售商设计师打开产品详情',array(
                '$cityId:'.$cityId,
                '$loginDealerId:'.$loginDealerId,
                '$legalDealerIds:'.\GuzzleHttp\json_encode($legalDealerIds),
                '$legalProductIds:'.\GuzzleHttp\json_encode($legalProductIds),
            ));

            if(in_array($targetProductId,$legalProductIds)){
                return $result;
            }

        }

        //条件不符合，返回不可访问
        $result['status'] = 0;
        $result['code'] = PageService::ErrorNoAuthority;
        return $result;



    }

}