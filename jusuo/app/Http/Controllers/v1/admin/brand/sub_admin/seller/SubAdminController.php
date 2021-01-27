<?php

namespace App\Http\Controllers\v1\admin\brand\sub_admin\seller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorDealer;
use App\Models\Area;
use App\Models\DetailBrand;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Services\v1\admin\OrganizationDealerService;
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
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();


        return $this->get_view('v1.admin_brand.sub_admin.seller.account_index',compact('provinces'));
    }


    //去前端预览销售商详情
    public function preview_seller_detail($web_id_code)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $preview_brand_id = $brand->id;
        session()->put('preview_brand_id',$preview_brand_id);

        return redirect('/dealer/sm/'.$web_id_code);
    }

    //账号创建
    public function account_create(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        //获取该城市的一级销售商
        $seller_lv1s = OrganizationDealerService::getBrandLegalSeller1Entry($brand->id)
            ->orderBy('id','desc')
            ->select(['id','name'])
            ->get();

        $cities = [];

        return $this->get_view('v1.admin_brand.sub_admin.seller.account_edit',compact(
            'provinces','seller_lv1s','cities'
        ));

    }

    //账号编辑
    public function account_edit($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $sellerIds = OrganizationDealer::where('p_brand_id',$brand->id)->get()->pluck('id')->toArray();

        $admin = AdministratorDealer::query()
            ->whereIn('dealer_id',$sellerIds)
            ->with('dealer')
            ->find($id);
        if(!$admin){
            return back();
        }

        $dealer = $admin->dealer;
        if(!$dealer){
            return back();
        }


        $data = $dealer->detail;
        if(!$data){
            return back();
        }

        $cities = Area::where('level',2)->get();

        $area_serving_cities = [];
        $area_visible_cities = [];
        $area_serving_city_ids = $data->area_serving_city?explode('|', trim($data->area_serving_city,'|')):[];
        $area_visible_city_ids = $data->area_visible_city?explode('|', trim($data->area_visible_city,'|')):[];

        if(is_array($area_serving_city_ids)){
            for($i=0;$i<count($area_serving_city_ids);$i++){
                $city_id = $area_serving_city_ids[$i];
                $city = $cities->where('id',$city_id)->first();
                $parent_id = $city['pid'];
                $city_data = $cities->where('pid',$parent_id)->all();
                $area_serving_cities[$i]['city_id'] = $city_id;
                $area_serving_cities[$i]['province_id'] = $parent_id;
                $area_serving_cities[$i]['city_data'] = $city_data;
            }
        }

        $data->area_serving_cities = $area_serving_cities;

        if(is_array($area_visible_city_ids)){
            for($i=0;$i<count($area_visible_city_ids);$i++){
                $city_id = $area_visible_city_ids[$i];
                $city = $cities->where('id',$city_id)->first();
                $parent_id = $city['pid'];
                $city_data = $cities->where('pid',$parent_id)->all();
                $area_visible_cities[$i]['city_id'] = $city_id;
                $area_visible_cities[$i]['province_id'] = $parent_id;
                $area_visible_cities[$i]['city_data'] = $city_data;
            }
        }

        $data->area_visible_cities = $area_visible_cities;

        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        //获取该城市的一级销售商
        $seller_lv1s = OrganizationDealerService::getBrandLegalSeller1Entry($brand->id)
            ->orderBy('id','desc')
            ->select(['id','name'])
            ->get();

        $admin_id = $id;

        return $this->get_view('v1.admin_brand.sub_admin.seller.account_edit',compact(
            'provinces','seller_lv1s','data','dealer','admin_id'
        ));
    }

    //配额管理
    public function account_config($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $sellerIds = OrganizationDealer::where('p_brand_id',$brand->id)->get()->pluck('id')->toArray();

        $data = AdministratorDealer::query()
            ->whereIn('dealer_id',$sellerIds)
            ->with('dealer')
            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_brand.sub_admin.seller.account_config',compact(
            'data'
        ));

    }

    //修改密码
    public function modify_pwd($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌不存在');
        }

        $admin_id = $id;
        $admin = AdministratorDealer::find($admin_id);
        if(!$admin){
            die('账号不存在');
        }

        $data = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->find($admin->dealer_id);
        if(!$data){
            die('销售商不存在');
        }


        return $this->get_view('v1.admin_brand.sub_admin.seller.account_modify_pwd',compact(
            'data','admin'
        ));

    }

    //发放积分
    public function integral_distribute($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌不存在');
        }

        $admin_id = $id;
        $admin = AdministratorDealer::find($admin_id);
        if(!$admin){
            die('账号不存在');
        }

        $data = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->find($admin->dealer_id);
        if(!$data){
            die('销售商不存在');
        }


        return $this->get_view('v1.admin_brand.sub_admin.seller.account_integral_distribute',compact(
            'data','admin'
        ));

    }

    //查看详情
    public function account_detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $handleAdmin = AdministratorDealer::find($id);
        if(!$handleAdmin){
            die('管理员不存在');
        }

        $data = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->find($handleAdmin->dealer_id);
        if(!$data){
            die('数据不存在');
        }
        
        if($data->status != OrganizationDealer::STATUS_ON){
            die('销售商未审核');
        }

        $sellerDetail = $data->detail;
        if(!$sellerDetail){
            die('暂无销售商详情信息');
        }

        //服务/可见城市范围
        $area_serving_text = '';
        $area_visible_text = '';

        //服务城市集合
        if ($sellerDetail->area_serving_city){
            $area_serving_city_ids = explode('|',trim($sellerDetail->area_serving_city,'|'));
            $area_serving_citys = Area::where('level',2)->whereIn('id',$area_serving_city_ids)->get()->pluck('shortname')->toArray();
            if(count($area_serving_citys)>0){
                $area_serving_text = implode(',',$area_serving_citys);
            }
        }

        //可见城市集合
        if ($sellerDetail->area_visible_city){
            $area_visible_city_ids = explode('|',trim($sellerDetail->area_visible_city,'|'));
            $area_visible_citys = Area::where('level',2)->whereIn('id',$area_visible_city_ids)->get()->pluck('shortname')->toArray();
            if(count($area_visible_citys)>0){
                $area_visible_text = implode(',',$area_visible_citys);
            }
        }
        $sellerDetail->area_serving_text = $area_serving_text;
        $sellerDetail->area_visible_text = $area_visible_text;
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


        return $this->get_view('v1.admin_brand.sub_admin.seller.account_detail',compact(
            'data','sellerDetail'
        ));

    }

}
