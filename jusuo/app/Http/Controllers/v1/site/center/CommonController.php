<?php

namespace App\Http\Controllers\v1\site\center;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Models\Area;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;

class CommonController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //错误页
    public function error(Request $request)
    {
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.error',compact('__BRAND_SCOPE'));
    }

}
