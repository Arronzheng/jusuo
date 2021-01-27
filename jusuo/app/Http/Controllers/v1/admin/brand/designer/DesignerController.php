<?php

namespace App\Http\Controllers\v1\admin\brand\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\RoleBrand;
use App\Services\v1\admin\OrganizationBrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerController extends VersionController
{

    private $authService;
    private $getNameServices;
    private $areaRepository;


    public function __construct(
        AuthService $authService,
        GetNameServices $getNameServices,
        AreaRepository $areaRepository
    )
    {
        $this->authService = $authService;
        $this->getNameService = $getNameServices;
        $this->areaRepository = $areaRepository;
    }

    //账号列表
    public function account_index(\Illuminate\Http\Request $request)
    {
        return $this->get_view('v1.admin_brand.designer.common.account_index');
    }

    //积分变动表
    public function integral_log($id)
    {

        return $this->get_view('v1.admin_brand.designer.common.integral_log',compact('id'));
    }

    //去前端预览设计师详情
    public function preview_designer_detail($web_id_code)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $preview_brand_id = $brand->id;
        session()->put('preview_brand_id',$preview_brand_id);

        return redirect('/designer/sm/'.$web_id_code);
    }

    //账号创建
    public function account_create(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $designer_count = OrganizationBrandService::countBrandDesignerOnAndVerifying($brand->id);
        if($designer_count >= $brand->quota_designer_brand){
            die('品牌设计师数量已达限额');
        }

        return $this->get_view('v1.admin_brand.designer.common.account_edit');

    }

    //修改密码
    public function modify_pwd($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $data = Designer::query()
            ->OrganizationId($brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_brand.designer.common.account_modify_pwd',compact(
            'data'
        ));

    }

    //修改设计师等级
    public function modify_level($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $data = Designer::query()
            ->OrganizationId($brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->find($id);
        if(!$data){
            return back();
        }

        $detail = $data->detail;

        return $this->get_view('v1.admin_brand.designer.common.account_modify_level',compact(
            'data','detail'
        ));

    }

    //查看详情
    public function account_detail($id)
    {
        $user = $this->authService->getAuthUser();

        $data = Designer::OrganizationId($user->brand->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_BRAND)
            ->with('detail')
            ->with('organization')
            ->find($id);

        $data->location = $this->areaRepository->getLocationByDistrictId($data->detail->area_belong_district);
        $data->area_serving_text = $data->detail->area_serving_district ? $this->areaRepository->getServiceAreaName($data->detail->area_serving_district) : '';
        $data->area_belong_text = $data->detail->area_belong_district ? $this->areaRepository->getServiceAreaName($data->detail->area_belong_district) : '';

        //擅长风格
        $styles = $data->styles()->get()->pluck('name')->toArray();
        $spaces = $data->spaces()->get()->pluck('name')->toArray();
        $data->style_text = implode('/',$styles);
        $data->space_text = implode('/',$spaces);

        //所属组织
        $data->organization_text = $data->organization->name;



        return $this->get_view('v1.admin_brand.designer.common.account_detail',compact(
            'data'
        ));

    }

}
