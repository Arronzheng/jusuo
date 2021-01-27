<?php

namespace App\Http\Controllers\v1\mobile\mall\api;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\HouseType;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\LikeAlbum;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Models\ShoppingAddress;
use App\Models\SiteConfigPlatform;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\StatisticDesigner;
use App\Models\Style;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\admin\StatisticDesignerService;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsAlbumDataService;
use App\Services\v1\site\BsProductDataService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\IntegralGoodService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class IndexController extends ApiController
{
    private $globalService;

    public function __construct(
        GlobalService $globalService
    ){
        $this->globalService = $globalService;
    }


    //首页-》Banner数据
    public function index_banner()
    {
        $loginBrandId =  session('designer_scope.brand_id');

        $builder = Banner::query()
            ->where('status',Banner::STATUS_ON)
            ->where('position',Banner::POSITION_INTEGRAL_INDEX)
            ->where('brand_id',$loginBrandId)
            ->orderBy('sort','desc')
            ->orderBy('id','desc');

        $banners = $builder->get();

        $result = [];

        if($banners->count()>0){
            foreach($banners as $item){
                $temp = [];
                $temp['image'] = $item->photo;
                $temp['url'] = $item->url;
                array_push($result,$temp);
            }
        }else{
            return $this->respDataReturn([]);
        }

        return $this->respDataReturn($result);
    }

    //首页-》获取一级类别
    public function index_cat1s()
    {
        $entry = IntegralGoodsCategory::query()
            ->where('pid',0)
            ->where('status',IntegralGoodsCategory::STATUS_ON)
            ->select(['id','name','photo','pid']);


        $entry->orderBy('sort','desc');
        $entry->orderBy('id','desc');

        $datas =$entry->limit(4)->get();

        //$datas = $this->globalService->array_to_tree($datas,'pid');



        return $this->respDataReturn($datas);
    }

    //首页-》精选推荐
    public function recommend_goods(Request $request)
    {

        $entry = IntegralGood::query()
            ->where('status',IntegralGood::STATUS_ON)
            ->where('top_status',IntegralGood::TOP_STATUS_ON)
            ->select(['web_id_code','cover','market_price','name','short_intro','integral','exchange_amount'])
            ->limit(3);

        $entry->orderBy('sort','desc');
        $entry->orderBy('id','desc');

        $datas =$entry->get();

        return $this->respDataReturn($datas);
    }

    //首页-》获取筛选项
    public function filter_options(Request $request)
    {
        $query = $request->input('query','');
        $query_string = urldecode($query);
        parse_str($query_string,$query_array);

        //20200712只显示本品牌的
        $loginDesigner = Auth::user();
        $loginBrandId =  session('designer_scope.brand_id');
        if(!isset($loginBrandId) || !$loginBrandId){
            $loginBrandId = 0;
        }
        $integral_brand_ids = IntegralGood::where([
            'brand_id'=>$loginBrandId,
            'status'=>IntegralGood::STATUS_ON,
        ])->groupBy('integral_brand_id')->pluck('integral_brand_id');

        $result = [];

        //获取筛选品牌
        $integral_brands = IntegralBrand::query()
            ->select(['id','name'])
            ->where('status',IntegralBrand::STATUS_ON)
            ->whereIn('id',$integral_brand_ids)
            ->get();
        $brands_info = array();
        $brands_info['title'] = '品牌';
        $brands_info['value'] = 'b';
        $brands_info['has_selected'] = false;
        if(isset($query_array[$brands_info['value']])){
            $type_query = $query_array[$brands_info['value']];
            for($i=0;$i<count($integral_brands);$i++){
                if($integral_brands[$i]->id == $type_query){
                    $integral_brands[$i]->selected = 1;
                    $brands_info['has_selected'] = true;
                }else{
                    $integral_brands[$i]->selected = 0;
                }
            }
        }
        $brands_info['data'] = $integral_brands;
        $result[] = $brands_info;

        //获取筛选二级分类
        $category2 = [];
        $cat2_info = array();
        $cat2_info['title'] = '分类';
        $cat2_info['value'] = 'c2';
        $cat2_info['has_selected'] = false;
        if(isset($query_array['c1']) && $query_array['c1']>0){
            $category2 = IntegralGoodsCategory::query()
                ->where('pid',$query_array['c1'])
                ->select(['id','name'])
                ->where('status',IntegralGoodsCategory::STATUS_ON)
                ->get();

            if(isset($query_array[$cat2_info['value']])){
                $type_query = $query_array[$cat2_info['value']];
                for($i=0;$i<count($category2);$i++){
                    if($category2[$i]->id == $type_query){
                        $category2[$i]->selected = 1;
                        $cat2_info['has_selected'] = true;
                    }else{
                        $category2[$i]->selected = 0;
                    }
                }
            }
        }
        $cat2_info['data'] = $category2;
        $result[] = $cat2_info;


        //获取积分范围
        $integral_ranges = [];
        $integral_config_row = SiteConfigPlatform::query()
            ->where('var_name','integral_config')
            ->first();
        if($integral_config_row){
            $integral_config = \Opis\Closure\unserialize($integral_config_row->content);
            if(isset($integral_config['filter_range'])){
                foreach($integral_config['filter_range'] as $item){
                    array_push($integral_ranges,$item['start'].'-'.$item['end']);
                }
            }
        }
        //索引数组转关联数组
        $new_ranges = [];
        for($i=0;$i<count($integral_ranges);$i++){
            $temp = [
                'id'=>$integral_ranges[$i],
                'name'=>$integral_ranges[$i]
            ];
            array_push($new_ranges,$temp);
        }
        $range_info = array();
        $range_info['title'] = '积分范围';
        $range_info['value'] = 'r';
        $range_info['has_selected'] = false;
        if(isset($query_array[$range_info['value']])){
            $type_query = $query_array[$range_info['value']];
            for($i=0;$i<count($new_ranges);$i++){
                if($new_ranges[$i]->id == $type_query){
                    $new_ranges[$i]->selected = 1;
                    $range_info['has_selected'] = true;
                }else{
                    $new_ranges[$i]->selected = 0;
                }
            }
        }
        $range_info['data'] = $new_ranges;
        $result[] = $range_info;


        //获取支付方式
        /*$pay_type = [];
        $result['pay_types'] = $pay_type;*/

        return $this->respDataReturn($result);
    }

    //首页-》积分商品列表
    public function good_list(Request $request)
    {
        $loginBrandId =  session('designer_scope.brand_id');
        if(!isset($loginBrandId) || !$loginBrandId){
            $loginBrandId = 0;
        }

        $brand_ids = $request->input('b',[]);
        $cat1_id = $request->input('c1',0);
        $cat2_ids = $request->input('c2',[]);
        $ranges = $request->input('r',[]);
        $pay_types = $request->input('p',[]);
        $keyword = $request->input('kw','');

        //支持前端以,英文逗号分隔传过来的字符串数据
        if(!is_array($brand_ids)){$brand_ids = array_filter(explode(',',$brand_ids));}
        if(!is_array($cat2_ids)){$cat2_ids = array_filter(explode(',',$cat2_ids));}
        if(!is_array($ranges)){$ranges = array_filter(explode(',',$ranges));}
        if(!is_array($pay_types)){$pay_types = array_filter(explode(',',$pay_types));}

        $good_ids = [];

        if($loginBrandId){
            $good_ids = IntegralGoodService::getOrgBrandGoodIds($loginBrandId);
        }

        if($cat1_id==0)
            $entry = IntegralGood::query()
                ->where('status',IntegralGood::STATUS_ON)
                ->select(['web_id_code','cover','name','short_intro',
                    'market_price','integral','exchange_amount'])
                ->whereIn('id',$good_ids)
                ;
        else
            $entry = IntegralGood::query()
                ->where('status',IntegralGood::STATUS_ON)
                ->where('category_id_1',$cat1_id)
                ->select(['web_id_code','cover','name','short_intro',
                    'market_price','integral','exchange_amount'])
                ->whereIn('id',$good_ids)
            ;

        //按商品名称搜索
        if($keyword){
            $entry->where('name','like','%'.$keyword.'%');
        }

        //筛选品牌
        if($brand_ids && count($brand_ids)>0){
            $entry->whereIn('integral_brand_id',$brand_ids);
        }

        //筛选二级分类
        if($cat2_ids && count($cat2_ids)>0){
            $entry->whereIn('category_id_2',$cat2_ids);
        }

        //筛选积分范围
        if($ranges && count($ranges)>0){
            $entry->where(function($query)use($ranges){
                for($i=0;$i<count($ranges);$i++){
                    $range_array = explode('-',$ranges[$i]);
                    if($i==0){
                        $query->whereBetween('integral',$range_array);
                    }else{
                        $query->orWhereBetween('integral',$range_array);
                    }
                }
            });
        }

        //筛选支付方式
        if($pay_types && count($pay_types)>0){
            //暂无相关逻辑
        }

        //设置排序（exchange/new/cost）
        if($order = $request->input('order','')){
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['exchange','new','cost'])){
                    if($m[1] == 'exchange'){
                        $entry->orderBy('exchange_amount',$m[2]);
                    }else if($m[1] == 'new'){
                        $entry->orderBy('id',$m[2]);
                    }else if($m[1] == 'cost'){
                        $entry->orderBy('integral',$m[2]);
                    }
                }
            }
        }

        //默认按兑换量最多
        $entry->orderBy('exchange_amount','desc');

        $datas =$entry->paginate(1000);

        return $this->respDataReturn($datas);
    }

}
