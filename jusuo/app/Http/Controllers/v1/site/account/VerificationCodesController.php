<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2019/10/25
 * Time: 15:13
 */

namespace App\Http\Controllers\v1\site\account;
use App\Http\Controllers\v1\VersionController;
use App\Models\Designer;
use App\Models\MobileCaptcha;
use App\Services\v1\site\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;

class VerificationCodesController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    //获取手机号注册的短信验证码
    public function getRegisterSmsCode(Request $request,EasySms $easySms){

        $validator = Validator::make($request->all(), [
            'login_mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('请输入手机号！');
        }

        $mobile = $request->login_mobile;

        //登录用户
        $user = Designer::where('login_mobile', $mobile)->first();
        if($user){
            return $this->apiSv->respFailReturn('用户已存在，请直接登录');
        }

        //验证码字符串
        //$code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
        $code = '1234';

        try{

            DB::beginTransaction();

            $mobileCaptcha = MobileCaptcha::where('type',MobileCaptcha::TYPE_REGISTER)
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
                $check->type = MobileCaptcha::TYPE_REGISTER;
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

    //获取手机+短信验证码登录的短信验证码
    public function getLoginSmsCode(Request $request,EasySms $easySms){

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

            $mobileCaptcha = MobileCaptcha::where('type',MobileCaptcha::TYPE_LOGIN)
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
                $check->type = MobileCaptcha::TYPE_LOGIN;
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

    //获取微信登录绑定手机的短信验证码
    public function getBindSmsCode(Request $request,EasySms $easySms){

        $validator = Validator::make($request->all(), [
            'login_mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('请输入手机号！');
        }

        $mobile = $request->login_mobile;

        //登录用户
        $user = Designer::where('login_mobile', $mobile)->first();
        if($user){
            return $this->apiSv->respFailReturn('用户已存在，请直接登录');
        }

        //验证码字符串
        //$code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);
        $code = '1234';

        try{

            DB::beginTransaction();

            $mobileCaptcha = MobileCaptcha::where('type',MobileCaptcha::TYPE_BIND)
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
                $check->type = MobileCaptcha::TYPE_BIND;
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
}
