<?php

namespace App\Http\Controllers\v1\site\album\api;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\HouseType;
use App\Models\LikeAlbum;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
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
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AlbumController extends ApiController
{

    public function __construct(

    ){

    }

    /*----------方案首页相关------------*/

    //获取筛选类型数据
    public function list_filter_types(Request $request)
    {
        $query = $request->input('query','');
        $query_string = urldecode($query);
        parse_str($query_string,$query_array);


        $result = array();

        $styles = Style::all();
        $house_types = HouseType::all();
        $space_type = SpaceType::all();
        $count_area = [
            ['id'=>'0_50','name'=>'50㎡及以下'],
            ['id'=>'50_80','name'=>'50-80㎡'],
            ['id'=>'80_100','name'=>'80-100㎡'],
            ['id'=>'100_130','name'=>'100-130㎡'],
            ['id'=>'130_m','name'=>'130㎡及以上'],
        ];

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


        $house_type_info = array();
        $house_type_info['title'] = '户型';
        $house_type_info['value'] = 'ht';
        $house_type_info['has_selected'] = false;
        if(isset($query_array[$house_type_info['value']])){
            $type_query = $query_array[$house_type_info['value']];
            for($i=0;$i<count($house_types);$i++){
                if($house_types[$i]->id == $type_query){
                    $house_types[$i]->selected = 1;
                    $house_type_info['has_selected'] = true;
                }else{
                    $house_types[$i]->selected = 0;
                }
            }
        }
        $house_type_info['data'] = $house_types;


        $space_type_info = array();
        $space_type_info['title'] = '空间';
        $space_type_info['value'] = 'spt';
        $space_type_info['has_selected'] = false;
        if(isset($query_array[$space_type_info['value']])){
            $type_query = $query_array[$space_type_info['value']];
            for($i=0;$i<count($space_type);$i++){
                if($space_type[$i]->id == $type_query){
                    $space_type[$i]->selected = 1;
                    $space_type_info['has_selected'] = true;

                }else{
                    $space_type[$i]->selected = 0;
                }
            }
        }
        $space_type_info['data'] = $space_type;


        $count_area_info = array();
        $count_area_info['title'] = '面积';
        $count_area_info['value'] = 'ca';
        $count_area_info['has_selected'] = false;

        if(isset($query_array[$count_area_info['value']])){
            $type_query = $query_array[$count_area_info['value']];
            for($i=0;$i<count($count_area);$i++){
                if($count_area[$i]['id'] == $type_query){
                    $count_area[$i]['selected'] = 1;
                    $count_area_info['has_selected'] = true;

                }else{
                    $count_area[$i]['selected'] = 0;
                }
            }
        }
        $count_area_info['data'] = $count_area;


        $result[] = $style_info;
        $result[] = $house_type_info;
        $result[] = $space_type_info;
        $result[] = $count_area_info;

        return $this->respDataReturn($result);

    }

    //获取设计方案列表数据
    public function list_albums(Request $request)
    {
        $designer = Auth()->user();

        $albums = BsAlbumDataService::listAlbumIndexData([
            'loginDesigner' => $designer,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id')
        ],$request);

        return $this->respDataReturn($albums);

    }

    //点赞操作
    public function like(Request $request){

        $designer = Auth()->user();

        $album_code = $request->input('aid',0);
        $operation = $request->input('op',1);  //1点赞2取消点赞

        $operation = intval($operation);
        if(!in_array($operation,[1,2])){
            return $this->respFailReturn('操作错误');
        }

        if(!$album_code){
            return $this->respFailReturn('参数错误');
        }

        $search_album = SearchAlbum::where('web_id_code',$album_code)->first();

        if(!$search_album){
            return $this->respFailReturn('方案不存在');
        }

        $album = Album::find($search_album->album_id);

        if(!$album){
            return $this->respFailReturn('信息不存在');
        }

        if($album->designer_id == $designer->id){
            return $this->respFailReturn('不能点赞自己的方案哦');
        }

        $album_id = $album->id;

        $result = OpService::likeAlbum($album_id);

        if($result['result'] <0){
            return $this->respFailReturn($result['msg']);
        }else{
            return $this->respDataReturn([],$result['msg']);
        }


    }

    //收藏操作
    public function collect(Request $request){

        $designer = Auth()->user();

        $album_code = $request->input('aid',0);
        $operation = $request->input('op',1);  //1点赞2取消点赞

        $operation = intval($operation);
        if(!in_array($operation,[1,2])){
            return $this->respFailReturn('操作错误');
        }

        if(!$album_code){
            return $this->respFailReturn('参数错误');
        }

        $search_album = SearchAlbum::where('web_id_code',$album_code)->first();

        if(!$search_album){
            return $this->respFailReturn('方案不存在');
        }

        $album = Album::find($search_album->album_id);

        if(!$album){
            return $this->respFailReturn('信息不存在');
        }

        if($album->designer_id == $designer->id){
            return $this->respFailReturn('不能收藏自己的方案哦');
        }


        $album_id = $album->id;

        $result = OpService::favAlbum($album_id);

        if($result['result'] <0){
            return $this->respFailReturn($result['msg']);
        }else{
            return $this->respDataReturn([],$result['msg']);
        }


    }

    
    /*----------方案详情页相关------------*/

    //获取方案基本信息
    public function get_album_info($id,Request $request)
    {

        $loginDesigner = Auth::user();

        $brand_scope = $request->input('__bs','');

        $album = Album::select([
            'id','code','designer_id','verify_time','title','address_province_id','address_city_id','address_area_id','address_street',
            'address_residential_quarter','address_building','address_layout_number','count_area',
            'description_design','count_visit','count_praise','count_fav','count_use',
            'description_layout','photo_layout','photo_cover',
            'period_status','visible_status'
        ])
            ->where('web_id_code',$id)
            ->first();


        if(!$album){
            return $this->respFailReturn('方案不存在');
        }

        //如果非预览，才判断方案状态
        $preview_designer_id = session()->get('designer_session.preview_designer_id');
        if(!isset($preview_designer_id) || !$preview_designer_id){
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
            ){
                return $this->respFailReturn('方案状态异常');
            }
        }


        //判断可访问性
        $check = $this->check_detail_api_access($loginDesigner,$album);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }

        //web随机码
        $album->web_id_code = $id;

        //户型图
        $album->photo_layout_data = \Opis\Closure\unserialize($album->photo_layout);

        ///所在城市
        $province = Area::find($album->address_province_id);
        $city = Area::find($album->address_city_id);
        $area = Area::find($album->address_area_id);
        $city_text = ($province?$province->name:'').($city?$city->name:'').($area?$area->name:'');
        $album->city_text = $city_text;

        //详细地址
        /*$province = Area::find($album->address_province_id);
        $city = Area::find($album->address_city_id);
        $area = Area::find($album->address_area_id);
        $address_text = $province->name?$province->name:''.$city?$city->short_name:''.$area?$area->short_name:'';
        $album->address_text = $address_text.$album->address_street.$album->address_residential_quarter.$album->address_building.$album->address_layout_number;
        */

        //户型
        $house_types = $album->house_types()->get()->pluck('name')->toArray();
        $album->house_type_text = implode('/',$house_types);

        //风格
        $styles = $album->style()->get();
        $style_text = $styles->pluck('name')->toArray();
        $style_ids = $styles->pluck('id')->toArray();
        $album->style_text = implode(' ',$style_text);
        $style_id = $style_ids[0];
        $album->more_similiar_url = url('/album?stl='.$style_id."&__bs=".$brand_scope);

        //设计师信息
        $designer_info = [];
        $designer_info['organization'] = '';
        $designer_info['fans'] = 0;
        $designer_info['count_upload_album'] = 0;
        $designer = Designer::select(['id','organization_type','organization_id','web_id_code'])->find($album->designer_id);
        if(!$designer){
            return $this->respFailReturn('设计师状态异常');
        }
        if($designer->organization){
            $designer_info['organization'] = $designer->organization->name;
        }
        $designer_detail = DesignerDetail::select(['nickname','url_avatar','count_fav'])->where('designer_id',$album->designer_id)->first();
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
        }

        $album->designer_info = $designer_info;

        $album->liked = false;
        if($loginDesigner){
            $liked = LikeAlbum::where([
                'designer_id'=>$loginDesigner->id,
                'album_id'=>$album->id,
            ])->count();
            if($liked){ $album->liked = true; }
        }

        $album->collected = false;
        if($loginDesigner){
            $collected = FavAlbum::where('designer_id',$loginDesigner->id)->where('album_id',$album->id)->first();
            if($collected){ $album->collected = true; }
        }

        if($loginDesigner){
            if($album->designer_id != $loginDesigner->id){
                /*$logVisit = new AlbumDesignerVisit();
                $logVisit->designer_id = $loginDesigner->id;
                $logVisit->album_id = $album->id;
                $logVisit->save();*/
            }
        }


        unset($album->designer_id);

        return $this->respDataReturn($album);

    }

    //获取方案空间信息
    public function list_album_sections($id)
    {
        $loginDesigner = Auth::user();

        $album = Album::query()
            ->where('web_id_code',$id)
            ->first();

        if(!$album){
            return $this->respFailReturn('方案不存在');
        }

        //如果非预览，才判断方案状态
        $preview_brand_id = session()->get('designer_session.preview_designer_id');
        if(!isset($preview_brand_id) || !$preview_brand_id){
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
            ){
                return $this->respFailReturn('方案状态异常');
            }
        }

        //判断可访问性
        $check = $this->check_detail_api_access($loginDesigner,$album);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


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
            $v->style_text = implode('/',$style_text);

            unset($v->space_type_id);
            unset($v->id);
            return $v;
        });

        return $this->respDataReturn($album_sections);

    }

    //获取方案产品信息
    public function list_album_products($id,Request $request){

        $album = Album::query()
            ->where('web_id_code',$id)
            ->first();

        if(!$album){
            return $this->respFailReturn('方案不存在');
        }

        //如果非预览，才判断方案状态
        $preview_brand_id = session()->get('designer_session.preview_designer_id');
        if(!isset($preview_brand_id) || !$preview_brand_id){
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
            ){
                return $this->respFailReturn('方案状态异常');
            }
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$album);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $album_products = BsProductDataService::listAlbumDetailProduct([
            'loginDesigner' => Auth::user(),
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
            'targetAlbumId' => $album->id
        ],$request);

        return $this->respDataReturn($album_products);


    }

    //获取相似方案数据
    public function list_album_similiars($id,Request $request)
    {

        ///随机显示同一风格的6个方案
        $album = Album::query()
            ->where('web_id_code',$id)
            ->first();

        if(!$album){
            return $this->respFailReturn('方案不存在');
        }

        //如果非预览，才判断方案状态
        $preview_brand_id = session()->get('designer_session.preview_designer_id');
        if(!isset($preview_brand_id) || !$preview_brand_id){
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
            ){
                return $this->respFailReturn('方案状态异常');
            }
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$album);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $style_ids = $album->style()->get()->pluck('id');

        $albums = BsAlbumDataService::listAlbumDetailSimiliar([
            'loginDesigner'=> Auth::user(),
            'brandScope' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
            'styleIds' => $style_ids
        ],$request);


        return $this->respDataReturn($albums);


    }

    //获取方案评论信息
    public function list_album_comments($id){
        $album = Album::query()
            ->where('web_id_code',$id)
            ->first();

        if(!$album){
            return $this->respFailReturn('方案不存在');
        }

        //如果非预览，才判断方案状态
        $preview_brand_id = session()->get('designer_session.preview_designer_id');
        if(!isset($preview_brand_id) || !$preview_brand_id){
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
            ){
                return $this->respFailReturn('方案状态异常');
            }
        }

        //判断可访问性
        $loginDesigner = Auth::user();
        $check = $this->check_detail_api_access($loginDesigner,$album);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        $album_comments = $album->comments()
            ->orderBy('created_at','desc')
            ->where('status',AlbumComments::STATUS_ON)
            ->select(['id','designer_id','content','created_at'])
            ->paginate(10);

        $album_comments->transform(function($v)use($loginDesigner){

            $v->publish_time = GlobalService::time_ago($v->created_at);

            $author = DesignerDetail::select(['designer_id','nickname','url_avatar'])->where('designer_id',$v->designer_id)->first();

            $v->followable = false;
            $v->author = '';
            $v->author_avatar = '';

            if($author){
                $v->author = $author->nickname;
                $v->author_avatar = $author->url_avatar;

                //当前用户有登录
                if($loginDesigner){
                    if($author->designer_id != $loginDesigner->id){
                        $v->followable = true;
                    }
                }else{
                    //没登录也给出可fallow
                    $v->followable = true;
                }

            }

            unset($v->created_at);
            unset($v->designer_id);
            return $v;
        });

        return $this->respDataReturn($album_comments);


    }
    
    //提交评论
    public function commit_comment($id,Request $request)
    {
        $designer = Auth::user();

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'content' => 'required',
            'follow' => 'filled',
        ]);

        if ($validator->fails()) {
            return $this->respFailReturn('参数缺失~');
        }

        $album_code = $id;
        $content = $input_data['content'];
        $follow_id = intval($request->input('follow',0));


        $album = Album::query()
            ->where('web_id_code',$id)
            ->first();

        $album_id = $album->id;

        if(!$album){
            return $this->respFailReturn('方案不存在');
        }

        //如果非预览，才判断方案状态
        $preview_brand_id = session()->get('designer_session.preview_designer_id');
        if(!isset($preview_brand_id) || !$preview_brand_id){
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH /*||
            $album->visible_status != Album::VISIBLE_STATUS_ON*/
            ){
                return $this->respFailReturn('方案状态异常');
            }
        }

        //判断可访问性
        $check = $this->check_detail_api_access(Auth::user(),$album);
        if($check['status'] == 0){ return $this->respFailReturn($check['msg']); }


        if($follow_id){
            $follow_comment = AlbumComments::where('album_id',$album_id)
                ->where('id',$follow_id)->first();
            if(!$follow_comment){
                return $this->respFailReturn('跟帖评论不存在！');
            }
        }


        $comment = AlbumComments::where('album_id',$album_id)
            ->where('designer_id',$designer->id)
            ->where('content',$content)->first();
        if($comment){
            return $this->respFailReturn('请勿重复发相同内容的评论~');
        }

        //增加评论
        $comment = new AlbumComments();
        $comment->album_id = $album_id;
        $comment->designer_id = $designer->id;
        $comment->target_comment_id = $follow_id;
        $comment->content = $content;
        $comment->status = AlbumComments::STATUS_ON;
        $comment->save();

        return $this->respDataReturn($comment,'评论成功');
    }

    //判断方案详情api可访问性
    private function check_detail_api_access($loginDesigner,$album){
        $result = [
            'status'=>1,
            'msg'=>''
        ];

        $targetAlbumOrganization = AlbumService::getAlbumOrganization($album->id);
        $targetBrandId = $targetAlbumOrganization['brand_id'];

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
            if(!isset($preview_brand_id) || !$preview_brand_id || $preview_brand_id != $targetBrandId){
                $preview_seller_id = session()->get('preview_seller_id');
                if(DesignerService::checkDesignerDealer($album->designer_id,$preview_seller_id)<>1) {
                    $result['status'] = 0;
                    $result['msg'] = '权限不足';
                    return $result;
                }
            }
        }

        return $result;
    }
}
