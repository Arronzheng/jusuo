<?php

namespace App\Http\Controllers\v1\admin\wechat;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\StrService;
use App\Http\Services\common\WechatService;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WechatController extends VersionController
{
    private $getNameServices;
    private $service;
    public function __construct(GetNameServices $getNameServices)
    {
        $this->app = app('wechat.official_account');
        $this->service = new WechatService();
        $this->getNameServices = $getNameServices;
    }

    public function bind(Request $request,$t,$type=1)
    {
        $user =  $this->service->user($request,$t,$type);
        $userInDb = $this->service->userInDb($request,$t);
        return $this->get_view('v1.member.bind',compact('t','user','userInDb'));
    }



    public function do_wechat_bind(Request $request)
    {
        return $this->service->bind($request);
        
    }

    public function checkWechatBind(Request $request)
    {
        return $this->service->checkBindStatus($request);
    }


}
