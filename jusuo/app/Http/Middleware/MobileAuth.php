<?php

namespace App\Http\Middleware;

use App\Models\Designer;
use App\Models\Guest;
use App\Models\Organization;
use App\Services\v1\mobile\GuestService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Closure;
use Illuminate\Support\Facades\Auth;

class MobileAuth
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
        //游客有可能通过个人中心绑定微信成为设计师，那么如果游客在手机端的session还在，会导致没有权限访问内容
        //所以每次进来游客都需要判断一下是否成为了设计师
        $guest_to_designer = false;
        if(Auth::guard('m_guest')->user()){
            $guest = Auth::guard('m_guest')->user();
            //是否已绑定设计师
            $designer = Designer::where('login_wx_openid',$guest->login_wx_openid)->first();
            if($designer){
                $guest_to_designer = true;
                Auth::guard('m_guest')->logout();
            }
        }
        if(
            (!Auth::guard('web')->user() && !Auth::guard('m_guest')->user()) || $guest_to_designer
        ){
            //$wechat_user = $app->oauth->user();
            $wechat_user = session('wechat.oauth_user.default');

            $openId = $wechat_user['id'];

            //是否已绑定设计师
            $designer = Designer::where('login_wx_openid',$openId)->first();
            if($designer){

                //用户已绑定设计师，手动登录
                Auth::guard('web')->login($designer);

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


            }else{

                //用户无绑定设计师，则登录游客
                $guest = Guest::where('login_wx_openid',$openId)->first();
                if(!$guest){
                    $guest = GuestService::addGuest($openId);
                }

                Auth::guard('m_guest')->login($guest);

                /*if ($request->ajax() || $request->wantsJson()) {

                    return response(['status' => 0, 'msg' => '请登录', 'code' => ApiService::API_CODE_AUTH_FAIL, 'data' => []]);

                    //return response('Unauthorized.', 401);
                } else {

                    //跳转至手机绑定页面
                    session()->put('m_bind_mobile_redirect',$request->fullUrl());
                    return redirect('/mobile/login/bind');

                }*/


            }
        }




        return $next($request);
    }
}
