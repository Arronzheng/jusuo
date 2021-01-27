<?php

namespace App\Http\Controllers\v1\admin\brand\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\OrganizationDealer;
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

        return $this->get_view('v1.admin_brand.designer.seller.account_index');
    }


    //查看详情
    public function account_detail($id)
    {
        $user = $this->authService->getAuthUser();

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        $data = Designer::whereIn('organization_id',$sellerIds)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->with('detail')
            ->with('organization')
            ->find($id);

        if(!$data){
            return '暂无相关数据';
        }

        $data->location = $this->areaRepository->getLocationByDistrictId($data->detail->area_belong_id);
        $data->area_serving_text = $data->detail->area_serving_id ? $this->areaRepository->getServiceAreaName($data->detail->area_serving_id) : '';
        $data->area_belong_text = $data->detail->area_belong_id ? $this->areaRepository->getServiceAreaName($data->detail->area_belong_id) : '';

        //擅长风格
        $styles = $data->styles()->get()->pluck('name')->toArray();
        $spaces = $data->spaces()->get()->pluck('name')->toArray();
        $data->style_text = implode('/',$styles);
        $data->space_text = implode('/',$spaces);

        //所属组织
        $data->organization_text = $data->organization->name;



        return $this->get_view('v1.admin_brand.designer.seller.account_detail',compact(
            'data'
        ));

    }

    //修改设计师等级
    public function modify_level($id)
    {
        $user = $this->authService->getAuthUser();

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        $data = Designer::whereIn('organization_id',$sellerIds)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($id);

        if(!$data){
            return back();
        }

        $detail = $data->detail;

        return $this->get_view('v1.admin_brand.designer.seller.account_modify_level',compact(
            'data','detail'
        ));

    }

    //积分调整
    public function modify_integral($id)
    {
        $user = $this->authService->getAuthUser();

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        $data = Designer::whereIn('organization_id',$sellerIds)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($id);

        if(!$data){
            return back();
        }

        $data->url_type = 'seller_designer';

        return $this->get_view('v1.admin_brand.designer.common.account_modify_integral',compact(
            'data'
        ));

    }

}
