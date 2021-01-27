<?php

namespace App\Http\Controllers\v1\admin\platform\security_center\api;

use App\Http\Repositories\common\OrganizationRepository;
use App\Http\Services\v1\admin\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SecurityCenterController extends ApiController
{
    private $organizationRepository;
    private $authService;

    public function __construct(
                                AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //提交密码修改
    public function modify_pwd(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //其他校验
        $admin = $this->authService->getAuthUser();
        if(!$admin){$this->respFail('权限不足！');}

        //旧密码正确性
        $old_password = $admin->login_password;
        if(!Hash::check($input_data['old_password'],$old_password)){
            $this->respFail('旧密码错误！');
        }

        //保存新密码
        $admin->login_password = bcrypt($input_data['new_password']);
        $admin->save();

        $this->respData([]);

    }

    //提交重置密码
    public function reset_pwd(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'secret_answer' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //其他校验
        $admin = $this->authService->getAuthUser();
        if(!$admin){$this->respFail('权限不足！');}

        //密保问题正确性
        $secret_answer = $admin->secret_answer;
        if($input_data['secret_answer']!==$secret_answer){
            $this->respFail(' 密保答案错误！');
        }

        //保存新密码
        $admin->login_password = bcrypt($input_data['new_password']);
        $admin->save();

        $this->respData([]);

    }
    
    //验证密码

    public function verify_pwd(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //其他校验
        $admin = $this->authService->getAuthUser();
        if(!$admin){$this->respFail('权限不足！');}

        $password = $admin->login_password;
        if(!Hash::check($input_data['password'],$password)){
            $this->respFail('密码输入错误！');
        }

        $this->respData([]);
        
    }


}
