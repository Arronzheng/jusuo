<?php

namespace App\Http\Controllers\v1\site\product\api;

use App\Http\Services\common\GlobalService;
use App\Http\Services\common\OrganizationService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Banner;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavProduct;
use App\Models\HouseType;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\ProductQa;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\StatisticDesigner;
use App\Models\StatisticProductCeramic;
use App\Models\Style;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\site\BsAlbumDataService;
use App\Services\v1\site\BsProductDataService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ProductCeramicsController extends ApiController
{

    public function __construct(

    ){

    }

    /*----------产品首页相关------------*/

    //获取筛选类型数据
    public function list_filter_types(Request $request)
    {
        $query = $request->input('query','');
        $query_string = urldecode($query);
        parse_str($query_string,$query_array);

        $result = array();

        /*$product_categorie_entry = ProductCategory::select(['id','name']);

        if(isset($query_array['bd']) && $query_array['bd']){
            $brand_id = Crypt::decrypt($query_array['bd']);
            $brand = OrganizationBrand::find($brand_id);
            $product_categorie_entry->where('id',$brand->product_category);
        }
        $product_categories = $product_categorie_entry->get();


        $product_category_info = array();
        $product_category_info['title'] = '经营类别';
        $product_category_info['value'] = 'pc';
        $product_category_info['has_selected'] = false;
        if(isset($query_array[$product_category_info['value']])){
            $type_query = $query_array[$product_category_info['value']];
            for($i=0;$i<count($product_categories);$i++){
                if($product_categories[$i]->id == $type_query){
                    $product_categories[$i]->selected = 1;
                    $product_category_info['has_selected'] = true;
                }else{
                    $product_categories[$i]->selected = 0;
                }
            }
        }
        $product_category_info['data'] = $product_categories;
        */


        $styles = Style::select(['id','name'])->get();
        $style_info = array();
        $style_info['title'] = '风格';
        $style_info['value'] = 'stl';
        $style_info['has_selected'] = false;
        if(isset($query_array[$style_info['value']])){
            $type_query = $query_array[$style_info['value']];
            for($i=0;$i<count($styles);$i++){
                if($styles[$i]->id == $type_query){
                    $styles[$i]->selected = 1;
                    $style_info['has_selected'] = true;
                }else{
                    $styles[$i]->selected = 0;
                }
            }
        }
        $style_info['data'] = $styles;


        $colors = CeramicColor::select(['id','name'])->get();
        $color_info = array();
        $color_info['title'] = '色系';
        $color_info['value'] = 'clr';
        $color_info['has_selected'] = false;
        if(isset($query_array[$color_info['value']])){
            $type_query = $query_array[$color_info['value']];
            for($i=0;$i<count($colors);$i++){
                if($colors[$i]->id == $type_query){
                    $colors[$i]->selected = 1;
                    $color_info['has_selected'] = true;
                }else{
                    $colors[$i]->selected = 0;
                }
            }
        }
        $color_info['data'] = $colors;

        //工艺类别
        $tech_cats = CeramicTechnologyCategory::select(['id','name'])->get();
        $tech_cat_info = array();
        $tech_cat_info['title'] = '工艺类别';
        $tech_cat_info['value'] = 'tc';
        $tech_cat_info['has_selected'] = false;
        if(isset($query_array[$tech_cat_info['value']])){
            $type_query = $query_array[$tech_cat_info['value']];
            for($i=0;$i<count($tech_cats);$i++){
                if($tech_cats[$i]->id == $type_query){
                    $tech_cats[$i]->selected = 1;
                    $tech_cat_info['has_selected'] = true;
                }else{
                    $tech_cats[$i]->selected = 0;
                }
            }
        }
        $tech_cat_info['data'] = $tech_cats;


        //规格
        $specs = CeramicSpec::select(['id','name'])->get();
        $spec_info = array();
        $spec_info['title'] = '规格';
        $spec_info['value'] = 'sc';
        $spec_info['has_selected'] = false;
        if(isset($query_array[$spec_info['value']])){
            $type_query = $query_array[$spec_info['value']];
            for($i=0;$i<count($specs);$i++){
                if($specs[$i]->id == $type_query){
                    $specs[$i]->selected = 1;
                    $spec_info['has_selected'] = true;
                }else{
                    $specs[$i]->selected = 0;
                }
            }
        }
        $spec_info['data'] = $specs;

        $pageBelongBrandId = session('pageBelongBrandId');

        //获取品牌内系列信息
        //获取系列ids
        $series_ids = ProductCeramic::where('brand_id',$pageBelongBrandId)
            ->where('visible',ProductCeramic::VISIBLE_YES)
            ->get()->pluck('series_id')->toArray();
        $series = CeramicSeries::select(['id','name'])->whereIn('id',$series_ids)->get();
        $series_info = array();
        $series_info['title'] = '系列';
        $series_info['value'] = 'series';
        $series_info['has_selected'] = false;
        if(isset($query_array[$series_info['value']])){
            $type_query = $query_array[$series_info['value']];
            for($i=0;$i<count($series);$i++){
                if($series[$i]->id == $type_query){
                    $series[$i]->selected = 1;
                    $series_info['has_selected'] = true;
                }else{
                    $series[$i]->selected = 0;
                }
            }
        }

        $series_info['data'] = $series;

        $result[] = $series_info;

        /*if(isset($query_array['__bs']) && $query_array['__bs'] ){
            //若品牌主页进，则按当前品牌的系列

        }else{
            $brand_info = array();
            $brand_info['title'] = '品牌';
            $brand_info['value'] = 'bd';
            $brand_info['has_selected'] = false;
            if(isset($query_array[$brand_info['value']]) && $query_array[$brand_info['value']]){
                $type_query = $query_array[$brand_info['value']];
                for($i=0;$i<count($brands);$i++){
                    if($brands[$i]['web_id_code'] == $type_query){
                        $brands[$i]['selected'] = 1;
                        $brand_info['has_selected'] = true;
                    }else{
                        $brands[$i]['selected'] = 0;
                    }
                    $brands[$i]['id'] = $brands[$i]['web_id_code'];
                }
            }
            for($i=0;$i<count($brands);$i++){
                $brands[$i]['id'] = $brands[$i]['web_id_code'];
                $brands[$i]['name'] = $brands[$i]['brand_name'];
                unset($brands[$i]['web_id_code']);
                unset($brands[$i]['brand_name']);
            }

            $brand_info['data']= $brands;

            $result[] = $brand_info;

        }*/


        $result[] = $tech_cat_info;
        $result[] = $style_info;
        $result[] = $color_info;
        $result[] = $spec_info;

        return $this->respDataReturn($result);

    }

    //获取产品列表数据
    public function list_products(Request $request)
    {

        $designer = Auth()->user();

        $datas = BsProductDataService::listProductIndexData([
            'loginDesigner'=> $designer,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        return $this->respDataReturn($datas);


    }

    //收藏操作
    public function collect(Request $request){

        $designer = Auth()->user();

        $product_code = $request->input('aid',0);
        $operation = $request->input('op',1);  //1点赞2取消点赞

        $operation = intval($operation);
        if(!in_array($operation,[1,2])){
            return $this->respFailReturn('操作错误');
        }

        if(!$product_code){
            return $this->respFailReturn('参数错误');
        }

        $product = ProductCeramic::where('web_id_code',$product_code)->first();

        if(!$product){
            return $this->respFailReturn('信息不存在');
        }

        $product_id = $product->id;

        $result = OpService::favProduct($product_id);

        if($result['result'] <0){
            return $this->respFailReturn($result['msg']);
        }else{
            return $this->respDataReturn([],$result['msg']);
        }


    }

    
    /*----------产品详情页相关------------*/

    //获取产品基本信息
    public function get_product_info($id,Request $request)
    {
        $designer = Auth::user();

        $data = ProductCeramic::select([
            'id','type','web_id_code','code','name','guide_price','brand_id','spec_id','series_id',
            'key_technology','physical_chemical_property','function_feature','customer_value',
            'photo_product','photo_practicality','status','visible','count_visit','count_fav','point_focus',
            'photo_video'
        ])
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        //产品图、实物图
        $data->photo_product = @unserialize($data->photo_product);
        $data->photo_practicality = @unserialize($data->photo_practicality);

        //产品视频
        $data->photo_video = @unserialize($data->photo_video);

        //理化性能
        $data->physical_chemical_property = @unserialize($data->physical_chemical_property);

        //功能特征
        $data->function_feature = @unserialize($data->function_feature);

        //品牌信息
        $brand = OrganizationBrand::find($data->brand_id);
        $data->brand_name = '';
        $data->product_category = '';
        if($brand){
            $data->brand_name = $brand->brand_name;
            $product_category = ProductCategory::find($brand->product_category);
            if($product_category){
                $data->product_category = $product_category->name;
            }
        }

        //系列
        $data->series_text = '';
        $series = CeramicSeries::find($data->series_id);
        if($series){$data->series_text = $series->name;}
        //规格
        $data->spec_text = '';
        $spec = CeramicSpec::find($data->spec_id);
        if($spec){$data->spec_text = $spec->name;}

        //应用类别
        $data->apply_categories_text = '';
        $apply_categories = $data->apply_categories()->get()->pluck('name')->toArray();
        if(is_array($apply_categories) && count($apply_categories)>0){
            $data->apply_categories_text = implode('/',$apply_categories);
        }

        //工艺类别
        $data->technology_categories_text = '';
        $technology_categories = $data->technology_categories()->get()->pluck('name')->toArray();
        if(is_array($technology_categories) && count($technology_categories)>0){
            $data->technology_categories_text = implode('/',$technology_categories);
        }

        //表面特征
        $data->surface_features_text = '';
        $surface_features = $data->surface_features()->get()->pluck('name')->toArray();
        if(is_array($surface_features) && count($surface_features)>0){
            $data->surface_features_text = implode('/',$surface_features);
        }

        //色系
        $data->colors_text = '';
        $colors = $data->colors()->get()->pluck('name')->toArray();
        if(is_array($colors) && count($colors)>0){
            $data->colors_text = implode('/',$colors);
        }

        //可应用空间风格
        $data->styles_text = '';
        $styles = $data->styles()->get()->pluck('name')->toArray();
        if(is_array($styles) && count($styles)>0){
            $data->styles_text = implode('/',$styles);
        }

        //本城市销售信息（默认是品牌的信息）
        $sales_price = $data->guide_price==0?'':'￥'.$data->guide_price;
        $sales_name = $brand->short_name;
        $sales_phone = $brand->contact_telephone;
        $sales_address = $brand->contact_address;
        $sales_avatar = $brand->detail->url_avatar;
        $sales_url = url('/brand/'.$brand->detail->brand_domain);;



        $data->sales_price = $sales_price;
        $data->sales_name = $sales_name;
        $data->sales_phone = $sales_phone;
        $data->sales_address = $sales_address;
        $data->sales_avatar = $sales_avatar;
        $data->sales_url = $sales_url;

        //当前定位城市
        $location_info = LocationService::getClientCity($request);

        if($location_info){

            //获取产品在当前城市的经销信息
            $result = ProductService::getAreaProductSaleInfo($data->id,$location_info['city_id'],$location_info['province_id']);

            if($result['sales_type'] == OrganizationService::ORGANIZATION_TYPE_SELLER){
                $sales_id = $result['sales_id'];
                if($sales_id){
                    $seller = OrganizationDealer::find($sales_id);
                    //销售商名称
                    $dealer_name = DealerService::getDealerNameRule($seller->id,$request,$designer);
                    $result['sales_name'] = $dealer_name;
                    if($seller){
                        $data->sales_phone = $seller->contact_telephone;
                        $area_text = '';
                        $province =  Area::where('id',$seller->detail->self_province_id)->first();
                        $city =  Area::where('id',$seller->detail->self_city_id)->first();
                        $district =  Area::where('id',$seller->detail->self_district_id)->first();
                        if($province){$area_text.= $province->name;}
                        if($city){$area_text.= $city->name;}
                        if($district){$area_text.= $district->name;}
                        $data->sales_address = $area_text.$seller->detail->self_address;
                        $data->sales_avatar = $seller->detail->url_avatar1;
                        $data->sales_url = url('/dealer/s/'.$seller->web_id_code);
                    }
                }
                $data->sales_price = $result['price'];
                $data->sales_name = $result['sales_name'];
            }

        }



        //关联方案数量
        $stat = StatisticProductCeramic::where('product_id',$data->id)
            ->orderBy('id','desc')
            ->first();
        if($stat){
            $data->count_album = $stat->count_album;

        }

        //是否产品
        $data->is_product = 1;
        if($data->type == ProductCeramic::TYPE_ACCESSORY){
            $data->is_product = 0;
        }

        //同类产品查看更多
        $brand = OrganizationBrand::find($data->brand_id);
        $styles = $data->styles()->get()->pluck('id')->toArray();
        //是否从品牌主页进
        if($brand_scope = $request->input('__bs',null)){
            $data->more_kind_url = url('/product?__bs='.$brand->web_id_code);
            $more_similiar_url = '/product?__bs='.$brand->web_id_code;
        }else{
            $data->more_kind_url = url('/product?bd='.$brand->web_id_code);
            $more_similiar_url = '/product?bd='.$brand->web_id_code;
        }

        if($styles && isset($styles[0]) && $styles[0]){
            $more_similiar_url .= "&stl=".$styles[0];
        }
        $data->more_similiar_url = $more_similiar_url;

        $data->collected = false;
        if($designer){
            $collected = FavProduct::where('designer_id',$designer->id)
                ->where('product_id',$data->id)
                ->first();
            if($collected){ $data->collected = true; }
        }


        unset($data->id);
        unset($data->type);


        return $this->respDataReturn($data);

    }

    //获取产品配件信息
    public function list_product_accessories($id)
    {
        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $accessories = $data->accessories()->get();

        $accessories->transform(function($v){
            $temp = new \stdClass();

            $temp->photo = \Opis\Closure\unserialize($v->photo);
            $temp->code = $v->code;
            $temp->technology = $v->technology;
            //规格
            $temp->spec_text = $v->spec_width."x".$v->spec_length."mm";

            return $temp;
        });

        return $this->respDataReturn($accessories);

    }

    //获取产品搭配信息
    public function list_product_collocations($id,Request $request){

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $accessories = $data->collocations()->get();

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
            }
            $temp->photo = \Opis\Closure\unserialize($v->photo);
            $temp->note = $v->note;




            return $temp;
        });

        return $this->respDataReturn($accessories);

    }

    //获取产品空间信息
    public function list_product_spaces($id,Request $request)
    {

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }



        $accessories = $data->spaces()->get();

        $accessories->transform(function($v){
            $temp = new \stdClass();

            $temp->photo = $v->photo;
            $temp->title = $v->title;
            $temp->note = $v->note;

            return $temp;
        });

        return $this->respDataReturn($accessories);


    }

    //获取产品相似产品信息
    public function list_product_similiars($id,Request $request)
    {
        $designer = Auth::user();

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        //随机显示同一品牌同一风格的4个产品
        $products = BsProductDataService::listProductDetailSimiliarData([
            'loginDesigner' => $designer,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
            'targetProductId' => $data->id
        ],$request);

        return $this->respDataReturn($products);


    }

    //获取同类产品信息
    public function list_product_kinds($id,Request $request)
    {
        $designer = Auth::user();

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();

        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        //随机显示同一品牌同一系列的5个产品
        $products = BsProductDataService::listProductDetailKindData([
            'loginDesigner' => $designer,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
            'targetProductId' => $data->id
        ],$request);

        $seriesId = $data->series_id;
        $series = CeramicSeries::find($seriesId);

        return $this->respDataReturn([
            'series'=>$series,
            'res'=>$products
        ]);


    }

    //获取产品相关方案的风格信息
    public function list_styles($product_id,Request $request){



        $data = ProductCeramic::query()
            ->where('web_id_code',$product_id)
            ->first();

        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $entry = DB::table('styles as s')
            ->select(['s.id','s.name'])
            ->join('album_styles as as','s.id','=','as.style_id')
            ->join('album_product_ceramics as apc','apc.album_id','=','as.album_id')
            ->join('product_ceramics as p','p.id','=','apc.product_ceramic_id')
            ->join('albums as a','a.id','=','apc.album_id')
            ->where('a.period_status',Album::PERIOD_STATUS_FINISH)
            ->where('a.visible_status',Album::VISIBLE_STATUS_ON)
            ->groupBy('s.id');

        if($product_id){
            $entry->where('p.web_id_code',$product_id);
        }

        $styles = $entry->get();

        return $this->respDataReturn($styles);

    }

    //获取产品相关方案信息
    public function list_product_albums($id,Request $request)
    {
        $style_id = $request->input('stl',0);
        $designer = Auth::user();

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $albums = BsAlbumDataService::listProductDetailAlbum([
            'loginDesigner' => Auth::user(),
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
            'targetProductId' => $data->id
        ],$request);

        return $this->respDataReturn($albums);


    }

    //获取产品问答
    public function list_product_qas($id,Request $request)
    {

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$data->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }



        $qas = $data->qas()->orderBy('created_at','desc')
            ->paginate(10);

        $qas->transform(function($v){
            $temp = new \stdClass();

            $designer = Designer::find($v->question_designer_id);

            $temp->ask_name = '';
            $temp->ask_avatar = '';
            if($designer){
                $temp->ask_name = $designer->detail->nickname;
                $temp->ask_avatar = $designer->detail->url_avatar;
            }
            $temp->question = $v->question;
            $temp->answer_name = '客服';
            $temp->answer = $v->answer;
            $temp->ask_time = date('Y-m-d H:i',strtotime($v->created_at));
            $temp->answer_time = date('Y-m-d H:i',strtotime($v->answered_at));

            return $temp;
        });

        return $this->respDataReturn($qas);


    }

    //提交问答
    public function commit_qa($id,Request $request)
    {
        $designer = Auth::user();

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->respFailReturn('参数缺失~');
        }

        $product_code = $id;
        $content = $input_data['content'];

        if(!$content){
            return $this->respFailReturn('内容不能为空~');
        }

        $product = ProductCeramic::query()
            ->where('web_id_code',$product_code)
            ->first();


        if(!$product){
            return $this->respFailReturn('产品不存在');
        }

        if(
            $product->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->respFailReturn('产品状态异常');
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$product->id);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $product_id = $product->id;



        $qa = ProductQa::where('product_id',$product_id)
            ->where('question_designer_id',$designer->id)
            ->where('question',$content)->first();
        if($qa){
            return $this->respFailReturn('请勿重复发相同内容的问题~');
        }

        //增加评论
        $qa = new ProductQa();
        $qa->product_id = $product_id;
        $qa->question_designer_id = $designer->id;
        $qa->question = $content;
        $qa->status = ProductQa::STATUS_ON;
        $qa->save();

        return $this->respDataReturn([],'提问成功');
    }


    //判断产品详情api可访问性
    private function check_detail_api_access($loginDesigner,$product_id){
        $result = [
            'status'=>1,
            'msg'=>''
        ];

        $targetProduct = ProductCeramic::find($product_id);
        if(!$targetProduct){
            $result['status'] = 0;
            $result['msg'] = '找不到信息';
            return $result;
        }
        $targetBrandId = $targetProduct->brand_id;

        if($loginDesigner){
            //设计师已登录
            $loginBrandId = DesignerService::getDesignerBrandScope($loginDesigner->id);
            if($loginBrandId!=$targetBrandId){
                $result['status'] = 0;
                $result['msg'] = '权限不足';
                return $result;
            }
        }else{
            //设计师未登录，判断是否预览
            $preview_brand_id = session('preview_brand_id');
            if(!isset($preview_brand_id) || !$preview_brand_id){
                $result['status'] = 0;
                $result['msg'] = '权限不足';
                return $result;
            }
            if($preview_brand_id != $targetBrandId){
                $result['status'] = 0;
                $result['msg'] = '权限不足';
                return $result;
            }
        }

        return $result;
    }

    //获取产品列表轮播图
    public function get_banner(Request $request){

        $banner = [];

        $pageBelongBrandId = session()->get('pageBelongBrandId');
        $loginDesigner = Auth::user();

        if(!isset($pageBelongBrandId) || !$pageBelongBrandId){
            //获取登录设计师所属品牌
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $pageBelongBrandId = $loginDesigner->organization_id;
            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
                $pageBelongBrandId = $loginDealer->p_brand_id;
            }
        }

        $builder = Banner::query()
            ->where('status',Banner::STATUS_ON)
            ->where('position',Banner::POSITION_PRODUCT_INDEX_TOP)
            ->orderBy('sort','desc')
            ->orderBy('id','desc');

        if($pageBelongBrandId){
            $builder->where('brand_id',$pageBelongBrandId);
        }else{
            //$builder->where('brand_id',0);
            return $this->respFailReturn([]);
        }

        $banners = $builder->get();

        if($banners){
            foreach($banners as $item){
                $temp = [];
                $temp['image'] = $item->photo;
                $temp['url'] = $item->url;
                array_push($banner,$temp);
            }
        }else{
            return $this->respFailReturn('');
        }

        return $this->respDataReturn($banner);
    }



    /*----------------废弃方法留存-------------------*/
    //获取产品列表数据
    public function list_products_old(Request $request)
    {

        $designer = Auth()->user();


        $builder = ProductCeramic::query()
            ->with(['brand'=>function($query){
                $query->select(['id','short_name']);
            }])
            ->select(['id','brand_id','web_id_code','code','photo_product','guide_price','name','count_fav'])
            ->where('visible',ProductCeramic::VISIBLE_YES);


        //搜索名称/型号
        if($search = $request->input('search','')){
            $like = '%'.$search.'%';

            $builder->where(function($query) use ($like){
                $query->where('name','like',$like);
                $query->orWhere('code','like',$like);
            });
        }

        //销售商设计师约束在本销售商可见域，品牌设计师约束在本品牌。
        $brand_scope = $request->input('__bs',null);
        if($brand_scope){
            $web_id_code = $brand_scope;
            if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                //品牌设计师可见性
                //品牌的所有产品
                $builder->whereHas('brand',function($query) use ($web_id_code){
                    $query->where('organization_brands.web_id_code',$web_id_code);
                });
            }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //属于销售商
                $seller_id = $designer->organization_id;
                $seller = OrganizationDealer::where('id',$seller_id)->first();
                if($seller->p_dealer_id == 0){
                    //是否一级
                    $product_ids =DB::table('product_ceramic_authorizations')
                        ->where('dealer_id',$seller->id)
                        ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
                        ->pluck('product_id')->toArray();
                }else{
                    //二级 找到上一级
                    $p_seller = OrganizationDealer::where('id',$seller->p_dealer_id)->first();
                    if($p_seller){
                        $product_ids =DB::table('product_ceramic_authorizations')
                            ->where('dealer_id',$p_seller->id)
                            ->where('status','!=',ProductCeramicAuthorization::statusGroup(ProductCeramicAuthorization::STATUS_OFF))
                            ->pluck('product_id')->toArray();
                    }
                }
                $builder->whereIn('product_ceramics.id',$product_ids);
            }else{
                return $this->goTo404(PageService::ErrorNoAuthority,$web_id_code);

            }

        }else{
            return $this->goTo404(PageService::ErrorNoAuthority,$web_id_code);
        }

        //是否筛选品牌或者品牌主页过来
        /*$brand_id_code = $request->input('bd',null);
        $brand_scope = $request->input('__bs',null);
        if($brand_id_code || $brand_scope){
            $web_id_code = $brand_id_code?$brand_id_code:$brand_scope;
            $builder->whereHas('brand',function($query) use ($web_id_code){

                $query->where('organization_brands.web_id_code',$web_id_code);

            });
        }*/

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

        //是否提交风格
        if($style = $request->input('stl',null)){
            $builder->whereHas('styles',function($query) use ($style){
                $query->where('styles.id',$style);
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
                return $this->respFailReturn('价格区间错误');
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
        }

        $datas = $builder->paginate(40);

        $datas->transform(function($v) use($designer){

            $v->collected = false;
            if($designer){
                $collected = FavProduct::where('designer_id',$designer->id)->where('product_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            $v->price = $v->guide_price==0?"":'￥'.$v->guide_price;

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

            return $v;
        });


        return $this->respDataReturn($datas);


    }



}
