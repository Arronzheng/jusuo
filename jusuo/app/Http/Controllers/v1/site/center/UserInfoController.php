<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/2/5
 * Time: 17:29
 */
namespace App\Http\Controllers\v1\site\center;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Models\Designer;
use App\Models\LogDealerCertification;
use App\Models\LogDesignerCertification;
use App\Models\LogDesignerDetail;
use App\Services\v1\site\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserInfoController extends VersionController{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function get_info(Request $request){
        $user = Auth::user();
        $login_username = $user->login_username;

        $detail = $user->detail;
        $data = [
            'designer_id' => $user->id,
            'nickname' => $detail->nickname,
            'url_avatar' => $detail->url_avatar,
            'gender' => $detail->gender,
            'approve_realname' => $detail->approve_realname,
            'login_username' => $login_username,
        ];

        return $this->apiSv->respDataReturn($data);

    }

    public function edit(Request $request){
        $user = $request->user();

        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.center.index',['user' => $user,'__BRAND_SCOPE'=>$__BRAND_SCOPE]);
    }

    public function update(Request $request){
        $user = $request->user();

        $designer = Designer::find($user->id);
        $designerDetail = $designer->detail;

        $validator = Validator::make($request->all(),[
            'avatar_url' => 'required',
            'nick_name' => 'required',
            'gender' => 'required'
        ],[
            'nick_name.required' => '请填写昵称',
            'avatar_url.required' => '请上传头像',
            'gender.required' => '请选择性别',
        ]);

        if($validator->fails()){
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        $url_avatar = $request->avatar_url;
        $nick_name = $request->nick_name;
        $gender = $request->gender;

        $designerDetail->url_avatar = $url_avatar;
        $designerDetail->nickname = $nick_name;
        $designerDetail->gender = $gender;
        $designerDetail->save();



        $this->apiSv->respData(['nickname' => $nick_name,'gender' => $gender,'url_avatar' => $url_avatar]);
    }

    //上传头像
    public function update_avatar(Request $request){
        $file = $request->file('avatar');

        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_AVATAR)){
            $this->apiSv->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
                'storage_path' => url($service->result['data']['access_path']),
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->apiSv->respFail($error_msg);
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

    public function save_real_name(Request $request){
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

    public function is_real_name(Request $request){
        $designer = $request->user();
        $log_status = false;
        $log = LogDesignerCertification::where('target_designer_id',$designer->id)
            ->orderBy('id','desc')
            ->first();
        if(!$log){
            $log_status = false;
        }else{
            if($log->is_approved!=LogDesignerCertification::IS_APROVE_APPROVAL){
                $log_status = false;
            }else{
                $log_status = true;
            }
        }

        return $this->apiSv->respDataReturn(['is_realname' => $log_status]);
    }

}