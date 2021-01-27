<?php
/**
 * 判断方案相关页面访问可见性
 */

namespace App\Services\v1\site;


use App\Models\Album;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\OrganizationDealer;

class BsAlbumPageAccessService
{

    /**
     * 调用者：方案列表页面访问
     */
    public static function albumIndex($params,$request)
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
     * 调用者：方案详情页面访问
     */
    public static function albumDetail($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $loginDesigner = $params['loginDesigner'];
        $targetAlbumId = $params['targetAlbumId'];
        $loginDealerId = $params['loginDealerId'];
        $loginBrandId = $params['loginBrandId'];

        $targetAlbum = Album::find($targetAlbumId);
        $targetDesigner = Designer::find($targetAlbum->designer_id);
        $targetDesignerId = $targetDesigner->id;
        $targetDealer = null;

        $pageBelongBrandId = 0;

        //记录目标页面内容所属品牌id
        if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $pageBelongBrandId = $targetDesigner->organization_id;
            session()->put('pageBelongBrandId',$pageBelongBrandId);
        }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $targetDealer = OrganizationDealer::find($targetDesigner->organization_id);
            $pageBelongBrandId = $targetDealer->p_brand_id;
            session()->put('pageBelongBrandId',$pageBelongBrandId);
        }

        //非品牌内设计师禁止访问
        if($loginBrandId != $pageBelongBrandId){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoAuthority;
            return $result;
        }


        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //品牌设计师可见性
            //品牌所有设计师的方案+旗下销售商的所有设计师的方案

            if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //目标设计师是否品牌直属设计师
                if($targetDesigner->organization_id == $loginBrandId){
                    return $result;
                }
            }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //目标设计师所属销售商是否品牌旗下销售商
                if($targetDealer->p_brand_id == $loginBrandId){
                    return $result;
                }
            }else{
                $result['status'] = 0;
                $result['code'] = PageService::ErrorNoAuthority;
                return $result;
            }

        }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //销售商设计师可见性
            //品牌所有设计师的方案+所在地可见的销售商的设计师的方案+所属销售商的设计师的方案

            $targetDesigner = Designer::find($targetDesignerId);
            if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //目标设计师是否品牌直属设计师
                if($targetDesigner->organization_id == $loginBrandId){
                    return $result;
                }
            }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){

                //目标设计师是否登录设计师的所属销售商旗下
                if($targetDesigner->organization_id == $loginDealerId){
                    return $result;
                }

                //目标设计师是否所在地可见的销售商的设计师
                $legalDealerIds = [];
                $legalDesignerIds = [];

                $locationInfo = LocationService::getClientCity($request);
                $cityId = 0;
                if(isset($locationInfo) && $locationInfo['city_id']){
                    $cityId = $locationInfo['city_id'];
                }
                //获取所在地可见的销售商ids
                if($cityId>0){
                    $areaVisibleDealerIds = DetailDealer::query()
                        ->whereHas('dealer',function($dealer) use($loginBrandId){
                            $dealer->where('p_brand_id',$loginBrandId);
                        })//所在地可见的销售商需要在本品牌内
                        ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                        ->get(['dealer_id'])->pluck('dealer_id')->toArray();

                    $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);

                    $legalDesignerIds = Designer::query()
                        ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                        ->whereIn('organization_id',$legalDealerIds)
                        ->get(['id'])->pluck('id')->toArray();
                }
                //获取可见销售商的设计师ids
                if(in_array($targetDesignerId,$legalDesignerIds)){
                    return $result;
                }

            }else{
                $result['status'] = 0;
                $result['code'] = PageService::ErrorNoAuthority;
                return $result;
            }

        }

        //条件不符合，返回不可访问
        $result['status'] = 0;
        $result['code'] = PageService::ErrorNoAuthority;
        return $result;
    }

}