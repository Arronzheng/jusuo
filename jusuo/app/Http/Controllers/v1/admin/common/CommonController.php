<?php

namespace App\Http\Controllers\v1\admin\common;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\OrganizationRepository;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use App\Models\Area;
use App\Models\PrivilegeOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonController extends VersionController
{

    public function __construct(){
    }

    public function message()
    {
        return $this->get_view('v1.admin.components.layout.blank_body');
    }


}
