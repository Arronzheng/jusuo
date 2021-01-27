<?php

namespace App\Http\Controllers\v1\admin\brand\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ParamCheckService;

use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\LogBrandSiteConfig;
use App\Models\LogDetailBrand;
use App\Models\OrganizationBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class BrandController extends ApiController
{
    private $organizationRepository;
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //提交品牌基本信息
    public function submit_basic_info(Request $request)
    {
        /*if(!$this->authService->getAuthUser()->can('organization_account.brand_info.edit')){
            return response([
                'status' => 2,
                'msg'=>'抱歉,您没有权限！'
            ]);
        }*/
        $input_data = $request->all();
        $validator = Validator::make($input_data, [
            'name' => 'required',//公司名称
            'code_license' => 'required',
            'legal_person_name' => 'required',
            'code_idcard' => 'required',
            'expired_at_idcard' => 'required',
            'url_idcard_front' => 'required',
            'url_idcard_back' => 'required',
            'url_license' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        if ($input_data['name']!=''){
            $length = mb_strlen($input_data['name'],'UTF-8');
            $limit = ParamConfigUseService::find_root('platform.basic_info.brand.name.character_limit');
            if ($length>$limit) {
                $this->respFail('公司名称不可超过'.$limit.'个字符！');
            }
        }

        if ($input_data['code_license']!=''){
            $validator = Validator::make($input_data, [
                'code_license' => 'alpha_num|size:18'
            ]);
            if ($validator->fails()) {
                $this->respFail('统一社会信用代码应为18位英文字符串！');
            }
        }

        if ($input_data['code_idcard']!=''){
            $validator = Validator::make($input_data, [
                'code_idcard' => 'alpha_num|size:18'
            ]);
            if ($validator->fails()) {
                $this->respFail('身份证号应为18位英文字符串！');
            }
        }

        //其他校验
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('权限不足！');
        }

        //相同品牌名称
        $exist = OrganizationBrand::where('brand_name',$input_data['brand_name'])
            ->where('id','<>',$brand->id)
            ->first();
        if($exist){$this->respFail('品牌名称已被使用，请换一个'.$exist->id);}

        //相同公司名称
        $exist = OrganizationBrand::where('name',$input_data['name'])
            ->where('id','<>',$brand->id)
            ->first();
        if($exist){$this->respFail('公司名称已被使用，请换一个'.$exist->id);}

        //更新信息
        $change_content['brand_name'] = $input_data['brand_name'];
        $change_content['name'] = $input_data['name'];
        $change_content['code_license'] = $input_data['code_license'];
        $change_content['legal_person_name'] = $input_data['legal_person_name'];
        $change_content['code_idcard'] = $input_data['code_idcard'];
        $change_content['expired_at_idcard'] = $input_data['expired_at_idcard'];
        $change_content['url_idcard_front'] = $input_data['url_idcard_front'];
        $change_content['url_idcard_back'] = $input_data['url_idcard_back'];
        $change_content['url_license'] = $input_data['url_license'];
        $log = LogBrandCertification::where(['target_brand_id'=>$brand->id,'is_approved'=>LogBrandCertification::IS_APROVE_VERIFYING])->first();

        if($log){
            return response([
                'status' => 2,
                'msg'=>'您提交的申请正在审核中,请耐心等候',
            ]);
        }
        $data = [
            'target_brand_id' => $brand->id,
            'content' => serialize($change_content),
            'is_approved' => LogBrandCertification::IS_APROVE_VERIFYING,
        ];
        $insert = DB::table('log_brand_certifications')->insert($data);
        if(!$insert){
            return response([
                'status' => 2,
                'msg'=>'修改失败,请重试'
            ]);
        }
        return response([
            'status' => 1,
            'msg'=>'提交成功,请耐心等候审核结果'
        ]);

    }

    //提交品牌应用信息
    public function submit_app_info(Request $request)
    {
        $newData = $input_data = $request->all();

        $loginAdmin = $this->authService->getAuthUser();
        $organization_data = $loginAdmin->brand;
        if(!$organization_data){
            $this->respFail('抱歉,不存在此品牌商!');
        }

        //参数判断
        $config = $pcu = new ParamConfigUseService($loginAdmin->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $rules = [
            'url_avatar' => $config->find('platform.app_info.brand.avatar.required') ? 'required' : '',
            'brand_image' => $config->find('platform.app_info.brand.avatar.required') ? 'required' : '',
            'area_belong_id' => $config->find('platform.app_info.brand.area_belong.required') ? 'required' : '',
            'contact_name' => $config->find('platform.app_info.brand.contact_name.required') ? 'required' : '',
            'contact_telephone' => $config->find('platform.app_info.brand.contact_telephone.required') ? 'required' : '',
            'contact_zip_code' => $config->find('platform.app_info.brand.contact_zip_code.required') ? 'required' : '',
            'company_address' => $config->find('platform.app_info.brand.company_address.required') ? 'required' : '',
            'self_introduction' => 'required',
            'self_introduction_scale' => $config->find('platform.app_info.brand.self_introduction_scale.required') ? 'required' : '',
            'self_introduction_brand' => $config->find('platform.app_info.brand.self_introduction_brand.required') ? 'required' : '',
            'self_introduction_product' => $config->find('platform.app_info.brand.self_introduction_product.required') ? 'required' : '',
            'self_introduction_service' => $config->find('platform.app_info.brand.self_introduction_service.required') ? 'required' : '',
            'brand_glory_title' => $config->find('platform.app_info.brand.self_award.required') ? 'required' : '',
            'team_building_title' => $config->find('platform.app_info.brand.self_staff.required') ? 'required' : '',
            'self_introduction_plan' => $config->find('platform.app_info.brand.self_introduction_plan.required') ? 'required' : '',
        ];

        $messages = [
            'url_avatar.required' => '请上传品牌LOGO',
            'brand_image.required' => '请上传品牌形象图',
            'area_belong_id.required' => '请选择所在省份',
            'contact_name.required' => '请填写联系人',
            'contact_telephone.required' => '请填写联系电话',
            'contact_zip_code.required' => '请填写邮政编码',
            'company_address.required' => '请填写公司地址',
            'self_introduction_scale.required' => '请填写公司规模',
            'self_introduction_brand.required' => '请填写品牌理念',
            'self_introduction_product.required' => '请填写产品理念',
            'self_introduction_service.required' => '请填写服务理念',
            'brand_glory_title.required' => '请添加品牌荣誉',
            'team_building_title.required' => '请添加团队建设',
            'self_introduction_plan.required' => '请填写品牌规划',
        ];

        $validator = Validator::make($input_data, $rules,$messages);
        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->respFail($msg_result);
        }
        $organization_detail = $organization_data->detail;

        //主页路径判断必填
        $brand_domain_required = $config->find('platform.app_info.brand.brand_domain.required');
        if($brand_domain_required && $organization_detail->brand_domain=='' && !isset($newData['brand_domain'])){
            $this->respFail('请填写主页路径!');
        }
        if(isset($newData['brand_domain'])){
            //判断该品牌是否已设置主页路径
            if($organization_detail->brand_domain){
                //已设置了主页路径
                $this->respFail('主页路径已设置，不能重复设置');
            }else{
                //判断主页路径是否有重复
                $exist = DetailBrand::where('brand_domain',trim($newData['brand_domain']))
                    ->where('id','<>',$organization_detail->id)
                    ->count();
                if($exist>0){$this->respFail('主页路径已存在，请修改后重新提交');}
                if(!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{1,10}$/',$newData['brand_domain'])) {
                    $this->respFail('主页路径请按照格式填写！');
                }

            }
        }

        try{

            DB::beginTransaction();

            $organizationDetail = DetailBrand::where('brand_id',$organization_data->id)->first();

            $contact_telephone_required = $config->find('platform.app_info.brand.contact_telephone.required');
            if ($contact_telephone_required && $organization_data->contact_telephone != $newData['contact_telephone']){
                $validator = Validator::make($newData, [
                    'contact_telephone' => 'phone'
                ]);

                if ($validator->fails()) {
                    DB::rollback();
                    $this->respFail('请填写正确的手机号！');
                }
            }

            $contact_zip_code_required = $config->find('platform.app_info.brand.contact_zip_code.required');
            if ($contact_zip_code_required && $organization_data->contact_zip_code != $newData['contact_zip_code']){
                $validator = Validator::make($newData, [
                    'contact_zip_code' => 'digits:6'
                ]);

                if ($validator->fails()) {
                    DB::rollback();
                    $this->respFail('请填写正确的邮政编码！');
                }
            }

            //判断品牌荣誉/团队建设是否没填
            if(!isset($newData['brand_glory_title'])){
                $newData['brand_glory_title'] = [];
            }
            if(!isset($newData['team_building_title'])){
                $newData['team_building_title'] = [];
            }


            $checkArray = [
                'platform.app_info.global.brand.contact_name.character_limit'=>$newData['contact_name'],
                //'platform.app_info.global.brand.contact_address.character_limit'=>$newData['contact_address'],
                'platform.app_info.global.brand.company_address.character_limit'=>$newData['company_address'],
                'platform.app_info.global.brand.self_introduction_scale.character_limit'=>$newData['self_introduction_scale'],
                'platform.app_info.global.brand.self_introduction_brand.character_limit'=>$newData['self_introduction_brand'],
                'platform.app_info.global.brand.self_introduction_product.character_limit'=>$newData['self_introduction_product'],
                'platform.app_info.global.brand.self_introduction_service.character_limit'=>$newData['self_introduction_service'],
                'platform.app_info.global.brand.self_introduction_plan.character_limit'=>$newData['self_introduction_plan'],
                'platform.app_info.global.brand.self_award.limit'=>count($newData['brand_glory_title']),
                'platform.app_info.global.brand.self_staff.limit'=>count($newData['team_building_title']),
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }

            $checkArray = [
                'platform.app_info.global.brand.self_award.limit'=>count($newData['brand_glory_title']),
                'platform.app_info.global.brand.self_staff.limit'=>count($newData['team_building_title']),
            ];
            $rejectReason = ParamCheckService::check_array_count_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }

            $rejectReason = ParamCheckService::check_multi_length_param_config($newData['brand_glory_title'],'platform.app_info.global.brand.self_award.character_limit');
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }

            $rejectReason = ParamCheckService::check_multi_length_param_config($newData['team_building_title'],'platform.app_info.global.brand.self_staff.character_limit');
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }

            //主页路径
            if(isset($newData['brand_domain'])) {
                if ($organizationDetail->brand_domain != $newData['brand_domain']) {
                    $detail_change_content['brand_domain'] = trim($newData['brand_domain']);
                }
            }
            //所在城市
            if($organizationDetail->area_belong_id != $newData['area_belong_id']){
                $detail_change_content['area_belong_id'] = $newData['area_belong_id'];
            }
            //联系人
            if($organization_data->contact_name != $newData['contact_name']){
                $organization_change_content['contact_name'] = $newData['contact_name'];
            }
            //联系电话
            if($organization_data->contact_telephone != $newData['contact_telephone']){
                $organization_change_content['contact_telephone'] = $newData['contact_telephone'];
            }
            //联系地址
            /*if($organization_data->contact_address != $newData['contact_address']){
                $organization_change_content['contact_address'] = $newData['contact_address'];
            }*/
            //邮政编码
            if($organization_data->contact_zip_code != $newData['contact_zip_code']){
                $organization_change_content['contact_zip_code'] = $newData['contact_zip_code'];
            }
            //品牌LOGO
            if($organizationDetail->url_avatar != $newData['url_avatar']){
                $detail_change_content['url_avatar'] = $newData['url_avatar'];
            }
            //品牌形象图
            if($organizationDetail->brand_image != $newData['brand_image']){
                $detail_change_content['brand_image'] = $newData['brand_image'];
            }
            //公司地址
            if($organizationDetail->company_address != $newData['company_address']){
                $detail_change_content['company_address'] = $newData['company_address'];
            }
            //品牌简介
            if($organizationDetail->self_introduction != $newData['self_introduction']){
                $detail_change_content['self_introduction'] = $newData['self_introduction'];
            }
            //公司规模
            if($organizationDetail->self_introduction_scale != $newData['self_introduction_scale']){
                $detail_change_content['self_introduction_scale'] = $newData['self_introduction_scale'];
            }
            //品牌理念
            if($organizationDetail->self_introduction_brand != $newData['self_introduction_brand']){
                $detail_change_content['self_introduction_brand'] = $newData['self_introduction_brand'];
            }
            //产品理念
            if($organizationDetail->self_introduction_product != $newData['self_introduction_product']){
                $detail_change_content['self_introduction_product'] = $newData['self_introduction_product'];
            }
            //服务理念
            if($organizationDetail->self_introduction_service != $newData['self_introduction_service']){
                $detail_change_content['self_introduction_service'] = $newData['self_introduction_service'];
            }

            //品牌荣誉
            $brand_glory = [];
            for($i=0;$i<count($newData['brand_glory_title']);$i++){
                $brand_glory[$i]['title'] = $newData['brand_glory_title'][$i];
                $brand_glory[$i]['photo'] = $newData['brand_glory_photo'][$i];
            }
            if($organizationDetail->self_award != serialize($brand_glory)){
                $detail_change_content['self_award'] = serialize($brand_glory);
            }

            //团队建设
            $team_building = [];
            for($i=0;$i<count($newData['team_building_title']);$i++){
                $team_building[$i]['title'] = $newData['team_building_title'][$i];
                $team_building[$i]['photo'] = $newData['team_building_photo'][$i];
            }
            if($organizationDetail->self_staff != serialize($team_building)){
                $detail_change_content['self_staff'] = serialize($team_building);
            }

            //品牌规划
            if($organizationDetail->self_introduction_plan != $newData['self_introduction_plan']){
                $detail_change_content['self_introduction_plan'] = $newData['self_introduction_plan'];
            }

            if(!isset($detail_change_content) && !isset($organization_change_content)){
                DB::rollback();
                $this->respFail('请修改信息');
            }


            $merge_change_content = [];
            if(isset($organization_change_content) && isset($detail_change_content)){
                $merge_change_content = array_merge($organization_change_content,$detail_change_content);
            }else if(isset($organization_change_content)){
                $merge_change_content = $organization_change_content;
            }else{
                $merge_change_content = $detail_change_content;
            }
            $data = [
                'target_brand_id' => $organization_data->id,
                'content' => serialize($merge_change_content),
                'is_approved' => LogDetailBrand::IS_APROVE_VERIFYING,
            ];
            $insert = DB::table('log_detail_brands')->insert($data);

            if(isset($detail_change_content)){
                $update1 = $organization_detail->update($detail_change_content);
                if(!$update1){
                    DB::rollback();
                    $this->respFail('详情信息更新失败,请重试');
                }
            }

            if(isset($organization_change_content)){
                $update2 = $organization_data->update($organization_change_content);
                if(!$update2){
                    DB::rollback();
                    $this->respFail('组织信息更新失败,请重试');
                }
            }


            if(!$insert){
                DB::rollback();
                $this->respFail('修改失败,请重试');
            }

            DB::commit();

            $this->respData([],'修改成功！');


        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.$e->getMessage());
        }


    }

    //提交品牌主页设置
    public function submit_site_config(Request $request)
    {
        $input_data = $request->all();

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('抱歉,不存在此品牌商!');
        }

        $info = LogBrandSiteConfig::query()
            ->where('target_brand_id',$brand->id)
            ->first();

        if(!$info){
            $info = new LogBrandSiteConfig();
            $info->target_brand_id = $brand->id;
            $info->save();
        }


        try{

            DB::beginTransaction();

            $info->content = \Opis\Closure\serialize($input_data);
            $info->save();

            DB::commit();

            $this->respData([],'修改成功！');


        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.$e->getMessage());
        }


    }

    //上传品牌法人代表身份证
    public function upload_avatar(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_BRAND_AVATAR)){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
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


    //上传品牌法人代表身份证
    public function upload_id_card(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_BRAND_IDCARD)){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
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

    //上传品牌营业执照
    public function upload_business_licence(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_BRAND_BUSINESS_LICENCE)){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }

        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_BUSINESS_LICENCE,$file,[
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ]);

        if($access_url = $service->form_upload()){
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail('上传失败！');
        }*/

    }

    //品牌荣耀图片(品牌商)
    public function upload_brandGloryPhoto(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 200,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_BRAND_PHOTO)){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }

        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_PRODUCT,$file,[
            'size' => 1024 * 200,
            'extension' => ['jpg','png']
        ]);
        if($access_url = $service->form_upload()){
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }

    //团队建设图片(品牌商)
    public function upload_teamBuildingPhoto(Request $request)
    {
        $file = $request->file('file');


        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 200,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_BRAND_PHOTO)){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }


        //参数配置
        /*$pcu = new ParamConfigUseService($this->authService->getAuthUserOrganizationId(), $this->authService->getAuthUserOrganizationType());
        $config['team_building'] = $pcu->get_by_keyword('format.brand.application.team_building');//身份证
        $service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_PHOTO,$file,[
            'size' => 1024 * $config['team_building']['format.brand.application.team_building.max_upload_size']*1024,
            'extension' => ['jpg','png']
        ]);
        if($access_url = $service->form_upload()){
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }

    public function update_log_status(Request $request){
        $update = LogBrandCertification::where('id',$request->id)
            ->update(['is_read'=>1]);
        if($update){
            $this->respData([]);
        }else{
            $this->respFail('操作失败');
        }
    }

    //品牌管理后台标题
    public function get_title()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$brand->id)->first();
        $brand  = OrganizationBrand::find($brand->id);
        $brand_name = $brand->brand_name;
        if($brand_site_config){
            $site_config = \Opis\Closure\unserialize($brand_site_config->content);
            $msg = isset($site_config['tool_name'])?$site_config['tool_name']:$brand_name.'管理后台';
            return [
                'status'=>1,
                'msg'=>$msg,
            ];
        }else{
            return [
                'status'=>1,
                'msg'=>$brand_name.'管理后台',
            ];
        }
    }


}
