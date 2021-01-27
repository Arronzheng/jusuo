<?php

namespace App\Http\Services\common;

use App\Http\Controllers\v1\VersionController;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\AdministratorOrganization;
use App\Models\AdministratorPlatform;
use App\Models\Designer;
use App\Models\Member;
use App\Models\Organization;
use App\Traits\CanLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WechatService
{
    use CanLogin;
    const DESIGNER = -1;
    const PLATFORM = 0;
    const BRAND = 1;
    const SELLER = 2;
    const DECORATION = 3;

    //二维码刷新时间
    const TIME_OUT_VALUE = 120000;

    public static function randomToken($table='designers'){
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

    public function getType($key=null)
    {
        $arr=[
           -1 =>'设计师',
            0 =>'平台管理',
            1 =>'品牌商管理',
            2 =>'销售商管理',
            3 =>'装饰公司管理'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:$arr[1];
        }
        return $arr;
    }

    
    public function user(Request $request)
    {
        $user = session('wechat.oauth_user.default');
        if (!$user){
            return '';
        }
        $type = $request->get('type',self::DESIGNER);
        $user->type = $type;
        $user->loginType = $this->getType($type);
        return $user;
    }

    public function userInDb(Request $request, $t)
    {
        $type = $request->get('type',self::DESIGNER);
        switch ($type){
            case self::DESIGNER:
                $userInDb = Designer::where('remember_token', $t)->first();
                break;
            case self::PLATFORM:
                $userInDb = AdministratorPlatform::where('remember_token', $t)->first();
                break;
            case self::BRAND:
                $userInDb = AdministratorBrand::where('remember_token', $t)->first();
                break;
            case self::SELLER:
                $userInDb = AdministratorDealer::where('remember_token', $t)->first();
                break;
            case self::DECORATION:
                //$userInDb = AdministratorOrganization::where('remember_token', $t)->first();
                break;
        }

        return $userInDb;
        
    }


    public function login(Request $request)
    {
        $type = $request->get('type','');
        $user = $this->user($request);
        $admin_user = AdministratorOrganization::where('login_wx_openid',$user->original['openid'])->where('organization_type', $type)->first();
        if ($admin_user){
            $admin_user->remember_token = $request->get('t');
            $admin_user->save();
            return response([
                'status'=>1,
            ]);
        }else {
            return response([
                'status' => 0,
            ]);
        }

    }

    public function checkLoginStatus(Request $request)
    {
        $t =$request->input('t');
        $userInDb = $this->userInDb($request, $t);
        if (!$this->canLoginByWechat($userInDb)){
            return response([
                'status'=>-1,
            ]);
        }
        if($userInDb){
            $type = $request->get('type', -1);
            switch ($type){
                case self::DESIGNER:
                    Auth::login($userInDb);
                    break;
                case self::PLATFORM:
                case self::BRAND:
                case self::SELLER:
                case self::DECORATION:
                    Auth::guard('admin')->login($userInDb);
                    break;
            }
            if ($type==self::DESIGNER){
                //设计师要检查是否含手机，多返回一个user;
                return response([
                    'status' => 1,
                    'user'=> $userInDb
                ]);
            }
            return response([
                'status' => 1,
            ]);

        }else {
            return response([
                'status' => 0,
            ]);
        }
    }

    public function bind(Request $request)
    {
        $t = $request->get('t');
        $user = $this->user($request);
        $userInDb = $this->userInDb($request,$t);
        $type = $request->get('type');

        //是否已有其他用户绑定了此微信
        $type = $request->get('type',self::DESIGNER);
        $openid_exist = 0;
        switch ($type){
            case self::DESIGNER:
                $openid_exist = Designer::where('login_wx_openid', $user->original['openid'])->count();
                break;
            case self::PLATFORM:
                $openid_exist = AdministratorPlatform::where('login_wx_openid', $user->original['openid'])->count();
                break;
            case self::BRAND:
                $openid_exist = AdministratorBrand::where('login_wx_openid', $user->original['openid'])->count();
                break;
            case self::SELLER:
                $openid_exist = AdministratorDealer::where('login_wx_openid', $user->original['openid'])->count();
                break;
            case self::DECORATION:
                //$userInDb = AdministratorOrganization::where('remember_token', $t)->first();
                break;
        }
        /*$openid_exist = Designer::where('login_wx_openid',$user->original['openid'])
            ->count();*/
        if($openid_exist>0){
            return response([
                'status'=>-1,
                'msg'=>'该微信已绑定其他账号！'
            ]);
        }

        if ($userInDb->login_wx_openid == $user->original['openid']){
            return response([
                'status'=>-1,
                'msg'=>'新旧微信号相同'
            ]);
        }
        $userInDb->login_wx_openid = $user->original['openid'];
        $userInDb = $userInDb->save();


        if ($userInDb){
            return response([
                'status'=>1
            ]);
        }else{
            return response([
                'status'=>0,
                'msg' =>'绑定失败，请稍后再试'
            ]);
        }

    }

    public function checkBindStatus(Request $request)
    {
        $t = $request->get('t');
        $userInDb = $this->userInDb($request, $t);
        $openid = $request->get('openid');
        //改绑
        if ($openid){
            if ($userInDb->login_wx_openid){
                if ($openid==$userInDb->login_wx_openid){
                    return response([
                        'status'=>0,
                    ]);
                }else{
                    return response([
                        'status'=>1,
                        'msg'=>'微信号改绑成功。'
                    ]);
                }
            }
        }
        //绑定
        if ($userInDb->login_wx_openid){
            return response([
                'status'=>1
            ]);
        }else{
            return response([
                'status'=>0
            ]);
        }
    }

}