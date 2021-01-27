<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/9/3
 * Time: 12:23
 */

namespace App\Services\common;


use App\Http\Controllers\ApiRootController;

class LoginService
{
    public static function randomToken($table='members'){
        $uid = sha1(uniqid(microtime(true), true));
        $data = $_SERVER['REQUEST_TIME'];
        $data .= isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $data = sha1($data);
        $hash = hash('sha256', $uid . md5($data));

        while(\DB::table($table)->where('remember_token',$hash)->count()>0){
            $uid = sha1(uniqid(microtime(true), true));
            $hash = hash('sha256', $uid . $data);
        }
        return $hash;
    }

    public static function handleIfUnAuthenticated($request)
    {
        if ($request->expectsJson()) {
            $result = new ApiRootController();
            return $result->respFailReturn('请重新登录');
        }

        $url = $request->fullUrl();

        /*
        https://charm100.com/admin/[brand]/xxx/xxx
        */

        $url_array = explode('/',$url);

        $route_name = 'admin.login';
        if(isset($url_array) && isset($url_array[4])){
            $guardName = $url_array[4];
            switch($guardName){
                case 'brand':
                    //$route_name = 'admin.login';
                    break;
                case 'seller':
                    //$route_name = 'admin.login';
                    break;
                default:break;
            }
        }

        return redirect(route($route_name));
    }
}