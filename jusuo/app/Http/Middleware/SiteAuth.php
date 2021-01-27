<?php

namespace App\Http\Middleware;

use App\Models\Designer;
use App\Models\Organization;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Closure;
use Illuminate\Support\Facades\Auth;

class SiteAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->guard('web')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {

                return response(['status' => 0, 'msg' => '请登录', 'code' => ApiService::API_CODE_AUTH_FAIL, 'data' => []]);

                //return response('Unauthorized.', 401);
            } else {
                //保存当前访问地址
                $access_url = $request->fullUrl();
                session()->put('login_redirect',$access_url);
                //return redirect('/404/404.html');
                $redirect_url = '/error/'.PageService::ErrorNoLogin;
                if($request->has('__bs')) {
                    $redirect_url.="?__bs=".$request->__bs;
                }
                return redirect($redirect_url);
            }
        }

        //获取当前设计师的品牌、销售商、所在城市信息
        $designer_scope_session = session('designer_scope');
        //当未有设计师品牌域信息或者品牌域信息中的定位城市为0时，都重新获取一次数据
        if(
            !isset($designer_scope_session) ||
            (isset($designer_scope_session) && $designer_scope_session['location_city_id'] == 0 )
        ){
            //当前定位城市
            $location_info = LocationService::getClientCity($request);
            $designer = Auth::user();
            $brand_id = 0;
            $dealer_id = 0;
            $location_city_id = 0;

            if($location_info && isset($location_info['city_id'])){
                $location_city_id = $location_info['city_id'];
            }

            if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
                $brand_id = $designer->organization_id;
            }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $dealer = $designer->seller;
                if(!$dealer){
                    $redirect_url = '/error/'.PageService::ErrorNoAuthority;
                    return redirect($redirect_url);
                }
                //如果销售商设计师是二级销售商旗下，则将$dealer_is置为其一级销售商id
                if($dealer->level == 2){
                    $dealer_id = $dealer->p_dealer_id;
                }else{
                    $dealer_id = $dealer->id;
                }
                $brand_id = $dealer->p_brand_id;
            }

            $designer_scope = array();
            $designer_scope['brand_id'] = $brand_id;
            $designer_scope['dealer_id'] = $dealer_id;
            $designer_scope['location_city_id'] = $location_city_id;

            session()->put('designer_scope',$designer_scope);
        }


        return $next($request);
    }
}
