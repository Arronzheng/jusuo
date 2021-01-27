<?php

namespace App\Http;

use App\Http\Middleware\WechatOauth;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            //\Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'auth.platform' => \App\Http\Middleware\Platform::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        'check.backend.auth' => \App\Http\Middleware\CheckBackendAuth::class,
        //'wechat.oauth' => \Overtrue\LaravelWeChat\Middleware\OAuthAuthenticate::class,
        'auth.site' => \App\Http\Middleware\SiteAuth::class,
        'site.check_formal_designer' => \App\Http\Middleware\SiteCheckFormalDesigner::class,
        'site.check_brand_scope' => \App\Http\Middleware\SiteCheckBrandScope::class,
        /*手机端*/

        'mobile.entry' => \App\Http\Middleware\MobileEntry::class,
        'wechat.oauth' => \App\Http\Middleware\WechatOauth::class,
        'auth.mobile' => \App\Http\Middleware\MobileAuth::class,
        'mobile.check_formal_designer' => \App\Http\Middleware\MobileCheckFormalDesigner::class,
        'mobile.location' => \App\Http\Middleware\MobileLocation::class,
        //'mobile.check_brand_scope' => \App\Http\Middleware\MobileCheckBrandScope::class,
        /*手机端*/
    ];
}
