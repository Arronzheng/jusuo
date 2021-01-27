<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Support\Facades\Auth;

class Platform
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
        if (auth()->guard('platform')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('admin/platform/login');
            }
        }
        /*if (Auth::guard('admin')->user()->organization_type!=Organization::ORGANIZATION_TYPE_PLATFORM){
            return redirect()->guest('admin/platform/login');
        }*/

        return $next($request);
    }
}
