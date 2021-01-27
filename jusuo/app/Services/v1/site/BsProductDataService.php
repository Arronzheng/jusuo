<?php
/**
 * BrandScopeProductDataService
 * 异步获取product方法数据筛选（品牌域专用）
 */

namespace App\Services\v1\site;

use App\Models\AlbumProductCeramic;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\FavProduct;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use Illuminate\Support\Facades\DB;

class BsProductDataService
{

    /**
     * 调用者：品牌主页->热门产品
     * 品牌的所有产品
     */
    public static function listBrandIndexProduct($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'categoryId'=>0,  //20200422改为以工艺类别筛选
            'skip'=>0,
            'take'=>6,
        ];
        
        $params = array_merge($default, $params);
        
        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $technologyCategoryId = $params['categoryId'];  //20200422改为以工艺类别筛选
        $skip = $params['skip'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        //目前只支持瓷砖产品
        $category = CeramicTechnologyCategory::find($technologyCategoryId);
        if($technologyCategoryId>0&&!$category){
            return [];
        }

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
            //品牌的所有产品
            $builder = ProductCeramic::where([
                'brand_id'=>$brandId
            ]);

        }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
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

            $builder = ProductCeramic::query()
                ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                    $query->whereIn('dealer_id',$legalDealerIds);
                });

        }else{
            //其他
            return [];
        }

        //筛选工艺类别
        if($technologyCategoryId>0){
            $builder->whereHas('technology_categories',function($technology_category)use($technologyCategoryId){
                $technology_category->where('ceramic_technology_categories.id',$technologyCategoryId);
            });
        }

        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);

        $builder->orderBy('weight_sort','desc');
        $builder->orderBy('id','desc');

        $builder->groupBy('id');

        $data_count = $builder->count();

        $builder->skip($skip)
            ->take($take);

        $builder->select([
            'id','web_id_code','name','code','photo_cover','photo_product'
        ]);

        $res = $builder->get();

        $res->transform(function($v)use($loginDesigner){
            $photo = unserialize($v->photo_product);
            $v->photo_cover = url($photo[0]);

            unset($v->id);
            return $v;
        });

        return [
            'data'=>$res,
            'total'=>$data_count,
            'brand'=>$brandId,
            'params'=>$params
        ];
    }


    /**
     * 调用者：产品列表主页->列表数据
     * 品牌的所有产品
     */
    public static function listProductIndexData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'loginBrandId'=>'',
            'skip'=>0,
            'take'=>40,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
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

        //如果是筛选销售商下的产品
        //筛选销售商
        if($requestDealerId = $request->input('dlr',null)){
            $dealer = OrganizationDealer::where('web_id_code',$requestDealerId)->first();
            if($dealer) {
                $builder = ProductCeramic::where([
                    'brand_id'=>$brandId
                ]);
                $builder->select([
                    'id','brand_id','web_id_code','code','photo_product',
                    'guide_price','name','count_fav'
                ]);
                $builder->whereHas('authorize_dealer',function($query)use($dealer){
                    $query->where('dealer_id',$dealer->id);
                    $query->where(function($query){
                        //销售商可设定价格的产品，在未设置价格前，不能展示在销售商前端页面。
                        //统一定价或不允许定价√
                        $query->whereIn('product_ceramic_authorizations.price_way',[ProductCeramic::PRICE_WAY_UNIFIED,ProductCeramic::PRICE_WAY_NOT_ALLOW]);
                        //渠道定价或浮动定价，且已设置价格√
                        $query->orWhere(function($allowSerPrice){
                            $allowSerPrice->whereIn('product_ceramic_authorizations.price_way',[ProductCeramic::PRICE_WAY_CHANNEL,ProductCeramic::PRICE_WAY_FLOAT]);
                            $allowSerPrice->where('product_ceramic_authorizations.price','>',0);
                        });
                    });
                });
                $builder->with(['authorize_dealer'=>function($query)use($dealer){
                    $query->select(['id','dealer_id','product_id','price']);

                    $query->where('dealer_id',$dealer->id);
                    $query->where(function($query){
                        $query->whereIn('product_ceramic_authorizations.price_way',[ProductCeramic::PRICE_WAY_UNIFIED,ProductCeramic::PRICE_WAY_NOT_ALLOW]);
                        $query->orWhere(function($allowSerPrice){
                            $allowSerPrice->whereIn('product_ceramic_authorizations.price_way',[ProductCeramic::PRICE_WAY_CHANNEL,ProductCeramic::PRICE_WAY_FLOAT]);
                            $allowSerPrice->where('product_ceramic_authorizations.price','>',0);
                        });
                    });
                }]);
            }else{
                return [];
            }
        }else{

            //非筛选销售商


            if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_BRAND) {
                //品牌设计师可见性
                //品牌的所有产品
                $builder = ProductCeramic::where([
                    'brand_id'=>$brandId
                ]);

            }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性
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

                $builder = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    });


            }else{
                //其他
                return [];
            }

            $builder->select([
                'id','brand_id','web_id_code','code','photo_product','guide_price','name','count_fav'
            ]);
        }



        $builder->with(['brand'=>function($query){
            $query->select(['id','short_name']);
        }]);

        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);


        //搜索名称/型号
        if($search = $request->input('search','')){
            $like = '%'.$search.'%';

            $builder->where(function($query) use ($like){
                $query->where('name','like',$like);
                $query->orWhere('code','like',$like);
            });
        }

        //是否提交工艺类别
        if($technology_category = $request->input('tc',null)){
            $builder->whereHas('technology_categories',function($query) use ($technology_category){
                $query->where('ceramic_technology_categories.id',$technology_category);
            });
        }

        //是否提交规格
        if($spec = $request->input('sc',null)){
            $builder->whereHas('spec',function($query) use ($spec){
                $query->where('ceramic_specs.id',$spec);
            });
        }

        //是否提交系列
        if($series = $request->input('series',null)){
            $builder->whereHas('series',function($query) use ($series){
                $query->where('ceramic_series.id',$series);
            });
        }

        //是否提交色系
        if($color = $request->input('clr',null)){
            $builder->whereHas('colors',function($query) use ($color){
                $query->where('ceramic_colors.id',$color);
            });
        }

        //是否提交价格区间
        $min_price = $request->input('mip',null);
        $max_price = $request->input('map',null);
        if($min_price && $max_price){
            $min_price = floatval($min_price);
            $max_price = floatval($max_price);
            if($max_price < $min_price){
                return [];
            }
            if($min_price){
                $builder->where('guide_price','>',$min_price);
            }
            if($max_price){
                $builder->where('guide_price','<=',$max_price);
            }

        }

        //是否提交关键字
        if($keyword = $request->input('k',null)){
            $builder->whereRaw('(name like "%'.$keyword.'%" or code like "%'.$keyword.'%")');
        }

        //排序
        if($order = $request->input('order','')){
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['comples','pop','time','visit','price'])){
                    if($m[1] == 'comples'){
                        $builder->orderBy('weight_sort',$m[2]);
                    }else if($m[1] == 'pop'){
                        $builder->orderBy('count_visit',$m[2])->orderBy('count_fav',$m[2]);
                    }else if($m[1] == 'time'){
                        $builder->orderBy('created_at',$m[2]);
                    }else if($m[1] == 'visit'){
                        $builder->orderBy('count_visit',$m[2]);
                    }else if($m[1] == 'price'){
                        $builder->orderBy('guide_price',$m[2]);
                    }else{
                        $builder->orderBy('count_visit',$m[2])->orderBy('count_fav',$m[2]);
                    }
                }else{
                    $builder->orderBy('weight_sort','desc');
                }
            }
        }else{
            $builder->orderBy('weight_sort','desc');
        }

        $res = $builder->paginate($take);

        $res->transform(function($v)use($loginDesigner,$requestDealerId){
            $v->collected = false;
            if($loginDesigner){
                $collected = FavProduct::where('designer_id',$loginDesigner->id)->where('product_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            if($requestDealerId){
                $v->price = '';
                if(isset($v->authorize_dealer)){
                    $v->price = '￥'.$v->authorize_dealer[0]->price;

                }

            }else{
                $v->price = $v->guide_price==0?"":'￥'.$v->guide_price;

            }

            //获取第一张产品图为封面
            $v->cover =  '';
            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $v->cover = $photo_product[0];
            }

            unset($v->id);
            unset($v->brand_id);
            unset($v->guide_price);
            if(isset($v->brand) && $v->brand){
                unset($v->brand->id);
            }
            if($requestDealerId){
                unset($v->authorize_dealer);
            }
            return $v;
        });

        return $res;
    }

    /**
     * 调用者：方案详情->产品清单
     * 品牌的所有产品(此方案使用的产品)
     */
    public static function listAlbumDetailProduct($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'targetAlbumId'=>0,
            'loginBrandId'=>'',
            'skip'=>0,
            'take'=>6,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $loginDealerId = $params['loginDealerId'];
        $targetAlbumId = $params['targetAlbumId'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        $preview_brand_id = session('preview_brand_id');
        if(isset($preview_brand_id) && $preview_brand_id){
            //预览获取数据
            $album_product_ids = AlbumProductCeramic::where('album_id',$targetAlbumId)
                ->get()->pluck('product_ceramic_id');
            $builder = ProductCeramic::query()
                ->whereIn('id',$album_product_ids);
        }else{
            //正常获取数据

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
                //品牌的所有产品（+此方案使用的产品）

                //获取该方案绑定的产品id
                $album_product_ids = AlbumProductCeramic::where('album_id',$targetAlbumId)
                    ->get()->pluck('product_ceramic_id');
                $builder = ProductCeramic::query()
                    ->where([
                        'brand_id'=>$brandId
                    ])
                    ->whereIn('id',$album_product_ids);

            }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性
                //所属销售商的产品+所在地可见销售商的产品
                $album_product_ids = AlbumProductCeramic::where('album_id',$targetAlbumId)
                    ->get()->pluck('product_ceramic_id');

                $locationInfo = LocationService::getClientCity($request);
                $cityId = 0;
                if(isset($locationInfo) && $locationInfo['city_id']){
                    $cityId = $locationInfo['city_id'];
                }
                $loginDealerId = $loginDesigner->organization_id;
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

                $builder = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    })
                    ->whereIn('id',$album_product_ids);

            }else{
                //其他
                return [];
            }

        }


        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);

        $builder->select(['id','code','photo_product','name','count_fav','web_id_code']);

        $builder->orderBy('weight_sort','desc');

        $res = $builder->paginate($take);

        $res->transform(function($v)use($loginDesigner,$request){

            //1、产品是先看本城市有没有这个产品的经销商
            //2、如果没有则再找本省份的
            //3、如果再没有，则再找品牌
            $rdata = new \stdClass();

            $rdata->code = $v->code;
            $rdata->web_id_code = $v->web_id_code;
            //获取第一张产品图为封面
            $rdata->cover =  '';
            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $rdata->cover = $photo_product[0];
            }
            $rdata->name = $v->name;
            $rdata->count_fav = $v->count_fav;

            $price = $v->guide_price;
            $sales_name = '';
            if(isset($v->brand)){
                $sales_name = $v->brand->short_name;
            }

            $sales_area = '全国';

            //当前定位城市
            $location_info = LocationService::getClientCity($request);

            if(!$location_info){
                $rdata->sales_price = $price;
                $rdata->sales_name = $sales_name;
                $rdata->sales_area = $sales_area;
                return $rdata;
            }

            //找不到城市信息
            if(!isset($location_info['city_id'])){
                $location_info['city_id'] = 0;
                $location_info['province_id'] = 0;
            }

            //获取产品在当前城市的经销信息
            $result = ProductService::getAreaProductSaleInfo($v->id,$location_info['city_id'],$location_info['province_id']);

            $rdata->sales_price = $result['price'];
            $rdata->sales_name = $result['sales_name'];
            $rdata->sales_area = $result['sales_area'];

            unset($v->id);
            return $rdata;
        });

        return $res;
    }

    /**
     * 调用者：产品详情->同类产品
     * 品牌的所有产品(与该产品同系列的产品)
     */
    public static function listProductDetailKindData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'targetProductId'=>0,
            'seriesId'=>0,
            'loginBrandId'=>'',
            'skip'=>0,
            'take'=>4,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $targetProductId = $params['targetProductId'];
        $loginDealerId = $params['loginDealerId'];
        $take = $params['take'];
        $brandId = $loginBrandId;


        $targetProduct = ProductCeramic::find($targetProductId);
        $seriesId = $targetProduct->series_id;

        $preview_brand_id = session('preview_brand_id');
        if(isset($preview_brand_id) && $preview_brand_id){
            //预览获取数据
            //品牌的所有产品（+此产品同系列的产品）

            //获取该方案绑定的产品id
            $builder = ProductCeramic::where([
                'brand_id'=>$preview_brand_id
            ])
                ->where('id','<>',$targetProductId)
                ->where('type',ProductCeramic::TYPE_PRODUCT);

        }else{
            //正常获取数据

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
                //品牌的所有产品（+此产品同系列的产品）

                //获取该方案绑定的产品id
                $builder = ProductCeramic::where([
                    'brand_id'=>$brandId
                ])
                    ->where('id','<>',$targetProductId)
                    ->where('type',ProductCeramic::TYPE_PRODUCT);

            }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性
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

                $builder = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    });

            }else{
                //其他
                return [];
            }
        }



        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES
        ]);

        $builder->where('series_id',$seriesId);
        $builder->where('id','<>',$targetProductId);

        $builder->select(['id','web_id_code','code','name','guide_price','count_fav','photo_product']);

        $builder->inRandomOrder();

        $res = $builder->take($take)->get();

        $res->transform(function($v)use($loginDesigner,$request){
            $rdata = new \stdClass();

            $rdata->web_id_code = $v->web_id_code;
            $rdata->code = $v->code;

            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $rdata->photo_cover = $photo_product[0];
            }
            $rdata->name = $v->name;
            $rdata->count_fav = $v->count_fav;

            $price = '￥'.$v->guide_price;
            $sales_name = '';
            if(isset($v->brand)){
                $sales_name = $v->brand->short_name;
            }

            $sales_area = '全国';

            //当前定位城市
            $location_info = LocationService::getClientCity($request);

            if(!$location_info){
                $rdata->sales_price = $price;
                $rdata->sales_name = $sales_name;
                $rdata->sales_area = $sales_area;
                return $rdata;
            }

            //获取产品在当前城市的经销信息
            $result = ProductService::getAreaProductSaleInfo($v->id,$location_info['city_id'],$location_info['province_id']);

            $rdata->sales_price = $result['price'];
            $rdata->sales_name = $result['sales_name'];
            $rdata->sales_area = $result['sales_area'];

            $rdata->collected = false;
            if($loginDesigner){
                $collected = FavProduct::where('designer_id',$loginDesigner->id)->where('product_id',$v->id)->first();
                if($collected){ $rdata->collected = true; }
            }

            unset($v->id);

            return $rdata;
        });

        return $res;
    }

    /**
     * 调用者：产品详情->相似产品
     * 品牌的所有产品(随机显示同一品牌同一风格的4个产品)
     */
    public static function listProductDetailSimiliarData($params,$request)
    {
        $default = [
            'loginDesigner'=>isset($params['loginDesigner'])?$params['loginDesigner']:null,
            'loginDealerId'=>isset($params['loginDealerId'])?$params['loginDealerId']:null,
            'targetProductId'=>0,
            'styleId'=>0,
            'loginBrandId'=>'',
            'skip'=>0,
            'take'=>5,
        ];

        $params = array_merge($default, $params);

        $loginBrandId = $params['loginBrandId'];
        $loginDesigner = $params['loginDesigner'];
        $targetProductId = $params['targetProductId'];
        $loginDealerId = $params['loginDealerId'];
        $take = $params['take'];
        $brandId = $loginBrandId;

        $targetProduct = ProductCeramic::find($targetProductId);
        //$styleIds = $targetProduct->styles()->get()->pluck('id')->toArray();
        $colorIds = $targetProduct->colors()->get()->pluck('id')->toArray();
        $specId = $targetProduct->spec_id;


        $preview_brand_id = session('preview_brand_id');
        if(isset($preview_brand_id) && $preview_brand_id){
            //预览获取数据
            //品牌的所有产品（+随机显示同一品牌同一风格的4个产品）

            $builder = ProductCeramic::query()
                ->where('brand_id',$preview_brand_id)
                ->where('id','<>',$targetProductId)
                ->where('type',ProductCeramic::TYPE_PRODUCT);

        }else{
            //正常获取数据
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
                //品牌的所有产品（+随机显示同一品牌同一风格的4个产品）

                $builder = ProductCeramic::query()
                    ->where('brand_id',$loginBrandId)
                    ->where('id','<>',$targetProductId)
                    ->where('type',ProductCeramic::TYPE_PRODUCT);

            }else if($loginDesigner->organization_type==Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性
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

                $builder = ProductCeramic::query()
                    ->whereHas('authorize_dealer',function($query)use($legalDealerIds){
                        $query->whereIn('dealer_id',$legalDealerIds);
                    });

            }else{
                //其他
                return [];
            }

        }


        $builder->where([
            //'status'=>ProductCeramic::STATUS_PASS,
            'visible'=>ProductCeramic::VISIBLE_YES,
            'spec_id'=>$specId
        ]);
        $builder->where('id','<>',$targetProductId);

        $builder->whereHas('colors',function($query)use($colorIds){
            $query->whereIn('ceramic_colors.id',$colorIds);
        });

        $builder->select(['id','web_id_code','code','name','guide_price','count_fav','photo_product']);

        $builder->inRandomOrder();

        $res = $builder->take($take)->get();

        $res->transform(function($v)use($loginDesigner,$request){
            $rdata = new \stdClass();

            $rdata->web_id_code = $v->web_id_code;
            $rdata->code = $v->code;

            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $rdata->photo_cover = $photo_product[0];
            }
            $rdata->name = $v->name;
            $rdata->count_fav = $v->count_fav;

            $price = '￥'.$v->guide_price;
            $sales_name = '';
            if(isset($v->brand)){
                $sales_name = $v->brand->short_name;
            }

            $sales_area = '全国';

            //当前定位城市
            $location_info = LocationService::getClientCity($request);

            if(!$location_info){
                $rdata->sales_price = $price;
                $rdata->sales_name = $sales_name;
                $rdata->sales_area = $sales_area;
                return $rdata;
            }

            //获取产品在当前城市的经销信息
            $result = ProductService::getAreaProductSaleInfo($v->id,$location_info['city_id'],$location_info['province_id']);

            $rdata->sales_price = $result['price'];
            $rdata->sales_name = $result['sales_name'];
            $rdata->sales_area = $result['sales_area'];

            $rdata->collected = false;
            if($loginDesigner){
                $collected = FavProduct::where('designer_id',$loginDesigner->id)->where('product_id',$v->id)->first();
                if($collected){ $rdata->collected = true; }
            }

            unset($v->id);

            return $rdata;
        });

        return $res;
    }

}