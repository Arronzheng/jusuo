<?php

namespace App\Http\Controllers\v1\admin\platform\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\RoleBrand;
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

        return $this->get_view('v1.admin_platform.designer.account_index');
    }

    //查看详情
    public function account_detail($id)
    {
        $user = $this->authService->getAuthUser();

        $data = Designer::query()
            ->OrganizationType(Designer::ORGANIZATION_TYPE_NONE)
            ->with('detail')
            ->find($id);

        $data->location = $this->areaRepository->getLocationByDistrictId($data->detail->area_belong_id);
        $data->area_serving_text = $data->detail->area_serving_id ? $this->areaRepository->getServiceAreaName($data->detail->area_serving_id) : '';
        $data->area_serving_text = '';
        $province =  Area::where('id',$data->detail->area_serving_province)->first();
        $city =  Area::where('id',$data->detail->area_serving_city)->first();
        $district =  Area::where('id',$data->detail->area_serving_district)->first();
        if($province){$data->area_serving_text.= $province->name;}
        if($city){$data->area_serving_text.= $city->name;}
        if($district){$data->area_serving_text.= $district->name;}

        //擅长风格
        $styles = $data->styles()->get()->pluck('name')->toArray();
        $spaces = $data->spaces()->get()->pluck('name')->toArray();
        $data->style_text = implode('/',$styles);
        $data->space_text = implode('/',$spaces);


        return $this->get_view('v1.admin_platform.designer.account_detail',compact(
            'data'
        ));

    }

}
