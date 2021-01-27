<?php

namespace App\Http\Controllers\v1\admin\brand\param_config\sub_account\app_info;

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

class SellerController extends VersionController
{

    private $authService;

    public function __construct(

        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    private $param_prefix = 'platform.app_info.seller.';

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

        $param = array();
        $param['configs'] = $configs;

        return $this->get_view('v1.admin_brand.param_config.sub_account.app_info.seller',
            compact('param'));
    }

}