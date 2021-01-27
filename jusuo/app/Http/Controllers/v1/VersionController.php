<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\common\StrService;
use App\Services\v1\site\PageService;
use Illuminate\Contracts\Encryption\DecryptException;

class VersionController extends Controller
{

    //需要以'/'结尾
    protected $url_prefix = '/';
    public $brand_scope;

    protected function get_view($view = null, $data = [], $mergeData = []){

        $data = array_merge($data,[
            'url_prefix'=>$this->url_prefix
        ]);
        return view($view, $data, $mergeData);

    }

    public function extractBrandScope($request)
    {
        $id = StrService::get_id_by_web_code('organization_brands',$request->input('__bs',''));
        if($id==-1)$this->goTo404();
        $this->brand_scope = $id;
    }

    public function compressBrandScope($id)
    {
        return StrService::get_web_code_by_id('organization_brands', $id);
    }

    public function goTo404($error=PageService::ErrorNoResult,$__BRAND_SCOPE='',$module="site"){
        if($module == 'mobile'){
            return PageService::showPageMobile($error,$__BRAND_SCOPE);

        }
        return PageService::showPage($error,$__BRAND_SCOPE);

        //header('location:/404/404.html');
    }

}
