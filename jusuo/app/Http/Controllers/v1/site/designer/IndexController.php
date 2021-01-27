<?php

namespace App\Http\Controllers\v1\site\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\StrService;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailBrand;
use App\Models\DetailDealer;
use App\Models\FavDesigner;
use App\Models\StatisticDesigner;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //预览详情页
    public function detail_preview($web_id_code,Request $request)
    {

        $data = Designer::where('web_id_code',$web_id_code)->first();

        if(!$data){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        if(
            $data->status != Designer::STATUS_ON
        ){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        $preview_brand_id = session()->get('preview_brand_id');

        $targetBrandId = DesignerService::getDesignerBrandScope($data->id);
        if($preview_brand_id != $targetBrandId){
            return $this->goTo404(PageService::ErrorNoAuthority);
        }

        $is_preview = true;

        $designerId = $data->web_id_code;

        return $this->get_view('v1.site.designer.detail',compact('is_preview','designerId'));


    }



    public function detail($web_id_code,Request $request){
        $loginDesigner = Auth::user();

        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);
        $designerId = StrService::get_id_by_web_code('designers',$web_id_code);
        if($designerId==-1){
            return $this->goTo404();
        }

        //目标设计师
        $designer = Designer::where('web_id_code',$web_id_code)->first();

        if(!$designer){
            return redirect('/')->withErrors(['设计师不存在']);
        }

        if(
            $designer->status != Designer::STATUS_ON
        ){
            return redirect('/')->withErrors(['设计师状态异常']);
        }

        //可见性判断
        if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //品牌设计师可见性
            //"品牌所有设计师+旗下销售商的所有设计师"
            $brand = $loginDesigner->brand;
            //如果目标设计师是品牌设计师
            if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                if($designer->organization_id != $brand->id){
                    //目标设计师不是同一品牌下
                    return $this->goTo404(PageService::ErrorNoAuthority,$__BRAND_SCOPE);
                }
            }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //如果目标设计师是销售商设计师
                $target_designer_dealer = $designer->seller;
                if($target_designer_dealer->p_brand_id != $brand->id){
                    //目标设计师所属销售商不是同一品牌下
                }
            }

        }else if($loginDesigner->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //销售商设计师可见性
            //品牌所有设计师+所在地可见的销售商的设计师+所属销售商的设计师

        }else{
            return redirect('/')->withErrors(['暂无权限']);
        }

        OpService::visitDesigner($designerId,$request);
        $designerId = $web_id_code;
        return $this->get_view('v1.site.designer.detail',compact('__BRAND_SCOPE','designerId'));
    }

    public function get_info(Request $request){
        $this->extractBrandScope($request);
        $designerId = $request->input('designer_id',0);
        $designerId = StrService::get_id_by_web_code('designers',$designerId);
        $designer = Designer::find($designerId);
        if(!$designer||$designer->status<>Designer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }

        $designerDetail = DesignerDetail::where('designer_id',$designerId)->first();
        if(!$designerDetail){
            return $this->apiSv->respFailReturn();
        }
        $user = Auth::user();
        $faved = false;
        if($user){
            $fav = FavDesigner::where(['target_designer_id'=>$designerId, 'designer_id'=>$user->id])->count();
            if($fav){
                $faved = true;
            }
        }

        //设计方案数
        $count_upload_album = 0;
        $stat = StatisticDesigner::where('designer_id',$designer->id)->orderBy('id','desc')->first();
        if($stat){
            $count_upload_album = intval($stat->count_upload_album);
        }

        //形象照显示
        $index_bg = '';
        if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //显示销售商形象照
            $dealerDetail = DetailDealer::where('dealer_id',$designer->organization_id)->first();
            if(!$dealerDetail){
                return $this->apiSv->respFailReturn('信息错误');
            }
            $index_bg = $dealerDetail->index_photo?:'/v1/images/site/dealer/bg.png';

        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //显示品牌形象照
            $brandDetail = DetailBrand::where('brand_id',$designer->organization_id)->first();
            if(!$brandDetail){
                return $this->apiSv->respFailReturn('信息错误');
            }
            $index_bg = $brandDetail->brand_image?:'/v1/images/site/dealer/bg.png';

        }
        //die(\GuzzleHttp\json_encode($index_bg));

        $data = [
            'bg'=>$index_bg,
            'avatar'=>url($designerDetail->url_avatar),
            'web_id_code'=>$designer->web_id_code,
            'nickname'=>$designerDetail->nickname,
            'title'=>Designer::designerTitle($designerDetail->self_designer_level),
            'working_year'=>$designerDetail->self_working_year,
            'point_focus'=>$designerDetail->point_focus,
            'count_fan'=>$designerDetail->count_fan,
            'album'=>$count_upload_album,
            'company'=>$designerDetail->self_organization,
            'introduction'=>$newstr = nl2br($designerDetail->self_introduction),
            'faved'=>$faved,
            'count_visit'=>$designerDetail->count_visit,
            'count_praise'=>$designerDetail->count_praise,
            'count_album'=>$count_upload_album
        ];
        $education = $designerDetail->self_education;
        if($education) {
            $education = unserialize($education);
            $data['school'] = $education[0]['school'];
        }
        else{
            $data['school'] = '';
        }
        $award = $designerDetail->self_award;
        if($award) {
            $award = unserialize($award);
            $awardString = '';
            foreach ($award as $v) {
                if ($awardString == '')
                    $awardString .= $v['award_name'];
            }
            $data['award'] = $awardString;
        }
        else{
            $data['award'] = '';
        }
        $data['style'] = DesignerService::getDesignerStyleString($designerId);
        $data['space'] = DesignerService::getDesignerSpaceString($designerId);
        $servingArea = LocationService::getServingArea(
            $designerDetail->area_serving_district,
            DetailDealer::PRIVILEGE_AREA_SERVING_CITY,
            DetailDealer::PRIVILEGE_AREA_SERVING_CITY
        );
        $data['serving_city'] = $servingArea['mergeString'];

        return $this->apiSv->respDataReturn([
            'data'=>$data,
            'designerId'=>$designerId,
        ]);
    }

    public function get_album(Request $request){
        $this->extractBrandScope($request);
        $designerId = $request->input('designer_id',0);
        $designerId = StrService::get_id_by_web_code('designers',$designerId);
        $designer = Designer::find($designerId);
        if(!$designer||$designer->status<>Designer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        $params = [
            'designerId'=>$designerId,
        ];
        if($request->has('style')){
            $params['styleId'] = $request->input('style',0);
        }
        $params['take'] = 6;
        $params['isOrderByWeight'] = false;
        $params['needPraiseFav'] = false;
        $params['needTypeStyle'] = false;
        $params['isStatusPass'] = true;
        //筛选旗下销售商方案时，不需要筛选被品牌显示的方案
        $params['statusBrandOn'] = false;
        $album = AlbumService::getAlbum($params);

        return $this->apiSv->respDataReturn($album);
    }

    public function get_album_top(Request $request){
        $this->extractBrandScope($request);
        $designerId = $request->input('designer_id',0);
        $designerId = StrService::get_id_by_web_code('designers',$designerId);
        $designer = Designer::find($designerId);
        if(!$designer||$designer->status<>Designer::STATUS_ON){
            return $this->apiSv->respFailReturn();
        }
        $params = [
            'designerId'=>$designerId,
        ];

        $params['take'] = 4;
        $params['needPraiseFav'] = true;
        $params['needTypeStyle'] = true;
        $params['isStatusPass'] = true;
        //筛选旗下销售商方案时，不需要筛选被品牌显示的方案
        $params['statusBrandOn'] = false;
        $params['isRepresent'] = true;
        $albumTop = AlbumService::getAlbum($params);

        return $this->apiSv->respDataReturn($albumTop['data']);
    }

}
