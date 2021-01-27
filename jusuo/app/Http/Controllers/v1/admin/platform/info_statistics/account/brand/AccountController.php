<?php

namespace App\Http\Controllers\v1\admin\platform\info_statistics\account\brand;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorBrand;
use App\Models\Area;
use App\Models\CertificationBrand;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class AccountController extends VersionController
{
    private $globalService;
    private $subAdminService;
    private $getNameServices;
    private $authService;

    public function __construct(GlobalService $globalService,
                                SubAdminService $subAdminService,
                                GetNameServices $getNameServices,
                                AuthService $authService
    ){

        $this->globalService = $globalService;
        $this->subAdminService = $subAdminService;
        $this->getNameServices = $getNameServices;
        $this->authService = $authService;

    }

    //账号列表
    public function account_index(\Illuminate\Http\Request $request)
    {
        $vdata = [];
        $product_categories = ProductCategory::all();
        $vdata['product_categories'] = $product_categories;
        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $vdata['provinces'] = $provinces;

        return $this->get_view('v1.admin_platform.info_statistics.account.brand.account_index',compact('vdata'));
    }

    //查看详情
    public function account_detail($id)
    {
        $brand = OrganizationBrand::query()
            ->find($id);
        if(!$brand){
            die('数据不存在');
        }
        $brand_id = $brand->id;

        if($brand->status != OrganizationBrand::STATUS_ON){
            die('品牌未审核');
        }

        $brandDetail = $brand->detail;
        if(!$brandDetail){
            die('暂无品牌详情信息');
        }

        //实名信息
        $certification = null;
        if($brand->status == OrganizationBrand::STATUS_ON){
            //审核通过
            $certification = CertificationBrand::where('brand_id',$brand_id)->orderBy('id','desc')->first();
        }

        //应用信息
        if ($brandDetail->area_belong_id){
            $district = Area::where('id',$brandDetail->area_belong_id)->first();
            if ($district){
                $city =  Area::where('id',$district->pid)->first();
                if ($city){
                    $province =  Area::where('id',$city->pid)->first();
                    if ($province){
                        $brandDetail->area_belong_text = $province->name.'/'.$city->name.'/'.$district->name;
                    }
                }
            }

        }

        $self_award = [];
        if($brandDetail->self_award){
            $self_award = unserialize($brandDetail->self_award);
        }
        $brandDetail->self_award_array = $self_award;
        $self_staff = [];
        if($brandDetail->self_staff){
            $self_staff = unserialize($brandDetail->self_staff);
        }
        $brandDetail->self_staff_array = $self_staff;

        return $this->get_view('v1.admin_platform.info_statistics.account.brand.account_detail',compact(
            'brandDetail','brand','certification'
        ));

    }

}
