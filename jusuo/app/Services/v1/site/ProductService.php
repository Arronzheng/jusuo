<?php

namespace App\Services\v1\site;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Models\Area;
use App\Models\CeramicSpec;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticProductCeramic;
use Illuminate\Support\Facades\DB;

class ProductService
{
    const JOINER = '|';
    const CATEGORY_COLLECTION = [0,1,2];

    public static function getProductByBrand($brandId, $isOrderByWeight=true, $skip=0, $take=6){
        $brand = OrganizationBrand::find($brandId);
        if($brand) {
            $product = ProductCeramic::where(['brand_id'=>$brandId,'visible'=>ProductCeramic::VISIBLE_YES]);
        }
        else{
            $product = ProductCeramic::where(['visible'=>ProductCeramic::VISIBLE_YES]);
        }
        if($isOrderByWeight){
            $product->orderBy('weight_sort','desc');
        }
        $product->orderBy('id','desc');
        $product = $product->skip($skip)
            ->take($take)
            ->get(['id','web_id_code','name','code','photo_cover','photo_product']);
        $product->transform(function($v){
            $photo = unserialize($v->photo_product);
            $v->photo_cover = url($photo[0]);
            return $v;
        });
        return $product;
    }

    public static function getProductByCategory($brand_scope, $categoryId, $isOrderByWeight=true, $skip=0, $take=6){
        //目前只支持瓷砖产品
        if(!in_array($categoryId, ProductService::CATEGORY_COLLECTION)){
            return [];
        }
        $category = ProductCategory::find($categoryId);
        if($categoryId>0&&!$category){
            return [];
        }
        $brandId = $brand_scope;
        if($brandId>0){
            $brand = OrganizationBrand::find($brandId);
            if($brand) {
                //在这里，根据不同的$categoryId，从不同的表查询产品
                $product = ProductCeramic::where(['brand_id'=>$brandId,'visible'=>ProductCeramic::VISIBLE_YES]);
            }
            else{
                return [];
            }
        }
        else{
            //在这里，根据不同的$categoryId，从不同的表查询产品
            $product = ProductCeramic::where(['visible'=>ProductCeramic::VISIBLE_YES]);
        }
        if($isOrderByWeight){
            $product->orderBy('weight_sort','desc');
        }
        $product->orderBy('id','desc');
        $product = $product->skip($skip)
            ->take($take)
            ->get(['id','web_id_code','name','code','photo_cover','photo_product']);
        $product->transform(function($v){
            $photo = unserialize($v->photo_product);
            $v->photo_cover = url($photo[0]);
            return $v;
        });
        return [
            'product'=>$product,
            'brand'=>$brandId
        ];
    }

    //获取某产品在某城市的经销信息
    public static function getAreaProductSaleInfo($product_id,$city_id,$province_id)
    {
        $price = '';
        $sales_name = '';
        $sales_area = '全国';

        $city = null;
        $province = null;
        $city_name = '';
        $province_name = '';

        if($city_id){
            $city = Area::find($city_id);
            $city_name = $city->shortname;
        }

        if($province_id){
            $province = Area::find($province_id);
            $province_name = $province->shortname;
        }


        $dealer_info = null;

        $entry =  DB::table('product_ceramic_authorizations as pca')
            ->select(['od.id as seller_id','od.short_name','pca.price_way','pca.price','pca.unit'])
            ->join('organization_dealers as od','pca.dealer_id','=','od.id')
            ->join('detail_dealers as dd','dd.dealer_id','=','od.id')
            ->where('pca.product_id',$product_id)
            ->orderBy('dd.point_focus','desc'); //按关注度（point_focus）排序 20200210确认

        //先看本城市有没有这个产品的经销商
        if($city){
            $dealer_info = $entry->where('dd.area_serving_city','like','%|'.$city_id.'|%')
                ->first();
            if($dealer_info){
                $sales_name = $dealer_info->short_name?:$dealer_info->name;
                $sales_area = $city_name;
            }

        }


        if($province){

            if(!$dealer_info){
                //本城市没有，再看看本省份有没有这个产品的经销商
                $dealer_info = $entry->where('dd.area_serving_province','like','%|'.$province_id.'|%')
                    ->first();

                if($dealer_info){
                    $sales_name = $dealer_info->short_name?:$dealer_info->name;
                    $sales_area = $province_name;
                }
            }
        }

        if($dealer_info){
            if($dealer_info->price==0){
                $price = '面议';
            }else{
                $price = '￥'.$dealer_info->price;
                if($dealer_info->unit){
                    $price.= '/'.ProductCeramicAuthorization::unitGroup($dealer_info->unit);
                }
            }
        }else{
            //本省份也没有，直接拿品牌的数据
            $product = ProductCeramic::find($product_id);
            if($product){
                $brand = OrganizationBrand::find($product->brand_id);
                $sales_name = $brand->short_name?:$brand->name;
                if($product->guide_price==0){
                    $price = '';
                }else{
                    $price = '￥'.$product->guide_price;
                }
            }


        }

        return [
            'sales_type' => $dealer_info?OrganizationService::ORGANIZATION_TYPE_SELLER:OrganizationService::ORGANIZATION_TYPE_BRAND,
            'sales_id' => $dealer_info?$dealer_info->seller_id:0,
            'price' => $price,
            'sales_name' => $sales_name,
            'sales_area' => $sales_area
        ];
    }

    public static function getCategoryName($categoryId){
        $category = ProductCategory::find($categoryId);
        if($category){
            return $category->name;
        }
        else{
            return '';
        }
    }

    public static function getCategory($organizationId, $organizationIsBrand=true){
        $brand = null;
        if(!$organizationIsBrand){
            $dealer = OrganizationDealer::find($organizationId);
            if($dealer){
                $brand = OrganizationBrand::find($dealer->p_brand_id);
            }
        }
        else{
            $brand = OrganizationBrand::find($organizationId);
        }
        if($brand){
            $category = ProductCategory::find($brand->product_category);
        }
        if($category){
            return [
                'id'=>$category->id,
                'name'=>$category->name,
            ];
        }
        return [
            'id'=>0,
            'name'=>'',
        ];
    }

    public static function getProductByDealer($params){
        $brand_scope = isset($params['brand_scope'])?$params['brand_scope']:0;
        $dealerId = isset($params['dealerId'])?$params['dealerId']:0;
        $cityId = isset($params['cityId'])?$params['cityId']:0;
        $categoryId = isset($params['categoryId'])?$params['categoryId']:0;
        $isOrderByWeight = isset($params['isOrderByWeight'])?$params['isOrderByWeight']:true;
        $needSpec = isset($params['needSpec'])?$params['needSpec']:true;
        $needCountAlbum = isset($params['needCountAlbum'])?$params['needCountAlbum']:true;
        $skip = isset($params['skip'])?$params['skip']:0;
        $take = isset($params['take'])?$params['take']:6;
        $brandId = $brand_scope;

        $dealer = \DB::table('organization_dealers as od')
            ->leftJoin('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
            ->leftJoin('detail_dealers as d', 'd.dealer_id', '=', 'od.id');
        if($cityId>0){
            $city = Area::find($cityId);
            if($city&&$city->level==2) {
                $dealer->whereRaw('(d.area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" or d.area_serving_city ="")');
            }
        }
        if($brandId>0){
            $dealer->where('b.id',$brandId);
        }
        if($categoryId>0){
            $dealer->where('b.product_category',$categoryId);
        }
        $dealer->where('od.id',$dealerId);
        if($dealer->count()==0){
            //dd($dealer,$brandId,$categoryId,$cityId);
            return [
                'data'=>[],
                'total'=>0,
                'dealerId'=>$dealerId,
                'categoryId'=>$categoryId,
            ];
        }
        $dealer = $dealer->first();
        if($dealer->p_dealer_id<>0){
            $pDealerId = $dealer->p_dealer_id;
        }
        else{
            $pDealerId = $dealer->dealer_id;
        }

        $product = DB::table('product_ceramic_authorizations as pca')
            ->leftJoin('product_ceramics as pc','pc.id','=','pca.product_id')
            ->where('pca.dealer_id',$pDealerId)
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
            ->where('pca.status',ProductCeramicAuthorization::STATUS_ON); //下架的产品不在前端销售商主页显示 20200312
        if($isOrderByWeight){
            $product->orderBy('weight_sort','desc');
        }

        $total = $product->count();

        $product = $product->skip($skip)
            ->take($take)
            ->get(['pc.photo_cover','pc.photo_product','pc.id','pc.web_id_code','pc.spec_id',
                'pc.name','pc.code','pc.sys_code','pca.price','count_visit','count_fav']);
        $product->transform(function($p)use($needSpec,$needCountAlbum) {
            $p->spec = '';
            if($needSpec){
                $spec = CeramicSpec::find($p->spec_id);
                if($spec){
                    $p->spec = $spec->name;
                }
            }
            $p->count_album = 0;
            if($needCountAlbum){
                $statistic = StatisticProductCeramic::where('product_id',$p->id)
                    ->orderBy('id','desc')
                    ->first();
                if($statistic){
                    $p->count_album = $statistic->count_album;
                }
            }
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
        return [
            'data'=>$product,
            'total'=>$total,
            'dealerId'=>$dealerId,
            'categoryId'=>$categoryId,
            'cityId'=>$cityId,
        ];
    }

    public static function addToSearch(){
        $web_id_code_count = StrService::str_table_field_unique('product_ceramics');
        return $web_id_code_count;
    }

}