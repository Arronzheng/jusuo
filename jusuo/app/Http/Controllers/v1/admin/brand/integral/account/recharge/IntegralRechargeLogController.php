<?php

namespace App\Http\Controllers\v1\admin\brand\integral\account\recharge;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\InfiniteTreeService;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class IntegralRechargeLogController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_brand.integral.account.recharge.log');
    }

}
