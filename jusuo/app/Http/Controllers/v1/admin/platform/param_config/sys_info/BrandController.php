<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\sys_info;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamConfigService;
use App\ParamConfigOrganization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BrandController extends VersionController
{
    private $param_prefix = 'platform.sys_info.brand';

    //设计师
    public function index()
    {
        $configs = ParamConfigService::get_config_sets_by_keyword($this->param_prefix,OrganizationService::ORGANIZATION_TYPE_PLATFORM);

        $configs = $configs->mapWithKeys(function ($item) {
            return [$item->name => $item->value];
        });

        $param = array();
        $param['configs'] = $configs;
        return $this->get_view('v1.admin_platform.param_config.sys_info.brand',
            compact('param'));
    }

}
