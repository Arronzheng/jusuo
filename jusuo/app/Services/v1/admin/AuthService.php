<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24
 * Time: 12:36
 */

namespace App\Http\Services\v1\admin;


use App\Exceptions\admin\UnAuthenticateException;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthService
 * @package App\Http\Services\v1\admin
 */
class AuthService
{

    private $guard_name = '';


    //后台
    /**
     * @return mixed
     *
     */
    protected function guard()
    {
        try {
            $guardName = Auth::guard($this->getAuthUserGuardName());
        } catch (UnAuthenticateException $e) {
        }
        return $guardName;
    }

    /**
     * @return mixed
     */
    public function getAuthUserGuardName()
    {
        $currentGuardName = '';
        $guardNamePool = ['platform','brand','seller'];
        foreach($guardNamePool as $temp){
            $auth_guard =  Auth::guard($temp);
            if(isset($auth_guard)){
                $user = Auth::guard($temp)->user();
                if($user){
                    $currentGuardName = $temp;
                    break;
                }
            }

        }
        if(!$currentGuardName){
            throw new UnAuthenticateException('请重新登录');
        }else{
            return $currentGuardName;
        }
    }


    /**
     * @return mixed
     * @return
     */
    public function getAuthUser()
    {
        return $this->guard()->user();
    }

    /**
     * @return mixed
     */
    public function getAuthId()
    {
        return $this->getAuthUser()->id;
    }


}