<?php

namespace App\Http\Controllers\v1\admin\seller\info_verify\designer_certification_candelete;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\LogDesignerCertification;
use App\Models\LogDesignerDetail;
use App\Models\Style;
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
        return $this->get_view('v1.admin_seller.info_verify.designer_certification.seller.index');
    }

    //查看详情
    public function detail($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;

        //审核信息
        $info = LogDesignerCertification::query()
            ->select(['log_designer_certifications.*'])
            ->join('designers','designers.id','=','log_designer_certifications.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_id',$seller->id)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->find($id);


        if(!$info){
            exit('暂无相关信息');
        }

        //资料提交的信息
        $verify_content = unserialize($info->content);
        $verify_content['is_approved'] = $info->is_approved;
        $verify_content['id'] = $info->id;

        //如果有地区id
        $province = Area::where('id',$verify_content['province_id'])->first();
        $city =  Area::where('id',$verify_content['city_id'])->first();
        $district =  Area::where('id',$verify_content['district_id'])->first();

        $verify_content['district'] = $district;
        $verify_content['province'] = $province;
        $verify_content['city'] = $city;

        return $this->get_view('v1.admin_seller.info_verify.designer_certification.seller.detail',compact('verify_content','member'));

    }

}
