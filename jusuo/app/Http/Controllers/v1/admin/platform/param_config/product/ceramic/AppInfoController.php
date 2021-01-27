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
        $configs_data = ParamConfigService::get_config_sets_by_keyword($this->param_prefix,OrganizationService::ORGANIZATION_TYPE_PLATFORM);

        //参数设置值
        $configs = $configs_data->mapWithKeys(function ($item) {
            return [
                $item->name => $item->value,
            ];
        });

        $param = array();
        $param['configs'] = $configs;

        return $this->get_view('v1.admin_platform.param_config.product.ceramic.app_info',
            compact('param'));
    }

}