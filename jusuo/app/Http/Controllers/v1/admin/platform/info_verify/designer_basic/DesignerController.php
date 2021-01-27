<?php

namespace App\Http\Controllers\v1\admin\platform\info_verify\designer_basic;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\LogDesignerDetail;
use App\Models\Space;
use App\Models\Style;
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

        return $this->get_view('v1.admin_platform.info_verify.designer_basic.index');
    }

    //查看详情
    public function detail($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        //审核信息
        $info = LogDesignerDetail::query()
            ->select(['log_designer_details.*'])
            ->join('designers','designers.id','=','log_designer_details.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_NONE)
            ->find($id);


        if(!$info){
            exit('暂无相关信息');
        }

        //资料提交的信息
        $verify_content = unserialize($info->content);
        $verify_content['is_approved'] = $info->is_approved;
        $verify_content['id'] = $info->id;

        $member = Designer::find($info->target_designer_id);

        //服务城市
        if (isset($verify_content['area_serving_district'])&&$verify_content['area_serving_district']){
            $member->area_serving_text = $this->areaRepository->getLocationByDistrictId($member->detail->area_serving_district);
            $verify_content['area_serving_text'] = $this->areaRepository->getLocationByDistrictId($verify_content['area_serving_district']);
        }

        //擅长空间
        if(isset($verify_content['space'])&&$verify_content['space']){
            $old_spaces = $member->spaces()->get()->pluck('name')->toArray();
            $member->space_text = implode('/',$old_spaces);
            //资料中存放的是风格id
            $space_ids = $verify_content['space'];
            $new_spaces = Space::whereIn('id',$space_ids)->get()->pluck('name')->toArray();
            $verify_content['space'] = implode('/',$new_spaces);
        }

        //擅长风格
        if(isset($verify_content['style'])&&$verify_content['style']){
            $old_styles = $member->styles()->get()->pluck('name')->toArray();
            $member->style_text = implode('/',$old_styles);
            //资料中存放的是风格id
            $style_ids = $verify_content['style'];
            $new_styles = Style::whereIn('id',$style_ids)->get()->pluck('name')->toArray();
            $verify_content['style'] = implode('/',$new_styles);
        }

        return $this->get_view('v1.admin_platform.info_verify.designer_basic.detail',compact('verify_content','member'));

    }

}
