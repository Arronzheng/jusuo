<?php

namespace App\Http\Controllers\v1\admin\seller\integral\index;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\v1\admin\AuthService;
use App\LogisticsCompany;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\Designer;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDealer;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use App\Services\v1\admin\OrganizationDealerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class IntegralIndexController extends VersionController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_seller.integral.index.index');
    }

}
