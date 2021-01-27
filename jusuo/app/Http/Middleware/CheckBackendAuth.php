<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiRootController;
use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBackendAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $is_ajax = $request->ajax();
        $check = false;
        $guard = '';
        $backend_auth_array = ['platform','brand'];
        foreach($backend_auth_array as $item){
            if(Auth::guard($item)->check()){
                $guard = $item;
                $check = true;
            }
        }
        if ($check) {
            $request->merge(['guard'=>$guard]);
        }
        return $next($request);
    }
}
