<?php

namespace App\Http\Controllers\v1\site\center\integral;

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
use App\Models\DesignerDetail;
use App\Models\HouseType;
use App\Models\LogBrandSiteConfig;
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

class IntegralController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $detail = Auth::user()->detail;

        $brand_id = session('designer_scope.brand_id');

        //积分规则链接
        $integral_rule_link = '';
        $brandConfig = LogBrandSiteConfig::where('target_brand_id',$brand_id)->first();
        if($brandConfig){
            $brandConfigContent = \Opis\Closure\unserialize($brandConfig->content);
            if(isset($brandConfigContent['integral_rule_link']) && $brandConfigContent['integral_rule_link']){
                $integral_rule_link = $brandConfigContent['integral_rule_link'];
            }
        }

        return $this->get_view('v1.site.center.integral.index',compact('detail','integral_rule_link'));
    }



}
