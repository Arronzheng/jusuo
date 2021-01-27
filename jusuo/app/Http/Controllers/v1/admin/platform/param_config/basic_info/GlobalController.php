<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\basic_info;

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

class GlobalController extends VersionController
{
    private $styleRepository;
    private $spaceRepository;
    private $houseTypeRepository;
    private $spaceTypeRepository;
    private $memberTypeRepository;
    private $authService;

    public function __construct(

        AuthService $authService,
        StyleRepository $styleRepository,
        SpaceRepository $spaceRepository,
        HouseTypeRepository $houseTypeRepository,
        SpaceTypeRepository $spaceTypeRepository,
        MemberTypeRepository $memberTypeRepository
    )
    {
        $this->authService = $authService;
        $this->styleRepository = $styleRepository;
        $this->spaceRepository = $spaceRepository;
        $this->houseTypeRepository = $houseTypeRepository;
        $this->spaceTypeRepository = $spaceTypeRepository;
        $this->memberTypeRepository = $memberTypeRepository;

    }

    //全局设置类
    private $param_prefix = 'platform.basic_info.global.';

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

        $style_options = $this->styleRepository->get_normal_data()->toArray();
        $space_options = $this->spaceRepository->get_normal_data()->toArray();
        $house_types = $this->houseTypeRepository->get_normal_data()->toArray();
        $space_types = $this->spaceTypeRepository->get_normal_data()->toArray();
        $member_types = $this->memberTypeRepository->get_normal_data()->toArray();


        $param['style_options'] = $style_options;
        $param['space_options'] = $space_options;
        $param['member_types'] = $member_types;
        $param['house_types'] = $house_types;
        $param['space_types'] = $space_types;


        return $this->get_view('v1.admin_platform.param_config.basic_info.global',
            compact('param'));
    }

}