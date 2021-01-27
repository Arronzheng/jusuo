<?php

namespace App\Http\Controllers\v1\admin\seller\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;

use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\DetailDealer;
use App\Models\LogBrandCertification;
use App\Models\LogBrandSiteConfig;
use App\Models\LogDealerCertification;
use App\Models\LogDetailDealer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Services\v1\admin\ParamCheckService;


class SellerController extends ApiController
{
    private $organizationRepository;
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //提交基本
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

        if ($input_data['code_license']!=''){
            $validator = Validator::make($input_data, [
                'code_license' => 'alpha_num|size:18'
            ]);
            if ($validator->fails()) {
                $this->respFail('统一社会信用代码应为18位中英文字符串！');
            }
        }

        if ($input_data['code_idcard']!=''){
            $validator = Validator::make($input_data, [
                'code_idcard' => 'alpha_num|size:18'
            ]);
            if ($validator->fails()) {
                $this->respFail('身份证号应为18位中英文字符串！');
            }
        }

        //其他校验
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        if(!$seller){
            $this->respFail('权限不足！');
        }

        try{

            DB::beginTransaction();

            //更新信息

            $change_content['code_license'] = $input_data['code_license'];
            $change_content['legal_person_name'] = $input_data['legal_person_name'];
            $change_content['code_idcard'] = $input_data['code_idcard'];
            $change_content['expired_at_idcard'] = $input_data['expired_at_idcard'];
            $change_content['url_idcard_front'] = $input_data['url_idcard_front'];
            $change_content['url_idcard_back'] = $input_data['url_idcard_back'];
            $change_content['url_license'] = $input_data['url_license'];
            $log = LogDealerCertification::where(['target_dealer_id'=>$seller->id,'is_approved'=>LogDealerCertification::IS_APROVE_VERIFYING])->first();

            if($log){
                DB::rollback();
                $this->respFail('您提交的申请正在审核中,请耐心等候');
            }
            $data = [
                'target_dealer_id' => $seller->id,
                'content' => serialize($change_content),
                'is_approved' => LogDealerCertification::IS_APROVE_VERIFYING,
            ];
            $insert = DB::table('log_dealer_certifications')->insert($data);
            if(!$insert){
                DB::rollback();
                $this->respFail('修改失败,请重试');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('修改失败,请重试'.$e->getMessage());


        }



    }

    //提交应用信息
    public function submit_app_info(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $newData = $input_data = $request->all();

        $config = $pcu = new ParamConfigUseService($loginAdmin->id,OrganizationService::ORGANIZATION_TYPE_SELLER);
        $rules = [
            'url_avatar' => $config->find('platform.app_info.seller.avatar.required') ? 'required' : '',
            'url_avatar1' => $config->find('platform.app_info.seller.avatar.required') ? 'required' : '',
            'area_belong_id' => $config->find('platform.app_info.seller.area_belong.required') ? 'required' : '',
            'contact_name' => $config->find('platform.app_info.seller.contact_name.required') ? 'required' : '',
            'contact_telephone' => $config->find('platform.app_info.seller.contact_telephone.required') ? 'required' : '',
            'contact_zip_code' => $config->find('platform.app_info.seller.contact_zip_code.required') ? 'required' : '',
            'company_address' => $config->find('platform.app_info.seller.company_address.required') ? 'required' : '',
            'self_introduction' => $config->find('platform.app_info.seller.self_introduction.required') ? 'required' : '',
            'self_promise' => $config->find('platform.app_info.seller.self_promise.required') ? 'required' : '',
            'self_address' => $config->find('platform.app_info.seller.self_address.required') ? 'required' : '',
            'self_province_id' => $config->find('platform.app_info.seller.self_address.required') ? 'required' : '',
            'self_city_id' => $config->find('platform.app_info.seller.self_address.required') ? 'required' : '',
            'self_district_id' => $config->find('platform.app_info.seller.self_address.required') ? 'required' : '',
            'self_latitude' => $config->find('platform.app_info.seller.self_address.required') ? 'required' : '',
            'self_longitude' => $config->find('platform.app_info.seller.self_address.required') ? 'required' : '',
            'self_photo' => $config->find('platform.app_info.seller.self_photo.required') ? 'required' : '',
            'self_promotion' => $config->find('platform.app_info.seller.self_promotion.required') ? 'required' : '',
        ];

        $messages = [
            'url_avatar.required' => '请上传头像信息',
            'area_belong_id.required' => '请选择所在省份',
            'contact_name.required' => '请填写联系人',
            'contact_telephone.required' => '请填写联系电话',
            'contact_zip_code.required' => '请填写邮政编码',
            'company_address.required' => '请填写公司地址',
            'self_introduction.required' => '请填写商家介绍',
            'self_promise.required' => '请填写服务承诺',
            'self_address.required' => '请完善店面地区、详细地址、定位信息',
            'self_photo.required' => '请添加店面形象照',
            'self_promotion.required' => '请填写近期促销',
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

        $organization_data = $loginAdmin->dealer;
        if(!$organization_data){
            $this->respFail('抱歉,不存在此销售商!');
        }

        $organization_detail = $organization_data->detail;

        //主页路径判断必填
        $dealer_domain_required = $config->find('platform.app_info.seller.dealer_domain.required');
        if($dealer_domain_required && $organization_detail->dealer_domain=='' && !isset($newData['dealer_domain'])){
            $this->respFail('请填写主页路径!');
        }
        if(isset($newData['dealer_domain'])){
            //判断该品牌是否已设置主页路径
            if($organization_detail->dealer_domain){
                //已设置了主页路径
                $this->respFail('主页路径已设置，不能重复设置');
            }else{
                //判断主页路径是否有重复
                $exist = DetailDealer::where('dealer_domain',trim($newData['dealer_domain']))
                    ->where('id','<>',$organization_detail->id)
                    ->count();
                if($exist>0){$this->respFail('主页路径已存在，请修改后重新提交');}
                if(!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{1,10}$/',$newData['dealer_domain'])) {
                    $this->respFail('主页路径请按照格式填写！');
                }

            }
        }

        try{

            DB::beginTransaction();

            $organizationDetail = DetailDealer::where('dealer_id',$organization_data->id)->first();

            $contact_telephone_required = $config->find('platform.app_info.seller.contact_telephone.required');
            if ($contact_telephone_required && $organization_data->contact_telephone != $newData['contact_telephone']){
                $validator = Validator::make($newData, [
                    'contact_telephone' => 'phone'
                ]);

                if ($validator->fails()) {
                    DB::rollback();
                    $this->respFail('请填写正确的手机号！');
                }
            }

            $contact_zip_code_required = $config->find('platform.app_info.seller.contact_zip_code.required');
            if ($contact_zip_code_required && $organization_data->contact_zip_code != $newData['contact_zip_code']){
                $validator = Validator::make($newData, [
                    'contact_zip_code' => 'digits:6'
                ]);

                if ($validator->fails()) {
                    DB::rollback();
                    $this->respFail('请填写正确的邮政编码！');
                }
            }

            $checkArray = [
                'platform.app_info.global.seller.contact_name.character_limit'=>$newData['contact_name'],
                /*'platform.app_info.global.seller.contact_address.character_limit'=>$newData['contact_address'],*/
                'platform.app_info.global.seller.company_address.character_limit'=>$newData['company_address'],
                'platform.app_info.global.seller.self_introduction.character_limit'=>$newData['self_introduction'],
                'platform.app_info.global.seller.self_promise.character_limit'=>$newData['self_promise'],
                'platform.app_info.global.seller.self_address.character_limit'=>$newData['self_address'],
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }

            $checkArray = [
                'platform.app_info.global.seller.self_photo.limit'=>isset($newData['self_photo'])?count($newData['self_photo']):0,
            ];
            $rejectReason = ParamCheckService::check_array_count_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }

            //主页路径
            if(isset($newData['dealer_domain'])) {
                if ($organizationDetail->dealer_domain != $newData['dealer_domain']) {
                    $detail_change_content['dealer_domain'] = trim($newData['dealer_domain']);
                }
            }
            
            //LOGO
            if($organizationDetail->url_avatar != $newData['url_avatar']){
                $detail_change_content['url_avatar'] = $newData['url_avatar'];
            }
            if($organizationDetail->url_avatar != $newData['url_avatar1']){
                $detail_change_content['url_avatar1'] = $newData['url_avatar1'];
            }
            //主页版头形象照
            if($organizationDetail->index_photo != $newData['index_photo']){
                $detail_change_content['index_photo'] = $newData['index_photo'];
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
            //公司地址
            if($organizationDetail->company_address != $newData['company_address']){
                $detail_change_content['company_address'] = $newData['company_address'];
            }
            //商家介绍
            if($organizationDetail->self_introduction != $newData['self_introduction']){
                $detail_change_content['self_introduction'] = $newData['self_introduction'];
            }
            //服务承诺
            if($organizationDetail->self_promise != $newData['self_promise']){
                $detail_change_content['self_promise'] = $newData['self_promise'];
            }
            //店面地址
            if($organizationDetail->self_address != $newData['self_address']){
                $detail_change_content['self_address'] = $newData['self_address'];
            }
            if($organizationDetail->self_province_id != $newData['self_province_id']){
                $detail_change_content['self_province_id'] = $newData['self_province_id'];
            }
            if($organizationDetail->self_city_id != $newData['self_city_id']){
                $detail_change_content['self_city_id'] = $newData['self_city_id'];
            }
            if($organizationDetail->self_district_id != $newData['self_district_id']){
                $detail_change_content['self_district_id'] = $newData['self_district_id'];
            }
            if($organizationDetail->self_latitude != $newData['self_latitude']){
                $detail_change_content['self_latitude'] = $newData['self_latitude'];
            }
            if($organizationDetail->self_longitude != $newData['self_longitude']){
                $detail_change_content['self_longitude'] = $newData['self_longitude'];
            }
            //店面形象照
            if(!isset($newData['self_photo']) ){
                $newData['self_photo'] = [];
            }
            if($organizationDetail->self_photo != $newData['self_photo']){
                $detail_change_content['self_photo'] = serialize($newData['self_photo']);
            }
            //近期促销
            if($organizationDetail->self_promotion != $newData['self_promotion']){
                $detail_change_content['self_promotion'] = $newData['self_promotion'];
            }

            if(!isset($detail_change_content) && !isset($organization_change_content)){
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
                'target_dealer_id' => $organization_data->id,
                'content' => serialize($merge_change_content),
                'is_approved' => LogDetailDealer::IS_APROVE_VERIFYING,
            ];
            $insert = DB::table('log_detail_dealers')->insert($data);
            if(!$insert){
                DB::rollback();
                $this->respFail('修改失败,请重试');
            }

            if(isset($detail_change_content)){
                $update1 = $organization_detail->update($detail_change_content);
                if(!$update1){
                    DB::rollback();
                    $this->respFail('详情信息更新失败,请重试'.\GuzzleHttp\json_encode($organization_data));
                }
            }

            if(isset($organization_change_content)){
                $update2 = $organization_data->update($organization_change_content);
                if(!$update2){
                    DB::rollback();
                    $this->respFail('组织信息更新失败,请重试');
                }
            }

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
        $update = LogDealerCertification::where('id',$request->id)
            ->update(['is_read'=>1]);
        if($update){
            $this->respData([]);
        }else{
            $this->respFail('操作失败');
        }
    }

    public function get_title()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $dealer = $loginAdmin->dealer;
        $dealer = OrganizationDealer::find($dealer->id);
        $brandId = $dealer->p_brand_id;
        $brand_site_config = LogBrandSiteConfig::where('target_brand_id',$brandId)->first();
        $brand  = OrganizationBrand::find($brandId);
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
