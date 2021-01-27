<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/3/20
 * Time: 13:30
 */

namespace App\Http\Controllers\v1\mobile\album;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;

use App\Models\GuestFavAlbum;
use App\Models\GuestFavDesigner;
use App\Models\GuestLikeAlbum;
use App\Models\LikeAlbum;
use App\Models\LogBrandSiteConfig;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\SearchAlbum;
use App\Models\SpaceType;
use App\Models\Style;
use App\Services\v1\admin\StatisticDesignerService;
use App\Services\v1\mobile\BsMobileAlbumDataService;
use App\Services\v1\mobile\BsMobileAlbumPageAccessService;
use App\Services\v1\mobile\BsMobileProductDataService;
use App\Services\v1\mobile\OpService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlbumController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(){
        return $this->get_view('v1.mobile.album.index');
    }

    public function detail($web_id_code,Request $request){
        $album = Album::where('web_id_code',$web_id_code)->first();

        if(!$album){
            return redirect('/')->withErrors(['方案不存在']);
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return redirect('/')->withErrors(['方案状态异常']);
        }

        //页面可见性
        $pageVisible = BsMobileAlbumPageAccessService::albumDetail([
            'targetAlbumId' => $album->id,
        ],$request);


        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code'],'','mobile');
        }


        OpService::visitAlbum($album->id,$request);

        $app = app('wechat.official_account.default');
        $jssdkConfig = $app->jssdk->buildConfig(array('updateAppMessageShareData','updateTimelineShareData'), false);

        return $this->get_view('v1.mobile.album.detail',compact('jssdkConfig'));
    }

    public function comment($web_id_code,Request $request){
        return $this->get_view('v1.mobile.album.comment');
    }

    /*--------------------api方法---------------------*/

    //获取方案列表
    public function list_albums(Request $request)
    {
        $dealerWebIdCode = $request->input('dlr','');

        if(!$dealerWebIdCode){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $dealer = OrganizationDealer::where('web_id_code',$dealerWebIdCode)->first();
        if(!$dealer){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $albums = BsMobileAlbumDataService::listAlbums([
            'dealerId' => $dealer->id,
            'take' => 10
        ],$request);

        return $this->apiSv->respDataReturn($albums);
    }

    //获取方案列表
    public function list(Request $request)
    {
        $dealerWebIdCode = $request->input('dlr','');

        if(!$dealerWebIdCode){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $dealer = OrganizationDealer::where('web_id_code',$dealerWebIdCode)->first();
        if(!$dealer){
            return $this->apiSv->respFailReturn('暂无相关信息');
        }

        $albums = BsMobileAlbumDataService::listAllAlbums($dealer->id);

        foreach($albums as $v){
            $designer = DesignerDetail::where('designer_id',$v->designer_id)->first();
            $v->designer = $designer->nickname;
            $v->avatar = $designer->url_avatar;
        }

        $title = '所有方案('.count($albums).')';
        return $this->get_view('v1.mobile.center.albums',compact('albums','title'));
    }
    
    //获取方案基本信息
    public function get_album_info($web_id_code,Request $request)
    {

        $loginDesigner = Auth::user();
        $loginGuest = Auth::guard('m_guest')->user();

        $search_album = SearchAlbum::where('web_id_code',$web_id_code)->first();

        if(!$search_album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        $album = Album::select([
            'id','code','designer_id','verify_time','title','address_city_id','address_area_id','address_street',
            'address_residential_quarter','address_building','address_layout_number','count_area',
            'description_design','count_visit','count_praise','count_fav','count_use',
            'description_layout','photo_layout','photo_cover',
            'period_status','visible_status'
        ])
            ->where('id',$search_album->album_id)
            ->first();


        if(!$album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return $this->apiSv->respFailReturn('方案状态异常');
        }

        //web随机码
        $album->web_id_code = $web_id_code;

        //户型图
        $album->photo_layout_data = \Opis\Closure\unserialize($album->photo_layout);

        ///所在城市
        $city = Area::find($album->address_city_id);
        $area = Area::find($album->address_area_id);
        $city_text = $city?$city->short_name:''.$area?$area->short_name:'';
        $album->city_text = $city_text;

        //户型
        $house_types = $album->house_types()->get()->pluck('name')->toArray();
        $album->house_type_text = implode('/',$house_types);

        //风格
        $styles = $album->style()->get();
        $style_text = $styles->pluck('name')->toArray();
        $style_ids = $styles->pluck('id')->toArray();
        $album->style_text = implode('/',$style_text);
        $style_id = $style_ids[0];
        $album->more_similiar_url = url('/album?stl='.$style_id);

        //设计师信息
        $designer_info = [];
        $designer_info['organization'] = '';
        $designer_info['fans'] = 0;
        $designer_info['count_upload_album'] = 0;
        $designer = Designer::select(['id','organization_type','organization_id','web_id_code'])->find($album->designer_id);
        if(!$designer){
            return $this->apiSv->respFailReturn('设计师状态异常');
        }
        if($designer->organization){
            $designer_info['organization'] = $designer->organization->name;
        }
        $designer_detail = DesignerDetail::select(['nickname','url_avatar','count_fav','brand_id','area_serving_cities','dealer_id'])->where('designer_id',$album->designer_id)->first();
        $designer_info['nickname'] = $designer_detail->nickname;
        $designer_info['url_avatar'] = $designer_detail->url_avatar;
        $designer_info['fans'] = $designer_detail->count_fav;
        $stat_service = new StatisticDesignerService();
        $designer_info['count_upload_album'] = $stat_service->count_upload_album($album->designer_id);
        $designer_info['web_id_code'] = $designer->web_id_code;
        $designer_info['focused'] = false;
        if($loginDesigner){
            $focused = FavDesigner::where('target_designer_id',$designer->id)
                ->where('designer_id',$loginDesigner->id)->first();
            if($focused){  $designer_info['focused'] = true;  }
        }else if($loginGuest){
            $focused = GuestFavDesigner::where('target_designer_id',$designer->id)
                ->where('guest_id',$loginGuest->id)->first();
            if($focused){  $designer_info['focused'] = true;  }
        }

        $album->designer_info = $designer_info;

        $album->liked = false;
        if($loginDesigner){
            $liked = LikeAlbum::where([
                'designer_id'=>$loginDesigner->id,
                'album_id'=>$album->id,
            ])->count();
            if($liked){ $album->liked = true; }
        }else if($loginGuest){
            $liked = GuestLikeAlbum::where([
                'guest_id'=>$loginGuest->id,
                'album_id'=>$album->id,
            ])->count();
            if($liked){ $album->liked = true; }
        }

        $album->collected = false;
        if($loginDesigner){
            $collected = FavAlbum::where('designer_id',$loginDesigner->id)->where('album_id',$album->id)->first();
            if($collected){ $album->collected = true; }
        }else if($loginGuest){
            $collected = GuestFavAlbum::where('guest_id',$loginGuest->id)->where('album_id',$album->id)->first();
            if($collected){ $album->collected = true; }
        }

        $site_title = '';
        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$designer_detail->brand_id)->first();
        if($brand_site_config){
            $site_config = \Opis\Closure\unserialize($brand_site_config->content);
            $site_title = isset($site_config['front_name'])?$site_config['front_name']:'';
        }

        $album->site_title=$site_title;
        
        //20200723
        $brand = OrganizationBrand::find($designer_detail->brand_id);
        $brand_name = $brand->short_name;
        $album->site_title = $brand_name;
        //dd($designer_detail);
        if($designer_detail->dealer_id>0) {
            $dealer = OrganizationDealer::find($designer_detail->dealer_id);
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
                        $album->site_title = $city->shortname . $brand_name;
                    }
                }
            }
        }

        unset($album->designer_id);

        return $this->apiSv->respDataReturn($album);

    }

    //获取方案评论列表
    public function list_album_comments($web_id_code)
    {

        $designer = Auth::user();

        $album = Album::query()
            ->where('web_id_code',$web_id_code)
            ->first();

        if(!$album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return $this->apiSv->respFailReturn('方案状态异常');
        }

        $album_comments = $album->comments()
            ->orderBy('created_at','desc')
            ->where('status',AlbumComments::STATUS_ON)
            ->select(['id','designer_id','content','created_at'])
            ->paginate(10);

        $album_comments->transform(function($v)use($designer){

            $v->publish_time = GlobalService::time_ago($v->created_at);

            $author = DesignerDetail::select(['designer_id','nickname','url_avatar'])->where('designer_id',$v->designer_id)->first();

            $v->author = '';
            $v->author_avatar = '';

            if($author){
                $v->author = $author->nickname;
                $v->author_avatar = $author->url_avatar;
            }

            unset($v->created_at);
            unset($v->designer_id);
            return $v;
        });

        return $this->apiSv->respDataReturn($album_comments);


    }

    //提交评论
    public function commit_comment($web_id_code,Request $request)
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

        $album = Album::query()
            ->where('web_id_code',$web_id_code)
            ->first();

        $album_id = $album->id;

        if(!$album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return $this->apiSv->respFailReturn('方案状态异常');
        }

        $comment = AlbumComments::where('album_id',$album_id)
            ->where('designer_id',$designer->id)
            ->where('content',$content)->first();
        if($comment){
            return $this->apiSv->respFailReturn('请勿重复发相同内容的评论~');
        }

        //增加评论
        $comment = new AlbumComments();
        $comment->album_id = $album_id;
        $comment->designer_id = $designer->id;
        $comment->content = $content;
        $comment->status = AlbumComments::STATUS_ON;
        $comment->save();

        return $this->apiSv->respDataReturn([],'评论成功');
    }

    //获取方案空间信息
    public function list_album_sections($id)
    {
        $search_album = SearchAlbum::where('web_id_code',$id)->first();

        if(!$search_album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        $album = Album::query()
            ->where('id',$search_album->album_id)
            ->first();

        if(!$album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return $this->apiSv->respFailReturn('方案状态异常');
        }

        $album_sections = $album->album_sections()->select(['id','title','count_area','space_type_id','content'])->get();

        $album_sections->transform(function($v){
            //空间类型
            $space_type_text = '';
            $space_type = SpaceType::find($v->space_type_id);
            if($space_type){
                $space_type_text = $space_type->name;
            }
            $v->space_type_text = $space_type_text;

            //章节内容
            $v->content = \Opis\Closure\unserialize($v->content);

            //空间风格
            $style_text = $v->styles()->get()->pluck('name')->toArray();
            $v->style_arr = $style_text;

            unset($v->space_type_id);
            unset($v->id);
            return $v;
        });

        return $this->apiSv->respDataReturn($album_sections);

    }

    //获取相似方案数据
    public function list_album_similiars($id,Request $request)
    {

        $search_album = SearchAlbum::where('web_id_code',$id)->first();

        if(!$search_album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        ///随机显示同一风格的6个方案
        $album = Album::query()
            ->where('id',$search_album->album_id)
            ->first();

        if(!$album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return $this->apiSv->respFailReturn('方案状态异常');
        }

        $style_ids = $album->style()->get()->pluck('id');

        $albums = BsMobileAlbumDataService::listAlbumDetailSimiliar([
            'targetAlbumId' => $album->id,
            'styleIds' => $style_ids
        ],$request);


        return $this->apiSv->respDataReturn($albums);


    }

    //收藏操作
    public function collect(Request $request){

        $album_code = $request->input('aid',0);

        if(!$album_code){
            return $this->apiSv->respFailReturn('参数错误');
        }

        $search_album = SearchAlbum::where('web_id_code',$album_code)->first();

        if(!$search_album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        $album = Album::find($search_album->album_id);

        if(!$album){
            return $this->apiSv->respFailReturn('信息不存在');
        }

        $album_id = $album->id;

        $result = OpService::favAlbum($album_id);

        if($result['result'] <0){
            return $this->apiSv->respFailReturn($result['msg']);
        }else{
            return $this->apiSv->respDataReturn([],$result['msg']);
        }


    }

    //点赞操作
    public function like(Request $request){

        $designer = Auth::user();

        $album_code = $request->input('aid',0);

        if(!$album_code){
            return $this->apiSv->respFailReturn('参数错误');
        }

        $search_album = SearchAlbum::where('web_id_code',$album_code)->first();

        if(!$search_album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        $album = Album::find($search_album->album_id);

        if(!$album){
            return $this->apiSv->respFailReturn('信息不存在');
        }

        $album_id = $album->id;

        $result = OpService::likeAlbum($album_id);

        if($result['result'] <0){
            return $this->apiSv->respFailReturn($result['msg']);
        }else{
            return $this->apiSv->respDataReturn([],$result['msg']);
        }


    }

    //获取方案产品信息
    public function list_album_products($id,Request $request){

        $search_album = SearchAlbum::where('web_id_code',$id)->first();

        if(!$search_album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        $album = Album::query()
            ->where('id',$search_album->album_id)
            ->first();

        if(!$album){
            return $this->apiSv->respFailReturn('方案不存在');
        }

        if(
            $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
        ){
            return $this->apiSv->respFailReturn('方案状态异常');
        }

        $album_products = BsMobileProductDataService::listAlbumDetailProduct([
            'targetAlbumId' => $album->id
        ],$request);

        return $this->apiSv->respDataReturn($album_products);


    }

}