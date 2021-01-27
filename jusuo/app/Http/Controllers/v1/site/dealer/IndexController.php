<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/12/11
 * Time: 15:47
 */

namespace App\Http\Controllers\v1\site\dealer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\StrService;
use App\Models\Banner;
use App\Models\Designer;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\FavDealer;
use App\Models\OrganizationDealer;
use App\Services\v1\admin\OrganizationDealerColumnStatisticService;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsAlbumDataService;
use App\Services\v1\site\BsDealerDataService;
use App\Services\v1\site\BsDealerPageAccessService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\OrganizationBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndexController extends VersionController{

    private $apiSv;
    private $city_scope = 0;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(Request $request){

        //页面可见性
        $pageVisible = BsDealerPageAccessService::dealerIndex([
            'loginDesigner' => Auth::user(),
            'loginDealerId' => session('designer_scope.dealer_id'),
            'loginBrandId' => session('designer_scope.brand_id'),
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        return $this->get_view('v1.site.dealer.index');
    }

    //获取列表轮播图
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
            ->where('position',Banner::POSITION_DEALER_INDEX_TOP)
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

    public function get_footer(){
        $footer = [
            'phone'=>'123-456-7890',
            'contact'=>[
                '邮箱：qianminghui@163.com',
                '微信：qianminghui',
                'QQ：368202567'
            ],
            'link'=>[
                [
                    ['text'=>'商务合作','link'=>''],
                    ['text'=>'知名品牌','link'=>'https://www.baidu.com/s?wd=知名品牌'],
                    ['text'=>'最新动态','link'=>'https://www.baidu.com/s?wd=最新动态'],
                    ['text'=>'联系合作','link'=>'https://www.baidu.com/s?wd=联系合作']
                ],
                [
                    ['text'=>'关于我们','link'=>''],
                    ['text'=>'公司简介','link'=>'https://www.baidu.com/s?wd=公司简介'],
                    ['text'=>'企业文化','link'=>'https://www.baidu.com/s?wd=企业文化'],
                    ['text'=>'发展历程','link'=>'https://www.baidu.com/s?wd=发展历程'],
                    ['text'=>'团队风采','link'=>'https://www.baidu.com/s?wd=团队风采'],
                    ['text'=>'联系我们','link'=>'https://www.baidu.com/s?wd=联系我们']
                ],
            ],
            'qrcode'=>[
                ['text'=>'官方微信','image'=>'/v1/images/site/index/erweima.png'],
                ['text'=>'官方客服','image'=>'/v1/images/site/index/erweima.png'],
            ],
            'relate'=>[
                ['text'=>'蒙娜丽莎','link'=>'http://www.monalisa.com.cn'],
                ['text'=>'金牌天纬','link'=>'http://www.tw100.cn'],
                ['text'=>'兴辉瓷砖','link'=>'http://www.sanfi.cc']
            ]
        ];
        return $this->apiSv->respDataReturn($footer);
    }

    public function get_filter(Request $request){
        $this->extractBrandScope($request);
        $category = [];
        $brand = [];
        $brandId = $this->brand_scope;
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
            $brand = $brand = OrganizationBrand::get(['id', 'brand_name as name']);
        }
        return $this->apiSv->respDataReturn([
            [
                'parent_id'=>1,
                'parent_name'=>'经营品类',
                'parent_key'=>'c',
                'content'=>$category,
            ]/*,[
                'parent_id'=>2,
                'parent_name'=>'品牌',
                'parent_key'=>'b',
                'content'=>$brand,
            ]*/
        ]);
    }

    public function get_sorter(){
        return $this->apiSv->respDataReturn([
            [
                'text'=>'综合',
                'attr'=>DealerService::SORT_BY_DEFAULT,
            ],[
                'text'=>'人气',
                'attr'=>DealerService::SORT_BY_FOCUS,
            ],[
                'text'=>'浏览量',
                'attr'=>DealerService::SORT_BY_VIEW,
            ],[
                'text'=>'最新',
                'attr'=>DealerService::SORT_BY_ID,
            ],
        ]);
    }


    //销售商列表-》热门推荐
    public function get_hot_dealer(Request $request){
        $designer = Auth::user();
        $c = $request->session()->get('location_code');

        $dealer = BsDealerDataService::listDealerIndexHotData([
            'loginDesigner' => $designer,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        return $this->apiSv->respDataReturn([
            'dealer'=>$dealer,
            'city'=>$c,
        ]);
    }

    //销售商列表-》列表数据
    public function get_dealer_by_filter(Request $request){
        $designer = Auth::user();
        $this->extractBrandScope($request);
        LocationService::getClientCity($request);
        $params = [];
        $city = $request->session()->get('location_code', 0);

        //此函数内，默认地，如果不传brand和city，会按当前访问者的brand和city范围查询
        //如要取消此限制，必须在此函数内传0值
        //相对地，服务内函数条件更加宽松
        //后面到服务内函数时会接受0值查询
        $defaultMap = [
            ['input'=>'c','default'=>0,'param'=>'categoryId'],
            ['input'=>'skip','default'=>0,'param'=>'skip'],
            ['input'=>'take','default'=>10,'param'=>'take'],
            ['input'=>'sort','default'=>DealerService::SORT_BY_DEFAULT,'param'=>'sort'],
            ['input'=>'direction','default'=>0,'param'=>'direction'],
            ['input'=>'city','default'=>$city,'param'=>'cityId']
        ];

        foreach($defaultMap as $v){
            if($request->has($v['input'])){
                $params[$v['param']] = $request->input($v['input'],$v['default']);
            }
        }

        $params['loginDesigner'] = $designer;
        $params['loginBrandId'] = session('designer_scope.brand_id');
        $params['loginDealerId'] = session('designer_scope.dealer_id');

        $dealer = BsDealerDataService::listDealerIndexData($params,$request);

        return $this->apiSv->respDataReturn([
            'dealer'=>$dealer['dealer'],
            'param'=>$dealer['params'],
            'total'=>$dealer['total'],
        ]);
    }

    //预览详情页
    public function detail_preview($id,Request $request)
    {

        $data = OrganizationDealer::where('web_id_code',$id)->first();

        if(!$data){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        if(
            $data->status != OrganizationDealer::STATUS_ON
        ){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        $preview_brand_id = session()->get('preview_brand_id');

        $targetBrandId = $data->brand->id;
        if($preview_brand_id != $targetBrandId){
            return $this->goTo404(PageService::ErrorNoAuthority);
        }

        $is_preview = true;

        $dealerId = $data->web_id_code;

        return $this->get_view('v1.site.dealer.detail',compact('is_preview','dealerId'));


    }

    //销售商详情
    public function detail($web_id_code,Request $request){
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$web_id_code);
        if($dealerId==-1){
            return $this->goTo404();
        }

        //页面可见性
        $pageVisible = BsDealerPageAccessService::dealerDetail([
            'loginDesigner' => Auth::user(),
            'targetDealerId' => $dealerId,
            'loginDealerId' => session('designer_scope.dealer_id'),
            'loginBrandId' => session('designer_scope.brand_id'),
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        OpService::visitDealer($dealerId,$request);
        $dealerId = $web_id_code;
        return $this->get_view('v1.site.dealer.detail',compact('dealerId'));
    }

    //销售商详情-》获取销售商信息（登录状态）
    public function get_info_login(Request $request){
        $this->extractBrandScope($request);
        $dealerId = $request->input('dealer_id',0);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$dealerId);
        $dealer = OrganizationDealer::find($dealerId);
        //销售商名称
        $loginDesigner = Auth::user();
        $dealer_name = DealerService::getDealerNameRule($dealer->id,$request,$loginDesigner);
        $dealer->name = $dealer_name;
        if(!$dealer||$dealer->status<>OrganizationDealer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        //如果已有品牌传参限制，但却不在此brand范围，不显示
        if(isset($this->brand_scope)&&$this->brand_scope<>0&&$dealer->p_brand_id<>$this->brand_scope){
            return $this->apiSv->respFailReturn();
        }

        $dealerDetail = DetailDealer::where('dealer_id',$dealerId)->first();
        if(!$dealerDetail){
            return $this->apiSv->respFailReturn();
        }
        $location = DealerService::getDealerBelongArea($dealerId);
        $locationAddress = DealerService::getDealerLocationArea($dealerId);
        $brand = OrganizationBrand::find($dealer->p_brand_id);
        $user = Auth::user();
        $faved = false;
        if($user){
            $fav = FavDealer::where(['target_dealer_id'=>$dealerId, 'designer_id'=>$user->id])->count();
            if($fav){
                $faved = true;
            }
        }
        $params = [
            'organizationId'=>$dealerId,
            'organizationType'=>Designer::ORGANIZATION_TYPE_SELLER,
        ];
        $brandDetail = DetailBrand::where('brand_id',$dealer->p_brand_id)->first();
        $designer = DesignerService::getDesignerByOrganization($params);
        $data = [
            'bg'=>$dealerDetail->index_photo?:'/v1/images/site/dealer/bg.png',
            'avatar'=>url($dealerDetail->url_avatar),
            'web_id_code'=>$dealer->web_id_code,
            'short_name'=>$dealer->short_name,
            'name'=>$dealer->name,
            'level'=>StrService::str_num_to_char($dealerDetail->star_level),
            'designer'=>count($designer),
            'fav'=>$dealerDetail->count_fav,
            'album'=>$dealerDetail->count_album,
            'city'=>isset($location['string'])?$location['string']:'',
            //'locationAddress'=>isset($locationAddress['string'])?$locationAddress['string']:'',
            'phone'=>$dealer->contact_telephone,
            'introduction'=>$dealerDetail->self_introduction,
            'brand_introduction'=>$brandDetail->self_introduction,
            'product_category'=>ProductService::getCategoryName($brand->product_category),
            'promise'=>$dealerDetail->self_promise,
            'faved'=>$faved,
            'self_photo'=>unserialize($dealerDetail->self_photo),
            'self_address'=>isset($locationAddress['string'])?$locationAddress['string'].$dealerDetail->self_address:'',
            'self_promotion'=>$dealerDetail->self_promotion,
            //数据未可上传，暂时以服务县区的坐标代替，修正后需替换成销售商设置的店铺坐标
            'lng'=>$dealerDetail->self_longitude,
            'lat'=>$dealerDetail->self_latitude,
        ];
        return $this->apiSv->respDataReturn([
            'data'=>$data,
            'dealerId'=>$dealerId,
        ]);
    }

    //销售商详情-》获取销售商信息
    public function get_info(Request $request){
        $this->extractBrandScope($request);
        $dealerId = $request->input('dealer_id',0);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$dealerId);
        $dealer = OrganizationDealer::find($dealerId);
        //销售商名称
        $loginDesigner = Auth::user();
        $dealer_name = DealerService::getDealerNameRule($dealer->id,$request,$loginDesigner);
        $dealer->name = $dealer_name;
        if(!$dealer||$dealer->status<>OrganizationDealer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        //如果已有品牌传参限制，但却不在此brand范围，不显示
        if(isset($this->brand_scope)&&$this->brand_scope<>0&&$dealer->p_brand_id<>$this->brand_scope){
            return $this->apiSv->respFailReturn();
        }

        $dealerDetail = DetailDealer::where('dealer_id',$dealerId)->first();
        if(!$dealerDetail){
            return $this->apiSv->respFailReturn();
        }
        $location = DealerService::getDealerBelongArea($dealerId);
        $locationAddress = DealerService::getDealerLocationArea($dealerId);
        $brand = OrganizationBrand::find($dealer->p_brand_id);
        $user = Auth::user();
        $faved = false;
        if($user){
            $fav = FavDealer::where(['target_dealer_id'=>$dealerId, 'designer_id'=>$user->id])->count();
            if($fav){
                $faved = true;
            }
        }
        $params = [
            'organizationId'=>$dealerId,
            'organizationType'=>Designer::ORGANIZATION_TYPE_SELLER,
        ];
        $brandDetail = DetailBrand::where('brand_id',$dealer->p_brand_id)->first();
        $designer = DesignerService::getDesignerByOrganization($params);
        $data = [
            'bg'=>$dealerDetail->index_photo?:'/v1/images/site/dealer/bg.png',
            'avatar'=>url($dealerDetail->url_avatar),
            'web_id_code'=>$dealer->web_id_code,
            'short_name'=>$dealer->short_name,
            'name'=>$dealer->name,
            'level'=>StrService::str_num_to_char($dealerDetail->star_level),
            'designer'=>count($designer),
            'fav'=>$dealerDetail->count_fav,
            'album'=>$dealerDetail->count_album,
            'city'=>isset($location['string'])?$location['string']:'',
            //'locationAddress'=>isset($locationAddress['string'])?$locationAddress['string']:'',
            'phone'=>$dealer->contact_telephone,
            'introduction'=>$dealerDetail->self_introduction,
            'brand_introduction'=>$brandDetail->self_introduction,
            'product_category'=>ProductService::getCategoryName($brand->product_category),
            'promise'=>$dealerDetail->self_promise,
            'faved'=>$faved,
            'self_photo'=>unserialize($dealerDetail->self_photo),
            'self_address'=>isset($locationAddress['string'])?$locationAddress['string'].$dealerDetail->self_address:'',
            'self_promotion'=>$dealerDetail->self_promotion,
            //数据未可上传，暂时以服务县区的坐标代替，修正后需替换成销售商设置的店铺坐标
            'lng'=>$dealerDetail->self_longitude,
            'lat'=>$dealerDetail->self_latitude,
        ];
        return $this->apiSv->respDataReturn([
            'data'=>$data,
            'dealerId'=>$dealerId,
        ]);
    }

    //销售商详情-》设计团队
    public function get_designer(Request $request){
        $this->extractBrandScope($request);
        $dealerId = $request->input('dealer_id',0);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$dealerId);
        $dealer = OrganizationDealer::find($dealerId);
        if(!$dealer||$dealer->status<>OrganizationDealer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        //如果已有品牌传参限制，但却不在此brand范围，不显示
        if(isset($this->brand_scope)&&$this->brand_scope<>0&&$dealer->p_brand_id<>$this->brand_scope){
            return $this->apiSv->respFailReturn();
        }
        $params = [
            'organizationId'=>$dealerId,
            'organizationType'=>Designer::ORGANIZATION_TYPE_SELLER,
            'isOrderByTopDealer'=>true,
        ];
        $designer = DesignerService::getDesignerByOrganization($params);
        return $this->apiSv->respDataReturn([
            'data'=>$designer,
            'dealerId'=>$dealerId,
        ]);
    }

    //销售商详情-》设计案例
    public function get_album(Request $request){
        $this->extractBrandScope($request);
        $dealerId = $request->input('dealer_id',0);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$dealerId);
        $dealer = OrganizationDealer::find($dealerId);
        if(!$dealer||$dealer->status<>OrganizationDealer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        //如果已有品牌传参限制，但却不在此brand范围，不显示
        if(isset($this->brand_scope)&&$this->brand_scope<>0&&$dealer->p_brand_id<>$this->brand_scope){
            return $this->apiSv->respFailReturn();
        }
        $params = [
            'targetDealerId'=>$dealer->id,
        ];
        if($request->has('style')){
            $params['styleId'] = $request->input('style',0);
        }
        $albums = BsAlbumDataService::listDealerDetailAlbum($params,$request);

        return $this->apiSv->respDataReturn($albums);
    }

    public function get_category(Request $request){
        $dealerId = $request->input('dealer_id',0);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$dealerId);
        $category = ProductService::getCategory($dealerId,false);
        return $this->apiSv->respDataReturn($category);
    }

    //热门产品
    public function get_product(Request $request){
        $this->extractBrandScope($request);
        LocationService::getClientCity($request);
        $dealerId = $request->input('dealer_id',0);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$dealerId);
        $dealer = OrganizationDealer::find($dealerId);
        if(!$dealer||$dealer->status<>OrganizationDealer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        //如果已有品牌传参限制，但却不在此brand范围，不显示
        if(isset($this->brand_scope)&&$this->brand_scope<>0&&$dealer->p_brand_id<>$this->brand_scope){
            return $this->apiSv->respFailReturn();
        }
        $params = [
            'dealerId'=>$dealerId,
            //'cityId'=>$request->session()->get('location_code'),
        ];
        if($request->has('category')){
            $params['categoryId'] = $request->input('category',0);
        }
        $product = ProductService::getProductByDealer($params);
        return $this->apiSv->respDataReturn($product);
    }


    /*-------------------废弃方法留存----------------------*/
    //销售商列表-》热门推荐
    public function get_hot_dealer_old(Request $request){
        $designer = Auth::user();
        $this->extractBrandScope($request);
        $c = $request->session()->get('location_code');
        $skip = 0;
        $take = $request->get('count',7);

        $isOrderByFocus = true;

        $builder = \DB::table('organization_dealers as od')
            ->leftJoin('detail_dealers as d', 'd.dealer_id', '=', 'od.id')
            ->where('od.status',OrganizationDealer::STATUS_ON);

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //品牌设计师可见性
            //旗下所有销售商（且产品数>0）
            $builder->where('od.p_brand_id',$this->brand_scope);
            $builder->where('d.count_product','>',0);

        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性

        }else{
            $builder->where('od.id',0);
        }

        $dealer = $builder;

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
            ->get(['d.dealer_id','d.url_avatar','od.web_id_code']);
        $dealer->transform(function($v){
            $v->url_avatar = url($v->url_avatar);
            return $v;
        });

        return $this->apiSv->respDataReturn([
            'dealer'=>$dealer,
            'city'=>$c,
        ]);
    }

    //销售商列表-》列表数据
    public function get_dealer_by_filter_old(Request $request){
        $designer = Auth::user();
        $this->extractBrandScope($request);
        LocationService::getClientCity($request);
        $params = [];
        $city = 0;

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //品牌设计师可见性
            //旗下所有销售商（且产品数>0）
            //下面默认条件已符合

        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //销售商直属设计师可见性
            $city = $request->session()->get('location_code', 0);

        }

        //此函数内，默认地，如果不传brand和city，会按当前访问者的brand和city范围查询
        //如要取消此限制，必须在此函数内传0值
        //相对地，服务内函数条件更加宽松
        //后面到服务内函数时会接受0值查询
        $defaultMap = [
            ['input'=>'b','default'=>$this->brand_scope,'param'=>'brand'],
            ['input'=>'c','default'=>0,'param'=>'category'],
            ['input'=>'skip','default'=>0,'param'=>'skip'],
            ['input'=>'take','default'=>10,'param'=>'take'],
            ['input'=>'sort','default'=>DealerService::SORT_BY_DEFAULT,'param'=>'sort'],
            ['input'=>'direction','default'=>0,'param'=>'direction'],
            ['input'=>'city','default'=>$city,'param'=>'city']

        ];

        foreach($defaultMap as $v){
            if($request->has($v['input'])){
                $params[$v['param']] = $request->input($v['input'],$v['default']);
            }
        }
        if(!$request->has('city')){
            $params['city'] = $request->session()->get('location_code', 0);
        }
        //如果已有品牌传参限制，但却给b传了0值，改过来只能看当前品牌（不允许再跳出品牌以外）
        if(isset($this->brand_scope)&&$params['brand']==0){
            $params['brand'] = $this->brand_scope;
        }
        $dealer = DealerService::getDealerByFilter($params,$request);
        return $this->apiSv->respDataReturn([
            'dealer'=>$dealer['dealer'],
            'param'=>$dealer['params'],
            'total'=>$dealer['total'],
        ]);
    }
}