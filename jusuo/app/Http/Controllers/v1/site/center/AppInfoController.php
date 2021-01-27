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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AppInfoController extends VersionController
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

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $cities = [];
        $districts = [];
        if($designerDetail->area_belong_province){
            $cities = Area::where('level',2)->where('pid',$designerDetail->area_belong_province)->orderBy('id','asc')->select(['id','name'])->get();
        }
        if($designerDetail->area_belong_city){
            $districts = Area::where('level',3)->where('pid',$designerDetail->area_belong_city)->orderBy('id','asc')->select(['id','name'])->get();
        }

        $designerDetail->self_education_data = [];
        if($designerDetail->self_education){
            $designerDetail->self_education_data = unserialize($designerDetail->self_education);
        }
        $designerDetail->self_work_data = [];
        if($designerDetail->self_work){
            $designerDetail->self_work_data = unserialize($designerDetail->self_work);
        }
        $designerDetail->self_award_data = [];
        if($designerDetail->self_award){
            $designerDetail->self_award_data = unserialize($designerDetail->self_award);
        }

        //参数设置
        $pcu = new ParamConfigUseService($loginUser->id);
        $config['avatar_required'] = $pcu->find('platform.app_info.designer.avatar.required');
        $config['nickname_required'] = $pcu->find('platform.app_info.designer.nickname.required');
        $config['gender_required'] = $pcu->find('platform.app_info.designer.gender.required');
        $config['self_birth_time_required'] = $pcu->find('platform.app_info.designer.self_birth_time.required');
        $config['area_belong_required'] = $pcu->find('platform.app_info.designer.area_belong.required');
        $config['self_working_address_required'] = $pcu->find('platform.app_info.designer.self_working_address.required');
        $config['self_education_required'] = $pcu->find('platform.app_info.designer.self_education.required');
        $config['self_work_required'] = $pcu->find('platform.app_info.designer.self_work.required');
        $config['self_award_required'] = $pcu->find('platform.app_info.designer.self_award.required');
        $config['self_introduction_required'] = $pcu->find('platform.app_info.designer.self_introduction.required');
        $config['contact_telephone_required'] = $pcu->find('platform.app_info.designer.contact_telephone.required');

        $brandId = DesignerService::getDesignerBrandScope($designer->id);
        $__BRAND_SCOPE = $this->compressBrandScope($brandId);

        return $this->get_view('v1.site.center.info_verify.app_info.index',compact(
            'provinces','cities','districts','designer','designerDetail','config','__BRAND_SCOPE'
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

    public function upload_avatar(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_DESIGNER_AVATAR)){
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
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }

    public function upload_photo(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_DESIGNER_PHOTO)){
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
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }


    //提交基本信息
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

            $this->apiSv->respFail('提交失败,请重试'.$e->getMessage());


        }
    }

    //提交应用信息
    public function submit_app_info(Request $request)
    {
        $input_data = $request->all();

        //设计师信息
        $designer = auth()->user();
        $designerDetail = $designer->detail;

        $config = $pcu = new ParamConfigUseService($designer->id);
        $rules = [
            'url_avatar' => $config->find('platform.app_info.designer.avatar.required') ? 'required' : '',
            'nickname' => $config->find('platform.app_info.designer.nickname.required') ? 'required' : '',
            'gender' => $config->find('platform.app_info.designer.gender.required') ? 'required' : '',
            'self_birth_time' => $config->find('platform.app_info.designer.self_birth_time.required') ? 'required' : '',
            'area_belong_province' => $config->find('platform.app_info.designer.area_belong.required') ? 'required' : '',
            'area_belong_city' => $config->find('platform.app_info.designer.area_belong.required') ? 'required' : '',
            'area_belong_district' => $config->find('platform.app_info.designer.area_belong.required') ? 'required' : '',
            'self_working_address' => $config->find('platform.app_info.designer.self_working_address.required') ? 'required' : '',
            'school' => $config->find('platform.app_info.designer.self_education.required') ? 'required' : '',
            'work_company' => $config->find('platform.app_info.designer.self_work.required') ? 'required' : '',
            'award_name' => $config->find('platform.app_info.designer.self_award.required') ? 'required' : '',
            'self_introduction' => $config->find('platform.app_info.designer.self_introduction.required') ? 'required' : '',
            'self_working_telephone' => $config->find('platform.app_info.designer.self_working_telephone.required') ? 'required' : '',
        ];

        $messages = [
            'url_avatar.required' => '请上传头像',
            'nickname.required' => '请填写昵称',
            'gender.required' => '请选择性别',
            'self_birth_time.required' => '请选择出生日期',
            'area_belong_province.required' => '请选择所在省份',
            'area_belong_city.required' => '请选择所在城市',
            'area_belong_district.required' => '请选择所在区县',
            'self_working_address.required' => '请填写工作地址',
            'school.required' => '请添加教育信息',
            'work_company.required' => '请添加工作信息',
            'award_name.required' => '请添加证书与奖项信息',
            'self_introduction.required' => '请填写自我介绍',
            'self_working_telephone.required' => '请填写联系手机'
        ];

        $validator = Validator::make($input_data, $rules,$messages);
        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }

        //表单校验

        if ($input_data['self_working_telephone']!=''){
            $validator = Validator::make($input_data, [
                'self_working_telephone' => 'phone'
            ]);
            if ($validator->fails()) {
                $this->apiSv->respFail('请填写正确的工作联系手机号！');
            }
        }

        $pcu = new ParamConfigUseService($designer->id);
        $checkArray = [
            'platform.app_info.global.designer.nickname.character_limit'=>$input_data['nickname'],
            'platform.app_info.global.designer.self_working_address.character_limit'=>$input_data['self_working_address'],
            'platform.app_info.global.designer.self_introduction.character_limit'=>$input_data['self_introduction'],
        ];
        $rejectReason = ParamCheckService::pcu_check_length_param_config($checkArray,$pcu);
        if($rejectReason<>''){
            $this->apiSv->respFail($rejectReason);
        }
        $checkArray = [
            'platform.app_info.global.designer.self_education.limit'=>isset($input_data['school'])?count($input_data['school']):0,
            'platform.app_info.global.designer.self_work.limit'=>isset($input_data['work_company'])?count($input_data['work_company']):0,
            'platform.app_info.global.designer.self_award.limit'=>isset($input_data['award_name'])?count($input_data['award_name']):0,
        ];
        $rejectReason = ParamCheckService::pcu_check_array_count_param_config($checkArray,$pcu);
        if($rejectReason<>''){
            $this->apiSv->respFail($rejectReason);
        }
        if(isset($input_data['school'])){
            foreach($input_data['school'] as $v){
                if($v==''){
                    $this->apiSv->respFail('请完善教育经历的学校信息');
                }
            }
        }

        if(isset($input_data['profession'])){
            foreach($input_data['profession'] as $v){
                if($v==''){
                    $this->apiSv->respFail('请完善教育经历的专业信息');
                }
            }
        }
        if(isset($input_data['work_company'])){
            foreach($input_data['work_company'] as $v){
                if($v==''){
                    $this->apiSv->respFail('请完善工作经历的公司名称信息');
                }
            }
        }

        if(isset($input_data['work_position'])){
            foreach($input_data['work_position'] as $v){
                if($v==''){
                    $this->apiSv->respFail('请完善工作经历的担任职位信息');
                }
            }
        }

        if(isset($input_data['award_name'])){
            foreach($input_data['award_name'] as $v){
                if($v==''){
                    $this->apiSv->respFail('请完善奖项证书的证书名称信息');
                }
            }
        }

        if(isset($input_data['award_photo'])){
            foreach($input_data['award_photo'] as $v){
                if($v==''){
                    $this->apiSv->respFail('请完善奖项证书的证书照片信息');
                }
            }
        }

        $checkArray = [
            'platform.app_info.global.designer.self_education_school.character_limit'=>isset($input_data['school'])?$input_data['school']:[],
            'platform.app_info.global.designer.self_education_major.character_limit'=>isset($input_data['profession'])?$input_data['profession']:[],
            'platform.app_info.global.designer.self_work_company.character_limit'=>isset($input_data['work_company'])?$input_data['work_company']:[],
            'platform.app_info.global.designer.self_work_position.character_limit'=>isset($input_data['work_position'])?$input_data['work_position']:[],
            'platform.app_info.global.designer.self_award.character_limit'=>isset($input_data['award_name'])?$input_data['award_name']:[],
        ];
        $rejectReason = ParamCheckService::pcu_check_array_value_length_param_config($checkArray,$pcu);
        if($rejectReason<>''){
            $this->apiSv->respFail($rejectReason);
        }

        //AVATAR
        if(isset($input_data['url_avatar'])){
            $designerDetail->url_avatar = trim($input_data['url_avatar']);
        }

        //昵称
        if(isset($input_data['nickname'])){
            $nickname = trim($input_data['nickname']);
            $exist = DesignerDetail::where('nickname',$nickname)->where('designer_id','<>',$designer->id)->count();
            if($exist){$this->apiSv->respFail('昵称已存在，请换一个');}
            $designerDetail->nickname = $nickname;
        }

        //性别
        if(isset($input_data['gender'])){
            $designerDetail->gender = intval($input_data['gender']);
        }

        //出生年月日
        if(isset($input_data['self_birth_time'])){
            $designerDetail->self_birth_time = $input_data['self_birth_time'];
        }

        //工作经验年数
        if(isset($input_data['self_working_year'])){
            $designerDetail->self_working_year = $input_data['self_working_year'];
        }

        //所在城市
        if(isset($input_data['area_belong_province']) &&
            isset($input_data['area_belong_city']) &&
            isset($input_data['area_belong_district'])){
            $designerDetail->area_belong_province = intval($input_data['area_belong_province']);
            $designerDetail->area_belong_city = intval($input_data['area_belong_city']);
            $designerDetail->area_belong_district = intval($input_data['area_belong_district']);
        }

        //工作地址
        if(isset($input_data['self_working_address'])){
            $designerDetail->self_working_address = trim($input_data['self_working_address']);
        }

        //教育信息
        if(isset($input_data['school'])){
            $school = $input_data['school'];
            if(count($school)>0){
                $result_data = [];
                $profession = $input_data['profession'];
                $education = $input_data['education'];
                $graduate_year = $input_data['graduate_year'];
                $graduate_month = $input_data['graduate_month'];
                for($i=0;$i<count($school);$i++){
                    $temp = [];
                    $temp['school'] = $school[$i];
                    $temp['education'] = $education[$i];
                    $temp['profession'] = $profession[$i];
                    $temp['graduate_year'] = $graduate_year[$i];
                    $temp['graduate_month'] = $graduate_month[$i];
                    array_push($result_data,$temp);
                }
                $designerDetail->self_education = serialize($result_data);
            }
        }

        //工作信息
        if(isset($input_data['work_company'])){
            $company = $input_data['work_company'];
            if(count($company)>0){
                $result_data = [];
                $position = $input_data['work_position'];
                $start_year = $input_data['work_start_year'];
                $start_month = $input_data['work_start_month'];
                $end_year = $input_data['work_end_year'];
                $end_month = $input_data['work_end_month'];
                for($i=0;$i<count($company);$i++){
                    $temp = [];
                    $temp['company'] = $company[$i];
                    $temp['position'] = $position[$i];
                    $temp['start_year'] = $start_year[$i];
                    $temp['start_month'] = $start_month[$i];
                    $temp['end_year'] = $end_year[$i];
                    $temp['end_month'] = $end_month[$i];
                    array_push($result_data,$temp);
                }
                $designerDetail->self_work = serialize($result_data);
            }
        }

        //证书与奖项
        if(isset($input_data['award_name'])){
            $award_name = $input_data['award_name'];
            if(count($award_name)>0){
                $result_data = [];
                $award_year = $input_data['award_year'];
                $award_month = $input_data['award_month'];
                $award_photo = $input_data['award_photo'];
                for($i=0;$i<count($award_name);$i++){
                    $temp = [];
                    $temp['award_name'] = $award_name[$i];
                    $temp['award_year'] = $award_year[$i];
                    $temp['award_month'] = $award_month[$i];
                    $temp['award_photo'] = $award_photo[$i];
                    array_push($result_data,$temp);
                }
                $designerDetail->self_award = serialize($result_data);
            }
        }

        //自我介绍
        if(isset($input_data['self_introduction'])){
            $designerDetail->self_introduction = trim($input_data['self_introduction']);
        }

        //业务联系电话
        if(isset($input_data['self_working_telephone'])){
            $designerDetail->self_working_telephone = trim($input_data['self_working_telephone']);
        }

        $update = $designerDetail->save();

        if(!$update){
            $this->apiSv->respFail('更新失败');
        }

        $this->apiSv->respData([]);


    }


    public function get_info(Request $request){
        $loginUser = Auth::user();
        $designer = Designer::find($loginUser->id);
        $designerDetail = $designer->detail;

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $cities = [];
        $districts = [];
        if($designerDetail->area_belong_province){
            $cities = Area::where('level',2)->where('pid',$designerDetail->area_belong_province)->orderBy('id','asc')->select(['id','name'])->get();
        }
        if($designerDetail->area_belong_city){
            $districts = Area::where('level',3)->where('pid',$designerDetail->area_belong_city)->orderBy('id','asc')->select(['id','name'])->get();
        }

        $designerDetail->self_education_data = [];
        if($designerDetail->self_education){
            $designerDetail->self_education_data = unserialize($designerDetail->self_education);
        }
        $designerDetail->self_work_data = [];
        if($designerDetail->self_work){
            $designerDetail->self_work_data = unserialize($designerDetail->self_work);
        }
        $designerDetail->self_award_data = [];
        if($designerDetail->self_award){
            $designerDetail->self_award_data = unserialize($designerDetail->self_award);
        }

        //参数设置
        $pcu = new ParamConfigUseService($loginUser->id);
        $config['avatar_required'] = $pcu->find('platform.app_info.designer.avatar.required');
        $config['nickname_required'] = $pcu->find('platform.app_info.designer.nickname.required');
        $config['gender_required'] = $pcu->find('platform.app_info.designer.gender.required');
        $config['self_birth_time_required'] = $pcu->find('platform.app_info.designer.self_birth_time.required');
        $config['area_belong_required'] = $pcu->find('platform.app_info.designer.area_belong.required');
        $config['self_working_address_required'] = $pcu->find('platform.app_info.designer.self_working_address.required');
        $config['self_education_required'] = $pcu->find('platform.app_info.designer.self_education.required');
        $config['self_work_required'] = $pcu->find('platform.app_info.designer.self_work.required');
        $config['self_award_required'] = $pcu->find('platform.app_info.designer.self_award.required');
        $config['self_introduction_required'] = $pcu->find('platform.app_info.designer.self_introduction.required');
        $config['contact_telephone_required'] = $pcu->find('platform.app_info.designer.contact_telephone.required');

        $data['provinces'] =  $provinces;
        $data['cities'] = $cities;
        $data['districts'] = $districts;
        $data['designer'] = $designer;
        $data['designerDetail'] = $designerDetail;
        $data['config'] = $config;

        return $this->apiSv->respDataReturn($data);
    }

    public function get_cities(Request $request){
        $province_id = $request->province_id;
        $cities = Area::where('level',2)->where('pid',$province_id)->orderBy('id','asc')->select(['id','name'])->get();

        return $this->apiSv->respDataReturn($cities);
    }

    public function get_districts(Request $request){
        $city_id = $request->city_id;
        $districts = Area::where('level',3)->where('pid',$city_id)->orderBy('id','asc')->select(['id','name'])->get();

        return $this->apiSv->respDataReturn($districts);
    }

    //提交应用信息
    public function update_info(Request $request)
    {
        $input_data = $request->all();
        $birth_year = $request->birth_year;
        $birth_month = $request->birth_month;
        $birth_day = $request->brth_day;
        $birth_time = Carbon::create($birth_year,$birth_month,$birth_day,0,0,0)->toDateTimeString();
        $input_data['self_birth_time'] = $birth_time;


        //设计师信息
        $designer = auth()->user();
        $designerDetail = $designer->detail;

        $config = $pcu = new ParamConfigUseService($designer->id);
        $rules = [
            'self_birth_time' => $config->find('platform.app_info.designer.self_birth_time.required') ? 'required' : '',
            'area_belong_province' => $config->find('platform.app_info.designer.area_belong.required') ? 'required' : '',
            'area_belong_city' => $config->find('platform.app_info.designer.area_belong.required') ? 'required' : '',
            'area_belong_district' => $config->find('platform.app_info.designer.area_belong.required') ? 'required' : '',
            'school' => $config->find('platform.app_info.designer.self_education.required') ? 'required' : '',
            'work_company' => $config->find('platform.app_info.designer.self_work.required') ? 'required' : '',
            'award_name' => $config->find('platform.app_info.designer.self_award.required') ? 'required' : '',
            'self_introduction' => $config->find('platform.app_info.designer.self_introduction.required') ? 'required' : '',
        ];

        $messages = [
            'self_birth_time.required' => '请选择出生日期',
            'area_belong_province.required' => '请选择所在省份',
            'area_belong_city.required' => '请选择所在城市',
            'area_belong_district.required' => '请选择所在区县',
            'school.required' => '请添加教育信息',
            'work_company.required' => '请添加工作信息',
            'award_name.required' => '请添加证书与奖项信息',
            'self_introduction.required' => '请填写自我介绍',
        ];

        $validator = Validator::make($input_data, $rules,$messages);
        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->apiSv->respFail($msg_result);
        }



//        $pcu = new ParamConfigUseService($designer->id);
//        $checkArray = [
////            'platform.app_info.global.designer.self_working_address.character_limit'=>$input_data['self_working_address'],
//            'platform.app_info.global.designer.self_introduction.character_limit'=>$input_data['self_introduction'],
//        ];
//        $rejectReason = ParamCheckService::pcu_check_length_param_config($checkArray,$pcu);
//        if($rejectReason<>''){
//            $this->apiSv->respFail($rejectReason);
//        }
//        $checkArray = [
//            'platform.app_info.global.designer.self_education.limit'=>isset($input_data['school'])?count($input_data['school']):0,
//            'platform.app_info.global.designer.self_work.limit'=>isset($input_data['work_company'])?count($input_data['work_company']):0,
//            'platform.app_info.global.designer.self_award.limit'=>isset($input_data['award_name'])?count($input_data['award_name']):0,
//        ];
//        $rejectReason = ParamCheckService::pcu_check_array_count_param_config($checkArray,$pcu);
//        if($rejectReason<>''){
//            $this->apiSv->respFail($rejectReason);
//        }
        if(isset($input_data['school'])){
            foreach ($input_data['school'] as $v){
                if($v['school'] == ''){
                    $this->apiSv->respFail('请完善教育经历的学校信息');
                }
                if($v['profession'] == ''){
                    $this->apiSv->respFail('请完善教育经历的专业信息');
                }
            }
        }

        if(isset($input_data['work_company'])){
            foreach ($input_data['work_company'] as $v){
                if($v['company'] == ''){
                    $this->apiSv->respFail('请完善工作经历的公司名称信息');
                }
                if($v['position'] == ''){
                    $this->apiSv->respFail('请完善工作经历的担任职位信息');
                }
            }
        }

        if(isset($input_data['award_name'])){
            foreach ($input_data['award_name'] as $v){
                if($v['award_name'] == ''){
                    $this->apiSv->respFail('请完善奖项证书的证书名称信息');
                }
            }
        }



//        $checkArray = [
//            'platform.app_info.global.designer.self_education_school.character_limit'=>isset($input_data['school'])?$input_data['school']:[],
//            'platform.app_info.global.designer.self_education_major.character_limit'=>isset($input_data['profession'])?$input_data['profession']:[],
//            'platform.app_info.global.designer.self_work_company.character_limit'=>isset($input_data['work_company'])?$input_data['work_company']:[],
//            'platform.app_info.global.designer.self_work_position.character_limit'=>isset($input_data['work_position'])?$input_data['work_position']:[],
//            'platform.app_info.global.designer.self_award.character_limit'=>isset($input_data['award_name'])?$input_data['award_name']:[],
//        ];
//        $rejectReason = ParamCheckService::pcu_check_array_value_length_param_config($checkArray,$pcu);
//        if($rejectReason<>''){
//            $this->apiSv->respFail($rejectReason);
//        }




        //出生年月日
        if(isset($input_data['self_birth_time'])){
            $designerDetail->self_birth_time = $input_data['self_birth_time'];
        }

        //所在城市
        if(isset($input_data['area_belong_province']) &&
            isset($input_data['area_belong_city']) &&
            isset($input_data['area_belong_district'])){
            $designerDetail->area_belong_province = intval($input_data['area_belong_province']);
            $designerDetail->area_belong_city = intval($input_data['area_belong_city']);
            $designerDetail->area_belong_district = intval($input_data['area_belong_district']);
        }

//        //工作地址
//        if(isset($input_data['self_working_address'])){
//            $designerDetail->self_working_address = trim($input_data['self_working_address']);
//        }

        //教育信息
        if(isset($input_data['school'])){
            $school = $input_data['school'];
            if(count($school)>0){
                $designerDetail->self_education = serialize($school);
            }else{
                $designerDetail->self_education = null;
            }
        }


        if(isset($input_data['work_company']) && count($input_data['work_company'])>0){
            $designerDetail->self_work = serialize($input_data['work_company']);
        }else{
            $designerDetail->self_work = null;
        }


        //证书与奖项
        if(isset($input_data['award_name'])){
            $award_name = $input_data['award_name'];
            if(count($award_name)>0){
                $designerDetail->self_award = serialize($award_name);
            }else{
                $designerDetail->self_award = null;
            }
        }

        //自我介绍
        if(isset($input_data['self_introduction'])){
            $designerDetail->self_introduction = trim($input_data['self_introduction']);
        }


        $update = $designerDetail->save();

        if(!$update){
            $this->apiSv->respFail('更新失败');
        }

        $this->apiSv->respData([]);


    }


}
