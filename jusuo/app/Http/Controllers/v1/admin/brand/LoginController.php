<?php

namespace App\Http\Controllers\v1\admin\brand;

use App\Http\Controllers\v1\VersionController;
use App\Models\AdministratorBrand;
use App\Models\LogBrandSiteConfig;
use App\Models\OrganizationBrand;
use App\Models\StatisticAccountDealer;
use App\Services\common\LoginService;
use App\Services\v1\admin\OrganizationBrandColumnStatisticService;
use App\Services\v1\admin\OrganizationDealerColumnStatisticService;
use App\Services\v1\admin\StatisticAccountBrandService;
use App\Services\v1\admin\StatisticAccountDealerService;
use App\Services\v1\admin\StatisticAlbumService;
use App\Services\v1\admin\StatisticDesignerService;
use App\Services\v1\admin\StatisticProductCeramicService;
use App\Traits\AuthenticatesLogout;
use App\Traits\CanLogin;
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
    
    protected $guard_name = 'brand';
    protected $redirectTo = '/admin/brand';

    public function __construct(){
        $this->middleware('guest:'.$this->guard_name)->except('logout');
    }

    //登录页前端
    public function showLoginForm(Request $request)
    {
 
        $token = '';

        return $this->get_view('v1.admin_brand.login', compact('token'));
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

        $exist_login_account = AdministratorBrand::where('login_account',$login_username)->count();
        if($exist_login_account>0){
            $login_username_column = 'login_account';
        }

        $exist_login_mobile = AdministratorBrand::where('login_mobile',$login_username)->count();
        if($exist_login_mobile>0){
            $login_username_column = 'login_mobile';
        }

        $password = $request->get('password');

        $admin = AdministratorBrand::where($login_username_column,$login_username)
            ->first();

        if (!$admin){
            $result['status'] = 0;
            $result['msg'] = '账号不存在';
            return $result;
        }

        //判断管理员账号是否启用
        if($admin->status == AdministratorBrand::STATUS_OFF){
            $result['status'] = 0;
            $result['msg'] = '账号被禁用';
            return $result;
        }

        //判断组织是否超过有效期
        $brand = $admin->brand;
        $now_time = time();
        $brand_expired_at = strtotime($brand->expired_at);
        if($now_time > $brand_expired_at){
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

        Auth::guard('brand')->login($admin);

        return $result;
    }

    protected function sendLoginResponse(Request $request)
    {

        //登录成功,生成remember_token
        $table = 'administrator_'.$this->guard_name.'s';
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
        $loginAdmin = Auth::guard('brand')->user();
        $brand = $loginAdmin->brand;
        if($brand->status==OrganizationBrand::STATUS_WAIT_VERIFY){
            return '/admin/brand/basic_info';
        }else{
            return '/admin/brand';
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

    public function redirectTologin($redirectTologin)
    {
        //后台登出的跳转
        return redirect($redirectTologin);
    }

    public function logout(Request $request)
    {

        $admin = Auth::guard('brand')->user();
        $brand = $admin->brand;

        $redirectTologin = '/admin/login?b='.$brand->web_id_code;

        $this->guard()->logout();

        //$request->session()->flush();

        return $this->redirectTologin($redirectTologin);
    }
}
