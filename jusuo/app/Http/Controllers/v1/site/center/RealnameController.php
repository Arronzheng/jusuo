<?php

namespace App\Http\Controllers\v1\site\center;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Models\Area;
use App\Models\CertificationDesigner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerCertification;
use App\Models\MobileCaptcha;
use App\Models\QrCodeWeixin;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LoginService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RealnameController extends VersionController
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
        $log = LogDesignerCertification::where('target_designer_id',$loginUser->id)
            ->orderBy('id','desc')
            ->first();
        if(!$log){
            //暂未有审核信息
            $log_status = 0;
        }else{
            if($log->is_approved == LogDesignerCertification::IS_APROVE_APPROVAL){
                //审核已通过
                $certification = CertificationDesigner::where('designer_id',$loginUser->id)->first();
                if ($designerDetail->area_belong_id){
                    $district = Area::where('id',$designerDetail->area_belong_id)->first();
                    if ($district){
                        $city =  Area::where('id',$district->pid)->first();
                        if ($city){
                            $province =  Area::where('id',$city->pid)->first();
                            if ($province){
                                $designerDetail->area_belong_text = $province->name.'/'.$city->name.'/'.$district->name;
                            }
                        }
                    }

                }
                $log_status = -1;
            }else if($log->is_approved==LogDesignerCertification::IS_APROVE_VERIFYING){
                //待审核
                $log_status = 1;
            }else if($log->is_approved==LogDesignerCertification::IS_APROVE_REJECT){
                //审核拒绝
                $log_status = 2;
            }
        }

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        $brandId = DesignerService::getDesignerBrandScope($designer->id);
        $__BRAND_SCOPE = $this->compressBrandScope($brandId);

        return $this->get_view('v1.site.center.info_verify.realname_info.index',compact(
            'log_status','provinces','designer','log','designerDetail','certification','__BRAND_SCOPE'
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
                'storage_path' => url($service->result['data']['access_path']),
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
    public function submit_realname_info(Request $request)
    {
        $input_data = $request->all();
        $validator = Validator::make($input_data, [
            'legal_person_name' => 'required',
            'code_idcard' => 'required',
            'expired_at_idcard' => 'required',
            'url_idcard_front' => 'required',
            'url_idcard_back' => 'required',
        ]);

        if ($validator->fails()) {
            $this->apiSv->respFail('请完整填写信息后再提交！');
        }

        if ($input_data['legal_person_name']!=''){
            if(mb_strlen($input_data['legal_person_name'])>30) {
                $this->apiSv->respFail('真实姓名不可超过30个字符！');
            }
        }

        if ($input_data['code_idcard']!=''){
            $validator = Validator::make($input_data, [
                'code_idcard' => 'alpha_num|size:18'
            ]);
            if ($validator->fails()) {
                $this->apiSv->respFail('身份证号应为18位中英文字符串！');
            }
        }

        //其他校验
        $loginUser = Auth::user();

        try{

            DB::beginTransaction();

            //更新信息
            $change_content['legal_person_name'] = $input_data['legal_person_name'];
            $change_content['code_idcard'] = $input_data['code_idcard'];
            $change_content['expired_at_idcard'] = $input_data['expired_at_idcard'];
            $change_content['url_idcard_front'] = $input_data['url_idcard_front'];
            $change_content['url_idcard_back'] = $input_data['url_idcard_back'];

            $log = LogDesignerCertification::where(['target_designer_id'=>$loginUser->id,'is_approved'=>LogDesignerCertification::IS_APROVE_VERIFYING])->first();

            if($log){
                DB::rollback();
                $this->apiSv->respFail('您提交的申请正在审核中,请耐心等候');
            }
            $data = [
                'target_designer_id' => $loginUser->id,
                'content' => serialize($change_content),
                'is_approved' => LogDesignerCertification::IS_APROVE_VERIFYING,
            ];
            $insert = DB::table('log_designer_certifications')->insert($data);
            if(!$insert){
                DB::rollback();
                $this->apiSv->respFail('修改失败,请重试');
            }

            DB::commit();

            $this->apiSv->respData([]);

        }catch(\Exception $e){

            DB::rollback();

            $this->apiSv->respFail('修改失败,请重试'.$e->getMessage());


        }
    }

    public function update_log_status(Request $request){
        $update = LogDesignerCertification::where('id',$request->id)
            ->update(['is_read'=>1]);
        if($update){
            $this->apiSv->respData();
        }else{
            $this->apiSv->respFail();
        }
    }


    public function getInfo(Request $request){
        $loginUser = Auth::user();
        $designer = Designer::find($loginUser->id);
        $designerDetail = $designer->detail;
        $certification = null;
        $log = LogDesignerCertification::where('target_designer_id',$loginUser->id)
            ->orderBy('id','desc')
            ->first();
        if(!$log){
            //暂未有审核信息
            $log_status = 0;
        }else{
            if($log->is_approved == LogDesignerCertification::IS_APROVE_APPROVAL){
                //审核已通过
                $certification = CertificationDesigner::where('designer_id',$loginUser->id)->first();
                if ($designerDetail->area_belong_id){
                    $district = Area::where('id',$designerDetail->area_belong_id)->first();
                    if ($district){
                        $city =  Area::where('id',$district->pid)->first();
                        if ($city){
                            $province =  Area::where('id',$city->pid)->first();
                            if ($province){
                                $designerDetail->area_belong_text = $province->name.'/'.$city->name.'/'.$district->name;
                            }
                        }
                    }

                }
                $log_status = -1;
            }else if($log->is_approved==LogDesignerCertification::IS_APROVE_VERIFYING){
                //待审核
                $log_status = 1;
            }else if($log->is_approved==LogDesignerCertification::IS_APROVE_REJECT){
                //审核拒绝
                $log_status = 2;
            }
            $log->content = \Opis\Closure\unserialize($log->content);
        }


        $data['log_status'] = $log_status;
        $data['certification'] = $certification;
        $data['log'] = $log;

        return $this->apiSv->respDataReturn($data);
    }

    public function save_real_name(Request $request){
        $input_data = $request->all();
        $validator = Validator::make($input_data, [
            'legal_person_name' => 'required',
            'code_idcard' => 'required',
            'url_idcard_front' => 'required',
            'url_idcard_back' => 'required',
        ]);

        if ($validator->fails()) {
            $this->apiSv->respFail('请完整填写信息后再提交！');
        }

        if ($input_data['legal_person_name']!=''){
            if(mb_strlen($input_data['legal_person_name'])>30) {
                $this->apiSv->respFail('真实姓名不可超过30个字符！');
            }
        }

        if ($input_data['code_idcard']!=''){
            $validator = Validator::make($input_data, [
                'code_idcard' => 'alpha_num|size:18'
            ]);
            if ($validator->fails()) {
                $this->apiSv->respFail('身份证号应为18位中英文字符串！');
            }
        }

        //其他校验
        $loginUser = Auth::user();

        try{

            DB::beginTransaction();

            //更新信息
            $change_content['legal_person_name'] = $input_data['legal_person_name'];
            $change_content['code_idcard'] = $input_data['code_idcard'];
            $change_content['url_idcard_front'] = $input_data['url_idcard_front'];
            $change_content['url_idcard_back'] = $input_data['url_idcard_back'];

            $log = LogDesignerCertification::where(['target_designer_id'=>$loginUser->id,'is_approved'=>LogDesignerCertification::IS_APROVE_VERIFYING])->first();

            if($log){
                DB::rollback();
                $this->apiSv->respFail('您提交的申请正在审核中,请耐心等候');
            }
            $data = [
                'target_designer_id' => $loginUser->id,
                'content' => serialize($change_content),
                'is_approved' => LogDesignerCertification::IS_APROVE_VERIFYING,
            ];
            $insert = DB::table('log_designer_certifications')->insert($data);
            if(!$insert){
                DB::rollback();
                $this->apiSv->respFail('修改失败,请重试');
            }

            DB::commit();

            $this->apiSv->respData([]);

        }catch(\Exception $e){

            DB::rollback();

            $this->apiSv->respFail('修改失败,请重试'.$e->getMessage());


        }
    }


}
