<?php

namespace App\Http\Controllers\v1\site\center\fav;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Area;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\HouseType;
use App\Models\LogProductCeramic;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicSpace;
use App\Models\ProductCeramicStructure;
use App\Models\ProductStructure;
use App\Models\SpaceType;
use App\Models\Style;
use App\Models\TestData;
use App\Services\v1\site\DesignerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class FavController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {

        $designer = Auth::user();
        $brandId = DesignerService::getDesignerBrandScope($designer->id);
        $__BRAND_SCOPE = $this->compressBrandScope($brandId);

        return $this->get_view('v1.site.center.fav.index',compact('__BRAND_SCOPE'));
    }

}
