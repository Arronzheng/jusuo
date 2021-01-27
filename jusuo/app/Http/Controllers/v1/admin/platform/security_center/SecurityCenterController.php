<?php

namespace App\Http\Controllers\v1\admin\platform\security_center;

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
        return $this->get_view('v1.admin_platform.security_center.bind_wechat');
    }


    //修改密码
    public function modify_pwd()
    {
        return $this->get_view('v1.admin_platform.security_center.modify_pwd');
    }

}
