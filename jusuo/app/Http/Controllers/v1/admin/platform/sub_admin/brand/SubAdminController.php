<?php

namespace App\Http\Controllers\v1\admin\platform\sub_admin\brand;

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
use App\Models\DetailBrand;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class SubAdminController extends VersionController
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

        return $this->get_view('v1.admin_platform.sub_admin.brand.account_index');
    }

    //账号创建
    public function account_create(Request $request)
    {
        //获取经营品类
        $productCats = ProductCategory::all();

        return $this->get_view('v1.admin_platform.sub_admin.brand.account_edit',compact(
            'productCats'
        ));

    }

    //账号编辑
    public function account_edit($id)
    {
        $is_super_admin = $this->authService->getAuthUser()->is_super_admin;

        $data = AdministratorBrand::query()
            ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)
            ->with('brand')
            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_platform.sub_admin.brand.account_config',compact(
            'data','is_super_admin'
        ));

    }

    //账号编辑权限
    public function edit_privilege($id)
    {
        $data = AdministratorBrand::find($id);
        if(!$data){die('品牌管理员不存在');}

        return $this->get_view('v1.admin_platform.sub_admin.brand.edit_privilege',compact('data'));

    }

    //在线课堂账号添加
    public function online_class_account($id)
    {
        $admin = AdministratorBrand::find($id);
        if(!$admin){die('品牌管理员不存在');}

        $brand = $admin->brand;

        return $this->get_view('v1.admin_platform.sub_admin.brand.online_class_account',compact('admin','brand'));

    }


    //修改密码
    public function modify_pwd($id)
    {
        $data = AdministratorBrand::query()
            ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)
            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_platform.sub_admin.brand.account_modify_pwd',compact(
            'data'
        ));

    }

    //查看详情
    public function account_detail($id)
    {
        $data = AdministratorBrand::query()
            ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)
            ->find($id);
        if(!$data){
            die('数据不存在');
        }

        $brand = $data->brand;
        if(!$brand){die('数据不存在');}

        if($brand->status != OrganizationBrand::STATUS_ON){
            die('品牌未审核');
        }

        $brandDetail = $brand->detail;
        if(!$brandDetail){
            die('暂无品牌详情信息');
        }

        $productCategoryText = '';
        $productCategory = ProductCategory::find($brandDetail->product_category);
        if($productCategory){
            $productCategoryText = $productCategory->name;
        }
        $brandDetail->product_category_text = $productCategoryText;

        $area_belong_text = '';
        $area_serving_text = '';
        if ($brandDetail->area_belong_id){
            $area = Area::where('id',$brandDetail->area_belong_id)->first();
            if ($area){
                $area_belong_text = $area->name;
            }
        }
        if ($brandDetail->area_serving_id){
            $area = Area::where('id',$brandDetail->area_serving_id)->first();
            if ($area){
                $area_serving_text = $area->name;
            }
        }
        $brandDetail->area_belong_text = $area_belong_text;
        $brandDetail->area_serving_text = $area_serving_text;
        $brandDetail->privilege_area_serving_text = DetailBrand::privilegeAreaServingGroup($brandDetail->privilege_area_serving);
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

        //die(json_encode($brandDetail->self_staff_array));

        return $this->get_view('v1.admin_platform.sub_admin.brand.account_detail',compact(
            'data','brandDetail'
        ));

    }

}
