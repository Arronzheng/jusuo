<?php

namespace App\Http\Controllers\v1\admin\brand\security_center;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\OrganizationRepository;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SecurityCenterController extends VersionController
{
    private $authService;
    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    //绑定所有者微信
    public function bind_wechat()
    {
        return $this->get_view('v1.admin_brand.security_center.bind_wechat');
    }

//    //绑定运营者微信
//    public function bind_operator_wechat()
//    {
//        return $this->get_view('v1.admin.brand.security_center.bind_operator_wechat');
//    }

    //修改密码
    public function modify_pwd()
    {
        return $this->get_view('v1.admin_brand.security_center.modify_pwd');
    }

    //修改密保问题
    public function secret_question()
    {
        $brand_id = $this->authService->getAuthUserOrganizationId();

        $info = Organization::find($brand_id);

        return $this->get_view('v1.admin.brand.security_center.secret_question',compact('info'));
    }

    //重置密码
    public function reset_pwd()
    {
        $brand_id = $this->authService->getAuthUserOrganizationId();

        $info = Organization::find($brand_id);

        return $this->get_view('v1.admin.brand.security_center.reset_pwd',compact('info'));
    }

}
