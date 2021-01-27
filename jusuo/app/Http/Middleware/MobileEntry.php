<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Overtrue\Socialite\User as SocialiteUser;

class MobileEntry
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

        $env = env('APP_ENV');
        if($env == 'local'){
            $user = new SocialiteUser([
                //'id' => 'oS3JrwAIcK6jUsgeSEEmmhJj1K6M',
                'id' => 'oS3JrwPg_uM_hVtHKe8hCUFr0FFI',
            ]);

            session(['wechat.oauth_user.default' => $user]);

        }

        return $next($request);
    }
}
