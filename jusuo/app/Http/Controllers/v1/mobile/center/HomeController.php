<?php

namespace App\Http\Controllers\v1\mobile\center;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\FavProduct;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index()
    {
        $designer = Auth::user();
        if(!$designer){
            return $this->goTo404(PageService::ErrorNoAuthority,'','mobile');
        }


        return $this->get_view('v1.mobile.center.index');
    }

    public function edit()
    {
        $designer = Auth::user();

        if(!$designer){
            return $this->goTo404(PageService::ErrorNoAuthority,'','mobile');
        }
        return $this->get_view('v1.mobile.center.edit');
    }

    public function fav_designers()
    {
        $designer = Auth::user();
        $designers = $designer->fav_designers()->where('status',Designer::STATUS_ON)->with('detail')->get();
        return $this->get_view('v1.mobile.center.designers',compact('designers'));
    }

    public function fav_albums()
    {
        $designer = Auth::user();
        $albums = $designer->fav_albums()->get();
        foreach($albums as $v){
            $array = $v->house_types()->get()->pluck('name')->toArray();
            $v->house_type = implode(' ',$array);
            $styles = $v->style()->get();
            $style_text = $styles->pluck('name')->toArray();
            $v->style = implode(' ',$style_text);
        }
        $title = '收藏的方案('.count($albums).')';
        return $this->get_view('v1.mobile.center.albums',compact('albums','title'));
    }

    public function fav_products()
    {
        $designer = Auth::user();
        $products = $designer->fav_products()->get();
        foreach($products as $v){
            $v->photo_product = unserialize($v->photo_product);
            $v->photo_product = $v->photo_product[0];
            $v->series = $v->series->name;
            $v->spec = $v->spec->name;
        }
        $title = '收藏的产品('.count($products).')';
        return $this->get_view('v1.mobile.center.products',compact('products','title'));
    }

    public function my_albums()
    {
        $designer = Auth::user();
        $albums = $designer->album()->get();
        foreach($albums as $v){
            $array = $v->house_types()->get()->pluck('name')->toArray();
            $v->house_type = implode(' ',$array);
            $styles = $v->style()->get();
            $style_text = $styles->pluck('name')->toArray();
            $v->style = implode(' ',$style_text);
        }
        $title = '我的方案('.count($albums).')';
        return $this->get_view('v1.mobile.center.albums',compact('albums','title'));
    }


    /*---------------------api方法----------------------*/

    public function getCenterData(Request $request)
    {
        $designer = Auth::user();
        if(!$designer){
            return $this->apiSv->respFailReturn('权限不足');
        }

        $count_album_fav = $designer->fav_albums()->count();
        $count_designer_fav = $designer->fav_designers()->count();

        $designerId = $designer->id;
        $web_id_code = $designer->web_id_code;

        //形象照显示
        $index_bg = '';
        if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //显示销售商形象照
            $dealerDetail = DetailDealer::where('dealer_id',$designer->organization_id)->first();
            if(!$dealerDetail){
                return $this->apiSv->respFailReturn('信息错误');
            }
            $index_bg = $dealerDetail->index_photo?:'/v1/images/mobile/designer-bg-0.jpg';

        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //显示品牌形象照
            $brandDetail = DetailBrand::where('brand_id',$designer->organization_id)->first();
            if(!$brandDetail){
                return $this->apiSv->respFailReturn('信息错误');
            }
            $index_bg = $brandDetail->brand_image?:'/v1/images/mobile/designer-bg-0.jpg';
        }
        $organization = DesignerService::getDesignerBelongOrganizationNameCode($designer);

        $designer = DesignerDetail::where('designer_id',$designerId)->first();
        $style = DesignerService::getDesignerStyleString($designerId);
        $servingArea = DesignerService::getDesignerBelongArea($designerId);

        $data['cover'] = [
            'avatar'=> url($designer->url_avatar),
            'bg'=> url($index_bg),
            'level_title'=> Designer::designerTitleCnFull($designer->self_designer_level),
        ];
        $data['data'] = [
            'web_id_code'=> $web_id_code,
            'nickname'=> $designer->nickname,
            'style'=> explode('，',$style),
            'company'=> $organization['name'],
            'company_link'=> $organization['code'],
            'city'=> $servingArea,
            'exp_year'=> $designer->self_working_year>0?$designer->self_working_year.'年设计经验':'',
            'count_album'=> $designer->count_album,
            'count_fan'=> $designer->count_fan,
            'count_praise'=> $designer->count_praise,
            'count_visit'=> $designer->count_visit,
            'count_album_all'=> Album::where('designer_id',$designerId)->count(),
            'count_product_fav'=> FavProduct::where('designer_id',$designerId)->count(),
            'count_album_fav'=> $count_album_fav,
            'count_designer_fav'=> $count_designer_fav,
            'point_money'=> $designer->point_money,
        ];
        return $this->apiSv->respDataReturn($data);
    }

    //获取编辑信息
    public function get_edit_info()
    {
        $designer = Auth::user();
        $designer_detail = $designer->detail;

        $result = array();
        $result['url_avatar'] = $designer_detail->url_avatar;
        $result['nickname'] = $designer_detail->nickname;
        $result['self_working_year'] = $designer_detail->self_working_year;

        return $this->apiSv->respDataReturn($result);

    }

    //提交编辑信息
    public function submit_edit(Request $request)
    {
        $designer = Auth::user();
        $designer_detail = $designer->detail;

        $inputData = $request->all();

        //参数设置
        $pcu = new ParamConfigUseService($designer->id);
        $config['nickname_required'] = $pcu->find('platform.app_info.designer.nickname.required');

        $validator = Validator::make($inputData, [
            'nickname' => $config['nickname_required']?'required':'present',
            'self_working_year' => 'required',
        ]);

        if ($validator->fails()) {
            $this->apiSv->respFail('请完整填写信息后再提交！');
        }

        $exist_nickname = DesignerDetail::where('nickname',$inputData['nickname'])
            ->where('designer_id','<>',$designer->id)
            ->count();
        if($exist_nickname>0){
            $this->apiSv->respFail('昵称已存在！');
        }

        if(!is_numeric($inputData['self_working_year'])){
            $this->apiSv->respFail('工作经验请输入数字！');
        }

        $designer_detail->nickname = $inputData['nickname'];
        $designer_detail->self_working_year = intval($inputData['self_working_year']);
        $designer_detail->save();

        return $this->apiSv->respDataReturn([],'修改成功');

    }

    //上传头像
    public function upload_avatar(Request $request)
    {
        $designer = Auth::user();

        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        $save_dir = trim(UploadOssService::KEY_DIR_DESIGNER_AVATAR,'/');
        $strRandom = StrService::strRandom(2);
        $final_dir = $save_dir."/".date('y')."/".date('m')."/".$strRandom;
        $save_file_name = $path = $file->store($final_dir,'public');
        $save_path = '/storage/'.$path;

        //更新用户的头像字段
        $designer_detail = $designer->detail;
        $designer_detail->url_avatar = $save_path;
        $designer_detail->save();

        if($save_file_name){
            $this->apiSv->respData([
                'access_path'=>'/storage/'.$path,
                'base_path'=>$path,
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->apiSv->respFail($error_msg);
        }

    }
}
