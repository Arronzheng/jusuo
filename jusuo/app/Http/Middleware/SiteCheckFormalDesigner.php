<?php

namespace App\Http\Middleware;

use App\Models\Designer;
use App\Models\Organization;
use Closure;
use Illuminate\Support\Facades\Auth;

class SiteCheckFormalDesigner
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
            if($loginUser->status == Designer::STATUS_VERIFYING){
                return redirect('/center/basic_info');
            }else if($loginUser->status == Designer::STATUS_OFF)
            {
                return redirect('/center/error')->withErrors(['您没有相关权限']);
            }
        }


        return $next($request);
    }
}
