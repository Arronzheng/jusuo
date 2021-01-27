<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\api;

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

class SiteConfigController extends ApiController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //上传图片
    public function upload_image(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_PLATFORM_PHOTO)){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }

        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_IDCARD,$file,[
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ]);

        if($access_url = $service->form_upload()){
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }

    //提交主页设置
    public function submit_site_config(Request $request)
    {
        $input_data = $request->all();

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


        try{

            DB::beginTransaction();

            $info->content = \Opis\Closure\serialize($input_data);
            $info->save();

            DB::commit();

            $this->respData([],'修改成功！');


        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.$e->getMessage());
        }


    }

}
