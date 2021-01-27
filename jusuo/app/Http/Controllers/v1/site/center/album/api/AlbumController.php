<?php

namespace App\Http\Controllers\v1\site\center\album\api;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AlbumService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Models\Album;
use App\Models\AlbumProductCeramic;
use App\Models\AlbumSection;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\CeramicSpec;
use App\Models\Designer;
use App\Models\HouseType;
use App\Models\LogProductCeramic;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\site\DesignerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlbumController extends ApiController
{
    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }


    //方案保存
    public function store(Request $request)
    {
        $loginDesigner = Auth::user();

        $input_data = $request->all();

        $check_result = $this->check_album_submit($input_data);
        if($check_result['status'] ==0){
            $this->respFail($check_result['msg']);
        }
        $input_data = $check_result['data']['input_data'];


        try{
            DB::beginTransaction();

            $data = new Album();
            $data->designer_id = $loginDesigner->id;
            $data->type = Album::TYPE_HD_PHOTO;
            $data->code = AlbumService::get_sys_code();
            $id_code = StrService::str_random_field_value('albums','web_id_code',16,10);
            if($id_code['tryLeft']>0){
                $data->web_id_code = $id_code['string'];
            }

            $result = $this->handle_album_save($data,$input_data);

            if($result['status']==0){
                DB::rollback();
                $this->respFail($result['msg']);
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！');
        }

    }

    //方案更新
    public function update($id,Request $request)
    {
        $loginDesigner = Auth::user();

        $input_data = $request->all();

        $data = Album::query()
            ->select(['albums.*'])
            ->where('designer_id',$loginDesigner->id)
            ->where('albums.web_id_code',$id)
            ->first();

        if(!$data){
            $this->respFail('方案不存在');
        }

        if($data->period_status != Album::PERIOD_STATUS_EDIT){
            $this->respFail('方案不能编辑');
        }

        $input_data['id'] = $data->id;

        $check_result = $this->check_album_submit($input_data);
        if($check_result['status'] ==0){
            $this->respFail($check_result['msg']);
        }
        $input_data = $check_result['data']['input_data'];

        DB::beginTransaction();

        try{

            $result = $this->handle_album_save($data,$input_data);

            if($result['status']==0){
                DB::rollback();
                $this->respFail($result['msg']);
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！');
        }

    }

    //检查方案提交
    private function check_album_submit($input_data)
    {
        $result = [];
        $result['status'] = 1;
        $result['msg'] = 'success';
        $result['data'] = null;

        $loginDesigner = Auth::user();

        //检查全局的是否必填判断
        $pcu = new ParamConfigUseService($loginDesigner->id);
        $app_config = $pcu->get_by_keyword('platform.album.app_info');
        $basic_config = $pcu->get_by_keyword('platform.album.basic_info.');
        $rules = [
            'count_area' => $basic_config['platform.album.basic_info.total_area.required']?'required':'',
            'title' => $app_config['platform.album.app_info.title.required']?'required':'',
            'address_street' => $app_config['platform.album.app_info.address_street.required']?'required':'',
            'address_residential_quarter' => $app_config['platform.album.app_info.address_residential_quarter.required']?'required':'',
            'address_building' => $app_config['platform.album.app_info.address_building.required']?'required':'',
            'description_design' => $app_config['platform.album.app_info.description_design.required']?'required':'',
            'description_layout' => $app_config['platform.album.app_info.description_layout.required']?'required':'',
            'address_layout_number' => $app_config['platform.album.app_info.address_layout_number.required']?'required':'',
            'photo_cover' => 'required',
            'address_province_id' => 'required',
            'address_city_id' => 'required',
            'address_area_id' => 'required',
            'house_type_id' => 'required',
            'style_ids' => 'required',
            'submit_type' => 'required',
        ];

        $messages = [
            'count_area.required' => '请填写方案总面积',
            'title.required' => '请填写方案名称',
            'address_street.required' => '请填写街道',
            'address_residential_quarter.required' => '请填写所在小区',
            'address_building.required' => '请填写所在楼栋',
            'description_design.required' => '请填写设计说明',
            'description_layout.required' => '请填写户型说明',
            'address_layout_number.required' => '请填写所在户型号',
            'photo_cover.required' => '请上传方案封面图',
            'address_province_id.required' => '请选择所在省份',
            'address_city_id.required' => '请选择所在城市',
            'address_area_id.required' => '请选择所在地区',
            'house_type_id.required' => '请选择方案户型',
            'style_ids.required' => '请选择方案风格',
        ];

        $validator = Validator::make($input_data,$rules,$messages);

        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $result['status'] = 0;
            $result['msg'] = $msg_result;
            return $result;
        }

        $submit_type = $input_data['submit_type'];
        if(!in_array($submit_type,['save','temp'])){
            $result['status'] = 0;
            $result['msg'] = '保存类型错误！';
            return $result;
        }

        //判断全局的名称、所属属性是否存在
        $exist_title_entry = Album::query()->where('title',$input_data['title']);
        if(isset($input_data['id']) && $input_data['id']){
            $exist_title_entry->where('id','<>',$input_data['id']);
        }
        $exist_title = $exist_title_entry->first();
        if($exist_title){
            $result['status'] = 0;
            $result['msg'] = '方案名称已存在！';
            return $result;
        }

        $exist_house_type = HouseType::query()
            ->where('id',$input_data['house_type_id'])
            ->count();
        if($exist_house_type<=0){
            $result['status'] = 0;
            $result['msg'] = '方案户型不存在！';
            return $result;
        }

        //风格初始化
        if(!isset($input_data['style_ids'])){
            $input_data['style_ids'] = [];
        }

        //实物图初始化
        if(!isset($input_data['photo_practicality'])){
            $input_data['photo_practicality'] = [];
        }

        //户型图初始化
        if(!isset($input_data['photo_layouts'])){
            $input_data['photo_layouts'] = [];
        }


        //空间图初始化
        if(!isset($input_data['sections'])){
            $input_data['sections'] = [];
            $input_data['sections']['section_space_type'] = [];
        }else{
            $input_data['sections'] = \GuzzleHttp\json_decode($input_data['sections'],true);
        }


        //关联产品初始化
        if(!isset($input_data['product_ids'])){
            $input_data['product_ids'] = [];
        }

        //检查全局的项目数量
        $checkArray = [
            'platform.album.basic_info.space.min_limit'=>count($input_data['sections']['section_space_type']),
            'platform.album.app_info.layout_photo.min_limit'=>count($input_data['photo_layouts']),
        ];
        if($loginDesigner->organization_type != Designer::ORGANIZATION_TYPE_NONE){
            $checkArray['platform.album.basic_info.related_product.min_limit'] = count($input_data['product_ids']);

        }
        $rejectReason = ParamCheckService::check_array_count_min_limit_param_config($checkArray);
        if($rejectReason<>''){
            $result['status'] = 0;
            $result['msg'] = $rejectReason;
            return $result;
        }

        //检查全局的限制字数
        $checkArray = [
            'platform.album.app_info.title.character_limit'=>$input_data['title'],
            'platform.album.app_info.address_street.character_limit'=>$input_data['address_street'],
            'platform.album.app_info.address_residential_quarter.character_limit'=>$input_data['address_residential_quarter'],
            'platform.album.app_info.address_building.character_limit'=>$input_data['address_building'],
            'platform.album.app_info.description_design.character_limit'=>$input_data['description_design'],
            'platform.album.app_info.description_layout.character_limit'=>$input_data['description_layout'],
            'platform.album.app_info.address_layout_number.character_limit'=>$input_data['address_layout_number'],
        ];
        $rejectReason = ParamCheckService::check_length_param_config($checkArray);
        if($rejectReason<>''){
            $result['status'] = 0;
            $result['msg'] = $rejectReason;
            return $result;
        }

        /*检查各空间数据*/
        for($i=0;$i<count($input_data['sections']['section_space_type']);$i++){
            //检查各空间的是否必填判断
            $checkArray = [
                'platform.album.basic_info.each_space_area.required'=>$input_data['sections']['section_area'][$i],
                'platform.album.app_info.each_space_description.required'=>$input_data['sections']['section_design_description'][$i],
                'platform.album.app_info.each_space_product_app_description.required'=>$input_data['sections']['section_product_description'][$i],
                'platform.album.app_info.each_space_product_build_description.required'=>$input_data['sections']['section_build_description'][$i],
            ];
            $rejectReason = ParamCheckService::check_array_required_param_config($checkArray);
            if($rejectReason<>''){
                $result['status'] = 0;
                $result['msg'] = $rejectReason;
                return $result;
            }

            //检查各空间的限制字数
            $checkArray = [
                'platform.album.app_info.each_space_description.character_limit'=>$input_data['sections']['section_design_description'][$i],
                'platform.album.app_info.each_space_product_app_description.character_limit'=>$input_data['sections']['section_product_description'][$i],
                'platform.album.app_info.each_space_product_build_description.character_limit'=>$input_data['sections']['section_build_description'][$i],
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                $result['status'] = 0;
                $result['msg'] = $rejectReason;
                return $result;
            }

            //检查各空间的项目数量
            $checkArray = [
                'platform.album.basic_info.space.min_limit'=>count($input_data['sections']['section_design_photos'][$i]),
                'platform.album.app_info.each_space_product_app_photo.min_limit'=>count($input_data['sections']['section_product_photos'][$i]),
                'platform.album.app_info.each_space_build_photo.min_limit'=>count($input_data['sections']['section_build_photos'][$i]),
            ];
            $rejectReason = ParamCheckService::check_array_count_min_limit_param_config($checkArray);
            if($rejectReason<>''){
                $result['status'] = 0;
                $result['msg'] = $rejectReason;
                return $result;
            }
        }

        $result['data']['input_data'] = $input_data;
        return $result;
    }

    //处理创建、更新方案时的保存同步工作
    private function handle_album_save($data,$input_data)
    {
        $designer = Auth::user();

        $result = [];
        $result['status'] = 1;
        $result['msg'] = 'success';

        $data->title = isset($input_data['title'])?$input_data['title']:'';
        $data->photo_cover = isset($input_data['photo_cover'])?$input_data['photo_cover']:'';
        $data->photo_layout = serialize($input_data['photo_layouts']);
        $data->address_province_id = $input_data['address_province_id'];
        $data->address_city_id = $input_data['address_city_id'];
        $data->address_area_id = $input_data['address_area_id'];
        $data->address_street = isset($input_data['address_street'])?$input_data['address_street']:'';
        $data->address_residential_quarter = isset($input_data['address_residential_quarter'])?$input_data['address_residential_quarter']:'';
        $data->address_building = isset($input_data['address_building'])?$input_data['address_building']:'';
        $data->address_layout_number = isset($input_data['address_layout_number'])?$input_data['address_layout_number']:'';
        $data->count_area = isset($input_data['count_area'])?$input_data['count_area']:'';
        $data->description_design = isset($input_data['description_design'])?$input_data['description_design']:'';
        $data->description_layout = isset($input_data['description_layout'])?$input_data['description_layout']:'';
        $data->count_section = count($input_data['sections']['section_space_type']);
        if($input_data['submit_type']=='save'){
            $data->status = Album::STATUS_VERIFYING;
            $data->period_status = Album::PERIOD_STATUS_VERIFY;
        }else if($input_data['submit_type']=='temp'){
            $data->status = Album::STATUS_TEMP;
            $data->period_status = Album::PERIOD_STATUS_EDIT;
        }
        $data->save();

        //方案户型
        $house_type_id = $input_data['house_type_id'];
        if($house_type_id){
            $data->house_types()->sync([$house_type_id]);
        }

        //方案风格
        $style_ids = $input_data['style_ids'];
        if(count($style_ids)>0){
            $data->style()->sync($style_ids);
        }

        //关联产品
        if($designer->organization_type != Designer::ORGANIZATION_TYPE_NONE){
            $product_ids = $input_data['product_ids'];
            if(count($product_ids)>0){
                $data->product_ceramics()->sync($product_ids);
            }
        }

        //章节
        $album_id = $data->id;
        $section_space_type_ids = [];
        //先清空原有章节数据
        $delete_old_sections = AlbumSection::where('album_id',$album_id)->delete();
        for($i=0;$i<count($input_data['sections']['section_space_type']);$i++){
            $section_style_ids = $input_data['sections']['section_style_ids'][$i];
            $space_type_id = intval($input_data['sections']['section_space_type'][$i]);
            array_push($section_space_type_ids,$space_type_id);
            //20200426去掉方案名称
            //$title = $input_data['sections']['section_title'][$i];
            $count_area = floatval($input_data['sections']['section_area'][$i]);
            $content = [];
            $content['design'] = [];
            $content['design']['photos'] = $input_data['sections']['section_design_photos'][$i];
            $content['design']['description'] = $input_data['sections']['section_design_description'][$i];
            $content['product'] = [];
            $content['product']['photos'] = $input_data['sections']['section_product_photos'][$i];
            $content['product']['description'] = $input_data['sections']['section_product_description'][$i];
            $content['build'] = [];
            $content['build']['photos'] = $input_data['sections']['section_build_photos'][$i];
            $content['build']['description'] = $input_data['sections']['section_build_description'][$i];
            $album_section = new AlbumSection();
            $album_section->album_id = $album_id;
            $album_section->space_type_id = $space_type_id;
            //$album_section->title = $title;
            $album_section->count_area = $count_area;
            $album_section->content = serialize($content);
            $album_section_save = $album_section->save();

            if(!$album_section_save){
                $result['status'] = 0;
                $result['msg'] = '方案章节新建失败，请联系客服';
                return $result;
            }
            //同步章节的关联风格
            $album_section_styles = [];
            for($j = 0;$j<count($section_style_ids);$j++){
                $album_section_styles[$section_style_ids[$j]] = [];
                $album_section_styles[$section_style_ids[$j]]['album_id'] = $album_id;
            }
            $album_section->styles()->sync($album_section_styles);
        }

        //冗余保存方案与空间类别的关系
        $data->space_types()->sync($section_space_type_ids);

        return $result;
    }

    //方案复制
    public function copy(Request $request)
    {
        $loginDesigner = Auth::user();

        $web_id_code = $request->input('id',0);
        if(!$web_id_code){
            $this->respFail('参数缺失');
        }
        $old_album = Album::query()
            ->where('designer_id',$loginDesigner->id) //当前用户的方案
            ->where('period_status',Album::PERIOD_STATUS_FINISH) //必须是完成阶段
            ->where('web_id_code',$web_id_code)
            ->first();
        if(!$old_album){
            $this->respFail('您没有相关权限');
        }

        $old_album_id = $old_album->id;

        try{

            DB::beginTransaction();

            $data = new Album();
            $data->designer_id = $loginDesigner->id;
            $data->type = Album::TYPE_HD_PHOTO;
            $data->code = AlbumService::get_sys_code();
            $id_code = StrService::str_random_field_value('albums','web_id_code',16,10);
            if($id_code['tryLeft']>0){
                $data->web_id_code = $id_code['string'];
            }
            $data->title = AlbumService::get_unname_title($loginDesigner->id);
            $data->photo_cover = $old_album->photo_cover;
            $data->photo_layout =  $old_album->photo_layout;
            $data->address_province_id = $old_album->address_province_id;
            $data->address_city_id = $old_album->address_city_id;
            $data->address_area_id = $old_album->address_area_id;
            $data->address_street = $old_album->address_street;
            $data->address_residential_quarter = $old_album->address_residential_quarter;
            $data->address_building = $old_album->address_building;
            $data->address_layout_number = $old_album->address_layout_number;
            $data->count_area = $old_album->count_area;
            $data->description_design = $old_album->description_design;
            $data->description_layout = $old_album->description_layout;
            $data->count_section = $old_album->count_section;
            //复制出来的方案，默认为未审核、编辑中
            $data->status = Album::STATUS_TEMP;
            $data->period_status = Album::PERIOD_STATUS_EDIT;
            $data->save();

            //方案户型
            $house_type_id = $old_album->house_types()->get()->pluck('id')->toArray();
            if($house_type_id){
                $data->house_types()->sync($house_type_id);
            }

            //方案风格
            $style_ids = $old_album->style()->get()->pluck('id')->toArray();
            if(count($style_ids)>0){
                $data->style()->sync($style_ids);
            }

            //关联产品
            $product_ids = $old_album->product_ceramics()->get()->pluck('id')->toArray();;
            if(count($product_ids)>0){
                $data->product_ceramics()->sync($product_ids);
            }

            //章节
            $oldSections = $old_album->album_sections()->get();
            $new_album_id = $data->id;
            $section_space_type_ids = [];
            for($i=0;$i<count($oldSections);$i++){
                $space_type_id = $oldSections[$i]->space_type_id;
                array_push($section_space_type_ids,$space_type_id);
                $album_section = new AlbumSection();
                $album_section->album_id = $new_album_id;
                $album_section->space_type_id = $space_type_id;
                $album_section->title = $oldSections[$i]->title;
                $album_section->count_area = $oldSections[$i]->count_area;
                $album_section->content = $oldSections[$i]->content;
                $album_section_save = $album_section->save();

                if(!$album_section_save){
                    DB::rollback();
                    $this->respFail('方案章节新建失败，请联系客服');
                }
                
                //同步章节的关联风格
                $album_section_styles = [];
                $section_style_ids = $oldSections[$i]->styles()->get()->pluck('id')->toArray();;
                for($j = 0;$j<count($section_style_ids);$j++){
                    $album_section_styles[$section_style_ids[$j]] = [];
                    $album_section_styles[$section_style_ids[$j]]['album_id'] = $new_album_id;
                }
                $album_section->styles()->sync($album_section_styles);
            }

            //冗余保存方案与空间类别的关系
            $data->space_types()->sync($section_space_type_ids);

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误'.$e->getMessage());

        }


    }

    //方案相关图片上传
    public function upload_img(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 2000,  //测试后改回1024 * 200
            'extension' => ['jpeg','jpg','png']
        ],$file);

        $designer = Auth::user();
        $brand_id = 0;
        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_BRAND:
                $brand_id = $designer->organization_id;
                break;
            case Designer::ORGANIZATION_TYPE_SELLER:
                $seller = OrganizationDealer::find($designer->organization_id);
                if($seller){
                    $brand_id = $seller->p_brand_id;
                }
                break;
        }

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_ALBUM_PHOTO.$brand_id."/")){
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

    //异步获取产品列表
    public function ajax_get_product(Request $request)
    {
        $designer = Auth::user();

        /*  自由设计师：暂不选
            品牌设计师：品牌所有
            销售商设计师：品牌已授权销售商的*/

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_NONE){
            return $this->respFailReturn('权限不足');
        }

        $limit= $request->input('limit',10);
        $name= $request->input('name',null);
        $code= $request->input('code',null);
        $type= $request->input('type',null);

        $entry = ProductCeramic::select(['id','name','spec_id','code','type','photo_cover'])
            ->with('spec')
            ->where('type',ProductCeramic::TYPE_PRODUCT)
            ->orderBy('created_at','desc');

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //当前是品牌设计师，则获取品牌所有的产品
            $entry->where('brand_id',$designer->organization_id);
        }

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //当前是销售商设计师，则获取品牌授权销售商的产品
            $seller = OrganizationDealer::find($designer->organization_id);
            $lv1_seller_id = $designer->organization_id;
            if($seller->level==2){
                $lv1_seller = OrganizationDealer::find($seller->p_dealer_id);
                if($lv1_seller){
                    $lv1_seller_id = $lv1_seller->id;
                }
            }
            $entry->whereHas('dealer',function($dealer)use($designer,$lv1_seller_id){
                $dealer->where('organization_dealers.id',$lv1_seller_id);
            });
        }


        if($type!==null && key_exists($type,ProductCeramic::typeGroup())){
            $entry->where('type',$type);
        }

        if($name!==null || $code!==null){
            if($name){
                $entry->where('name','like',"%".$name."%");
            }
            if($code){
                $entry->where('code','like',"%".$code."%");
            }

        }

        $datas=$entry->paginate($limit);


        $datas->transform(function($v){
            $v->type_text = ProductCeramic::typeGroup($v->type);
            $v->spec_text = '';
            if($v->spec){
                $v->spec_text = $v->spec->name;
            }
            return $v;
        });

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'curr' =>$datas->currentPage(),
            'data'  =>$datas->items()
        ]);
    }

    //方案删除
    public function destroy(Request $request)
    {
        $loginDesigner = Auth::user();

        $web_id_code = $request->input('id',0);
        if(!$web_id_code){
            $this->respFail('参数缺失');
        }
        $old_album = Album::query()
            ->select('albums.*')
            ->where('designer_id',$loginDesigner->id)
            ->where('albums.web_id_code',$web_id_code)
            ->first();
        if(!$old_album){
            $this->respFail('您没有相关权限');
        }

        if($old_album->status == Album::STATUS_DELETE){
            $this->respFail('不能重复删除');
        }

        try{

            DB::beginTransaction();

            //暂删方案
            $old_album->status = Album::STATUS_DELETE;
            $old_album->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误'.$e->getMessage());

        }


    }

    //上下架
    public function change_visible($id,Request $request)
    {
        $loginDesigner = Auth::user();

        if(!$id){
            $this->respFail('参数缺失');
        }

        $web_id_code = $id;

        $old_album = Album::query()
            ->where('designer_id',$loginDesigner->id) //当前用户的方案
            ->where('web_id_code',$web_id_code)
            ->first();
        if(!$old_album){
            $this->respFail('您没有相关权限');
        }


        DB::beginTransaction();

        try{

            //更新状态
            if($old_album->visible_status==Album::VISIBLE_STATUS_ON){
                $old_album->visible_status = Album::VISIBLE_STATUS_OFF;
            }else{
                $old_album->visible_status = Album::VISIBLE_STATUS_ON;
            }

            $result = $old_album->save();

            if(!$result){
                DB::rollback();
                $this->respFail('数据更新错误');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //设为/取消代表作
    public function change_represent(Request $request)
    {
        $loginDesigner = Auth::user();

        $web_id_code = $request->input('id',0);
        if(!$web_id_code){
            $this->respFail('参数缺失');
        }

        $old_album = Album::query()
            ->where('designer_id',$loginDesigner->id) //当前用户的方案
            ->where('web_id_code',$web_id_code)
            ->first();
        if(!$old_album){
            $this->respFail('您没有相关权限');
        }


        DB::beginTransaction();

        try{

            //更新状态
            if($old_album->is_representative_work==Album::IS_REPRESENTATIVE_WORK_ON){
                $old_album->is_representative_work = Album::IS_REPRESENTATIVE_WORK_OFF;
            }else{
                $old_album->is_representative_work = Album::IS_REPRESENTATIVE_WORK_ON;
            }

            $result = $old_album->save();

            if(!$result){
                DB::rollback();
                $this->respFail('数据更新错误');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }
}