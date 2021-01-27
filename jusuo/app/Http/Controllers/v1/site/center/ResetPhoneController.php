<?php

namespace App\Http\Controllers\v1\site\center;

use App\Models\Designer;
use App\Models\MobileCaptcha;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\LoginService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\VersionController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\Hash;

class ResetPhoneController extends VersionController
{
    //
    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.reset_phone.index',compact('__BRAND_SCOPE'));
    }

    //获取手机+短信验证码登录的短信验证码
    public function getResetPhoneSmsCode(Request $request,EasySms $easySms){

        $validator = Validator::make($request->all(), [
            'login_mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('请输入手机号！');
        }

        $mobile = $request->login_mobile;

        //登录用户
        $user = Designer::where('login_mobile', $mobile)->first();
        if(!$user){
            return $this->apiSv->respFailReturn('用户不存在，请先注册');
        }

        //验证码字符串
        //$code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
        $code = '1234';

        try{

            DB::beginTransaction();

            $mobileCaptcha = MobileCaptcha::where('type',MobileCaptcha::TYPE_RESET_PHONE)
                ->where('mobile',$mobile)->first();

            if($mobileCaptcha){
                if( time() - (strtotime($mobileCaptcha->updated_at))<60) {
                    return $this->apiSv->respFailReturn('60秒内只能发送一次');
                }else{
                    $mobileCaptcha->captcha = $code;
                    $mobileCaptcha->updated_at = Carbon::now();
                    $mobileCaptcha->save();
                }
            }else{
                $check = new MobileCaptcha();
                $check->type = MobileCaptcha::TYPE_RESET_PHONE;
                $check->mobile = $mobile;
                $check->captcha = $code;
                $check->save();
            }

            //发送手机验证码
            /*$result = $easySms->send($mobile, [
                'content' => "验证码为{$code}"
            ]);*/

            DB::commit();

            return $this->apiSv->respDataReturn(['code'=>'^_^'],'验证码发送成功');

        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
            DB::rollback();
            //$message = $exception->getException('')->getMessage();
            return $this->apiSv->respFailReturn('短信平台繁忙，请稍后再试');
        } catch (InvalidArgumentException $exception) {
            DB::rollback();
            //$message = $exception->getMessage();
            return $this->apiSv->respFailReturn('短信发送异常');
        }

    }

    public function resetPhone(Request $request){
        $validator = Validator::make($request->all(),[
            'oldphone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'newphone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'verification_code' => 'required|string',
        ],[
            'oldphone.required' => '请填写原手机号',
            'oldphone.regex' => '请填写正确的原手机号',
            'newphone.required' => '请填写新手机号',
            'newphone.regex' => '请填写正确的新手机号',
            'verification_code.required' => '请填写验证码',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $newphone = $request->newphone;
        $oldphone = $request->oldphone;
        $verification_code = $request->verification_code;

        $user = Auth::user();

        if($user->login_mobile != $oldphone){
            $this->apiSv->respFail('绑定手机不正确，请重新填写');
        }

        $check_mobile_capture = LoginService::check_mobile_capture($oldphone,$verification_code,MobileCaptcha::TYPE_RESET_PHONE);

        if($check_mobile_capture['status']==0){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        $user->login_mobile = $newphone;
        $user->save();

        $this->apiSv->respData([]);
    }

    public function sendSmsCode(Request $request){

        $validator = Validator::make($request->all(), [
            'new_phone' => 'required',
            'pwd' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('请输入手机号！');
        }

        $mobile = $request->new_phone;
        $password = $request->pwd;

        $exist = Designer::where('login_mobile',$mobile)->first();
        if($exist){
            $this->apiSv->respFail('手机已存在，请换一个手机');
        }

        $user = Auth::user();
        $oldpassword = $user->login_password;

        $check = Hash::check($password,$oldpassword);
        if(!$check){
            $this->apiSv->respFail('密码不正确，请重新填写');
        }

        //验证码字符串
        //$code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
        $code = '1234';

        try{

            DB::beginTransaction();

            $mobileCaptcha = MobileCaptcha::where('type',MobileCaptcha::TYPE_RESET_PHONE)
                ->where('mobile',$mobile)->first();

            if($mobileCaptcha){
                if( time() - (strtotime($mobileCaptcha->updated_at))<60) {
                    return $this->apiSv->respFailReturn('60秒内只能发送一次');
                }else{
                    $mobileCaptcha->captcha = $code;
                    $mobileCaptcha->updated_at = Carbon::now();
                    $mobileCaptcha->save();
                }
            }else{
                $check = new MobileCaptcha();
                $check->type = MobileCaptcha::TYPE_RESET_PHONE;
                $check->mobile = $mobile;
                $check->captcha = $code;
                $check->save();
            }

            //发送手机验证码
            /*$result = $easySms->send($mobile, [
                'content' => "验证码为{$code}"
            ]);*/

            DB::commit();

            return $this->apiSv->respDataReturn(['code'=>'^_^'],'验证码发送成功');

        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
            DB::rollback();
            //$message = $exception->getException('')->getMessage();
            return $this->apiSv->respFailReturn('短信平台繁忙，请稍后再试');
        } catch (InvalidArgumentException $exception) {
            DB::rollback();
            //$message = $exception->getMessage();
            return $this->apiSv->respFailReturn('短信发送异常');
        }
    }

    public function resetUserPhone(Request $request){
        $validator = Validator::make($request->all(),[
            'new_phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'verification_code' => 'required|string',
            'password' => 'required|min:6|string',
        ],[
            'new_phone.required' => '请填写新手机号',
            'new_phone.regex' => '请填写正确的新手机号',
            'verification_code.required' => '请填写验证码',
            'password.required' => '请填写登录密码',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $newphone = $request->new_phone;
        $verification_code = $request->verification_code;
        $input_password = $request->password;

        $user = Auth::user();
        $oldpassword = $user->login_password;

        $check = Hash::check($input_password,$oldpassword);
        if(!$check){
            $this->apiSv->respFail('原密码不正确，请重新填写');
        }

        $exist = Designer::where('login_mobile',$newphone)->first();
        if($exist){
            $this->apiSv->respFail('手机已存在，请换一个手机');
        }

        $check_mobile_capture = LoginService::check_mobile_capture($newphone,$verification_code,MobileCaptcha::TYPE_RESET_PHONE);

        if($check_mobile_capture['status'] == 0){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        $user->login_mobile = $newphone;
        $user->save();

        $this->apiSv->respData([],'操作成功，您的手机已改为：'.$newphone);
    }
}
