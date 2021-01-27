<?php

namespace App\Http\Controllers\v1\admin\platform\info_statistics\activity\brand;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorBrand;
use App\Models\Area;
use App\Models\CertificationBrand;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class AccountController extends VersionController
{
    private $globalService;
    private $subAdminService;
    private $getNameServices;
    private $authService;

    public function __construct(GlobalService $globalService,
                                SubAdminService $subAdminService,
                                GetNameServices $getNameServices,
                                AuthService $authService
    ){

        $this->globalService = $globalService;
        $this->subAdminService = $subAdminService;
        $this->getNameServices = $getNameServices;
        $this->authService = $authService;

    }

    //账号列表
    public function account_index(\Illuminate\Http\Request $request)
    {
        $vdata = [];
        $product_categories = ProductCategory::all();
        $vdata['product_categories'] = $product_categories;
        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $vdata['provinces'] = $provinces;

        return $this->get_view('v1.admin_platform.info_statistics.activity.brand.account_index',compact('vdata'));
    }

}
