<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24
 * Time: 11:28
 */

namespace App\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait LogoutOtherUser
{
    //废弃
    public function LogoutOther(Request $request,$guard)
    {
        $backend_auth_array = ['platform','brand','seller','designCompany'];
        foreach($backend_auth_array as $item){
            if(Auth::guard($item)->check()&& $guard != $item){
                Auth::guard($item)->logout();
                $request->session()->forget(Auth::guard($item)->getName());
            }
        }
        
    }
}