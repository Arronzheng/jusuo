<?php

namespace App\Http\Controllers\v1\admin\brand\info_verify\designer_certification;

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

class BrandDesignerController extends VersionController
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
        return $this->get_view('v1.admin_brand.info_verify.designer_certification.brand.index');
    }

    //查看详情
    public function detail($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //审核信息
        $info = LogDesignerCertification::query()
            ->select(['log_designer_certifications.*'])
            ->join('designers','designers.id','=','log_designer_certifications.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_id',$brand->id)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->find($id);


        if(!$info){
            exit('暂无相关信息');
        }

        //资料提交的信息
        $verify_content = unserialize($info->content);
        $verify_content['is_approved'] = $info->is_approved;
        $verify_content['id'] = $info->id;


        return $this->get_view('v1.admin_brand.info_verify.designer_certification.brand.detail',compact('verify_content'));

    }

}
