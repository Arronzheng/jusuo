<?php

namespace App\Http\Middleware;

use App\Models\Designer;
use App\Models\Organization;
use App\Services\v1\site\PageService;
use Closure;
use Illuminate\Support\Facades\Auth;

class MobileCheckFormalDesigner
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
        $loginUser = auth()->guard('web')->user();
        if($loginUser){
            if($loginUser->status == Designer::STATUS_VERIFYING || $loginUser->status == Designer::STATUS_OFF)
            {
                $redirect_url = '/mobile/error/'.PageService::ErrorNoAuthority;
                return redirect($redirect_url);
            }
        }


        return $next($request);
    }
}
