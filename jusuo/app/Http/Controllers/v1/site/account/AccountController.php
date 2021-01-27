<?php

namespace App\Http\Controllers\v1\site\account;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GetNameServices;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\MobileCaptcha;
use App\Models\QrCodeWeixin;
use App\Models\StatisticDesigner;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LoginService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends VersionController
{

    private $apiSv;
    private $getNameServices;

    public function __construct(ApiService $apiService,GetNameServices $getNameServices)
    {
        $this->apiSv = $apiService;
        $this->getNameServices = $getNameServices;
    }

    public function login(Request $request)
    {
        return $this->get_view('v1.site.account.login');
    }

    //手机号注册
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'login_mobile' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'password' => 'required|string|min:6',
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('请检查填写项后再提交！');
        }

        $mobile = $request->get('login_mobile');
        $verification_code = $request->get('verification_code');
        $password = $request->get('password');

        //登录用户
        $user = Designer::where('login_mobile', $mobile)->first();
        if($user){
            return $this->apiSv->respFailReturn('用户已存在，请直接登录');
        }

        $check_mobile_capture = LoginService::check_mobile_capture($mobile,$verification_code);
        if($check_mobile_capture['status']==0){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        //生成登录用户名
        $login_account = $this->getNameServices->getDesignerAccount();
        $designer_id_code = $this->getNameServices->getDesignerIdCode();

        try{

            DB::beginTransaction();

            $user = Designer::create([
                'sys_code' => DesignerService::get_sys_code(),
                'login_mobile' => $mobile,
                'login_username' => $login_account,
                'designer_account' => $login_account,
                'designer_id_code' => $designer_id_code,
                'login_password' => bcrypt($request->get('password')),
                'remember_token' => LoginService::random_token(),
            ]);

            $userDetail = DesignerDetail::create([
                'designer_id' => $user->id,
                'nickname' => $login_account,
            ]);

            $stat = new StatisticDesigner();
            $stat->designer_id = $user->id;
            $stat->save();
            if (!$stat){$this->respFail('系统错误');}

            DB::commit();

            //进入登陆状态
            Auth::login($user);

            $login_redirect = session()->get('login_redirect');
            session()->forget('login_redirect');

            //此处不能用die/exit方法返回，需要使用return，否则session丢失
            return $this->apiSv->respDataReturn([
                'id' => $user->id,
                'login_redirect' => isset($login_redirect)?$login_redirect:''
            ]);

        }catch (\Exception $e){

            DB::rollback();

            return $this->apiSv->respFailReturn('系统错误'.$e->getMessage());
        }


    }

    //用户名+密码登录
    public function login_by_pwd(Request $request){
        $validator = Validator::make($request->all(), [
            'login_mobile' => [
                'required',
                ],
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('请检查填写项后再提交！');
        }

        $login_username = $request->get('login_mobile');
        $password = $request->get('password');

        $login_username_column = 'login_username';
        $exist_login_account = Designer::where('designer_account',$login_username)->count();
        if($exist_login_account>0){
            $login_username_column = 'designer_account';
        }

        $exist_login_mobile = Designer::where('login_mobile',$login_username)->count();
        if($exist_login_mobile>0){
            $login_username_column = 'login_mobile';
        }

        $designer = Designer::where($login_username_column,$login_username)
            ->first();

        if (!$designer){
            $result['status'] = 0;
            $result['msg'] = '用户不存在';
            return $result;
        }
        if(!Hash::check($password,$designer->login_password)){
            $result['status'] = 0;
            $result['msg'] = '用户名或密码不正确';
            return $result;
        }

        $designer->makeVisible('login_password');

        Auth::login($designer);

        $login_redirect = session()->get('login_redirect');
        session()->forget('login_redirect');

        return $this->apiSv->respDataReturn([
            'login_redirect' => isset($login_redirect)?$login_redirect:''
        ]);

    }

    //手机号+短信验证码登录
    public function login_by_sms(Request $request){
        $validator = Validator::make($request->all(), [
            'login_mobile' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('参数缺失！');
        }

        $mobile = $request->get('login_mobile');
        $verification_code = $request->get('verification_code');

        $designer = Designer::where('login_mobile',$mobile)
            ->first();

        if (!$designer){
            $result['status'] = 0;
            $result['msg'] = '用户不存在';
            return $result;
        }

        $check_mobile_capture = LoginService::check_mobile_capture($mobile,$verification_code,MobileCaptcha::TYPE_LOGIN);
        if($check_mobile_capture['status']==0){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        $designer = Designer::where('login_mobile',$mobile)
            ->first();

        if (!$designer){
            $result['status'] = 0;
            $result['msg'] = '用户不存在';
            return $result;
        }

        //清除已登录的其他用户
        session()->flush();

        Auth::login($designer);

        $login_redirect = session()->get('login_redirect');
        session()->forget('login_redirect');

        return $this->apiSv->respDataReturn([
            'login_redirect' => isset($login_redirect)?$login_redirect:''

        ]);

    }

    //微信登录后绑定手机号码，并真实注册用户
    public function bind_by_sms(Request $request){
        $validator = Validator::make($request->all(), [
            'login_mobile' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/'
            ],
            'verification_code' => 'required|string',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiSv->respFailReturn('参数缺失！');
        }

        $mobile = $request->get('login_mobile');
        $verification_code = $request->get('verification_code');
        $token = $request->get('token');

        $exist = Designer::where('login_mobile',$mobile)->first();
        if($exist){
            return $this->apiSv->respFailReturn('该手机号已注册，请直接登录');
        }

        $qrcodeWeixin = QrCodeWeixin::where('remember_token',$token)->first();
        if(!$qrcodeWeixin){
            return $this->apiSv->respFailReturn('微信登录状态异常，请重试！');
        }

        if($qrcodeWeixin->status!=QrCodeWeixin::STATUS_WAIT_VERIFY){
            return $this->apiSv->respFailReturn('用户状态异常，请重试！');
        }

        $check_mobile_capture = LoginService::check_mobile_capture($mobile,$verification_code,MobileCaptcha::TYPE_BIND);
        if($check_mobile_capture['status']==0){
            return $this->apiSv->respFailReturn($check_mobile_capture['msg']);
        }

        //生成登录用户名
        $login_account = $this->getNameServices->getDesignerAccount();
        $designer_id_code = $this->getNameServices->getDesignerIdCode();

        try{

            DB::beginTransaction();

            $user = Designer::create([
                'sys_code' => DesignerService::get_sys_code(),
                'login_mobile' => $mobile,
                'login_username' => $login_account,
                'designer_account' => $login_account,
                'designer_id_code' => $designer_id_code,
                'login_wx_openid' => $qrcodeWeixin->login_wx_openid,
                'login_password' => bcrypt($request->get('password')),
                'remember_token' => LoginService::random_token(),
            ]);

            $userDetail = DesignerDetail::create([
                'designer_id' => $user->id,
                'nickname' => $login_account,
            ]);

            $stat = new StatisticDesigner();
            $stat->designer_id = $user->id;
            $stat->save();
            if (!$stat){$this->respFail('系统错误');}

            //更新微信登录信息
            $qrcodeWeixin->status = QrCodeWeixin::STATUS_ON;
            $qrcodeWeixin->login_mobile = $mobile;
            $qrcodeWeixin->verify_code = $verification_code;
            $qrcodeWeixin->save();

            DB::commit();

            //进入登陆状态
            Auth::login($user);

            $login_redirect = session()->get('login_redirect');
            session()->forget('login_redirect');

            //此处不能用die/exit方法返回，需要使用return，否则session丢失
            return $this->apiSv->respDataReturn([
                'id' => $user->id,
                'login_redirect' => isset($login_redirect)?$login_redirect:''

            ]);

        }catch (\Exception $e){

            DB::rollback();

            return $this->apiSv->respFailReturn('系统错误'.$e->getMessage());
        }

    }

    //检测微信注册状态
    public function checkWechatRegister(Request $request)
    {
        $token =$request->input('t');

        $qrcodeWeixin = QrCodeWeixin::where('remember_token',$token)->first();

        if(!$qrcodeWeixin){
            $this->apiSv->respFail('暂无信息');
        }

        if($qrcodeWeixin->status == QrCodeWeixin::STATUS_ON){
            $this->apiSv->respFail('用户已注册过，请重新登录',-1);
        }

        //用户通过微信预注册成功
        $this->apiSv->respData($token);

    }

    //检测微信登录状态
    public function checkWechatLogin(Request $request)
    {
        $token =$request->input('t');

        $user = Designer::where('remember_token',$token)->first();

        if(!$user){
            $this->apiSv->respFail('暂无信息');
        }

        //进入登陆状态
        Auth::login($user);

        //此处不能用die/exit方法返回，需要使用return，否则session丢失
        return $this->apiSv->respDataReturn([
            'id' => $user->id
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        /*session()->forget('designer_session');
        session()->forget('designer_scope');
        session()->forget('preview_brand_id');
        session()->forget('pageBelongBrandId');*/

        $request->session()->invalidate();

        return redirect('/index');
    }

}
