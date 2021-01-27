<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/3/20
 * Time: 13:30
 */

namespace App\Http\Controllers\v1\mobile\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\FavDesigner;

use App\Models\GuestFavDesigner;
use App\Models\LogBrandSiteConfig;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Services\v1\mobile\BsMobileDesignerPageAccessService;
use App\Services\v1\mobile\OpService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(){
        return $this->get_view('v1.mobile.designer.index');
    }

    public function detail($web_id_code,Request $request){

        $designer = Designer::where('web_id_code',$web_id_code)->first();

        if(!$designer){
            return $this->goTo404(PageService::ErrorNoResult,'');
        }

        if(
            $designer->status != Designer::STATUS_ON
        ){
            return $this->goTo404(PageService::ErrorNoResult,'');
        }

        //页面可见性
        $pageVisible = BsMobileDesignerPageAccessService::designerDetail([
            'targetDesignerId' => $designer->id,
        ],$request);


        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code'],'','mobile');
        }

        OpService::visitDesigner($designer->id,$request);

        $app = app('wechat.official_account.default');
        $jssdkConfig = $app->jssdk->buildConfig(array('updateAppMessageShareData','updateTimelineShareData'), false);

        return $this->get_view('v1.mobile.designer.detail',compact('jssdkConfig'));
    }

    /*--------------------api方法---------------------*/

    public function get_designer_info($web_id_code, Request $request)
    {
        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();

        $data = Designer::query()
            ->has('detail')
            ->select(['id','web_id_code','status','organization_type','organization_id'])
            ->with(['detail'=>function($query){
                $query->select([
                    'designer_id','url_avatar','nickname','approve_realname','area_serving_province',
                    'area_serving_city', 'self_working_year','self_organization','brand_id','dealer_id',
                    'self_introduction','count_album','count_praise','count_visit','count_fan','self_designer_level',
                ]);
            }])
            ->where('web_id_code',$web_id_code)
            ->first();


        if(!$data){
            return $this->apiSv->respFailReturn('设计师不存在');
        }

        if(
            $data->status != Designer::STATUS_ON
        ){
            return $this->apiSv->respFailReturn('设计师状态异常');
        }

        $data->focused = false;
        if($loginDesigner){
            $focused = FavDesigner::where('target_designer_id',$data->id)->where('designer_id',$loginDesigner->id)->first();
            if($focused){ $data->focused = true; }
        }else if($loginGuest){
            $focused = GuestFavDesigner::where('target_designer_id',$data->id)->where('guest_id',$loginGuest->id)->first();
            if($focused){ $data->focused = true; }
        }

        //形象照显示
        $index_bg = '';
        if($data->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //显示销售商形象照
            $dealerDetail = DetailDealer::where('dealer_id',$data->organization_id)->first();
            if(!$dealerDetail){
                return $this->apiSv->respFailReturn('信息错误');
            }
            $index_bg = $dealerDetail->index_photo?:'/v1/images/mobile/designer-bg-0.jpg';

        }else if($data->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //显示品牌形象照
            $brandDetail = DetailBrand::where('brand_id',$data->organization_id)->first();
            if(!$brandDetail){
                return $this->apiSv->respFailReturn('信息错误');
            }
            $index_bg = $brandDetail->brand_image?:'/v1/images/mobile/designer-bg-0.jpg';
        }
        $data->bg = $index_bg;
        //$data->bg = '/v1/images/mobile/designer-bg-0.jpg';

        $organization = DesignerService::getDesignerBelongOrganizationNameCode($data);
        $data->company = $organization['name'];
        $data->company_link = $organization['code'];

        //擅长风格
        $data->styles = [];
        $styles = $data->styles()->get()->pluck('name')->toArray();
        if(is_array($styles) && count($styles)>0){
            $data->styles = $styles;
        }
        $data->space = DesignerService::getDesignerSpaceString($data->id);

        $data->level_title = Designer::designerTitle($data->detail->self_designer_level);
        $data->level_name = Designer::designerTitleCn($data->detail->self_designer_level);

        $site_title = '';
        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$data->detail->brand_id)->first();
        if($brand_site_config){
            $site_config = \Opis\Closure\unserialize($brand_site_config->content);
            $site_title = isset($site_config['front_name'])?$site_config['front_name']:'';
        }

        $data->site_title=$site_title;

        //20200723
        $brand = OrganizationBrand::find($data->detail->brand_id);
        $brand_name = $brand->short_name;
        $data->site_title = $brand_name;
        if($data->detail->dealer_id>0) {
            $dealer = OrganizationDealer::find($data->detail->dealer_id);
            $location_info = LocationService::getWxLocationCity($request);
            if ($location_info) {
                $detailDealer = DetailDealer::where('dealer_id', $dealer->id)->first();
                if ($detailDealer) {
                    $cityIds = $detailDealer->area_serving_city;
                    $cityIds = explode('|', $cityIds);
                    $cityIds = array_diff($cityIds, ['']);
                    if (count($cityIds) > 0) {
                        if (in_array($location_info['city_id'], $cityIds)) {
                            $cityId = $location_info['city_id'];
                        } else {
                            $cityId = $cityIds[0];
                        }
                        $city = Area::where('id', $cityId)->first();
                        $data->site_title = $city->shortname . $brand_name;
                    }
                }
            }
        }

        unset($data->id);
        unset($data->status);
        unset($data->detail->id);

        return $this->apiSv->respDataReturn($data);


    }

    //关注操作
    public function focus(Request $request){


        $designer_code = $request->input('aid',0);
        $operation = $request->input('op',1);  //1关注2取消关注

        $operation = intval($operation);
        if(!in_array($operation,[1,2])){
            return $this->apiSv->respFailReturn('操作错误');
        }

        if(!$designer_code){
            return $this->apiSv->respFailReturn('参数错误');
        }

        $target_designer = Designer::where('web_id_code',$designer_code)->first();

        if(!$target_designer){
            return $this->apiSv->respFailReturn('信息不存在');
        }

        $result = OpService::favDesigner($target_designer->id);

        if($result['result'] <0){
            return $this->apiSv->respFailReturn($result['msg']);
        }else{
            return $this->apiSv->respDataReturn([
                'faved'=> $result['result']
            ],$result['msg']);
        }


    }


    //获取优秀方案数据
    public function list_nice_album($web_id_code,Request $request)
    {

        $designer = Designer::where('web_id_code',$web_id_code)->first();

        //显示最新上传的3个方案
        $builder = Album::query()
            ->join('search_albums as sa','sa.album_id','=','albums.id')
            ->select(['sa.web_id_code',
                'albums.id','albums.web_id_code','albums.designer_id','albums.photo_cover',
                'albums.count_area','albums.title','albums.count_visit','albums.count_praise',
                'albums.count_fav','albums.created_at','albums.verify_time'
            ])
            ->has('designer.detail')
            ->with(['designer'=>function($designer){
                $designer->select(['id']);
                $designer->with(['detail'=>function($designer_detail){
                    $designer_detail->select(['id','designer_id','nickname','url_avatar']);
                }]);
            }])
            ->where('designer_id',$designer->id)
            ->where('period_status',Album::PERIOD_STATUS_FINISH)
            ->where('visible_status',Album::VISIBLE_STATUS_ON);

        //排序
        $builder->orderBy('albums.verify_time','desc');

        //$builder->limit(3);

        $albums = $builder->get();

        $albums->transform(function($v) use($designer){

            $styles = [];
            $styles_data = $v->style;
            if($styles_data){
                $styles = $styles_data->pluck('name')->toArray();
            }
            $v->styles = $styles;
            $v->count_comment = $v->comments()->count();
            $v->created_time = GlobalService::time_ago_string((string)$v->verify_time);

            unset($v->id);
            unset($v->style);

            return $v;
        });


        return $this->apiSv->respDataReturn($albums);


    }

}