<?php

namespace App\Http\Controllers\v1\site\center;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Area;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;

class HomeController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //
    public function index(Request $request)
    {
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);
        /*$value = new ParamConfigUseService(auth()->user()->id);
        $gender_required = $value->find('platform.app_info.designer.gender.required');
        die(\GuzzleHttp\json_encode($gender_required));*/
        return $this->get_view('v1.site.center.index',compact('__BRAND_SCOPE'));
    }

}
