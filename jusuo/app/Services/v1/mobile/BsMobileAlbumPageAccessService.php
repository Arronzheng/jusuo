<?php
/**
 * 手机端
 * 判断设计师相关页面访问可见性
 */

namespace App\Services\v1\mobile;


use App\Models\Album;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\Guest;
use App\Models\OrganizationDealer;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Illuminate\Support\Facades\Auth;

class BsMobileAlbumPageAccessService
{
    /**
     * 调用者：方案详情页面访问
     */
    public static function albumDetail($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $targetAlbumId = $params['targetAlbumId'];
        $targetAlbum = Album::find($targetAlbumId);
        $targetDesigner = Designer::find($targetAlbum->designer_id);
        $targetDesignerId = $targetDesigner->id;

        //目标设计师的所属品牌
        $targetBrandId = 0;
        $targetDealerId = 0;
        if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $targetBrandId = $targetDesigner->organization_id;

        }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $targetDealer = OrganizationDealer::find($targetDesigner->organization_id);
            $targetDealerId = $targetDealer->id;
            $targetBrandId = $targetDealer->p_brand_id;
        }


        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoAuthority;
            return $result;
        }

        if($loginUserInfo['type'] == 'guest'){
            //游客
            //服务于所在地的销售商的设计师的方案
            if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //如果目标设计师是品牌设计师，则已经不符合条件。
                $result['status'] = 0;
                $result['code'] = PageService::ErrorNoAuthority;
                return $result;
            }

            //目标设计师是销售商设计师：

            //所在地
            $locationSession = session()->get('location_city');
            $cityId = isset($locationSession)?$locationSession:0;

            //符合条件的销售商id合集
            $legalDealerIds =  [];

            //获取服务于所在地的销售商ids
            if($cityId>0){
                $areaVisibleDealerIds = DetailDealer::query()
                    ->whereRaw('(area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();

                $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);
            }

            if(in_array($targetDealerId,$legalDealerIds)){
                return $result;
            }

        }else{
            //登录设计师的信息
            $loginDesigner = $loginUserInfo['data'];

            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //可见：品牌所有设计师的方案+旗下销售商的所有设计师的方案

                if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                    //目标设计师如果是品牌设计师
                    if($targetDesigner->organization_id == $targetBrandId){
                        return $result;
                    }else{
                        $result['status'] = 0;
                        $result['code'] = PageService::ErrorNoAuthority;
                        return $result;
                    }
                }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                    //目标设计师如果是销售商设计师
                    //则判断是否目标品牌的旗下销售商设计师
                    $validTargetDealerIds = OrganizationDealer::query()
                        ->where('p_brand_id',$targetBrandId)
                        ->get(['id'])->pluck('id')->toArray();

                    if(in_array($targetDesigner->organization_id,$validTargetDealerIds)){
                        return $result;
                    }else{
                        $result['status'] = 0;
                        $result['code'] = PageService::ErrorNoAuthority;
                        return $result;
                    }
                }


            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //品牌所有设计师的方案+所在地可见的销售商的设计师的方案+所属销售商的设计师的方案

                if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                    //目标设计师如果是品牌设计师
                    if($targetDesigner->organization_id == $targetBrandId){
                        return $result;
                    }else{
                        $result['status'] = 0;
                        $result['code'] = PageService::ErrorNoAuthority;
                        return $result;
                    }
                }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                    //目标设计师如果是销售商设计师
                    //1、判断目标设计师是否登录用户所属销售商的设计师
                    //登录设计师所属销售商
                    $loginDealerId = $loginDesigner->organization_id;
                    if($targetDesigner->organization_id == $loginDealerId){
                        return $result;
                    }else{
                        //2、判断目标设计师是否所在地可见的销售商的设计师
                        //所在地
                        $locationSession = session()->get('location_city');
                        $cityId = isset($locationSession)?$locationSession:0;

                        //符合条件的销售商id合集
                        $legalDealerIds =  [];

                        //获取所在地可见的销售商ids
                        if($cityId>0){
                            $loginDealer = OrganizationDealer::find($loginDealerId);
                            $loginBrandId = $loginDealer->p_brand_id;
                            $areaVisibleDealerIds = DetailDealer::query()
                                ->whereHas('dealer',function($dealer) use($loginBrandId){
                                    $dealer->where('p_brand_id',$loginBrandId);
                                })//所在地可见的销售商需要在本品牌内
                                ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                                ->get(['dealer_id'])->pluck('dealer_id')->toArray();

                            $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);

                            //获取可见销售商的设计师ids
                            $legalDesignerIds = Designer::query()
                                ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
                                ->whereIn('organization_id',$legalDealerIds)
                                ->get(['id'])->pluck('id')->toArray();

                            if(in_array($targetDesignerId,$legalDesignerIds)){
                                return $result;
                            }
                        }


                    }
                }

            }
        }

        //不符合条件
        $result['status'] = 0;
        $result['code'] = PageService::ErrorNoAuthority;
        return $result;
    }

}