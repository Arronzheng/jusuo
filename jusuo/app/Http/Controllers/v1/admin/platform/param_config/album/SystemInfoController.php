<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\album;

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

class SystemInfoController extends VersionController
{
    public function __construct()
    {}

    //全局设置类
    private $param_prefix = 'platform.album.sys_info.';

    public function index()
    {

        $configs = ParamConfigService::get_config_sets_by_keyword($this->param_prefix,OrganizationService::ORGANIZATION_TYPE_PLATFORM);

        $configs = $configs->mapWithKeys(function ($item) {
            return [$item->name => $item->value];
        });

        $param = array();
        $param['configs'] = $configs;
        return $this->get_view('v1.admin_platform.param_config.album.sys_info',
            compact('param'));
    }

}