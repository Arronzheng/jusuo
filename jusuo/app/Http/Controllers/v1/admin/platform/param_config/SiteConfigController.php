<?php

namespace App\Http\Controllers\v1\admin\platform\param_config;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\common\OrganizationService;
use App\Models\AdministratorBrand;
use App\Models\CertificationBrand;
use App\Models\Area;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\LogBrandSiteConfig;
use App\Models\LogDetailBrand;
use App\Models\OrganizationBrand;
use App\Models\SiteConfigPlatform;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\v1\admin\ParamConfigUseService;

use Illuminate\Support\Facades\DB;

class SiteConfigController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //平台主页设置
    public function site_config()
    {

        //主页设置信息
        $info = SiteConfigPlatform::query()
            ->where('var_name','site_config')
            ->first();

        if(!$info){
            $info = new SiteConfigPlatform();
            $info->config_name = '主页设置';
            $info->var_name = 'site_config';
            $info->type = SiteConfigPlatform::TYPE_SERIALIZE;
            $info->value = '';
            $info->save();
        }

        $content = unserialize($info->content);
        $content['id'] = $info->id;


        return $this->get_view('v1.admin_platform.param_config.site_config',
            compact(
                'content'
            ));
    }


}
