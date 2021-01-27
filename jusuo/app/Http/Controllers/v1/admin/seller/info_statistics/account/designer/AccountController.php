<?php

namespace App\Http\Controllers\v1\admin\seller\info_statistics\account\designer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;
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
use App\Models\CertificationDesigner;
use App\Models\Designer;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\LogDesignerDetail;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\Space;
use App\Models\Style;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class AccountController extends VersionController
{
    private $globalService;
    private $subAdminService;
    private $getNameServices;
    private $authService;
    private $areaRepository;

    public function __construct(GlobalService $globalService,
                                SubAdminService $subAdminService,
                                GetNameServices $getNameServices,
                                AuthService $authService,
                                AreaRepository $areaRepository

    ){

        $this->globalService = $globalService;
        $this->subAdminService = $subAdminService;
        $this->getNameServices = $getNameServices;
        $this->authService = $authService;
        $this->areaRepository = $areaRepository;


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

        //风格数据
        $styles = Style::select(['id','name'])->get();
        $vdata['styles'] = $styles;

        //风格数据
        $spaces = Space::select(['id','name'])->get();
        $vdata['spaces'] = $spaces;

        return $this->get_view('v1.admin_seller.info_statistics.account.designer.account_index',compact('vdata'));
    }

    //查看详情
    public function account_detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;

        $designer = Designer::query()
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->where('organization_id',$seller->id)
            ->find($id);
        if(!$designer){
            die('数据不存在');
        }
        $designer_id = $designer->id;
        
        $designerDetail = $designer->detail;
        if(!$designerDetail){
            die('暂无设计师详情信息');
        }

        //基本信息
        $detail_log = null;
        $detail_log = LogDesignerDetail::query()
            ->where('target_designer_id',$designer_id)
            ->first();
        //擅长风格
        $style_text = $designer->styles()->get()->pluck('name')->toArray();
        $designerDetail->style_text = implode('/',$style_text);
        //擅长空间
        $space_text = $designer->spaces()->get()->pluck('name')->toArray();
        $designerDetail->space_text = implode('/',$space_text);
        //服务城市
        if (isset($designerDetail->area_serving_district)&&$designerDetail->area_serving_district){
            $designerDetail->area_serving_text = $this->areaRepository->getLocationByDistrictId($designerDetail->area_serving_district);
        }

        //实名信息
        $certification = null;
        if($designer->status == Designer::STATUS_ON){
            //审核通过
            $certification = CertificationDesigner::where('designer_id',$designer_id)->first();
        }

        //应用信息
        //服务城市
        if (isset($designerDetail->area_belong_district)&&$designerDetail->area_belong_district){
            $designerDetail->area_belong_text = $this->areaRepository->getLocationByDistrictId($designerDetail->area_belong_district);
        }


        return $this->get_view('v1.admin_seller.info_statistics.account.designer.account_detail',compact(
            'designerDetail','designer','detail_log','certification'
        ));

    }

}
