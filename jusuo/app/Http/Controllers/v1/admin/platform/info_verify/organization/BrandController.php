<?php

namespace App\Http\Controllers\v1\admin\platform\info_verify\organization;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\Area;
use App\Models\LogBrandCertification;
use App\Models\LogOrganizationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laratrust\Models\LaratrustPermission;

class BrandController extends VersionController
{
    private $globalService;
    private $subAdminService;
    private $authService;

    public function __construct(GlobalService $globalService,
                                SubAdminService $subAdminService,
                                AuthService $authService
    ){
        $this->globalService = $globalService;
        $this->subAdminService = $subAdminService;
        $this->authService = $authService;
    }

    //审核列表
    public function index()
    {
        return $this->get_view('v1.admin_platform.info_verify.organization.brand.index');
    }

    //审核信息查看
    public function detail($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        //审核信息
        $info = LogBrandCertification::query()
            ->leftJoin('organization_brands as org', 'org.id','=', 'log_brand_certifications.target_brand_id')
            ->select(['log_brand_certifications.*'])
            ->find($id);


        //销售商注册提交的信息
        $verify_content = unserialize($info->content);
        $verify_content['is_approved'] = $info->is_approved;
        $verify_content['id'] = $info->id;

        //如果有地区id
        if(isset($verify_content['area_belong_id'])&&$verify_content['area_belong_id']){
            $district = Area::where('id',$verify_content['area_belong_id'])->first();
            $city =  Area::where('id',$district->pid)->first();
            $province =  Area::where('id',$city->pid)->first();

            $verify_content['district'] = $district;
            $verify_content['province'] = $province;
            $verify_content['city'] = $city;
        }

        return $this->get_view('v1.admin_platform.info_verify.organization.brand.detail',compact('verify_content'));

    }


}
