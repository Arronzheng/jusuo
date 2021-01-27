<?php

namespace App\Http\Controllers\v1\site\common;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Models\Area;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;

class CommonController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //获取地区数据
    public function get_area_children(Request $request)
    {
        $province_id = $request->input('pi',0);

        $data = Area::orderBy('id','asc')->where('pid',$province_id)->select(['id','name'])->get();

        return $this->apiSv->respDataReturn($data);
    }

    //获取地区
    public function location_get_city(Request $request)
    {
        $data = LocationService::getClientCity($request);
        return $this->apiSv->respDataReturn($data['city']);
    }

    //获取地区
    public function ip_get_city(Request $request)
    {
        $data = LocationService::getClientCity($request);
        return $this->apiSv->respDataReturn($data);
    }

    //自定义错误页面
    /*public function error(Request $request)
    {
        return $this->get_view('v1.site.common.error');
    }*/
    public function error($code,Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->goTo404($code,$__BRAND_SCOPE);
    }

    public function get_album_web_id_code(){
        return AlbumService::addWebIdCode();
    }

    public function get_album_to_search(){
        return AlbumService::addToSearch();
    }

    public function get_dealer_to_search(){
        return DealerService::addToSearch();
    }

    public function get_designer_to_search(){
        return DesignerService::addToSearch();
    }

    public function get_product_to_search(){
        return ProductService::addToSearch();
    }

    public function set_dealer_category(){
        return DealerService::setDealerCategory();
    }
}
