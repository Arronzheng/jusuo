<?php

namespace App\Http\Controllers\v1\site\account;

use App\Events\WechatScanLogin;
use App\Http\Services\common\WechatService;
use App\Services\v1\site\ApiService;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthorizationsController extends Controller
{
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //
    public function wechatRegister(){

        $app = app('wechat.official_account.default');

        $app->server->push(function($message){
            if($message['Event'] === 'SCAN'){
                $open_id = $message['FromUserName'];

                $user = new User();
                $user->weixin_oprnid = $open_id;
                $user->save();

                $token = $user->createToken($user)->accessToken;

                event(new WechatScanLogin($token));

                return response()->json(['']);
            }

        });
    }

    public function getRandomToken()
    {
        $token = WechatService::randomToken();

        $this->apiSv->respData($token);
    }
}
