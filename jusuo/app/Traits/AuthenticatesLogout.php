<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait AuthenticatesLogout
{
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->forget($this->guard()->getName());

        $request->session()->regenerate();

        return $this->redirectTologin();
    }

    public function redirectTologin()
    {
        //退出登录默认回到首页
        return redirect('/');
    }
}