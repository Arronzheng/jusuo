<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/3/20
 * Time: 13:30
 */

namespace App\Http\Controllers\v1\mobile\product;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\OrganizationService;
use App\Models\Album;
use App\Models\Area;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\FavAlbum;
use App\Models\FavProduct;
use App\Models\GuestFavProduct;
use App\Models\LikeAlbum;
use App\Models\LogBrandSiteConfig;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductQa;
use App\Models\StatisticProductCeramic;
use App\Services\v1\mobile\BsMobileAlbumDataService;
use App\Services\v1\mobile\BsMobileProductDataService;
use App\Services\v1\mobile\BsMobileProductPageAccessService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\mobile\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function detail($web_id_code,Request $request){
        $product = ProductCeramic::where('web_id_code',$web_id_code)->first();

        if(!$product){
            return redirect('/')->withErrors(['产品不存在']);
        }

        if(
            $product->visible != ProductCeramic::VISIBLE_YES
        ){
            return redirect('/')->withErrors(['产品状态异常']);
        }

        //页面访问可见性
        //页面可见性
        $pageVisible = BsMobileProductPageAccessService::productDetail([
            'targetProductId' => $product->id,
        ],$request);


        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code'],'','mobile');
        }


        OpService::visitProduct($product->id,$request);

        $app = app('wechat.official_account.default');
        $jssdkConfig = $app->jssdk->buildConfig(array('updateAppMessageShareData','updateTimelineShareData'), false);
        //$jssdkConfig= '';
        return $this->get_view('v1.mobile.product.detail',compact('jssdkConfig'));
    }

    public function more_products(){
        return $this->get_view('v1/mobile.product.more_product');
    }

    public function comment($web_id_code,Request $request){
        return $this->get_view('v1.mobile.product.comment');
    }

    /*--------------------api方法---------------------*/

    //方案列表
    public function list_products(Request $request){

        $dealerWebIdCode = $request->input('dlr','');

        if(!$dealerWebIdCode){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $dealer = OrganizationDealer::where('web_id_code',$dealerWebIdCode)->first();
        if(!$dealer){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }


        $collocations = BsMobileProductDataService::listProducts([
            'dealerId' => $dealer->id,
            'take' => 10,
        ],$request);

        return $this->apiSv->respDataReturn($collocations);

    }

    public function list(Request $request){

        $dealerWebIdCode = $request->input('dlr','');

        if(!$dealerWebIdCode){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $dealer = OrganizationDealer::where('web_id_code',$dealerWebIdCode)->first();
        if(!$dealer){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $products = BsMobileProductDataService::listAllProducts($dealer->id);
        $title = '所有产品('.count($products).')';
        return $this->get_view('v1.mobile.center.products',compact('products','title'));

    }

    public function list_product_comments($web_id_code)
    {

        $designer = Auth()->user();

        $data = ProductCeramic::query()
            ->where('web_id_code',$web_id_code)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }


        $qas = ProductQa::query()
            ->where('product_id',$data->id)
            ->orderBy('created_at','desc')
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
            $temp->ask_time = GlobalService::time_ago((string)$v->created_at);
            $temp->answer_time = GlobalService::time_ago((string)$v->answered_at);

            return $temp;
        });

        return $this->apiSv->respDataReturn($qas);

    }

    //提交评论
    public function commit_comment($id,Request $request)
    {
        $designer = Auth::user();

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('参数缺失~');
        }

        $content = $input_data['content'];

        if(!$content){
            return $this->apiSv->respFailReturn('内容不能为空~');
        }

        $product = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$product){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $product->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }

        $product_id = $product->id;



        $qa = ProductQa::where('product_id',$product_id)
            ->where('question_designer_id',$designer->id)
            ->where('question',$content)->first();
        if($qa){
            return $this->apiSv->respFailReturn('请勿重复发相同内容的问题~');
        }

        //增加评论
        $qa = new ProductQa();
        $qa->product_id = $product_id;
        $qa->question_designer_id = $designer->id;
        $qa->question = $content;
        $qa->status = ProductQa::STATUS_ON;
        $qa->save();

        return $this->apiSv->respDataReturn([],'提问成功');
    }


    public function show($id,Request $request){
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();

        $data = ProductCeramic::select([
            'id','type','web_id_code','code','name','guide_price','brand_id','spec_id','series_id',
            'key_technology','physical_chemical_property','function_feature','customer_value',
            'photo_product','photo_practicality','status','visible','count_visit','count_fav','point_focus',
            'photo_video'
        ])
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }


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
        $location_info = LocationService::getWxLocationCity($request);

        if($location_info){

            //获取产品在当前城市的经销信息
            $result = ProductService::getAreaProductSaleInfo($data->id,$location_info['city_id'],$location_info['province_id']);

            if($result['sales_type'] == OrganizationService::ORGANIZATION_TYPE_SELLER){
                $sales_id = $result['sales_id'];
                if($sales_id){
                    $seller = OrganizationDealer::find($sales_id);
                    //销售商名称
                    $dealer_name = DealerService::getDealerNameRule($seller->id,$request,$loginDesigner);
                    $result['sales_name'] = $dealer_name;
                    if($seller){
                        $data->sales_phone = $seller->contact_telephone;
                        $area_text = '';
                        $detailDealer = DetailDealer::where('dealer_id',$seller->id)->first();
                        if($detailDealer){
                            $cityIds = $detailDealer->area_serving_city;
                            $cityIds = explode('|',$cityIds);
                            $cityIds = array_diff($cityIds,['']);
                            if(count($cityIds)>0) {
                                if(in_array($location_info['city_id'],$cityIds)){
                                    $cityId = $location_info['city_id'];
                                }
                                else {
                                    $cityId = $cityIds[0];
                                }
                                $city =  Area::where('id',$cityId)->first();
                                $province = Area::where('id',$city->pid)->first();
                                if($province){$area_text.= $province->name;}
                                $data->sales_name = $city->shortname.$sales_name;
                            }
                        }
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
        if($loginDesigner){
            $collected = FavProduct::where('designer_id',$loginDesigner->id)
                ->where('product_id',$data->id)
                ->first();
            if($collected){ $data->collected = true; }
        }else if($loginGuest){
            $collected = GuestFavProduct::where('guest_id',$loginGuest->id)
                ->where('product_id',$data->id)
                ->first();
            if($collected){ $data->collected = true; }
        }


        unset($data->id);
        unset($data->type);

        $site_title = '';
        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$data->brand_id)->first();
        if($brand_site_config){
            $site_config = \Opis\Closure\unserialize($brand_site_config->content);
            $site_title = isset($site_config['front_name'])?$site_config['front_name']:'';
        }

        $data->site_title=$site_title;

        //20200723显示城市+品牌
        $data->site_title=$data->sales_name;


        return $this->apiSv->respDataReturn($data);
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
            return $this->apiSv->respFailReturn('参数缺失~');
        }

        $product_code = $id;
        $content = $input_data['content'];

        if(!$content){
            return $this->apiSv->respFailReturn('内容不能为空~');
        }

        $product = ProductCeramic::query()
            ->where('web_id_code',$product_code)
            ->first();


        if(!$product){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $product->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }

        $product_id = $product->id;



        $qa = ProductQa::where('product_id',$product_id)
            ->where('question_designer_id',$designer->id)
            ->where('question',$content)->first();
        if($qa){
            return $this->apiSv->respFailReturn('请勿重复发相同内容的问题~');
        }

        //增加评论
        $qa = new ProductQa();
        $qa->product_id = $product_id;
        $qa->question_designer_id = $designer->id;
        $qa->question = $content;
        $qa->status = ProductQa::STATUS_ON;
        $qa->save();

        return $this->apiSv->respDataReturn([],'提问成功');
    }

    //收藏操作
    public function collect(Request $request){


        $product_code = $request->input('aid',0);

        if(!$product_code){
            return $this->apiSv->respFailReturn('参数错误');
        }

        $product = ProductCeramic::where('web_id_code',$product_code)->first();

        if(!$product){
            return $this->apiSv->respFailReturn('信息不存在');
        }

        $product_id = $product->id;

        $result = OpService::favProduct($product_id);

        if($result['result'] <0){
            return $this->apiSv->respFailReturn($result['msg']);
        }else{
            return $this->apiSv->respDataReturn([],$result['msg']);
        }


    }

    //获取产品空间信息
    public function list_product_spaces($id,Request $request)
    {

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }


        $accessories = $data->spaces()->get();

        $accessories->transform(function($v){
            $temp = new \stdClass();

            $temp->photo = $v->photo;
            $temp->title = $v->title;
            $temp->note = $v->note;

            return $temp;
        });

        return $this->apiSv->respDataReturn($accessories);


    }

    //获取产品配件信息
    public function list_product_accessories($id)
    {
        $designer = Auth()->user();
        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }


        $accessories = $data->accessories()->get();

        $accessories->transform(function($v) use ($designer){
            $temp = new \stdClass();

            $temp->photo = \Opis\Closure\unserialize($v->photo);
            $temp->code = $v->code;
            $temp->technology = $v->technology;
            //规格
            $temp->spec_text = $v->spec_width."x".$v->spec_length."mm";



            $temp->collected = false;
            if($designer){
                $collected = FavProduct::where('designer_id',$designer->id)
                    ->where('product_id',$v->id)
                    ->first();
                if($collected){ $temp->collected = true; }
            }

            return $temp;
        });

        return $this->apiSv->respDataReturn($accessories);

    }

    //获取产品搭配信息
    public function list_product_collocations($id,Request $request){

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
                }


        $collocations = BsMobileProductDataService::listProductDetailCollocation([
            'targetProductId' => $data->id,
            'take' => 10,
        ],$request);

        return $this->apiSv->respDataReturn($collocations);

    }

    //获取产品相关方案信息
    public function list_product_albums($id,Request $request)
    {
        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }

        $albums = BsMobileAlbumDataService::listProductDetailAlbum([
            'targetProductId' => $data->id
        ],$request);

        return $this->apiSv->respDataReturn($albums);


    }

    //获取产品问答
    public function list_product_qas($id,Request $request)
    {

        $data = ProductCeramic::query()
            ->where('web_id_code',$id)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('产品不存在');
        }

        if(
            $data->visible != ProductCeramic::VISIBLE_YES
        ){
            return $this->apiSv->respFailReturn('产品状态异常');
        }


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

        return $this->apiSv->respDataReturn($qas);


    }
}