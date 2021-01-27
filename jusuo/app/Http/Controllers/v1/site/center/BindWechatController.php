<?php

namespace App\Http\Controllers\v1\site\center;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\WechatService;
use App\Models\Designer;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BindWechatController extends VersionController
{
    //
    //
    private $apiSv;
    private $service;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
        $this->service = new WechatService();
    }

    public function index(Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.bind_wechat.index',compact('__BRAND_SCOPE'));
    }

    public function bind_wechat(Request $request,$t){
        //$user =  $this->service->user($request,$t);

        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.bind_wechat.bind_wechat',compact('t','__BRAND_SCOPE'));
    }

    //处理确认微信绑定
    public function doWecharBind(Request $request){

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

    public function checkWechatBind(Request $request)
    {
        return $this->service->checkBindStatus($request);
    }
}
