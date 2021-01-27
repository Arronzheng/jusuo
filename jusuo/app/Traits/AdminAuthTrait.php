<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24
 * Time: 12:36
 */

namespace App\Traits;


use App\Exceptions\admin\UnAuthenticateException;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthService
 * @package App\Http\Services\v1\admin
 */
trait AdminAuthTrait
{

    private $guard_name = '';

    public function __construct()
    {
        $currentGuardName = '';
        $guardNamePool = ['platform','brand'];
        $user = Auth::guard('platform')->user();
        die(json_encode($user));
        foreach($guardNamePool as $temp){
            $user = Auth::guard($temp)->user();
            if($user){
                $currentGuardName = $temp;
                break;
            }
        }
        if(!$currentGuardName){
            throw new UnAuthenticateException('请重新登录');
        }else{
            $this->guard_name = $temp;
        }
    }

    //后台
    /**
     * @return mixed
     *
     */
    protected function guard()
    {
        return Auth::guard($this->guard_name);
    }

    /**
     * @return mixed
     */
    public function getAuthUserGuardName()
    {
        return $this->guard_name;
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

    /**
     * @return mixed
     */
    public function getAuthUserOrganizationId()
    {
        return $this->getAuthUser()->organization_id;
    }

    /**
     * @return mixed
     */
    public function getAuthUserOrganizationType()
    {
        return $this->getAuthUser()->organization_type;
        
    }


}