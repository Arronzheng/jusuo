<?php

namespace App\Http\Controllers\v1\admin\seller\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\OrganizationDealer;
use App\Models\RoleBrand;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\admin\OrganizationDealerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDesignerController extends VersionController
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

        return $this->get_view('v1.admin_seller.designer.seller.account_index');
    }

    //积分变动表
    public function integral_log($id)
    {

        return $this->get_view('v1.admin_seller.designer.seller.integral_log',compact('id'));
    }

    //兑换列表
    public function exchange_log($id)
    {

        return $this->get_view('v1.admin_seller.designer.seller.exchange_log',compact('id'));
    }

    //充值列表
    public function charge_log($id)
    {
        return $this->get_view('v1.admin_seller.designer.seller.charge_log',compact('id'));
    }

    //账号创建
    public function account_create(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;

        $designer_count = OrganizationDealerService::countSellerDesignerOnAndVerifying($seller->id);
        if($designer_count >= $seller->quota_designer){
            //die($designer_count.'-'.$seller->id.'-'.$seller->quota_designer);
            die('您的设计师账号名额已用完');
        }

        return $this->get_view('v1.admin_seller.designer.seller.account_edit');

    }

    //修改密码
    public function modify_pwd($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        if(!$seller){
            die('销售商信息错误');
        }

        $data = Designer::query()
            ->OrganizationId($seller->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($id);
        if(!$data){
            return back();
        }

        return $this->get_view('v1.admin_seller.designer.seller.account_modify_pwd',compact(
            'data'
        ));

    }

    //查看详情
    public function account_detail($id)
    {
        $user = $this->authService->getAuthUser();

        $data = Designer::OrganizationId($user->dealer->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->with('detail')
            ->with('organization')
            ->find($id);

        $data->location = $this->areaRepository->getLocationByDistrictId($data->detail->area_belong_id);
        $data->area_serving_text = $data->detail->area_serving_id ? $this->areaRepository->getServiceAreaName($data->detail->area_serving_id) : '';
        $data->area_belong_text = $data->detail->area_belong_id ? $this->areaRepository->getLocationByDistrictId($data->detail->area_belong_id) : '';

        //擅长风格
        $styles = $data->styles()->get()->pluck('name')->toArray();
        $spaces = $data->spaces()->get()->pluck('name')->toArray();
        $data->style_text = implode('/',$styles);
        $data->space_text = implode('/',$spaces);

        //所属组织
        $data->organization_text = $data->organization->name;

        return $this->get_view('v1.admin_seller.designer.seller.account_detail',compact(
            'data'
        ));

    }

    //积分调整
    public function modify_integral($id)
    {
        $user = $this->authService->getAuthUser();

        $data = Designer::OrganizationId($user->dealer->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($id);

        if(!$data){
            return back();
        }

        $data->url_type = 'seller_designer';

        return $this->get_view('v1.admin_seller.designer.seller.account_modify_integral',compact(
            'data'
        ));

    }

}
