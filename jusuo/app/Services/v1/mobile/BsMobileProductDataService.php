<?php
/**
 * BrandScopeProductDataService
 * 异步获取product方法数据筛选（品牌域专用）
 */

namespace App\Services\v1\mobile;

use App\Models\Album;
use App\Models\AlbumProductCeramic;
use App\Models\Area;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\FavProduct;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicCollocation;
use App\Models\StatisticProductCeramic;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\PageService;
use App\Services\v1\site\ProductService;
use Illuminate\Support\Facades\DB;

class BsMobileProductDataService
{
    /**
     * 调用者：产品列表->列表数据
     */
    public static function listProducts($params,$request)
    {
        $default = [
            'dealerId'=>0,
            'skip'=>0,
            'take'=>6,
        ];

        $params = array_merge($default, $params);

        $take = $params['take'];

        $targetDealerId = $params['dealerId'];

        $dealer = OrganizationDealer::find($targetDealerId);
        if($dealer->level == 2){
            //二级销售商拿一级销售商的产品数据
            $targetDealerId = $dealer->p_dealer_id;
        }

        $builder = ProductCeramic::query()
            ->whereHas('authorize_dealer',function($query)use($targetDealerId){
                $query->where('dealer_id',$targetDealerId);
            });


        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);

        $builder->select(['code','photo_product','name','web_id_code']);

        $builder->orderBy('weight_sort','desc');

        $res = $builder->paginate($take);

        $res->transform(function($v){

            $temp = new \stdClass();
            $temp->web_id_code = '';
            $temp->code = '';
            $temp->name = '';
            $temp->photo_cover = '';

            $temp->web_id_code = $v->web_id_code;
            $temp->code = $v->code;
            $temp->name = $v->name;
            $photo_product = unserialize($v->photo_product);
            $temp->photo_cover = $photo_product[0];

            return $temp;
        });

        return $res;
    }

    /**
     * 调用者：产品列表->销售商全部产品列表数据
     */
    public static function listAllProducts($dealerId)
    {
        $res = DB::table('product_ceramic_authorizations as pca')
            ->leftJoin('product_ceramics as pc','pc.id','=','pca.product_id')
            ->where('pca.dealer_id',$dealerId)
            ->orderBy('pc.id','desc')
            ->get(['pc.photo_product','pc.web_id_code','pc.id','pc.series_id',
                'pc.name','pc.code','pc.spec_id']);

        foreach($res as $v){

        }
        foreach($res as $v){
            $v->photo_product = unserialize($v->photo_product);
            $v->photo_product = $v->photo_product[0];
            $series = CeramicSeries::find($v->series_id);
            $v->series = $series->name;
            $spec = CeramicSpec::find($v->spec_id);
            $v->spec = $spec->name;
        }

        return $res;
    }



    /**
     * 调用者：方案详情->产品清单
     */
    public static function listAlbumDetailProduct($params,$request)
    {
        $default = [
            'targetAlbumId'=>0,
            'skip'=>0,
            'take'=>30,
        ];

        $params = array_merge($default, $params);

        $take = $params['take'];

        $targetAlbumId = $params['targetAlbumId'];
        $targetAlbum = Album::find($targetAlbumId);
        $targetDesigner = Designer::find($targetAlbum->designer_id);

        //定位城市
        $locationSession = session()->get('location_city');
        $cityId = isset($locationSession)?$locationSession:0;
        $provinceId = 0;
        if($cityId){
            $city = Area::where('level',2)->where('id',$cityId)->first();
            if($city){
                $provinceId = $city->pid;
            }
        }

        //目标设计师的所属品牌
        $targetBrandId = 0;
        if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $targetBrandId = $targetDesigner->organization_id;

        }else if($targetDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $targetDealer = OrganizationDealer::find($targetDesigner->organization_id);
            $targetBrandId = $targetDealer->p_brand_id;
        }

        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            return [];
        }

        if($loginUserInfo['type'] == 'guest'){
            //游客
            //服务于所在地的销售商的产品
            //获取该方案绑定的产品id
            $album_product_ids = AlbumProductCeramic::where('album_id',$targetAlbumId)
                ->get()->pluck('product_ceramic_id')->toArray();

            //符合条件的销售商id合集（默认放进所属销售商）
            $legalDealerIds =  [];

            //获取所在地可见的销售商ids
            if($cityId>0){
                $areaVisibleDealerIds = DetailDealer::query()
                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();

                $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);
            }

            $builder = ProductCeramic::query()
                ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                    $query->whereIn('dealer_id',$legalDealerIds);
                })
                ->whereIn('id',$album_product_ids);
        }else{
            //登录设计师的信息
            $loginDesigner = $loginUserInfo['data'];

            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //品牌的所有产品
                //获取该方案绑定的产品id
                $album_product_ids = AlbumProductCeramic::where('album_id',$targetAlbumId)
                    ->get()->pluck('product_ceramic_id');

                $builder = ProductCeramic::query()
                    ->where([
                        'brand_id'=>$targetBrandId
                    ])
                    ->whereIn('id',$album_product_ids);

            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //所属销售商的产品+所在地可见销售商的产品
                $album_product_ids = AlbumProductCeramic::where('album_id',$targetAlbumId)
                    ->get()->pluck('product_ceramic_id');
                //符合条件的销售商id合集（默认放进所属销售商）
                $loginDealerId = $loginDesigner->organization_id;
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

                $builder = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    })
                    ->whereIn('id',$album_product_ids);
            }else{
                return [];
            }
        }

        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);

        $builder->select(['id','code','photo_product','photo_cover','name','spec_id','count_visit','count_fav','web_id_code']);

        $builder->orderBy('weight_sort','desc');

        $res = $builder->paginate($take);

        $res->transform(function($v)use($cityId,$provinceId,$request){

            //1、产品是先看本城市有没有这个产品的经销商
            //2、如果没有则再找本省份的
            //3、如果再没有，则再找品牌
            $rdata = new \stdClass();

            $rdata->code = $v->code;
            $rdata->web_id_code = $v->web_id_code;
            //获取第一张产品图为封面
            $rdata->cover =  '';
            $photo_product = $v->photo_product;
            $photo_product = unserialize($photo_product);
            $photo_product = $photo_product[0];
            $rdata->photo_product = url($photo_product);
            $rdata->name = $v->name;
            $rdata->count_fav = $v->count_fav;
            $rdata->count_visit = $v->count_visit;
            $rdata->count_album = 0;
            $statistic = StatisticProductCeramic::where('product_id',$v->id)
                ->orderBy('id','desc')
                ->first();
            if($statistic){
                $rdata->count_album = $statistic->count_album;
            }
            $price = $v->guide_price;
            $sales_name = '';
            if(isset($v->brand)){
                $sales_name = $v->brand->short_name;
            }

            //規格
            $rdata->spec = '';
            $spec = CeramicSpec::find($v->spec_id);
            if($spec){
                $rdata->spec = $spec->name;
            }
            $sales_area = '全国';

            //当前定位城市
            if(!$cityId){
                $rdata->sales_price = $price;
                $rdata->sales_name = $sales_name;
                $rdata->sales_area = $sales_area;
                return $rdata;
            }

            //获取产品在当前城市的经销信息
            $result = ProductService::getAreaProductSaleInfo($v->id,$cityId,$provinceId);

            $rdata->sales_price = $result['price'];
            $rdata->sales_name = $result['sales_name'];
            $rdata->sales_area = $result['sales_area'];

            unset($v->id);
            return $rdata;
        });

        return $res;
    }

    /**
     * 调用者：产品详情->产品搭配
     */
    public static function listProductDetailCollocation($params,$request)
    {
        $default = [
            'targetProductId'=>0,
            'skip'=>0,
            'take'=>6,
        ];

        $params = array_merge($default, $params);

        $take = $params['take'];

        $targetProductId = $params['targetProductId'];
        $targetProduct = ProductCeramic::find($targetProductId);

        //20200529直接获取搭配产品，点击后再判断可见性
        $accessories = $targetProduct->collocations()->get();

        $accessories->transform(function($v){
            $temp = new \stdClass();

            $product = ProductCeramic::find($v->collocation_id);

            $temp->web_id_code = '';
            $temp->code = '';
            $temp->name = '';
            $temp->technology_categories_text = '';
            $temp->spec_text = '';

            if($product){
                $temp->web_id_code = $product->web_id_code;
                $temp->code = $product->code;
                $temp->name = $product->name;
                //工艺类别
                $technology_categories = $product->technology_categories()->get()->pluck('name')->toArray();
                if(is_array($technology_categories) && count($technology_categories)>0){
                    $temp->technology_categories_text = implode('/',$technology_categories);
                }
                //规格
                $spec = CeramicSpec::find($product->spec_id);
                if($spec){$temp->spec_text = $spec->name;}
                //产品名
                $temp->name = $product->name;
                $temp->count_visit = $v->count_visit;
                $temp->count_fav = $v->count_fav;
                //关联方案数量
                $stat = StatisticProductCeramic::where('product_id',$v->id)
                    ->orderBy('id','desc')
                    ->first();
                if($stat) {
                    $temp->count_album = $stat->count_album;
                }
            }
            $temp->photo = \Opis\Closure\unserialize($v->photo);
            $temp->note = $v->note;

            return $temp;
        });
        return $accessories;

        //定位城市
        $locationSession = session()->get('location_city');
        $cityId = isset($locationSession)?$locationSession:0;
        $provinceId = 0;
        if($cityId){
            $city = Area::where('level',2)->where('id',$cityId)->first();
            if($city){
                $provinceId = $city->pid;
            }
        }

        //目标设计师的所属品牌
        $targetBrandId = $targetProduct->brand_id;

        $loginUserInfo = LoginService::getBsLoginUser($targetBrandId);

        if(!$loginUserInfo){
            return [];
        }

        if($loginUserInfo['type'] == 'guest'){
            //游客
            //服务于所在地的销售商的产品

            //符合条件的销售商id合集（默认放进所属销售商）
            $legalDealerIds =  [];

            //获取所在地可见的销售商ids
            if($cityId>0){
                $areaVisibleDealerIds = DetailDealer::query()
                    ->whereRaw('(area_visible_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" )')
                    ->get(['dealer_id'])->pluck('dealer_id')->toArray();

                $legalDealerIds = array_merge($legalDealerIds,$areaVisibleDealerIds);
            }

            $builder = ProductCeramic::query()
                ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                    $query->whereIn('dealer_id',$legalDealerIds);
                });
        }else{
            //登录设计师的信息
            $loginDesigner = $loginUserInfo['data'];

            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //品牌的所有产品

                $builder = ProductCeramic::query()
                    ->where([
                        'brand_id'=>$targetBrandId
                    ]);

            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师可见性
                //所属销售商的产品+所在地可见销售商的产品

                //符合条件的销售商id合集（默认放进所属销售商）
                $loginDealerId = $loginDesigner->organization_id;
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

                $builder = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    });
            }else{
                return [];
            }
        }

        //获取该产品绑定的搭配产品id
        $collocationProductIds = ProductCeramicCollocation::where('product_id',$targetProductId)
            ->get()->pluck('collocation_id')->toArray();
        $builder->whereIn('id',$collocationProductIds);

        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);

        $builder->select(['id','code','photo_product','name','count_fav','web_id_code','spec_id']);

        $builder->orderBy('weight_sort','desc');

        $builder->limit($take);
        $res = $builder->get();

        $res->transform(function($v)use($cityId,$provinceId,$loginUserInfo,$targetProductId,$request){

            $collocationRel = ProductCeramicCollocation::where('product_id',$targetProductId)
                ->where('collocation_id',$v->id)
                ->first();

            $temp = new \stdClass();
            $temp->web_id_code = '';
            $temp->code = '';
            $temp->name = '';
            $temp->technology_categories_text = '';
            $temp->spec_text = '';

            $temp->web_id_code = $v->web_id_code;
            $temp->code = $v->code;
            $temp->name = $v->name;
            //工艺类别
            $technology_categories = $v->technology_categories()->get()->pluck('name')->toArray();
            if(is_array($technology_categories) && count($technology_categories)>0){
                $temp->technology_categories_text = implode('/',$technology_categories);
            }
            //规格
            $spec = CeramicSpec::find($v->spec_id);
            if($spec){$temp->spec_text = $spec->name;}
            //产品名
            $temp->name = $v->name;

            $temp->count_visit = $v->count_visit;
            $temp->count_fav = $v->count_fav;
            //关联方案数量
            $stat = StatisticProductCeramic::where('product_id',$v->id)
                ->orderBy('id','desc')
                ->first();
            if($stat){
                $temp->count_album = $stat->count_album;

            }

            $temp->collected = false;
            if($loginUserInfo['type'] == 'designer'){
                $loginDesigner = $loginUserInfo['data'];
                $collected = FavProduct::where('designer_id',$loginDesigner->id)
                    ->where('product_id',$v->id)
                    ->first();
                if($collected){ $temp->collected = true; }
            }

            $temp->photo = \Opis\Closure\unserialize($collocationRel->photo);
            $temp->note = $collocationRel->note;

            return $temp;
        });

        return $res;
    }


}