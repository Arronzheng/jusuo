<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/12/16
 * Time: 14:47
 */

namespace App\Http\Controllers\v1\site\album;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Designer;
use App\Models\HouseType;
use App\Models\IntegralGood;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\SearchAlbum;
use App\Models\Style;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsAlbumPageAccessService;
use App\Services\v1\site\BsDesignerPageAccessService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AlbumController extends VersionController{


    public function index(Request $request){

        $loginDesigner = Auth::user();

        //页面可见性
        $pageVisible = BsAlbumPageAccessService::albumIndex([
            'loginDesigner' => $loginDesigner,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        $keyword = $request->input('k','');

        return $this->get_view('v1.site.album.index',compact('keyword'));

    }

    public function detail($id,Request $request)
    {
        $loginDesigner = Auth::user();

        $album = Album::where('web_id_code',$id)->first();

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
        $pageVisible = BsAlbumPageAccessService::albumDetail([
            'loginDesigner' => $loginDesigner,
            'targetAlbumId' => $album->id,
            'loginBrandId' => session('designer_scope.brand_id'),
            'loginDealerId' => session('designer_scope.dealer_id'),
        ],$request);

        if(!$pageVisible['status']){
            return $this->goTo404($pageVisible['code']);
        }

        OpService::visitAlbum($album->id,$request);

        return $this->get_view('v1.site.album.detail');
    }

    //预览详情页
    public function detail_preview($id)
    {
        $album = Album::where('web_id_code',$id)->first();

        if(!$album){
            return $this->goTo404(PageService::ErrorNoResult);
        }

        //如果是设计师在预览，则要判断该方案是否设计师旗下的
        $preview_designer_id = session()->get('designer_session.preview_designer_id');

        if(isset($preview_designer_id) && $preview_designer_id){
            if($preview_designer_id != $album->designer_id){
                return $this->goTo404(PageService::ErrorNoAuthority);
            }
        }else{
            //如果是后台在预览，则需要判断审核通过
            if(
                $album->period_status != Album::PERIOD_STATUS_FINISH
            ){
                return $this->goTo404(PageService::ErrorNoResult);
            }
        }

        //设计师、品牌管理员预览都有这个preview_brand_id的session，用于详情页数据获取中的规定品牌域
        $preview_brand_id = session()->get('preview_brand_id');
        $targetBrandId = DesignerService::getDesignerBrandScope($album->designer_id);
        if($preview_brand_id != $targetBrandId){
            //检查是否属于本销售商旗下设计师
            $preview_seller_id = session()->get('preview_seller_id');
            if(DesignerService::checkDesignerDealer($album->designer_id,$preview_seller_id)<>1) {
                return $this->goTo404(PageService::ErrorNoAuthority);
            }
        }

        $is_preview = true;

        return $this->get_view('v1.site.album.detail',compact('is_preview'));
    }

    //处理

    public function comment(Album $album,Request $request){

        $validator = Validator::make($request->all(),[
            'content' => 'required',
            'target_comment_id' => 'required',
        ],[
            'content.required' => '请填写评论内容',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            return $msg_result;
        }

        $concent = $request->concent;
        $target_comment_id = $request->target_comment_id;

        $comment = new AlbumComments();
        $comment->designer_id = $request->user()->id;
        $comment->albun_id = $album->id;
        $comment->target_comment_id = $target_comment_id;
        $comment->status = 1;
        $comment->comment = $concent;
        $comment->save();

        return $this->apiSv->respDataReturn('评论成功');
    }


}