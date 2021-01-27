<?php

namespace App\Http\Controllers\v1\mobile\login;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\FavProduct;
use App\Models\MobileCaptcha;
use App\Services\v1\common\CaptchaService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //绑定手机页
    public function bind_mobile()
    {
        return $this->get_view('v1.mobile.login.bind_mobile');
    }

    //登录跳板
    public function redirect(Request $request)
    {
        return redirect($request->input('r'));
    }


    /*---------------------api方法------------------------*/
    //获取绑定手机的验证码
    public function get_sms(Request $request)
    {

        try{

            $mobile = $request->input('mobile_phone','');

            if(!$mobile){
                $this->apiSv->respFail('手机号错误');
            }

            $captcha_service = new CaptchaService();
            $send_captcha = $captcha_service->send_captcha($mobile,MobileCaptcha::TYPE_M_BIND_MOBILE);

            if($send_captcha['status']){
                return $this->apiSv->respDataReturn(['code'=>'~'],'验证码发送成功');
            }else{
                return $this->apiSv->respFailReturn($send_captcha['msg']);
            }


        }catch(\Exception $e){
            SystemLogService::simple('h5发送手机绑定验证码',array($e->getMessage()));

            $this->apiSv->respFail('验证码发送失败，请稍后再试');

        }

    }

    //提交绑定手机
    public function submit_bind_mobile(Request $request)
    {
        $app = app('wechat.official_account');

        $wechat_user = session('wechat.oauth_user.default');

        $input_data = $request->all();

        $mobile = $input_data['phone'];
        $code = $input_data['code'];

        if(!$mobile || !$code ){
            $this->apiSv->respFail('参数缺失');

        }

        try {

            DB::beginTransaction();

            $captcha_service = new CaptchaService();
            $check_mobile_capture = $captcha_service->check_mobile_capture($mobile, $code ,MobileCaptcha::TYPE_M_BIND_MOBILE);
            if ($check_mobile_capture['status'] == 0) {
                $this->apiSv->respFail($check_mobile_capture['msg']);

            }

            //验证码正确，获取设计师信息
            $designer = Designer::where('login_mobile', $mobile)->first();

            if(!$designer){
                $this->apiSv->respFail('用户不存在！');
            }

            //设计师绑定openid
            $open_id = $wechat_user['id'];
            $designer->login_wx_openid = $open_id;
            $designer->save();

            //登录用户
            Auth::guard('web')->login($designer);

            //写入session的代码不能出现die和exit！只能用return
            //绑定手机成功后的跳转url
            $url = session()->get('m_bind_mobile_redirect');


            DB::commit();

            if(!isset($url) || !$url){
                $url = url('/mobile/error/'.PageService::ErrorNoAuthority);
            }

            session()->forget('m_bind_mobile_redirect');

            return $this->apiSv->respDataReturn(['url'=>$url], '登录成功');

        }catch(\Exception $e){

            DB::rollback();
            $this->apiSv->respFail('参数缺失');


        }

    }

}
