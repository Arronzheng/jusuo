<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\product\ceramic;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\HouseTypeRepository;
use App\Http\Repositories\common\MemberTypeRepository;
use App\Http\Repositories\common\SpaceRepository;
use App\Http\Repositories\common\SpaceTypeRepository;
use App\Http\Repositories\common\StyleRepository;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigService;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicLifePhase;
use App\Models\CeramicProductStatus;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\Color;
use App\Models\ProductApplyCategory;
use App\Models\ProductCeramicApplyCategory;
use App\Models\ProductCeramicColor;
use App\Models\ProductCeramicSurfaceFeature;
use App\Models\ProductCeramicTechnologyCategory;
use App\Models\ProductLifePhase;
use App\Models\ProductSpec;
use App\Models\ProductStatus;
use App\Models\ProductTechnologyCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BasicInfoController extends VersionController
{


    public function __construct()
    {}

    public function index()
    {

        //参数设置值

        $param = array();

        $spec_options = CeramicSpec::all()->toArray();
        $color_options = CeramicColor::all()->toArray();
        $apply_category_options = CeramicApplyCategory::all()->toArray();
        $technology_category_options = CeramicTechnologyCategory::all()->toArray();
        $ceramic_surface_feature_options = CeramicSurfaceFeature::all()->toArray();
        $life_phase_options = CeramicLifePhase::all()->toArray();
        $status_options = CeramicProductStatus::all()->toArray();

        $param['ceramic_spec_options'] = $spec_options;
        $param['ceramic_color_options'] = $color_options;
        $param['ceramic_apply_category_options'] = $apply_category_options;
        $param['ceramic_technology_category_options'] = $technology_category_options;
        $param['ceramic_surface_feature_options'] = $ceramic_surface_feature_options;
        $param['ceramic_life_phase_options'] = $life_phase_options;
        $param['ceramic_product_status_options'] = $status_options;

        return $this->get_view('v1.admin_platform.param_config.product.ceramic.basic_info',
            compact('param'));
    }

}