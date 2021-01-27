<?php

namespace App\Http\Controllers\v1\admin\brand\info_verify;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\Area;
use App\Models\LogDealerCertification;
use App\Models\LogOrganizationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laratrust\Models\LaratrustPermission;

class SellerVerifyController extends VersionController
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
        return $this->get_view('v1.admin_brand.info_verify.seller.index');
    }

    //审核信息查看
    public function detail($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //审核信息
        $info = LogDealerCertification::query()
            ->leftJoin('organization_dealers as org', 'org.id','=', 'log_dealer_certifications.target_dealer_id')
            ->where('org.p_brand_id', $brand->id)
            ->select(['*','log_dealer_certifications.id as log_id'])
            ->find($id);

        if(!$info){
            die('暂无信息');
        }



        //销售商注册提交的信息
        $verify_content = unserialize($info->content);
        $verify_content['is_approved'] = $info->is_approved;
        $verify_content['id'] = $info->log_id;

        //如果有地区id
        if(isset($verify_content['area_serving_id'])&&$verify_content['area_serving_id']){
            $district = Area::where('id',$verify_content['area_serving_id'])->first();
            $city =  Area::where('id',$district->pid)->first();
            $province =  Area::where('id',$city->pid)->first();

            $verify_content['district'] = $district->name;
            $verify_content['province'] = $province->name;
            $verify_content['city'] = $city->name;
        }

        return $this->get_view('v1.admin_brand.info_verify.seller.detail',compact('verify_content'));

    }


}
