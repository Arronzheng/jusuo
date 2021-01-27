<?php

namespace App\Http\Controllers\v1\admin\seller;

use App\Http\Controllers\v1\VersionController;
use App\Models\AdministratorDealer;
use App\Models\OrganizationDealer;
use App\Services\common\LoginService;
use App\Traits\AuthenticatesLogout;
use App\Traits\CanLogin;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends VersionController
{
    use AuthenticatesUsers,CanLogin, AuthenticatesLogout{
        AuthenticatesLogout::logout insteadof AuthenticatesUsers;
    }
    
    protected $guard_name = 'seller';
    //protected $redirectTo = '/admin/seller';

    public function __construct(){
        $this->middleware('guest:'.$this->guard_name)->except('logout');
    }

    //登录页前端
    public function showLoginForm(Request $request)
    {
        /*$service = new WechatService();
        $token = $service::randomToken();
        if ($request->session()->has('url.intended')) {
            if (!strpos($request->session()->get('url.intended'), '/admin/brand')) {
                $request->session()->forget('url.intended');
            }
        }*/

        $token = '';

        return $this->get_view('v1.admin_seller.login', compact('token'));
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $attemptResult = $this->attemptLogin($request);
        if ($attemptResult['status']==1) {
            return $this->sendLoginResponse($request);
        }else{
            return redirect(route($this->guard_name.'.login'))->withErrors(['code'=>$attemptResult['msg']]);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function guard()
    {
        return Auth::guard($this->guard_name);
    }

    protected function attemptLogin(Request $request)
    {
        $result = array();
        $result['status'] = 1;
        $result['msg'] = '成功';

        $login_username = $request->get('login_username');
        $login_username_column = 'login_username';

        $exist_login_account = AdministratorDealer::where('login_account',$login_username)->count();
        if($exist_login_account>0){
            $login_username_column = 'login_account';
        }

        $exist_login_mobile = AdministratorDealer::where('login_mobile',$login_username)->count();
        if($exist_login_mobile>0){
            $login_username_column = 'login_mobile';
        }

        $password = $request->get('password');

        $admin = AdministratorDealer::where($login_username_column,$login_username)
            ->first();

        if (!$admin){
            $result['status'] = 0;
            $result['msg'] = '账号不存在';
            return $result;
        }

        //判断管理员账号是否启用
        if($admin->status == AdministratorDealer::STATUS_OFF){
            $result['status'] = 0;
            $result['msg'] = '账号被禁用';
            return $result;
        }

        //判断组织是否超过有效期
        $seller = $admin->dealer;
        if(!$seller){
            $result['status'] = 0;
            $result['msg'] = '组织信息不存在';
            return $result;
        }
        $now_time = time();
        $seller_expired_at = strtotime($seller->expired_at);
        if($now_time > $seller_expired_at){
            $result['status'] = 0;
            $result['msg'] = '账号不在有效期内';
            return $result;
        }

        if(!Hash::check($password,$admin->login_password)){
            $result['status'] = 0;
            $result['msg'] = '账号或密码不正确';
            return $result;
        }

        //清除已登录的其他用户
        session()->flush();

        Auth::guard('seller')->login($admin);

        return $result;
    }

    protected function sendLoginResponse(Request $request)
    {

        //登录成功,生成remember_token
        $table = 'administrator_dealers';
        $service = new LoginService();
        $token = $service::randomToken($table);
        $this->guard()->user()->setRememberToken($token);
        $this->guard()->user()->save();

        //标识当前登录的用户类型
        session()->put('guard_name',$this->guard_name);

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    //登录成功跳转逻辑
    private function redirectTo()
    {
        $loginAdmin = Auth::guard('seller')->user();
        $seller = $loginAdmin->dealer;
        if($seller->status==OrganizationDealer::STATUS_WAIT_VERIFY){
            return '/admin/seller/basic_info';
        }else{
            return '/admin/seller';
        }
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    public function username()
    {
        return 'login_username';
    }

    public function redirectTologin()
    {
        //后台登出的跳转
        return redirect(route($this->guard_name.'.login'));
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        //$request->session()->flush();

        return $this->redirectTologin();
    }
}
