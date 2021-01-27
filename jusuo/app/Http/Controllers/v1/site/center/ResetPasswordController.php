<?php

namespace App\Http\Controllers\v1\site\center;

use App\Http\Controllers\v1\VersionController;
use App\Models\Designer;
use App\Models\MobileCaptcha;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\LoginService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Overtrue\EasySms\EasySms;

class ResetPasswordController extends VersionController
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

        return $this->get_view('v1.site.center.reset_password.index',compact('__BRAND_SCOPE'));
    }

    public function reset(Request $request){

        $validator = Validator::make($request->all(),[
            'oldpassword' => 'required|string|min:6',
            'newpassword' => 'required|string|min:6|confirmed',
        ],[
            'oldpassword.required' => '请填写原密码',
            'oldpassword.min' => '旧密码密码长度至少6位',
            'newpassword.min' => '新密码长度至少6位',
            'newpassword.required' => '请填写新密码',
            'newpassword.confirmed' => '新密码与确认密码不匹配',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $user = Auth::user();
        //用户旧密码
        $oldpassword = $user->login_password;

        //新输入密码
        $newpassword = $request->newpassword;


        $check = Hash::check($request->oldpassword,$oldpassword);
        if(!$check){
            $this->apisv->respFail('原密码不正确，请重新填写');
        }

        $password = Hash::make($newpassword);
        $user->login_password = $password;
        $user->save();

        //清除已登录的其他用户
        session()->flush();


        Auth::login($user);

        return $this->apiSv->respDataReturn([]);
    }

    public function UnbindWx(Request $request){

        $user = Auth::user();
        try{

            Designer::where('id',$user->id)
                ->update(['login_wx_openid'=>'']);

            $this->apiSv->respData([]);

        }catch(\Exception $exception){
            $this->apiSv->respFail($exception->getMessage());
        }

    }

    public function phone_reset_index(Request $request){
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.reset_password.phone_reset_pwd',compact('__BRAND_SCOPE'));
    }


    //获取手机+短信验证码登录的短信验证码
    public function getResetSmsCode(Request $request,EasySms $easySms){

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
        $code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
        //$code = '1234';

        try{

            DB::beginTransaction();

            $mobileCaptcha = MobileCaptcha::where('type',MobileCaptcha::TYPE_RESET_PWD)
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
                $check->type = MobileCaptcha::TYPE_RESET_PWD;
                $check->mobile = $mobile;
                $check->captcha = $code;
                $check->save();
            }

            //发送手机验证码
            $result = $easySms->send($mobile, [
                'content' => "您的验证码为：{code}，5分钟内有效，请勿泄漏于他人。",
                'template' => 'SMS_190276533',
                'data'=>[
                    'code'=>$code
                ]
            ]);

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


    public function phone_reset_password(Request $request){
        $validator = Validator::make($request->all(),[
            'newpassword' => 'required|min:6|string|confirmed',
            'phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'verification_code' => 'required|string',
        ],[
            'newpassword.min' => '新密码长度至少6位',
            'newpassword.required' => '请填写新密码',
            'newpassword.confirmed' => '新密码与确认密码不匹配',
            'phone.required' => '请填写手机号',
            'phone.regex' => '请填写正确的手机号',
            'verification_code' => '请填写验证码',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $phone = $request->phone;
        $verification_code = $request->get('verification_code');
        $newpassword = $request->newpassword;

        $user = Auth::user();

        if($user->login_mobile != $phone){
            $this->apisv->respFail('绑定手机不正确，请重新填写');
        }

        $check_mobile_capture = LoginService::check_mobile_capture($phone,$verification_code,MobileCaptcha::TYPE_RESET_PWD);

        if(!$check_mobile_capture){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        $password = Hash::make($newpassword);
        $user->login_password = $password;
        $user->save();

        //清除已登录的其他用户
        session()->flush();

        Auth::login($user);

        $this->apiSv->respData([]);

    }

    public function reset_by_pwd(Request $request){
        $validator = Validator::make($request->all(),[
            'oldpassword' => 'required|string|min:6',
            'newpassword' => 'required|string|min:6',
        ],[
            'oldpassword.required' => '请填写原密码',
            'oldpassword.min' => '旧密码密码长度至少6位',
            'newpassword.min' => '新密码长度至少6位',
            'newpassword.required' => '请填写新密码',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $user = Auth::user();
        //用户旧密码
        $oldpassword = $user->login_password;

        //新输入密码
        $newpassword = $request->newpassword;


        $check = Hash::check($request->oldpassword,$oldpassword);
        if(!$check){
            $this->apisv->respFail('原密码不正确，请重新填写');
        }

        $password = Hash::make($newpassword);
        $user->login_password = $password;
        $user->save();

        //清除已登录的其他用户
        session()->flush();


        Auth::login($user);


        return $this->apiSv->respDataReturn([]);
    }

    public function getUserPhone(Request $request){
        $user = Auth::user();

        $designer = Designer::find($user->id);
        $phone = $designer->login_mobile;

        $data['phone'] = $phone;

        return $this->apiSv->respDataReturn($data);
    }

    public function reset_by_phone(Request $request){
        $validator = Validator::make($request->all(),[
            'newpassword' => 'required|min:6|string',
            'phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'verification_code' => 'required|string',
        ],[
            'newpassword.min' => '新密码长度至少6位',
            'newpassword.required' => '请填写新密码',
            'phone.required' => '请填写手机号',
            'phone.regex' => '请填写正确的手机号',
            'verification_code' => '请填写验证码',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $phone = $request->phone;
        $verification_code = $request->get('verification_code');
        $newpassword = $request->newpassword;

        $user = Auth::user();

        if($user->login_mobile != $phone){
            $this->apisv->respFail('绑定手机不正确，请重新填写');
        }

        $check_mobile_capture = LoginService::check_mobile_capture($phone,$verification_code,MobileCaptcha::TYPE_RESET_PWD);

        if(!$check_mobile_capture){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        $password = Hash::make($newpassword);
        $user->login_password = $password;
        $user->save();

        //清除已登录的其他用户
        session()->flush();

        Auth::login($user);

        return $this->apiSv->respDataReturn([]);
    }
}
