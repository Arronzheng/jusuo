<?php
/**
 * 手机端
 * 判断销售商相关页面访问可见性
 */

namespace App\Services\v1\mobile;


use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\Guest;
use App\Models\OrganizationDealer;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\PageService;
use Illuminate\Support\Facades\Auth;

class BsMobileDealerPageAccessService
{
    /**
     * 调用者：销售商详情页面访问
     */
    public static function dealerDetail($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $targetDealerId = $params['targetDealerId'];

        $targetDealer = OrganizationDealer::find($targetDealerId);
        $targetBrandId = $targetDealer->p_brand_id;

        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoService;
            return $result;
        }

        if($loginUserInfo['type'] == 'guest'){
            //游客
            //可打开服务于所在地的销售商

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

            if(!in_array($targetDealerId,$legalDealerIds)){
                $result['status'] = 0;
                $result['code'] = PageService::ErrorNoService;
                return $result;
            }

        }else{
            $loginDesigner = $loginUserInfo['data'];
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //可打开所有销售商
                //所以直接放行

            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //可打开所在地可见的销售商+所属销售商

                //所属销售商
                $loginDealerId = $loginDesigner->organization_id;;
                if($loginDealerId == $targetDealerId){
                    return $result;
                }

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
                }

                if(!in_array($targetDealerId,$legalDealerIds)){
                    $result['status'] = 0;
                    $result['code'] = PageService::ErrorNoService;
                    return $result;
                }

            }
        }



        return $result;
    }

}