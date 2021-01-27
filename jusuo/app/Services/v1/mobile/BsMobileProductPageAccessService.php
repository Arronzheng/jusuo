<?php
/**
 * 手机端
 * 判断产品相关页面访问可见性
 */

namespace App\Services\v1\mobile;


use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\Guest;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Illuminate\Support\Facades\Auth;

class BsMobileProductPageAccessService
{
    /**
     * 调用者：产品详情页面访问
     */
    public static function productDetail($params,$request)
    {
        $result = [];
        $result['status'] = 1;
        $result['code'] = 0;
        $result['msg'] = '';

        $targetProductId = $params['targetProductId'];
        $targetProduct = ProductCeramic::find($targetProductId);

        //目标产品的所属品牌
        $targetBrandId = $targetProduct->brand_id;

        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            $result['status'] = 0;
            $result['code'] = PageService::ErrorNoAuthority;
            return $result;
        }



        if($loginUserInfo['type'] == 'guest'){
            //游客
            //服务于所在地的销售商的设计师的方案

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

                $legalProductIds = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    })
                    ->get(['id'])->pluck('id')->toArray();

                if(in_array($targetProductId,$legalProductIds)){
                    return $result;
                }
            }

        }else{
            //登录设计师的信息
            $loginDesigner = $loginUserInfo['data'];

            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //品牌的所有产品
                if($targetProduct->brand_id == $loginDesigner->organization_id){
                    //通过
                    return $result;
                }


            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //所属销售商的产品+所在地可见销售商的产品

                $locationSession = session()->get('location_city');
                $cityId = isset($locationSession)?$locationSession:0;

                $loginDealerId = $loginDesigner->organization_id;

                //符合条件的销售商id合集（默认放进所属销售商）
                $legalDealerIds =  [$loginDealerId];

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

                $legalProductIds = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    })
                    ->get(['id'])->pluck('id')->toArray();

                if(in_array($targetProductId,$legalProductIds)){
                    return $result;
                }


            }
        }

        SystemLogService::simple('手机端访问产品详情不符合条件',array(
            'Auth::guard("m_guest")->user()：'.json_encode(Auth::guard('m_guest')->user()),
            'Auth::user()：'.json_encode(Auth::user()),
            '$loginUserInfo：'.json_encode($loginUserInfo),
            '$targetProduct：'.json_encode($targetProduct)
        ));

        //不符合条件
        $result['status'] = 0;
        $result['code'] = PageService::ErrorNoAuthority;
        return $result;
    }

}