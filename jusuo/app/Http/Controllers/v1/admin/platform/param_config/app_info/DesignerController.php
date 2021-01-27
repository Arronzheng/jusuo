<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\app_info;

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

class DesignerController extends VersionController
{

    private $authService;

    public function __construct(

        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //全局设置类
    private $param_prefix = 'platform.app_info.designer.';

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

        return $this->get_view('v1.admin_platform.param_config.app_info.designer',
            compact('param'));
    }

}