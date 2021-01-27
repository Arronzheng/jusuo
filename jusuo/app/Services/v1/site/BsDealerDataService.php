<?php
/**
 * BrandScopeDealerDataService
 * 异步获取dealer方法数据筛选（品牌域专用）
 */

namespace App\Services\v1\site;

use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use Illuminate\Support\Facades\DB;

class BsDealerDataService
{


    /**
     * 调用者：品牌主页->材料商家
     */
    public static function listBrandIndexDealer($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'cityId'=>0,
            'categoryId'=>0,
            'skip'=>0,
            'take'=>6
        ];
        
        $params = array_merge($default, $params);
        
        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $categoryId = $params['categoryId'];
        $skip = $params['skip'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        //经营品类信息
        $category = ProductCategory::find($categoryId);
        $categoryName = '';

        //品牌信息合法性
        if($brandId<0){
            return [];
        }
        $brand = OrganizationBrand::find($brandId);
        if(!$brand){
            return [];
        }

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //旗下所有销售商

            $builder = DB::table('organization_dealers as od')
                ->join('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
                ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('b.id',$brandId)
                ->where('od.status',OrganizationDealer::STATUS_ON);

        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //所在地可见销售商+所属销售商

            $locationInfo = LocationService::getClientCity($request);
            $cityId = 0;
            if(isset($locationInfo) && $locationInfo['city_id']){
                $cityId = $locationInfo['city_id'];
            }

            //符合条件的销售商id合集（默认放进所属销售商）
            $legalDealerIds =  [$loginDealerId];

            //获取所在地可见的销售商ids
            $areaVisibleDealerIds = [];
            if($cityId>0){
                $areaVisibleDealerIds = DetailDealer::query()
                    ->whereHas('dealer',function($dealer) use($brandId){
                        $dealer->where('p_brand_id',$brandId);
                    })//所在地可见的销售商需要在本品牌内
                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();


                $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);
            }

            /*SystemLogService::simple('品牌主页-》材料商家显示数据：',array(
                '所在地城市信息：'.\GuzzleHttp\json_encode($locationInfo),
                '所在地城市id：'.$cityId,
                '设计师id：'.$loginDesigner->id,
                '设计师所属销售商id：'.$loginDealerId,
                '所在地可见的销售商ids：'.\GuzzleHttp\json_encode($areaVisibleDealerIds),
                '最终符合条件的销售商ids：'.\GuzzleHttp\json_encode($legalDealerIds),
            ));*/

            $builder = DB::table('organization_dealers as od')
                ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('od.status',OrganizationDealer::STATUS_ON)
                ->whereIn('od.id',$legalDealerIds);

        }else{
            //其他
            return [];
        }

        //筛选产品数>0的
        $builder->where(function($query){
            //一级销售商
            $query->where(function($query1){
                $query1->where('od.level',1);
                $query1->where('d.count_product','>',0);
            });
            //二级销售商的产品数跟随一级销售商
            $query->orWhere(function($query1){
                $query1->where('od.level',2);
                $query1->whereExists(function ($level2Dealer) {
                    $level2Dealer->select(DB::raw(1))
                        ->from('organization_dealers as lv1dealer')
                        ->join('detail_dealers as lv1dealer_detail','lv1dealer.id','=','lv1dealer_detail.dealer_id')
                        ->whereRaw('od.p_dealer_id = lv1dealer.id and lv1dealer_detail.count_product > 0 ');
                });
            });
        });

        //经营品类
        if($categoryId) {
            $builder->where(['d.product_category' => $categoryId]);
            $categoryName = $category->name;
        }

        $builder->select([
            'd.dealer_id','d.star_level','d.self_introduction',
            'd.point_focus','d.url_avatar','d.self_photo','d.product_category',
            'od.web_id_code',
        ]);


        $builder->orderBy('d.point_focus','desc');
        $builder->orderBy('d.is_top','desc');
        $builder->orderBy('d.id','desc');

        $builder->groupBy('d.dealer_id');

        $builder->skip($skip)
            ->take($take);


        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner,$categoryName,$request){
            $v->short_name = DealerService::getDealerNameRule($v->dealer_id,$request,$loginDesigner);

            $v->star_level = StrService::str_num_to_char($v->star_level).'级';
            $v->url_avatar = url($v->url_avatar);
            $photo = unserialize($v->self_photo);
            $v->self_photo = url($photo[0]);
            if($categoryName==''){
                $category = ProductCategory::find($v->product_category);
                if ($category)
                    $v->category = $category->name;
            }
            else{
                $v->category = $categoryName;
            }

            unset($v->dealer_id);
            return $v;
        });

        return [
            'dealer'=>$res,
            'brand'=>$brandId
        ];
    }

    /**
     * 调用者：材料商家列表主页->热门推荐
     */
    public static function listDealerIndexHotData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'skip'=>0,
            'take'=>7,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $skip = $params['skip'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        //品牌信息合法性
        if($brandId<0){
            return [];
        }
        $brand = OrganizationBrand::find($brandId);
        if(!$brand){
            return [];
        }

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //旗下所有销售商

            $builder = DB::table('organization_dealers as od')
                ->join('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
                ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('b.id',$brandId)
                ->where('od.status',OrganizationDealer::STATUS_ON);

        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //所在地可见销售商+所属销售商
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

            $builder = DB::table('organization_dealers as od')
                ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('od.status',OrganizationDealer::STATUS_ON)
                ->whereIn('od.id',$legalDealerIds);

        }else{
            //其他
            return [];
        }

        //筛选产品数>0的
        $builder->where(function($query){
            //一级销售商
            $query->where(function($query1){
                $query1->where('od.level',1);
                $query1->where('d.count_product','>',0);
            });
            //二级销售商的产品数跟随一级销售商
            $query->orWhere(function($query1){
                $query1->where('od.level',2);
                $query1->whereExists(function ($level2Dealer) {
                    $level2Dealer->select(DB::raw(1))
                        ->from('organization_dealers as lv1dealer')
                        ->join('detail_dealers as lv1dealer_detail','lv1dealer.id','=','lv1dealer_detail.dealer_id')
                        ->whereRaw('od.p_dealer_id = lv1dealer.id and lv1dealer_detail.count_product > 0 ');
                });
            });
        });

        //推荐
        $builder->orderBy('d.point_focus','desc');
        $builder->orderBy('d.is_top','desc');
        $builder->orderBy('d.id','desc');

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'd.dealer_id','d.url_avatar','od.web_id_code'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner,$request){
            $v->name = DealerService::getDealerNameRule($v->dealer_id,$request,$loginDesigner);
            $v->url_avatar = url($v->url_avatar);
            unset($v->dealer_id);
            return $v;
        });

        return $res;
    }

    /**
     * 调用者：材料商家列表主页->列表数据
     */
    public static function listDealerIndexData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'categoryId'=>0,
            'direction'=>0,
            'sort'=>DealerService::SORT_BY_DEFAULT,
            'skip'=>0,
            'take'=>10,
            'cityId'=>0,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $categoryId = $params['categoryId'];
        $direction = $params['direction'];
        $sortBy = $params['sort'];
        $skip = $params['skip'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        //品牌信息合法性
        if($brandId<0){
            return [];
        }
        $brand = OrganizationBrand::find($brandId);
        if(!$brand){
            return [];
        }

        if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
            //品牌设计师可见性
            //旗下所有销售商

            $builder = DB::table('organization_dealers as od')
                ->join('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
                ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('b.id',$brandId)
                ->where('od.status',OrganizationDealer::STATUS_ON);

        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            //所在地可见销售商+所属销售商
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

            $builder = DB::table('organization_dealers as od')
                ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('od.status',OrganizationDealer::STATUS_ON)
                ->whereIn('od.id',$legalDealerIds);
        }else{
            //其他
            return [];
        }

        //筛选经营品类
        if($categoryId>0){
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $builder->where('b.product_category',$categoryId);
            }else{
                $builder->where('od.product_category',$categoryId);
            }
        }

        //排序方式
        $direction = ($direction==0)?'desc':'asc';
        switch($sortBy){
            case DealerService::SORT_BY_FOCUS:
                $builder->orderBy('d.point_focus',$direction);
                break;
            case DealerService::SORT_BY_VIEW:
                $builder->orderBy('d.count_view',$direction);
                break;
            case DealerService::SORT_BY_ID:
                $builder->orderBy('od.id',$direction);
                break;
            default:
                $builder->orderBy('d.star_level',$direction);
                break;
        }

        //筛选产品数>0的
        $builder->where(function($query){
            //一级销售商
            $query->where(function($query1){
                $query1->where('od.level',1);
                $query1->where('d.count_product','>',0);
            });
            //二级销售商的产品数跟随一级销售商
            $query->orWhere(function($query1){
                $query1->where('od.level',2);
                $query1->whereExists(function ($level2Dealer) {
                    $level2Dealer->select(DB::raw(1))
                        ->from('organization_dealers as lv1dealer')
                        ->join('detail_dealers as lv1dealer_detail','lv1dealer.id','=','lv1dealer_detail.dealer_id')
                        ->whereRaw('od.p_dealer_id = lv1dealer.id and lv1dealer_detail.count_product > 0 ');
                });
            });
        });

        $builder->groupBy('od.id');

        //默认排序
        $builder->orderBy('d.is_top','desc');

        $total = $builder->count();

        $builder->skip($skip)
            ->take($take);

        $selectColumn = [
            'od.id','od.p_dealer_id','od.name','d.dealer_domain','od.web_id_code',
            'd.self_introduction','d.url_avatar',
            'd.count_designer','d.count_album','d.count_fav'
        ];

        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            array_push($selectColumn,'b.product_category');
        }else{
            array_push($selectColumn,'od.product_category');
        }

        $builder->select($selectColumn);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner,$request){
            //销售商名称
            $dealer_name = DealerService::getDealerNameRule($v->id,$request,$loginDesigner);
            $v->name = $dealer_name;

            //如果是二级销售商，则查找其上级销售商的产品
            $pDealerId = $v->p_dealer_id==0?$v->id:$v->p_dealer_id;
            $product = DB::table('product_ceramic_authorizations as pca')
                ->leftJoin('product_ceramics as pc', 'pc.id', '=', 'pca.product_id')
                ->where('pca.dealer_id', $pDealerId)
                ->where('pca.status', ProductCeramicAuthorization::STATUS_ON)  //销售商下架的产品下架的产品不在前端销售商主页显示
                ->where(function($query){
                    //销售商可设定价格的产品，在未设置价格前，不能展示在销售商前端页面。
                    //统一定价或不允许定价√
                    $query->whereIn('pca.price_way',[ProductCeramic::PRICE_WAY_UNIFIED,ProductCeramic::PRICE_WAY_NOT_ALLOW]);
                    //渠道定价或浮动定价，且已设置价格√
                    $query->orWhere(function($allowSerPrice){
                        $allowSerPrice->whereIn('pca.price_way',[ProductCeramic::PRICE_WAY_CHANNEL,ProductCeramic::PRICE_WAY_FLOAT]);
                        $allowSerPrice->where('pca.price','>',0);
                    });
                })
                ->orderBy('weight_sort', 'desc')
                ->get(['pc.photo_cover', 'pc.photo_product', 'pc.web_id_code', 'pc.name', 'pca.price']);
            $product->transform(function($p) {
                $photo_product = $p->photo_product;
                $photo_product = unserialize($photo_product);
                $photo_product = $photo_product[0];
                $p->photo_product = url($photo_product);
                if($p->price==0){
                    $p->price='价格面议';
                }
                else{
                    $p->price='￥'.$p->price;
                }
                return $p;
            });
            $v->product = $product;
            $v->url_avatar = url($v->url_avatar);
            $v->product_category = ProductCategory::getProductCategoryText($v->product_category);

            unset($v->id);
            unset($v->p_dealer_id);
            return $v;
        });

        unset($params['loginDesigner']);
        unset($params['loginDealerId']);
        unset($params['loginBrandId']);

        return [
            'dealer'=>$res,
            'params'=>$params,
            'total'=>$total,
        ];
    }

}