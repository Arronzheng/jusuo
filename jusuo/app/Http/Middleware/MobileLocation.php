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

class MobileLocation
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

        $env = env('APP_ENV');
        if($env == 'production'){
            $locationCity = session()->get('location_city');

            if(!isset($locationCity)){
                //无位置信息
                session()->put('get_location_redirect',$request->fullUrl());
                return redirect('/mobile/common/get_location');
            }
        }else{
            //手动设置为佛山市
            session()->put('location_city',2011);
        }


        return $next($request);


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
