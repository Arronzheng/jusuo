<?php

namespace App\Services\v1\site;

use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\FavDealer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealerService
{
    const JOINER = '|';
    const SORT_BY_DEFAULT = 0;
    const SORT_BY_FOCUS = 1;
    const SORT_BY_VIEW = 2;
    const SORT_BY_ID = 3;

    public static function getDealerByCategory($params){
        $default = [
            'requestDesigner'=>isset($params['requestDesigner'])?$params['requestDesigner']:null,
            'brand_scope'=>'',
            'cityId'=>0,
            'categoryId'=>0,
            'isOrderByFocus'=>true,
            'skip'=>0,
            'take'=>4,
        ];
        $params = array_merge($default, $params);
        $categoryId = $params['categoryId'];
        $brand_scope = $params['brand_scope'];
        $cityId = $params['cityId'];
        $isOrderByFocus = $params['isOrderByFocus'];
        $categoryId = $params['categoryId'];
        $skip = $params['skip'];
        $take = $params['take'];

        $category = ProductCategory::find($categoryId);
        $categoryName = '';
        $brandId = $brand_scope;
        if($brandId>0){
            $requestDesigner = $params['requestDesigner'];
            if($requestDesigner==null){
                return [
                    'dealer'=>[],
                    'brand'=>$brandId
                ];
            }
            if($requestDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //旗下所有销售商
                $dealer = \DB::table('organization_dealers as od')
                    ->join('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
                    ->join('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                    ->where('b.id',$brandId)
                    ->where('count_product','>',0)
                    ->where('od.status',OrganizationDealer::STATUS_ON);

            }else if($requestDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商直属设计师可见性

            }else{
                return [
                    'dealer'=>[],
                    'brand'=>$brandId
                ];
            }

        }
        else {
            $dealer = \DB::table('organization_dealers as od')
                ->leftJoin('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                ->where('count_product','>',0)
                ->where('od.status',OrganizationDealer::STATUS_ON);
        }
        $city = Area::find($cityId);
        if($city&&$city->level==2){
            $dealer->whereRaw('(d.area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" or d.area_serving_city ="")');
        }
        if($category) {
            $dealer->where(['d.product_category' => $categoryId]);
            $categoryName = $category->name;
        }
        if($isOrderByFocus){
            $dealer->orderBy('d.point_focus','desc');
        }
        else{
            $dealer->orderBy('d.star_level','desc');
        }
        $dealer->orderBy('d.is_top','desc');
        $dealer->orderBy('d.id','desc');
        $dealer = $dealer->skip($skip)
            ->take($take)
            ->get(['d.dealer_id','od.web_id_code','d.star_level',
                'd.point_focus','d.url_avatar','d.self_photo','d.product_category']);
        $dealer->transform(function($v) use($categoryName){
            $d = OrganizationDealer::find($v->dealer_id);
            if($d){
                $v->short_name = $d->short_name;
            }
            else{
                $v->short_name = null;
            }
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
            return $v;
        });
        return [
            'dealer'=>$dealer,
            'brand'=>$brandId
        ];
    }

    //获取销售商显示名称
    public static function getDealerNameRuleLogin($dealer_id,$loginDesigner,$request)
    {
        //销售商名称显示规则:服务城市+上级品牌名
        //1、获取销售商服务城市
        $dealer_name = '';
        $dealer = OrganizationDealer::find($dealer_id);
        $dealer_detail = $dealer->detail;
        $brand = OrganizationBrand::find($dealer->p_brand_id);
        $dealer_name = $brand->brand_name;
        $dealer_serving_city = $dealer_detail->area_serving_city;
        $dealer_serving_city_array = explode('|',trim($dealer_serving_city,'|'));

        //记录目标页面内容所属品牌id
        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $pageBelongBrandId = $loginDesigner->organization_id;
            $serving_city_id = $dealer_serving_city_array[0];
        }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $loginDealer = DetailDealer::where('dealer_id',$loginDesigner->organization_id)->first();
            $visible_city = $loginDealer->area_visible_city;
            $visible_city_array = explode('|',trim($visible_city,'|'));
            $visible_city_array = array_intersect($visible_city_array,$dealer_serving_city_array);
            if(count($visible_city_array)>0){
                $serving_city_id = $visible_city_array[0];
            }
            else{
                $serving_city_id = $dealer_serving_city_array[0];
            }
        }
        $serving_city = Area::where('level',2)->find($serving_city_id);
        if($serving_city){
            $dealer_name = $serving_city->shortname.$dealer_name;

        }
        return $dealer_name;
    }

    //获取销售商显示名称
    public static function getDealerNameRule($dealer_id,$request,$loginDesigner=null)
    {
        //销售商名称显示规则:服务城市+上级品牌名
        //1、获取销售商服务城市
        $dealer_name = '';
        $dealer = OrganizationDealer::find($dealer_id);
        $dealer_detail = $dealer->detail;
        $brand = OrganizationBrand::find($dealer->p_brand_id);
        $dealer_name = $brand->brand_name;
        $dealer_serving_city = $dealer_detail->area_serving_city;
        $dealer_serving_city_array = explode('|',trim($dealer_serving_city,'|'));


        if($loginDesigner){
            //记录目标页面内容所属品牌id
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $serving_city_id = $dealer_serving_city_array[0];
            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $loginDealer = DetailDealer::where('dealer_id',$loginDesigner->organization_id)->first();
                $visible_city = $loginDealer->area_visible_city;
                $visible_city_array = explode('|',trim($visible_city,'|'));
                $visible_city_array = array_intersect($visible_city_array,$dealer_serving_city_array);
                $visible_city_array = array_values($visible_city_array); //关联数组转为索引数组
                if(count($visible_city_array)>0){
                    $serving_city_id = $visible_city_array[0];
                }
                else{
                    $serving_city_id = $dealer_serving_city_array[0];
                }
            }
            $serving_city = Area::where('level',2)->find($serving_city_id);
            if($serving_city){
                $dealer_name = $serving_city->shortname.$dealer_name;

            }
        }
        else {
            //首选跟当前定位城市一样的城市
            $location = LocationService::getClientCity($request);
            $city_id = (isset($location['city_id']) && $location['city_id'] <> 0) ? $location['city_id'] : 0;
            //存在定位城市、销售商有设置至少一个服务城市
            if ($city_id ) {
                if(count($dealer_serving_city_array) > 0){
                    if (in_array($city_id, $dealer_serving_city_array)) {
                        //有当前定位城市的服务城市
                        $serving_city_id = $city_id;
                    } else {
                        //无当前定位城市的服务城市，则获取第一个服务城市
                        $serving_city_id = $dealer_serving_city_array[0];
                    }
                    $serving_city = Area::where('level', 2)->find($serving_city_id);
                    if ($serving_city) {
                        $dealer_name = $serving_city->shortname . $dealer_name;

                    }
                }

            }else{
                if(count($dealer_serving_city_array) > 0){
                    //无定位城市信息，则获取第一个服务城市
                    $serving_city_id = $dealer_serving_city_array[0];
                    $serving_city = Area::where('level', 2)->find($serving_city_id);
                    if ($serving_city) {
                        $dealer_name = $serving_city->shortname . $dealer_name;

                    }
                }
            }
        }
        return $dealer_name;
    }

    public static function getDealerByFilter($params,$request){
        $default = [
            'brand'=>0,
            'category'=>0,
            'city'=>0,
            'skip'=>0,
            'take'=>10,
            'sort'=>DealerService::SORT_BY_DEFAULT,
            'direction'=>0,
        ];
        $params = array_merge($default, $params);
        $brandId = $params['brand'];
        $cityId = $params['city'];
        $categoryId = $params['category'];
        $skip = $params['skip'];
        $take = $params['take'];
        $sortBy = $params['sort'];
        $direction = $params['direction'];

        $dealer = \DB::table('organization_dealers as od')
            ->leftJoin('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
            ->leftJoin('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
            ->where('count_product','>',0)
            ->where('od.status',OrganizationDealer::STATUS_ON);
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
        $direction = ($direction==0)?'desc':'asc';
        switch($sortBy){
            case DealerService::SORT_BY_FOCUS:
                $dealer->orderBy('d.point_focus',$direction);
                break;
            case DealerService::SORT_BY_VIEW:
                $dealer->orderBy('d.count_view',$direction);
                break;
            case DealerService::SORT_BY_ID:
                $dealer->orderBy('od.id',$direction);
                break;
            default:
                $dealer->orderBy('d.star_level',$direction);
                break;
        }
        $dealer->orderBy('d.is_top','desc');
        $total = $dealer->count();
        $dealer = $dealer->skip($skip)
            ->take($take)
            ->get(['od.id','od.p_dealer_id','od.name','d.dealer_domain','od.web_id_code',
                'd.self_introduction','b.product_category','d.url_avatar',
                'd.count_designer','d.count_album','d.count_fav']);
        $dealer->transform(function($v) use($request){
            //销售商名称
            $dealer_name = self::getDealerNameRule($v->id,$request);
            $v->name = $dealer_name;

            //如果是二级销售商，则查找其上级销售商的产品
            $pDealerId = $v->p_dealer_id==0?$v->id:$v->p_dealer_id;
            $product = DB::table('product_ceramic_authorizations as pca')
                ->leftJoin('product_ceramics as pc', 'pc.id', '=', 'pca.product_id')
                ->where('pca.dealer_id', $pDealerId)
                ->where('pca.status', ProductCeramicAuthorization::STATUS_ON)  //销售商下架的产品下架的产品不在前端销售商主页显示
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
            return $v;
        });
        return [
            'dealer'=>$dealer,
            'params'=>$params,
            'total'=>$total,
        ];
    }

    public static function getDealerByCity($params,$request){
        $default = [
            'brand_scope'=>0,
            'isOrderByFocus'=>true,
            'city'=>0,
            'skip'=>0,
            'take'=>7,
        ];
        $params = array_merge($default,$params);
        $cityId = $params['city'];
        $brand_scope = $params['brand_scope'];
        $isOrderByFocus = $params['isOrderByFocus'];
        $skip = $params['skip'];
        $take = $params['take'];

        $city = Area::find($cityId);
        $brandId = $brand_scope;
        if($city&&$city->level==2){
            if($brandId>0){
                $dealer = \DB::table('organization_dealers as od')
                    ->leftJoin('organization_brands as b', 'od.p_brand_id', '=', 'b.id')
                    ->leftJoin('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                    ->where('b.id',$brandId)
                    ->where('od.status',OrganizationDealer::STATUS_ON)
                    ->whereRaw('(d.area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" or d.area_serving_city ="")');

                //筛选产品数>0的
                $dealer->where(function($query){
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
            }
            else{
                return [];
                $dealer = \DB::table('organization_dealers as od')
                    ->leftJoin('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
                    ->where('d.count_product','>',0)
                    ->where('od.status',OrganizationDealer::STATUS_ON)
                    ->whereRaw('(d.area_serving_city like "%' . DealerService::JOINER . $cityId . DealerService::JOINER . '%" or d.area_serving_city ="")');
            }
        }
        else{
            return [];
        }
        if($isOrderByFocus){
            $dealer->orderBy('d.point_focus','desc');
        }
        else{
            $dealer->orderBy('d.star_level','desc');
        }
        $dealer->orderBy('d.is_top','desc');
        $dealer->orderBy('d.id','desc');
        $dealer = $dealer->skip($skip)
            ->take($take)
            ->get(['d.dealer_id','d.url_avatar','od.web_id_code','d.url_avatar1']);
        $dealer->transform(function($v)use($request){
            $v->name = DealerService::getDealerNameRule($v->dealer_id,$request);

            $v->url_avatar = url($v->url_avatar);
            $v->url_avatar1 = url($v->url_avatar1);
            unset($v->dealer_id);
            return $v;
        });
        return $dealer;
    }

    public static function addToSearch()
    {
        $detailDealer = DetailDealer::where('d.id','>',0)
            ->leftJoin('organization_dealers as d','d.id','=','detail_dealers.dealer_id')
            ->orderBy('dealer_id')
            ->get(['dealer_id','area_serving_id','privilege_area_serving','d.p_dealer_id']);
        $count = 0;
        $dealerProductCount = [];
        if($detailDealer){
            foreach ($detailDealer as $v){
                $dealerId = $v->dealer_id;
                $cityId = $v->area_serving_id;
                $privilege = $v->privilege_area_serving;
                $pDealerId = $v->p_dealer_id;
                $cityString = '';
                $provinceString = '';
                if($privilege==DetailDealer::PRIVILEGE_AREA_SERVING_COUNTRY){
                    //$cityString = '';
                }
                else if($privilege==DetailDealer::PRIVILEGE_AREA_SERVING_CITY){
                    $city = Area::find($cityId);
                    if($city&&$city->level==3){
                        $city = Area::find($city->pid);
                        if($city&&$city->level==2){
                            $cityString = DealerService::JOINER.$cityId.DealerService::JOINER;
                        }
                    }
                }
                else{
                    $city = Area::find($cityId);
                    if($city&&$city->level==3){
                        $city = Area::find($city->pid);
                        if($city&&$city->level==2){
                            $province = Area::find($city->pid);
                            if($province&&$province->level==1) {
                                $city = Area::where('pid', $province->id)->pluck('id')->all();
                                $cityString = DealerService::JOINER.implode(DealerService::JOINER,array_unique($city)).DealerService::JOINER;
                                $provinceString = DealerService::JOINER.$province->id.DealerService::JOINER;
                            }
                        }
                    }
                }

                //设计师数、方案数、关注数
                $countGet = DB::table('designers as d')
                    ->leftJoin('designer_details as dd','dd.designer_id','=','d.id')
                    ->select(DB::raw('count(d.id) as count_designer, sum(dd.count_album) as count_album'))
                    ->where([
                        'd.organization_type'=>Designer::ORGANIZATION_TYPE_SELLER,
                        'd.organization_id'=>$dealerId,
                        'd.status'=>Designer::STATUS_ON,
                    ])->get(['count_designer','count_album']);

                $countGet = $countGet[0];
                if($countGet->count_album===null) {
                    $countGet->count_album = 0;
                }
                if($countGet->count_designer===null){
                    $countGet->count_designer = 0;
                }
                $count_fav = FavDealer::where('target_dealer_id',$dealerId)->count();

                //产品数
                if($pDealerId==0){
                    $productCount = DB::table('product_ceramic_authorizations as pca')
                        ->leftJoin('product_ceramics as pc','pc.id','=','pca.product_id')
                        ->where('pca.dealer_id',$dealerId)
                        ->where('pc.visible',ProductCeramic::VISIBLE_YES)
                        ->count();
                    $dealerProductCount[''.$dealerId]=$productCount;
                }
                else{
                    $productCount = $dealerProductCount[''.$pDealerId];
                }

                DetailDealer::where('dealer_id',$dealerId)->update([
                    //'area_serving_city' => $cityString,
                    //'area_serving_province' => $provinceString,
                    'count_designer' => $countGet->count_designer,
                    'count_album' => $countGet->count_album,
                    'count_product' => $productCount,
                    'count_fav' => $count_fav,
                ]);

                //经营品类
                $brand = OrganizationBrand::find($v->p_brand_id);
                if($brand){
                    OrganizationDealer::where('id',$v->id)->update(['product_category'=>$brand->product_category]);
                    DetailDealer::where('dealer_id',$v->id)->update(['product_category'=>$brand->product_category]);
                }

                $count++;
            }
        }

        $web_id_code_count = StrService::str_table_field_unique('organization_dealers');
        $web_id_code_count_1 = StrService::str_table_field_unique('organization_brands');

        return $count.' '.$web_id_code_count.' '.$web_id_code_count_1;
    }

    public static function getDealerBelongArea($dealerId){
        $cityStringShort = '';
        $provinceStringShort = '';

        $lng = 0;
        $lat = 0;

        /*$designer = Designer::find($dealerId);
        if(!$designer)
            return '';*/

        $organization = OrganizationDealer::find($dealerId);
        while($organization&&$organization->p_dealer_id<>0){
            $organization = OrganizationDealer::find($organization->p_dealer_id);
        }
        if(!$organization)
            return '';
        $dealer = DetailDealer::where('dealer_id',$organization->id)->first();
        if(!$dealer)
            return '';
        $dealer = OrganizationDealer::find($organization->id);
        if(!$dealer)
            return '';
        if($dealer->p_dealer_id>0){
            $dealer = DetailDealer::where('dealer_id',$dealer->p_dealer_id)->first();
        }
        else{
            $dealer = DetailDealer::where('dealer_id',$dealer->id)->first();
        }
        //dd($dealer);
        $cityId = $dealer->area_serving_city;
        if(!$cityId||strpos($cityId,'|')==false){
            return [
                'string'=>'',
                'lng'=>0,
                'lat'=>0
            ];
        }
        $cityId = explode('|',$cityId,3);
        $cityId = $cityId[1];
        $city = Area::find($cityId);

        if($city&&$city->level==2){

            $cityStringShort = $city->shortname;
            $lng = $city->lng;
            $lat = $city->lat;
            $province = Area::find($city->pid);
            if($province&&$province->level==1) {
                $provinceStringShort = $province->shortname;
            }
        }

        return [
            'string'=>$provinceStringShort.'/'.$cityStringShort,
            'lng'=>$lng,
            'lat'=>$lat
        ];
    }

    public static function getDealerLocationArea($dealerId)
    {
        $districtString = '';
        $cityString = '';
        $provinceString = '';
        $lng = 0;
        $lat = 0;

        $organization = OrganizationDealer::find($dealerId);
        if (!$organization)
            return '';
        $dealer = DetailDealer::where('dealer_id', $organization->id)->first();
        if (!$dealer)
            return '';
        $districtId = $dealer->self_district_id;
        $district = Area::find($districtId);
        if ($district && $district->level == 3) {
            $districtString = $district->name;
            $lng = $dealer->self_longitude;
            $lat = $dealer->self_latitude;
            $city = Area::find($district->pid);
            if ($city && $city->level == 2) {
                $cityString = $city->name;
                $province = Area::find($city->pid);
                if ($province && $province->level == 1) {
                    $provinceString = $province->name;
                }
            }
        }

        return [
            'string'=>$provinceString.$cityString.$districtString,
            'lng'=>$lng,
            'lat'=>$lat
        ];
    }

}