<?php

namespace App\Http\Controllers\v1\admin\platform\integral\brand_account;

use App\Http\Controllers\v1\VersionController;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\IntegralBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class BrandController extends VersionController
{

    public function integral_list(Request $request)
    {
        return $this->get_view('v1.admin_platform.integral.brand_account.integral_list');
    }

    public function recharge_list(Request $request)
    {
        return $this->get_view('v1.admin_platform.integral.brand_account.recharge_list');
    }

}
