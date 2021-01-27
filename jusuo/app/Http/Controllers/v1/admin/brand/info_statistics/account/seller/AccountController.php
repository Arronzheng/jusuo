<?php

namespace App\Http\Controllers\v1\admin\brand\info_statistics\account\seller;

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
use App\Models\AdministratorDealer;
use App\Models\Area;
use App\Models\CertificationBrand;
use App\Models\CertificationDealer;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
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

        return $this->get_view('v1.admin_brand.info_statistics.account.seller.account_index',compact('vdata'));
    }

    //查看详情
    public function account_detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $seller = OrganizationDealer::query()
            ->where('p_brand_id',$brand->id)
            ->find($id);
        if(!$seller){
            die('数据不存在');
        }
        $seller_id = $seller->id;

        $administrator = AdministratorDealer::where('dealer_id',$id)
            ->where('is_super_admin',AdministratorDealer::IS_SUPER_ADMIN_YES)
            ->first();

        if($seller->status != OrganizationDealer::STATUS_ON){
            die('销售商未审核');
        }

        $sellerDetail = $seller->detail;
        if(!$sellerDetail){
            die('暂无销售商详情信息');
        }

        $brand = $seller->brand;

        //实名信息
        $certification = null;
        if($seller->status == OrganizationDealer::STATUS_ON){
            //审核通过
            $certification = CertificationDealer::where('dealer_id',$seller_id)->orderBy('id','desc')->first();
        }
        $seller->product_category_name = '';
        $product_category = ProductCategory::find($brand->product_category);
        if($product_category){
            $seller->product_category_name = $product_category->name;
        }
        $seller->brand_name = $brand->name;

        //应用信息
        $area_serving_text = '';
        if ($sellerDetail->area_serving_id){
            $district = Area::where('id',$sellerDetail->area_serving_id)->first();
            if ($district){
                $city =  Area::where('id',$district->pid)->first();
                if ($city){
                    $province =  Area::where('id',$city->pid)->first();
                    if ($province){
                        $area_serving_text = $province->name.'/'.$city->name.'/'.$district->name;
                    }
                }
            }
        }
        $sellerDetail->area_serving_text = $area_serving_text;
        $sellerDetail->privilege_area_serving_text = DetailBrand::privilegeAreaServingGroup($sellerDetail->privilege_area_serving?:0);
        $self_photo = [];
        if($sellerDetail->self_photo){
            $self_photo = unserialize($sellerDetail->self_photo);
        }
        $sellerDetail->self_photo = $self_photo;

        $self_award = [];
        if($sellerDetail->self_award){
            $self_award = unserialize($sellerDetail->self_award);
        }
        $sellerDetail->self_award_array = $self_award;
        $self_staff = [];
        if($sellerDetail->self_staff){
            $self_staff = unserialize($sellerDetail->self_staff);
        }
        $sellerDetail->self_staff_array = $self_staff;

        return $this->get_view('v1.admin_brand.info_statistics.account.seller.account_detail',compact(
            'sellerDetail','seller','certification','administrator'
        ));

    }

}
