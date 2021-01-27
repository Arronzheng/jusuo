<?php

namespace App\Http\Controllers\v1\site\center;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerDetail;
use App\Models\MobileCaptcha;
use App\Models\QrCodeWeixin;
use App\Models\Space;
use App\Models\Style;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LoginService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BasicInfoController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index(Request $request)
    {

        $loginUser = Auth::user();
        $designer = Designer::find($loginUser->id);
        $designerDetail = $designer->detail;
        $certification = null;
        $log = LogDesignerDetail::where('target_designer_id',$loginUser->id)
            ->orderBy('id','desc')
            ->first();
        if(!$log){
            //暂未有审核信息
            $log_status = 0;
        }else{
            if($log->is_approved == LogDesignerDetail::IS_APROVE_APPROVAL && $designer->status == Designer::STATUS_ON){
                //审核已通过
                $log_status = -1;
                //服务城市
                if ($designerDetail->area_serving_district){
                    $district = Area::where('id',$designerDetail->area_serving_district)->first();
                    $city =  Area::where('id',$designerDetail->area_serving_city)->first();
                    $province =  Area::where('id',$designerDetail->area_serving_province)->first();
                    $designerDetail->area_serving_text = $province->name.'/'.$city->name.'/'.$district->name;
                }
                //擅长风格
                $style_text = $designer->styles()->get()->pluck('name')->toArray();
                $designerDetail->style_text = implode('/',$style_text);
                //擅长空间
                $space_text = $designer->spaces()->get()->pluck('name')->toArray();
                $designerDetail->space_text = implode('/',$space_text);
            }else if($log->is_approved==LogDesignerDetail::IS_APROVE_VERIFYING){
                //待审核
                $log_status = 1;
            }else if($log->is_approved==LogDesignerDetail::IS_APROVE_REJECT){
                //审核拒绝
                $log_status = 2;
            }
        }


        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        //风格数据
        $styles = Style::select(['id','name'])->get();

        //风格数据
        $spaces = Space::select(['id','name'])->get();

        //参数配置
        $pcu = new ParamConfigUseService($designer->id);
        $config['self_expert_character_limit'] = $pcu->find('platform.basic_info.global.self_expert.limit');
        $config['style_limit'] = $pcu->find('platform.basic_info.global.style.limit');
        $config['space_limit'] = $pcu->find('platform.basic_info.global.space.limit');

        $brandId = DesignerService::getDesignerBrandScope($designer->id);
        $__BRAND_SCOPE = $this->compressBrandScope($brandId);

        return $this->get_view('v1.site.center.info_verify.basic_info.index',compact(
            'log_status','provinces','designer','log','designerDetail',
            'spaces','styles','config','__BRAND_SCOPE'
        ));
    }

    //上传身份证
    public function upload_id_card(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_DESIGNER_IDCARD)){
            $this->apiSv->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->apiSv->respFail($error_msg);
        }

        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_IDCARD,$file,[
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ]);

        if($access_url = $service->form_upload()){
            $this->apiSv->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->apiSv->respFail($error_msg);
        }*/

    }

    //提交资料审核
    public function submit_basic_info(Request $request)
    {
        $input_data = $request->all();
        $validator = Validator::make($input_data, [
            'self_designer_type' => 'required',
            'area_serving_province' => 'required',
            'area_serving_city' => 'required',
            'area_serving_district' => 'required',
            'self_organization' => 'required',
            'style' => 'required',
            'space' => 'required',
            'self_expert' => 'required',
        ]);

        if ($validator->fails()) {
            $this->apiSv->respFail('请完整填写信息后再提交！');
        }

        //其他校验
        $loginUser = Auth::user();

        try{

            DB::beginTransaction();

            //更新信息
            $change_content['self_designer_type'] = $input_data['self_designer_type'];
            $change_content['area_serving_province'] = $input_data['area_serving_province'];
            $change_content['area_serving_city'] = $input_data['area_serving_city'];
            $change_content['area_serving_district'] = $input_data['area_serving_district'];
            $change_content['self_organization'] = $input_data['self_organization'];
            $change_content['style'] = $input_data['style'];
            $change_content['space'] = $input_data['space'];
            $change_content['self_expert'] = $input_data['self_expert'];

            $log = LogDesignerDetail::where(['target_designer_id'=>$loginUser->id,'is_approved'=>LogDesignerDetail::IS_APROVE_VERIFYING])->first();

            if($log){
                DB::rollback();
                $this->apiSv->respFail('您提交的申请正在审核中,请耐心等候');
            }

            //表单校验
            $pcu = new ParamConfigUseService($loginUser->id);
            $checkArray = [
                'platform.basic_info.global.self_organization.limit'=>$input_data['self_organization'],
                'platform.basic_info.global.self_expert.limit'=>$input_data['self_expert'],
            ];
            $rejectReason = ParamCheckService::pcu_check_length_param_config($checkArray,$pcu);
            if($rejectReason<>''){
                DB::rollback();
                $this->apiSv->respFail($rejectReason);
            }
            $checkArray = [
                'platform.basic_info.global.style.limit'=>count($input_data['style']),
                'platform.basic_info.global.space.limit'=>count($input_data['space']),
            ];
            $rejectReason = ParamCheckService::pcu_check_array_count_param_config($checkArray,$pcu);
            if($rejectReason<>''){
                DB::rollback();
                $this->apiSv->respFail($rejectReason);
            }

            $data = [
                'target_designer_id' => $loginUser->id,
                'content' => serialize($change_content),
                'is_approved' => LogDesignerDetail::IS_APROVE_VERIFYING,
            ];
            $insert = DB::table('log_designer_details')->insert($data);
            if(!$insert){
                DB::rollback();
                $this->apiSv->respFail('提交失败,请重试');
            }

            DB::commit();

            $this->apiSv->respData([]);

        }catch(\Exception $e){

            DB::rollback();

            $this->apiSv->respFail('提交失败,请重试'.$e->getTrace());


        }
    }


}
