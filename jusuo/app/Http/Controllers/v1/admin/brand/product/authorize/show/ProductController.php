<?php

namespace App\Http\Controllers\v1\admin\brand\product\authorize\show;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\Style;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class ProductController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $vdata = [];
        //按应用类别、工艺类别、色系、规格、产品状态、产品结构进行组合筛选
        //应用类别
        $apply_categories = CeramicApplyCategory::all();
        $technology_categories = CeramicTechnologyCategory::all();
        $colors = CeramicColor::all();
        $specs = CeramicSpec::all();
        $product_status = ProductCeramic::statusGroup();
        $visible_status = ProductCeramic::visibleGroup();
        $vdata['apply_categories'] = $apply_categories;
        $vdata['technology_categories'] = $technology_categories;
        $vdata['colors'] = $colors;
        $vdata['specs'] = $specs;
        $vdata['product_status'] = $product_status;
        $vdata['visible_status'] = $visible_status;

        return $this->get_view('v1.admin_brand.product.authorize.show.index',compact('vdata'));
    }

}
