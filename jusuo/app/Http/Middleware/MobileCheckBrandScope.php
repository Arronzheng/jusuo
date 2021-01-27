<?php

namespace App\Http\Middleware;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\StrService;
use App\Models\Designer;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\PageService;
use Closure;
use Illuminate\Support\Facades\Auth;

class MobileCheckBrandScope
{
    private $api_service;

    function __construct(ApiService $apiService)
    {
        $this->api_service = $apiService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $is_ajax = $request->ajax();
        //此中间件前必须先验证用户已经登录

        //获取当前请求的品牌域信息
        $version_controller = new VersionController();
        $version_controller->extractBrandScope($request);
        $brand_id = $version_controller->brand_scope;

        if(!$brand_id){
            //无品牌域信息
            return $this->return_fail($request,$is_ajax,ApiService::API_CODE_INVISIBLE,'暂无权限');
        }

        //是否有品牌可见记录session
        $brand_visible = session()->get('brand_visible');
        if(isset($brand_visible) && count($brand_visible)>0){
            if(in_array($brand_id,$brand_visible)){
                //当前用户在访问的品牌域内，放行
                return $next($request);
            }else{
                return $this->return_fail($request,$is_ajax,ApiService::API_CODE_INVISIBLE,'暂无权限');
            }
        }

        //是否有品牌不可见记录session
        $brand_invisible = session()->get('brand_invisible');
        if(isset($brand_invisible) && count($brand_invisible)>0){
            if(in_array($brand_id,$brand_invisible)){
                //当前用户不在访问的品牌域内，返回错误
                return $this->return_fail($request,$is_ajax,ApiService::API_CODE_INVISIBLE,'暂无权限');
            }
        }

        //判断品牌可见性
        $loginUser = auth()->guard('web')->user();
        $is_visible = DesignerService::isDesignerInBrandScope($loginUser->id,$brand_id);
        if($is_visible){
            //品牌可见，放行
            if(!isset($brand_visible)){
                $brand_visible = [];
            }
            array_push($brand_visible,$brand_id);
            session()->put('brand_visible',$brand_visible);
            return $next($request);
        }else{
            //品牌不可见，返回错误
            if(!isset($brand_invisible)){
                $brand_invisible = [];
            }
            array_push($brand_invisible,$brand_id);
            session()->put('brand_invisible',$brand_invisible);
            return $this->return_fail($request,$is_ajax,ApiService::API_CODE_INVISIBLE,'暂无权限');
        }

    }

    private function return_fail($request,$is_ajax,$code,$msg)
    {
        if($is_ajax){
            return $this->api_service->respFailReturn($msg,$code);
        }else{
            $redirect_url = '/mobile/error/'.PageService::ErrorNoAuthority;
            if($request->has('__bs')) {
                $redirect_url.="?__bs=".$request->__bs;
            }
            return redirect($redirect_url);
        }
    }
}
