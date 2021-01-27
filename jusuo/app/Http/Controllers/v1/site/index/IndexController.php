<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/12/11
 * Time: 15:47
 */

namespace App\Http\Controllers\v1\site\index;

use App\Http\Controllers\v1\admin\brand\api\BrandController;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Banner;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\FavDealer;
use App\Models\FavDesigner;
use App\Models\LikeAlbum;
use App\Models\FavAlbum;
use App\Models\LogBrandSiteConfig;
use App\Models\NewsBrand;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Models\SiteConfigPlatform;
use App\Models\Style;
use App\Services\v1\admin\ProductColumnStatisticService;
use App\Services\v1\admin\StatisticAlbumService;
use App\Services\v1\admin\StatisticDesignerService;
use App\Services\v1\site\BsAlbumDataService;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsDealerDataService;
use App\Services\v1\site\BsDesignerDataService;
use App\Services\v1\site\BsProductDataService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class IndexController extends VersionController{

    private $apiSv;
    private $city_scope = 0;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function error($code,Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->goTo404($code,$__BRAND_SCOPE);
    }

    public function index(Request $request){

        $pageBelongBrandId = session()->get('pageBelongBrandId');

        if(!isset($pageBelongBrandId) || !$pageBelongBrandId){

            $loginDesigner = Auth::user();

            //记录目标页面内容所属品牌id
            if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $pageBelongBrandId = $loginDesigner->organization_id;
                session()->put('pageBelongBrandId',$pageBelongBrandId);
            }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
                $pageBelongBrandId = $loginDealer->p_brand_id;
                session()->put('pageBelongBrandId',$pageBelongBrandId);
            }

        }

        $brandDetail = DetailBrand::where('brand_id', $pageBelongBrandId)->first();

        return redirect('/brand/'.$brandDetail->brand_domain);
    }

    public function index_brand($brandDomain){

        $brand = DetailBrand::where('brand_domain',$brandDomain)->first();
        if($brand){
            $brand = OrganizationBrand::find($brand->brand_id);
            if($brand&&$brand->status==OrganizationBrand::STATUS_ON){
                $brandId = $brand->id;

                //非品牌内设计师禁止访问（需要在下方记录目标品牌id之前判断）
                $loginBrandId = session('designer_scope.brand_id');

                if($loginBrandId != $brandId){
                    return $this->goTo404(PageService::ErrorNoAuthority);
                }

                //记录目标页面内容所属品牌id
                session()->put('pageBelongBrandId',$brandId);


                $__BRAND_SCOPE = $this->compressBrandScope($brandId);

                return $this->get_view('v1.site.index.index',compact('__BRAND_SCOPE'));
            }
            else{
                return redirect('/index');
            }
        }
        else{
            return redirect('/index');
        }
    }

    public function get_banner(Request $request){
        /*$banner = [
            ['image'=>"../../v1/images/site/index/cookingroom_3.jpg",'url'=>null]
            ,['image'=>"../../v1/images/site/index/dinningroom.jpg",'url'=>null]
            ,['image'=>"../../v1/images/site/index/dinningroom_4.jpg",'url'=>null]
        ];*/

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
            ->where('position',Banner::POSITION_INDEX_TOP)
            ->orderBy('sort','desc')
            ->orderBy('id','desc');

        if($pageBelongBrandId){
            $builder->where('brand_id',$pageBelongBrandId);
        }else{
            //$builder->where('brand_id',0);
            return $this->apiSv->respFailReturn([]);
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
            return $this->apiSv->respFailReturn('');
        }

        return $this->apiSv->respDataReturn($banner);
    }

    public function get_footer(Request $request){

        $footer = [];

        $pageBelongBrandId = session()->get('pageBelongBrandId');
        $loginDesigner = Auth::user();

        if(!isset($pageBelongBrandId) || !$pageBelongBrandId){
            if($loginDesigner){
                //获取登录设计师所属品牌
                if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                    $pageBelongBrandId = $loginDesigner->organization_id;
                    session()->put('pageBelongBrandId',$pageBelongBrandId);
                }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                    $loginDealer = OrganizationDealer::find($loginDesigner->organization_id);
                    $pageBelongBrandId = $loginDealer->p_brand_id;
                    session()->put('pageBelongBrandId',$pageBelongBrandId);
                }
            }else{
                $pageBelongBrandId = session()->get('preview_brand_id');
                if(isset($preview_brand_id) && $preview_brand_id){
                    session()->put('pageBelongBrandId',$preview_brand_id);

                }
            }
        }

        if(!$pageBelongBrandId){
            return $this->apiSv->respDataReturn(null);
        }

        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$pageBelongBrandId)->first();
        $brand  = OrganizationBrand::find($pageBelongBrandId);
        $brand_name = $brand->brand_name;
        if($brand_site_config){
            $site_config = \Opis\Closure\unserialize($brand_site_config->content);
            $site_config['site_title'] = isset($site_config['front_name'])?$site_config['front_name']:$brand_name;
            //设置网站标题名称
            /*$site_config['site_title'] = $brand_name;
            if($tool_name){
                $site_config['site_title'].=" - ".$tool_name;
            }*/
            $footer = $site_config;
        }else{
            return $this->apiSv->respFailReturn('');
        }


        return $this->apiSv->respDataReturn($footer);
    }

    public function get_style(){
        $style = Style::get(['id','name']);
        $loginBrandId = session('designer_scope.brand_id');

        //由于涉及到从后台预览详情页，所以这里不判断登录用户，取全部数据20200515

        /*$designerDetail = Auth::user()->detail;
        $visibleCityIds = $designerDetail->area_visible_cities;
        $visibleCityIdArray = array_diff(explode('|',$visibleCityIds),['']);*/

        $albumStyle = SearchAlbum::where('brand_id',$loginBrandId);
        /*if($designerDetail->dealer_id<>0) {
            $whereRawString = '';
            foreach ($visibleCityIdArray as $v) {
                if ($whereRawString <> '') {
                    $whereRawString .= ' or ';
                }
                $whereRawString .= 'area_visible_cities like "%' . AlbumService::JOINER . $v . AlbumService::JOINER . '%"';
            }
            $albumStyle = $albumStyle->whereRaw('(' . $whereRawString . ')');
        }*/
        $albumStyle = $albumStyle->pluck('style')->toArray();
        //dd($albumStyle,$whereRawString);
        $albumStyle = array_unique(array_diff(explode('|',implode('',$albumStyle)),['']));
        $styles = [];
        foreach($style as $v){
            if(in_array($v->id,$albumStyle)){
                array_push($styles, $v);
            }
        }

        return $this->apiSv->respDataReturn($styles);
    }

    public function get_brand(Request $request){
        $this->extractBrandScope($request);
        $brandId = $this->brand_scope;
        if($brandId>0){
            $brand = OrganizationBrand::find($brandId);
            if($brand&&$brand->product_category){
                $category = ProductCategory::where('id',$brand->product_category)
                    ->get(['id','name']);
                return $this->apiSv->respBrandReturn($category);
            }
            return $this->apiSv->respFailReturn();
        }
        else {
            $brand = OrganizationBrand::get(['id', 'brand_name']);
            return $this->apiSv->respDataReturn($brand);
        }
    }

    public function get_city(Request $request){
        //获取当前定位城市
        //首页人气设计师，筛选项，依据可见性显示城市（可见性是指当前设计师能看到哪些，就显示哪些）
        $designerDetail = Auth::user()->detail;
        $visibleCityIds = $designerDetail->area_visible_cities;
        $visibleCityIdArray = explode('|',$visibleCityIds);
        $visibleCities = [];
        if(count($visibleCityIdArray)>0){
            $visibleCities = Area::whereIn('id',$visibleCityIdArray)
                ->where('status',Area::STATUS_ON)
                ->get(['id','shortname']);
        }

        return $this->apiSv->respDataReturn($visibleCities);
    }

    //获取主页材料商家的筛选类别
    public function get_category(Request $request)
    {
        $category = [];
        $brandId = session('designer_scope.brand_id');
        if($brandId>0){
            $brand = OrganizationBrand::find($brandId);
            if($brand&&$brand->product_category){
                $category = ProductCategory::where('id',$brand->product_category)
                    ->get(['id','name']);
            }
        }
        else {
            $category = ProductCategory::where('id', '>', 0)
                ->get(['id', 'name']);
        }
        return $this->apiSv->respDataReturn($category);
    }

    //获取主页热门产品的筛选类别
    public function get_technology_category(Request $request){
        //20200422 改为获取工艺类别数据进行筛选
        $this->extractBrandScope($request);
        $data = CeramicTechnologyCategory::get(['id','name']);

        return $this->apiSv->respDataReturn($data);
    }

    //
    public function get_dealer_by_city(Request $request){
        $loginBrandId = session('designer_scope.brand_id');

        LocationService::getClientCity($request);
        $c = $request->session()->get('location_code');
        if($request->has('count')){
            $params = [
                'city'=>$c,
                'take'=>$request->get('count',0),
                'brand_scope'=>$loginBrandId,
            ];
        }
        else{
            $params = [
                'city'=>$c,
                'brand_scope'=>$loginBrandId,
            ];
        }
        $dealer = DealerService::getDealerByCity($params,$request);
        return $this->apiSv->respDataReturn([
            'dealer'=>$dealer,
            'city'=>$c,
        ]);
    }

    //品牌主页-》材料商家
    public function get_dealer_by_category(Request $request){
        LocationService::getClientCity($request);
        $categoryId = $request->input('c',0);
        $cityId = $request->session()->get('location_code');

        $dealer = BsDealerDataService::listBrandIndexDealer([
            'loginDesigner'=>Auth::user(),
            'loginBrandId'=>session('designer_scope.brand_id'),
            'loginDealerId'=>session('designer_scope.dealer_id'),
            'categoryId'=>$categoryId,
            'cityId'=>$cityId,
        ],$request);

        return $this->apiSv->respDataReturn([
            'dealer'=>$dealer['dealer'],
            'brand'=>$dealer['brand'],
            'category'=>$categoryId,
            'city'=>$cityId,
        ]);
    }

    public function get_product_by_brand(Request $request){
        $b = $request->input('b',0);
        $product = ProductService::getProductByBrand($b,true);
        return $this->apiSv->respDataReturn([
            'product'=>$product,
            'brand'=>$b,
        ]);
    }

    public function get_product_by_category(Request $request){
        //20200422改为以工艺类别筛选
        $designer = Auth::user();

        $categoryId = $request->input('c',0);

        $product = BsProductDataService::listBrandIndexProduct([
            'loginDesigner'=>$designer,
            'loginBrandId'=>session('designer_scope.brand_id'),
            'loginDealerId'=>session('designer_scope.dealer_id'),
            'categoryId'=>$categoryId,
        ],$request);
        return $this->apiSv->respDataReturn([
            'product'=>$product['data'],
            'brand'=>$product['brand'],
            'category'=>$categoryId,
        ]);
    }

    public function get_designer_by_city(Request $request){
        $c = $request->input('c',0);

        $designer = BsDesignerDataService::listBrandIndexDesigner([
            'loginDesigner'=>Auth::user(),
            'loginBrandId'=>session('designer_scope.brand_id'),
            'loginDealerId'=>session('designer_scope.dealer_id'),
            'cityId'=>$c,
        ],$request);
        return $this->apiSv->respDataReturn([
            'designer'=>$designer,
            'city'=>$c,
        ]);
    }

    //品牌主页->设计方案
    public function get_album_by_style(Request $request){
        $designer = Auth::user();
        $s = $request->input('s',0);
        //$brandShow = $request->input('brand_show',false);
        $album = BsAlbumDataService::listBrandIndexAlbum([
            'loginDesigner'=>$designer,
            'loginBrandId'=>session('designer_scope.brand_id'),
            'loginDealerId'=>session('designer_scope.dealer_id'),
            'styleId'=>$s,
            'take'=>5
        ],$request);
        return $this->apiSv->respDataReturn([
            'album'=>$album['data'],
            'style'=>$s,
        ]);
    }

    public function post_fav_dealer(Request $request){
        $user = Auth::user();
        if(!$user) {
            return $this->apiSv->respFailReturn();
        }
        $dealerId = $request->input('code','');
        $dealerId = StrService::get_id_by_web_code('organization_dealers', $dealerId);
        if($dealerId>0){
            $dealer = OrganizationDealer::find($dealerId);
            if($dealer
                &&$dealer->status==OrganizationDealer::STATUS_ON){
                $res = OpService::favDealer($dealerId);
                return $this->apiSv->respDataReturn([
                    'faved'=>$res['result']==1?true:false,
                    'count'=>$res['count'],
                ]);
            }
        }
        return $this->apiSv->respFailReturn();
    }

    public function post_fav_designer(Request $request){
        $user = Auth::user();
        if(!$user) {
            return $this->apiSv->respFailReturn();
        }
        $designerId = $request->input('code','');
        $designerId = StrService::get_id_by_web_code('designers', $designerId);
        if($designerId>0){
            $designer = Designer::find($designerId);
            if($designer
                &&$designer->status==Designer::STATUS_ON){
                $res = OpService::favDesigner($designerId);
                return $this->apiSv->respDataReturn([
                    'faved'=>$res['result']==1?true:false,
                    'count'=>$res['count'],
                ]);
            }
        }
        return $this->apiSv->respFailReturn();
    }

    public function post_fav_album(Request $request){
        $user = Auth::user();
        if(!$user) {
            return $this->apiSv->respFailReturn();
        }
        $albumId = $request->input('id',0);
        if($albumId>0){
            $album = Album::find($albumId);
            if($album
                &&$album->period_status==Album::PERIOD_STATUS_FINISH
                &&$album->visible_status==Album::VISIBLE_STATUS_ON){
                $res = OpService::favAlbum($albumId);
                return $this->apiSv->respDataReturn([
                    'liked'=>$res['result']==1?true:false,
                    'count'=>$res['count'],
                ]);
            }
        }
        return $this->apiSv->respFailReturn();
    }

    public function post_like_album(Request $request){
        $user = Auth::user();
        if(!$user) {
            return $this->apiSv->respFailReturn();
        }
        $albumId = $request->input('id',0);
        if($albumId>0){
            $album = Album::find($albumId);
            if($album
                &&$album->period_status==Album::PERIOD_STATUS_FINISH
                &&$album->visible_status==Album::VISIBLE_STATUS_ON){
                $res = OpService::likeAlbum($albumId);
                return $this->apiSv->respDataReturn([
                    'liked'=>$res['result']==1?true:false,
                    'count'=>$res['count'],
                ]);
            }
        }
        return $this->apiSv->respFailReturn();
    }

    // 设计方案
    public function albums_type(Request $request){

        $validator = Validator::make($request->all(),[
            'style_id' => 'required',
        ],[
            'style_id' => '选择设计方案类型'
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            return $msg_result;
        }

        $style_id = $request->style_id;
        $ablum_ids = DB::table('album_styles')->where('style_id',$style_id)->limit(5)->pluck('album_id')->toArray();
        $ablums = Album::where('status',3)->whereIn('id',$ablum_ids)->orderBy('count_visit','desc')->orderBy('count_praise','desc')->orderBy('count_fav','desc')->get();

        return $this->apiSv->respDataReturn($ablums);
    }

    //热门产品品牌
    public function hot_product_band(Request $request){
        $bands = OrganizationBrand::where('status',OrganizationBrand::STATUS_ON)->limit('9')->get();

        return $bands;
    }

    //热门产品 品牌筛选
    public function hot_product_by_band(Request $request){

        $validator = Validator::make($request->all(),[
            'band_id' => 'required',
        ],[
            'band_id' => '选择产品品牌'
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            return $msg_result;
        }

        $band_id = $request->band_id;
        $products = ProductCeramic::where('band_id',$band_id)->where('visible',ProductCeramic::VISIBLE_YES)->orderBy('count_visit','desc')->orderBy('count_fav','desc')->orderBy('point_focus','desc')->orderBy('weight_sort','desc')->limit(6)->get();

        return $this->apiSv->respDataReturn($products);
    }

    //热门产品
    public function hot_product_type(Request $request){

        $validator = Validator::make($request->all(),[
            'type_id' => 'required',
        ],[
            'type_id' => '选择产品系列'
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            return $msg_result;
        }

        $type_id = $request->type_id;
        $hot_product = ProductCeramic::where('series_id',$type_id)->where('status',ProductCeramic::STATUS_PASS)->where('visible',1)->orderBy('count_visit','desc')->orderBy('count_fav','desc')->orderBy('point_focus','desc')->orderBy('weight_sort','desc')->limit(6)->get();

        return $this->apiSv->respDataReturn($hot_product);
    }


    //设计师
    public function designers_type(Request $request){

        $validator = Validator::make($request->all(),[
            'province_id' => 'required',
        ],[
            'province_id' => '选择设计师区域'
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            return $msg_result;
        }

        $province = $request->province_id;
        $designers = Designer::where('status',Designer::STATUS_ON)->whereHas('detail',function($query) use ($province){
            $query->where('area_belong_province',$province)->orderBy('count_visit','desc')->orderBy('count_praise','desc')->orderBy('count_fav','desc');
        })->orderBy('count_visit','desc')->orderBy('count_praise','desc')->orderBy('count_fav','desc')->limit(6)->get();

        return $this->apiSv->respDataReturn($designers);
    }

    //装饰公司
    public function company_type(Request $request){
        $type_id = $request->type_id;


    }

    //获取组织
    public function location_get_organization(Request $request)
    {
        $loginDesigner = Auth::user();
        if(!$loginDesigner){
            return $this->apiSv->respDataReturn('');
        }
        $organization = DesignerService::getDesignerBelongOrganizationName($loginDesigner);
        return $this->apiSv->respDataReturn($organization);
    }


}