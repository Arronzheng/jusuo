<?php

namespace App\Http\Controllers\v1\admin\login;

use App\Http\Controllers\v1\VersionController;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\LogBrandSiteConfig;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
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
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends VersionController
{
    use AuthenticatesUsers,CanLogin, AuthenticatesLogout{
        AuthenticatesLogout::logout insteadof AuthenticatesUsers;
    }
    
    protected $guard_name = 'brand';
    protected $redirectTo = '/admin/brand';

    public function __construct(){
        //$this->middleware('guest:'.$this->guard_name)->except('logout');
    }

    //登录页前端
    public function showLoginForm(Request $request)
    {
        $params = [];
        $params['login_title'] = '设计营销管理系统管理端';
        $params['login_background'] = '';

        $brandWebIdCode = $request->input('b','');
        if($brandWebIdCode){
            $brand = OrganizationBrand::where('web_id_code',$brandWebIdCode)->first();
            if($brand){
                $brandConfig = LogBrandSiteConfig::where('target_brand_id',$brand->id)->first();
                if($brandConfig){
                    $brandConfigContent = \Opis\Closure\unserialize($brandConfig->content);
                    if(isset($brandConfigContent['tool_name']) && $brandConfigContent['tool_name']){
                        $params['login_title'] = $brandConfigContent['tool_name'];
                    }
                    if(isset($brandConfigContent['admin_login_bg']) && $brandConfigContent['tool_name']){
                        $params['login_background'] = $brandConfigContent['admin_login_bg'];
                    }
                }
            }
        }
 
        $token = '';

        return $this->get_view('v1.admin.login.login', compact('token','params'));
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
            return redirect('/admin/login')->withErrors(['code'=>$attemptResult['msg']]);
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
        $password = $request->get('password');

        $login_type = 'brand';

        //先判断品牌表
        $admin = AdministratorBrand::query()
            ->where(function($query)use($login_username){
                $query->where('login_username',$login_username);
                $query->orWhere('login_account',$login_username);
                $query->orWhere('login_mobile',$login_username);
            })
            ->first();
        if(!$admin){
            $admin = AdministratorDealer::query()
                ->where(function($query)use($login_username){
                    $query->where('login_username',$login_username);
                    $query->orWhere('login_account',$login_username);
                    $query->orWhere('login_mobile',$login_username);
                })
                ->first();
            $login_type = 'seller';
            $this->guard_name = 'seller';
            $this->redirectTo = 'admin/seller';
        }

        if (!$admin){
            $result['status'] = 0;
            $result['msg'] = '账号不存在';
            return $result;
        }

        //判断管理员账号是否启用
        if($login_type=='brand'){
            if($admin->status == AdministratorBrand::STATUS_OFF){
                $result['status'] = 0;
                $result['msg'] = '账号被禁用';
                return $result;
            }
        }else{
            if($admin->status == AdministratorDealer::STATUS_OFF){
                $result['status'] = 0;
                $result['msg'] = '账号被禁用';
                return $result;
            }
        }


        //判断组织是否超过有效期
        if($login_type=='brand'){
            $organization = $admin->brand;
        }else{
            $organization = $admin->dealer;
        }

        $now_time = time();
        $organization_expired_at = strtotime($organization->expired_at);
        if($now_time > $organization_expired_at){
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

        if($login_type=='brand'){
            Auth::guard('brand')->login($admin);
        }else{
            Auth::guard('seller')->login($admin);
        }

        return $result;
    }

    protected function sendLoginResponse(Request $request)
    {

        //登录成功,生成remember_token
        $table = 'administrator_brands';
        if($this->guard_name=='brand'){
            $table = 'administrator_dealers';
        }
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
        if($this->guard_name=='brand'){
            $loginAdmin = Auth::guard('brand')->user();
            $brand = $loginAdmin->brand;
            if($brand->status==OrganizationBrand::STATUS_WAIT_VERIFY){
                return '/admin/brand/basic_info';
            }else{
                return '/admin/brand';
            }
        }else{
            $loginAdmin = Auth::guard('seller')->user();
            $seller = $loginAdmin->dealer;
            OrganizationDealer::where('id',$seller->id)->update(['last_active_time'=>Carbon::now()]);
            if($seller->status==OrganizationDealer::STATUS_WAIT_VERIFY){
                return '/admin/seller/basic_info';
            }else{
                return '/admin/seller';
            }
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

    public function redirectTologin($url)
    {
        //后台登出的跳转
        return redirect($url);
    }

    public function logout(Request $request)
    {

        $redirectTologin = '/admin/login';

        if(Auth::guard('brand')->user()){
            $admin = Auth::guard('brand')->user();
            $brand = $admin->brand;
            $redirectTologin = '/admin/login?b='.$brand->web_id_code;
            Auth::guard('brand')->logout();
        }

        if(Auth::guard('seller')->user()){
            Auth::guard('seller')->logout();
        }

        $this->guard()->logout();


        return $this->redirectTologin($redirectTologin);
    }
}
