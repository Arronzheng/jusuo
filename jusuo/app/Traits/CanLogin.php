<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 16:16
 */

namespace App\Traits;


use App\Models\AdministratorBrand;
use App\Models\AdministratorOrganization;
use App\Models\AdministratorPlatform;
use App\Models\Designer;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait CanLogin
{
    protected function canLogin($request = '', $type='platform')
    {
        $checkResult = $this->checkWhichLogin($request, $type);
        return $checkResult;
    }

    protected function canLoginByWechat($user)
    {
        return $this->checkStatus($user,$type='platform');
    }

    protected function checkWhichLogin($request, $type)
    {
        $result = array();
        $result['status'] = 1;
        $result['msg'] = '成功';

        if ($request->get('account')||$request->get('login_username')){
            $fieldName = 'login_username';
            $isMember = false;
            $pwdCheckResult = $this->checkPassword($request->get($fieldName),$request->get('password'),$isMember,$type);
            if ($pwdCheckResult['status']==1){
                 $statusCheckResult = $this->checkStatus($pwdCheckResult['data']['user'],$type);

                if($statusCheckResult['status']==0){
                     $result['status'] = 0;
                     $result['msg'] = $statusCheckResult['msg'];
                     return $result;
                 }else{
                     return $result;
                 }
            }else{
                $result['status'] = 0;
                $result['msg'] = $pwdCheckResult['msg'];
                return $result;
            }
        }else{
            $result['status'] = 0;
            $result['msg'] = '登录提交参数错误';
            return $result;
        }

    }

    protected function checkPassword($account, $password, $isDesigner=false,$type)
    {
        $result = array();
        $result['status'] = 1;
        $result['msg'] = '成功';
        $result['data'] = [];

        $fieldName = 'login_username';
        $table = 'administrator_platforms';
        if ($isDesigner){
            $table = 'designers';
        }
        if($type == 'brand'){
            $table = 'administrator_brands';
        }
        $entry = DB::table($table)->where($fieldName, $account);

        if ($isDesigner){
            $entry = $entry->orWhere('login_telephone', $account);
        }

        $user = $entry->first();

        if (!$user){
            $result['status'] = 0;
            $result['msg'] = '账号不存在';
            return $result;
        }
        if(!Hash::check($password,$user->login_password)){
            $result['status'] = 0;
            $result['msg'] = '账号或密码不正确';
            return $result;
        }

        $result['data']['user'] = $user;
        return $result;
        
    }

    protected function checkStatus($user,$type)
    {
        $result = array();
        $result['status'] = 1;
        $result['msg'] = '成功';

        switch($type){
            case 'platform':
                if($user->status!=AdministratorPlatform::STATUS_ON){
                    $result['status'] = 0;
                    $result['msg'] = '账号被禁用';
                    return $result;
                }
                return $result;
                break;
            case 'brand':
                if($user->status!=AdministratorBrand::STATUS_ON){
                    $result['status'] = 0;
                    $result['msg'] = '账号被禁用';
                    return $result;
                }
                $organization = OrganizationBrand::where('create_administrator_id',$user->id)->first();
                /*if (!$organization){
                    $result['status'] = 0;
                    $result['msg'] = '组织不存在';
                    return $result;
                }
                if ($organization->status==OrganizationBrand::STATUS_OFF){
                    $result['status'] = 0;
                    $result['msg'] = '组织被禁用';
                    return $result;
                }*/
                return $result;
                break;
            case 'designer':
                if (isset($user->status)&&$user->status == Designer::STATUS_OFF){
                    $result['status'] = 0;
                    $result['msg'] = '用户不存在';
                    return $result;
                }
                return $result;
                break;
            default:
                //type不存在
                $result['status'] = 0;
                $result['msg'] = '系统错误';
                return $result;
                break;
        }
    }



}