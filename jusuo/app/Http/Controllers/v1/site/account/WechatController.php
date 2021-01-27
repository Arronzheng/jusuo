<?php

namespace App\Http\Controllers\v1\site\account;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GetImageService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\common\WechatService;
use App\Models\Designer;
use App\Models\Member;
use App\Models\MemberDetail;
use App\Models\QrCodeWeixin;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WechatController extends VersionController
{
    private $getNameServices;
    private $service;
    private $apiSv;

    public function __construct(GetNameServices $getNameServices,ApiService $apiService)
    {
        $this->app = app('wechat.official_account');
        $this->service = new WechatService();
        $this->getNameServices = $getNameServices;
        $this->apiSv = $apiService;
    }

    //展示微信注册的wap页面
    public function wechatRegister(Request $request,$t,$type=1){
        $user =  $this->service->user($request,$t,$type);
        return $this->get_view('v1.site.account.wechatRegister',compact('t','user'));
    }

    //处理确认微信注册逻辑
    public function doWechatRegister(Request $request){

        $token = $request->t;
        $user = session('wechat.oauth_user.default');
        try{

            $qrcodeWeixin = QrCodeWeixin::firstOrNew(['login_wx_openid'=>$user->original['openid']]);
            if (!$qrcodeWeixin->login_wx_openid){
                $qrcodeWeixin->login_wx_openid = $user->original['openid'];
            }
            $qrcodeWeixin->remember_token = $token;
            $qrcodeWeixin->save();

            SystemLogService::simple('',[
                json_encode($qrcodeWeixin)
            ]);

            $this->apiSv->respData([]);

        }catch(\Exception $exception){
            $this->apiSv->respFail($exception->getMessage());

        }


    }

    //展示微信登录的wap页面
    public function wechatLogin(Request $request,$t){
        $user =  $this->service->user($request,$t);
        return $this->get_view('v1.site.account.wechatLogin',compact('t','user'));
    }

    //处理确认微信登录逻辑
    public function doWechatLogin(Request $request){

        $token = $request->t;
        $user = session('wechat.oauth_user.default');
        $openid = $user->original['openid'];
        try{

            $user = Designer::where('login_wx_openid',$openid)->first();
            if (!$user){
                $this->apiSv->respFail('用户不存在！');
            }
            $user->remember_token = $token;
            $user->save();

            $this->apiSv->respData([]);

        }catch(\Exception $exception){
            $this->apiSv->respFail($exception->getMessage());

        }
    }



}
