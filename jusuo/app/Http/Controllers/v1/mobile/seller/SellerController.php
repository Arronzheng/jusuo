<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/3/20
 * Time: 13:30
 */

namespace App\Http\Controllers\v1\mobile\seller;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\FavDealer;
use App\Models\FavDesigner;

use App\Models\GuestFavDealer;
use App\Models\LogBrandSiteConfig;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Services\v1\mobile\BsMobileDealerPageAccessService;
use App\Services\v1\mobile\OpService;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(){
        return $this->get_view('v1.mobile.seller.index');
    }

    public function detail($web_id_code,Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$web_id_code);
        if($dealerId==-1){
            return $this->goTo404(PageService::ErrorNoResult,'','mobile');
        }

        //页面可见性
        $pageVisible = BsMobileDealerPageAccessService::dealerDetail([
            'targetDealerId' => $dealerId,
        ],$request);


        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code'],'','mobile');
        }

        OpService::visitDealer($dealerId,$request);
        $dealerId = $web_id_code;

        $app = app('wechat.official_account.default');
        $jssdkConfig = $app->jssdk->buildConfig(array('openLocation','updateAppMessageShareData','updateTimelineShareData'), false);

        return $this->get_view('v1.mobile.seller.detail',compact('__BRAND_SCOPE','dealerId','jssdkConfig'));
    }

    /*--------------------api方法---------------------*/

    public function get_seller_info($web_id_code,Request $request)
    {
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();

        $this->extractBrandScope($request);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$web_id_code);
        $dealer = OrganizationDealer::find($dealerId);
        //销售商名称
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
        $brand = OrganizationBrand::find($dealer->p_brand_id);

        $faved = false;
        if($loginDesigner){
            $fav = FavDealer::where(['target_dealer_id'=>$dealerId, 'designer_id'=>$loginDesigner->id])->count();
            if($fav){
                $faved = true;
            }
        }else if($loginGuest){
            $fav = GuestFavDealer::where(['target_dealer_id'=>$dealerId, 'guest_id'=>$loginGuest->id])->count();
            if($fav){
                $faved = true;
            }
        }
        $brandDetail = $brand->detail;

        $site_title = '';
        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$dealer->p_brand_id)->first();
        if($brand_site_config){
            $site_config = \Opis\Closure\unserialize($brand_site_config->content);
            $site_title = isset($site_config['front_name'])?$site_config['front_name']:'';
        }

        //获取销售商方案的总赞数
        $album_praise_count = \DB::table('albums as b')
            ->leftJoin('designers as d', 'd.id', '=', 'b.designer_id')
            ->leftJoin('albums as a', 'a.designer_id','=','d.id')
            ->where([
                'd.organization_type'=>Designer::ORGANIZATION_TYPE_SELLER,
                'd.organization_id'=>$dealer->id,
                'a.visible_status'=>Album::VISIBLE_STATUS_ON,
                'a.period_status'=>Album::PERIOD_STATUS_FINISH,
            ])
            ->whereNotNull('a.id')
            ->sum('a.count_praise');

        $params = [
            'organizationId'=>$dealerId,
            'organizationType'=>Designer::ORGANIZATION_TYPE_SELLER,
        ];
        $designer = DesignerService::getDesignerByOrganization($params);

        $data = [
            'album_praise_count'=>$album_praise_count,
            'brand_image'=>$dealerDetail->index_photo?url($dealerDetail->index_photo):'http://pf.fsqmh.com/v1/images/mobile/seller-bg-1.jpg',
            'brand_logo'=>url($brandDetail->url_avatar),
            'site_title'=>$site_title,
            'avatar'=>url($dealerDetail->url_avatar),
            'web_id_code'=>$dealer->web_id_code,
            'short_name'=>$dealer->short_name,
            'name'=>$dealer->name,
            'contact_telephone'=>$dealer->contact_telephone,
            'level'=>StrService::str_num_to_char($dealerDetail->star_level),
            'designer'=>count($designer),
            'fav'=>$dealerDetail->count_fav,
            'album'=>$dealerDetail->count_album,
            'product'=>$dealerDetail->count_product,
            'city'=>$location['string'],
            'phone'=>$dealer->contact_telephone,
            'introduction'=>$dealerDetail->self_introduction,
            'product_category'=>ProductService::getCategoryName($brand->product_category),
            'promise'=>$dealerDetail->self_promise,
            'faved'=>$faved,
            'self_photo'=>unserialize($dealerDetail->self_photo),
            'self_address'=>$location['string'].' '.$dealerDetail->self_address,
            'self_promotion'=>$dealerDetail->self_promotion,
            //数据未可上传，暂时以服务县区的坐标代替，修正后需替换成销售商设置的店铺坐标
            //'lng'=>$dealerDetail->self_longitude,
            //'lat'=>$dealerDetail->self_latitude,
            'lng'=>$location['lng'],
            'lat'=>$location['lat'],
            'self_lat'=>$dealerDetail->self_latitude,
            'self_lng'=>$dealerDetail->self_longitude,
        ];

        return $this->apiSv->respDataReturn($data);

    }

    public function list_designer($web_id_code,Request $request){
        $this->extractBrandScope($request);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$web_id_code);
        $dealer = OrganizationDealer::find($dealerId);
        if(!$dealer||$dealer->status<>OrganizationDealer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        //如果已有品牌传参限制，但却不在此brand范围，不显示
        if(isset($this->brand_scope)&&$this->brand_scope<>0&&$dealer->p_brand_id<>$this->brand_scope){
            return $this->apiSv->respFailReturn('暂无权限');
        }
        $params = [
            'organizationId'=>$dealerId,
            'organizationType'=>Designer::ORGANIZATION_TYPE_SELLER,
            //'isTop'=>true,
        ];
        $designer = DesignerService::getDesignerByOrganization($params);
        return $this->apiSv->respDataReturn($designer);
    }

    //设计方案
    public function list_album($web_id_code,Request $request){
        $this->extractBrandScope($request);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$web_id_code);
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
            'take'=>3,
            'needTypeStyle'=>true,
            'needCommentCount'=>true,
        ];
        if($request->has('style')){
            $params['styleId'] = $request->input('style',0);
        }
        $album = AlbumService::getAlbum($params);
        return $this->apiSv->respDataReturn([
            'data' =>$album['data'],
            'total'=>$album['total']
        ]);
    }

    //热门产品
    public function list_product($web_id_code,Request $request){
        $this->extractBrandScope($request);
        LocationService::getClientCity($request);
        $dealerId = StrService::get_id_by_web_code('organization_dealers',$web_id_code);
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
            'cityId'=>$request->session()->get('location_code'),
            'take'=>4,
        ];
        if($request->has('category')){
            $params['categoryId'] = $request->input('category',0);
        }
        $product = ProductService::getProductByDealer($params);
        return $this->apiSv->respDataReturn([
            'data' =>$product['data'],
            'total'=>$product['total']
        ]);
    }

    //关注操作
    public function focus($web_id_code,Request $request){

        if(!$web_id_code){
            return $this->apiSv->respFailReturn('参数错误');
        }

        $target_seller = OrganizationDealer::where('web_id_code',$web_id_code)->first();

        if(!$target_seller){
            return $this->apiSv->respFailReturn('信息不存在');
        }

        $result = OpService::favDealer($target_seller->id);


        if($result['result'] <0){
            return $this->apiSv->respFailReturn($result['msg']);
        }else{
            return $this->apiSv->respDataReturn([
                //0是取消关注成功，1是关注成功
                'faved' => $result['result']
            ],$result['msg']);
        }


    }

    //获取优秀方案数据
    public function list_nice_album($web_id_code,Request $request)
    {

        $seller = Designer::where('web_id_code',$web_id_code)->first();

        //显示最新上传的3个方案
        $builder = Album::query()
            ->join('search_albums as sa','sa.album_id','=','albums.id')
            ->select(['sa.web_id_code',
                'albums.id','albums.web_id_code','albums.seller_id','albums.photo_cover',
                'albums.count_area','albums.title','albums.count_visit','albums.count_praise',
                'albums.count_fav','albums.created_at'
            ])
            ->has('seller.detail')
            ->with(['seller'=>function($seller){
                $seller->select(['id']);
                $seller->with(['detail'=>function($seller_detail){
                    $seller_detail->select(['id','seller_id','nickname','url_avatar']);
                }]);
            }])
            ->where('period_status',Album::PERIOD_STATUS_FINISH)
            ->where('visible_status',Album::VISIBLE_STATUS_ON);

        //排序
        $builder->orderBy('albums.verify_time','desc');

        $builder->limit(3);

        $albums = $builder->get();

        $albums->transform(function($v) use($seller){

            $styles = [];
            $styles_data = $v->style;
            if($styles_data){
                $styles = $styles_data->pluck('name')->toArray();
            }
            $v->styles = $styles;
            $v->count_comment = $v->comments()->count();
            $v->created_time = GlobalService::time_ago((string)$v->verify_time);

            unset($v->id);
            unset($v->style);

            return $v;
        });


        return $this->apiSv->respDataReturn($albums);


    }

}