<?php

namespace App\Http\Middleware;

use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Models\Designer;
use App\Models\Guest;
use App\Models\Organization;
use App\Services\v1\mobile\GuestService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\PageService;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;

class WechatOauth
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
        $account = 'default';
        $scopes = null;

        // $account 与 $scopes 写反的情况
        if (is_array($scopes) || (\is_string($account) && Str::is('snsapi_*', $account))) {
            list($account, $scopes) = [$scopes, $account];
            $account || $account = 'default';
        }

        $isNewSession = false;
        $sessionKey = \sprintf('wechat.oauth_user.%s', $account);
        $config = config(\sprintf('wechat.official_account.%s', $account), []);
        $officialAccount = app(\sprintf('wechat.official_account.%s', $account));
        $scopes = $scopes ?: Arr::get($config, 'oauth.scopes', ['snsapi_base']);

        if (is_string($scopes)) {
            $scopes = array_map('trim', explode(',', $scopes));
        }

        $session = session($sessionKey, []);


        if (!$session) {
            if ($request->ajax() || $request->wantsJson()) {

                return response(['status' => 0, 'msg' => '请登录', 'code' => ApiService::API_CODE_AUTH_FAIL, 'data' => []]);
            }else{
                if ($request->has('code')) {
                    //$officialAccount->oauth->user()不能多次使用，将其放入一个变量来用
                    $wechatUser = $officialAccount->oauth->user();

                    session([$sessionKey => $wechatUser ?? []]);

                    //写入微信用户信息到session
                    $openId = $wechatUser['id'];

                    //判断微信用户是否绑定设计师
                    $designer = Designer::where('login_wx_openid',$openId)->first();

                    if($designer){
                        //设计师
                        Auth::login($designer);
                    }else{
                        //游客
                        $guest = Guest::where('login_wx_openid',$openId)->first();
                        if(!$guest){
                            $guest = GuestService::addGuest($openId);
                        }
                        Auth::guard('m_guest')->login($guest);
                    }

                    $isNewSession = true;

                    event(new WeChatUserAuthorized(session($sessionKey), $isNewSession, $account));


                    return redirect()->to($this->getTargetUrl($request));
                }

                session()->forget($sessionKey);

                return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
            }

        }

        event(new WeChatUserAuthorized(session($sessionKey), $isNewSession, $account));


        return $next($request);



    }

    protected function getTargetUrl($request)
    {
        $queries = Arr::except($request->query(), ['code', 'state']);

        return $request->url().(empty($queries) ? '' : '?'.http_build_query($queries));
    }
}
