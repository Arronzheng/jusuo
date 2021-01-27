<?php

namespace App\Http\Controllers\v1\admin\brand\param_config\product\ceramic;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\HouseTypeRepository;
use App\Http\Repositories\common\MemberTypeRepository;
use App\Http\Repositories\common\SpaceRepository;
use App\Http\Repositories\common\SpaceTypeRepository;
use App\Http\Repositories\common\StyleRepository;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppInfoController extends VersionController
{

    private $authService;

    public function __construct(

        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //全局设置类
    private $param_prefix = 'platform.product.ceramic.app_info';

    public function index()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $configs_data = ParamConfigService::get_config_sets_by_keyword($this->param_prefix,OrganizationService::ORGANIZATION_TYPE_BRAND,$brand->id);

        //参数设置值
        $configs = $configs_data->mapWithKeys(function ($item) {
            return [
                $item->name => $item->value,
            ];
        });

        $platform_config_datas = ParamConfigService::get_config_sets_by_keyword($this->param_prefix,OrganizationService::ORGANIZATION_TYPE_PLATFORM);
        $platform_configs = $platform_config_datas->mapWithKeys(function ($item) {
            return [
                $item->name => $item->value,
            ];
        });

        $param = array();
        $param['configs'] = $configs;
        $param['platform_configs'] = $platform_configs;

        return $this->get_view('v1.admin_brand.param_config.product.ceramic.app_info',
            compact('param'));
    }

}