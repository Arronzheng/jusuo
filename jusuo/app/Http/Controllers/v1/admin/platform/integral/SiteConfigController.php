<?php

namespace App\Http\Controllers\v1\admin\platform\integral;

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
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\v1\admin\ParamConfigUseService;

use Illuminate\Support\Facades\DB;

class SiteConfigController extends VersionController
{
    private $authService;
    private $apiSv;



    public function __construct(AuthService $authService,ApiService $apiService)
    {
        $this->authService = $authService;
        $this->apiSv = $apiService;

    }

    //积分商城设置
    public function site_config()
    {

        //主页设置信息
        $info = SiteConfigPlatform::query()
            ->where('var_name','integral_config')
            ->first();

        if(!$info){
            $info = new SiteConfigPlatform();
            $info->config_name = '积分商城设置';
            $info->var_name = 'integral_config';
            $info->type = SiteConfigPlatform::TYPE_SERIALIZE;
            $info->value = '';
            $info->save();
        }


        $content = unserialize($info->content);
        $content['id'] = $info->id;

        return $this->get_view('v1.admin_platform.integral.site_config',
            compact(
                'content'
            ));
    }

    //提交主页设置
    public function submit_site_config(Request $request)
    {
        $input_data = $request->all();

        $info = SiteConfigPlatform::query()
            ->where('var_name','integral_config')
            ->first();

        if(!$info){
            $info = new SiteConfigPlatform();
            $info->config_name = '积分商城设置';
            $info->var_name = 'integral_config';
            $info->type = SiteConfigPlatform::TYPE_SERIALIZE;
            $info->value = '';
            $info->save();
        }


        try{

            DB::beginTransaction();

            $info->content = \Opis\Closure\serialize($input_data);
            $info->save();

            DB::commit();

            $this->apiSv->respData([],'修改成功！');


        }catch(\Exception $e){
            DB::rollback();

            $this->apiSv->respFail('系统错误'.$e->getMessage());
        }


    }



}
