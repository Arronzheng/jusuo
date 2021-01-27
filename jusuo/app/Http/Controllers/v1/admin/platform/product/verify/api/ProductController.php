<?php

namespace App\Http\Controllers\v1\admin\platform\product\verify\api;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Models\AlbumProductCeramic;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\CeramicSpec;
use App\Models\MsgProductCeramicBrand;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\MsgProductCeramicBrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //表格异步获取数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $product_name = $request->input('pn',null);
        $product_category_id = $request->input('pc',null);
        $brand_id = $request->input('bi',null);
        $apply_category_id = $request->input('ac',null);
        $technology_category_id = $request->input('tc',null);
        $color_id = $request->input('clr',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = ProductCeramic::query()
            ->where('status',ProductCeramic::STATUS_PASS); //必须是品牌已经审核通过的产品

        //筛选经营产品类别
        if($product_category_id!==null){
            $entry->whereHas('brand',function($query)use($product_category_id){
                $query->where('organization_brands.product_category',$product_category_id);
            });
        }

        //所属产品
        if($brand_id!==null){
            $entry->where('brand_id',$brand_id);
        }

        //应用类别
        if($apply_category_id!==null){
            $entry->whereHas('apply_categories',function($query)use($apply_category_id){
                $query->where('ceramic_apply_categories.id',$apply_category_id);
            });
        }

        //工艺类别
        if($technology_category_id!==null){
            $entry->whereHas('technology_categories',function($query)use($technology_category_id){
                $query->where('ceramic_technology_categories.id',$technology_category_id);
            });
        }

        //色系
        if($color_id!==null){
            $entry->whereHas('colors',function($query)use($color_id){
                $query->where('ceramic_colors.id',$color_id);
            });
        }

        if($product_name!==null){
            $entry->where(function($query) use($product_name){
                $query->where('name','like','%'.$product_name.'%');
                $query->orWhere('web_id_code','like','%'.$product_name.'%');
                $query->orWhere('code','like','%'.$product_name.'%');
            });
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $v->brand_name = '';
            $brand = $v->brand;
            if($brand){
                $v->brand_name = $brand->brand_name;
            }
            $v->spec = '';
            $spec = CeramicSpec::find($v->spec_id);
            if($spec){
                $v->spec = $spec->name;
            }
            $v->series = '';
            $series = CeramicSeries::find($v->series_id);
            if($series){
                $v->series = $series->name;
            }
            $v->image = '';
            //获取第一个产品图作为缩略图
            $photo_product = unserialize($v->photo_product);
            if(is_array($photo_product) && isset($photo_product[0]) && $photo_product[0]){
                $v->image = $photo_product[0];
            }
            //应用类别
            $v->apply_categories_text = '';
            $apply_categories = $v->apply_categories()->get()->pluck('name')->toArray();
            if(is_array($apply_categories) && count($apply_categories)>0){
                $v->apply_categories_text = implode(',',$apply_categories);
            }
            //工艺类别
            $v->technology_categories_text = '';
            $technology_categories = $v->technology_categories()->get()->pluck('name')->toArray();
            if(is_array($technology_categories) && count($technology_categories)>0){
                $v->technology_categories_text = implode(',',$technology_categories);
            }
            //表面特征
            $v->surface_features_text = '';
            $surface_features = $v->surface_features()->get()->pluck('name')->toArray();
            if(is_array($surface_features) && count($surface_features)>0){
                $v->surface_features_text = implode(',',$surface_features);
            }
            //色系
            $v->colors_text = '';
            $colors = $v->colors()->get()->pluck('name')->toArray();
            if(is_array($colors) && count($colors)>0){
                $v->colors_text = implode(',',$colors);
            }
            //可应用空间风格
            $v->styles_text = '';
            $styles = $v->styles()->get()->pluck('name')->toArray();
            if(is_array($styles) && count($styles)>0){
                $v->styles_text = implode(',',$styles);
            }
            //使用量
            //一个方案算一次使用量（即使在多个空间都使用到这个产品，也只算1次）
            $v->usage = 0;
            $usage = AlbumProductCeramic::query()
                ->where('product_ceramic_id',$v->id)
                ->groupBy('album_id')
                ->count();
            $v->usage = $usage;

            $v->changeStatusApiUrl = url('admin/platform/product/verify/api/'.$v->id.'/status');
            $v->status_text = ProductCeramic::statusGroup(isset($v->status)?$v->status:'');
            $v->visible_text = ProductCeramic::visibleGroup(isset($v->visible)?$v->visible:'');
            $v->price_way_text = ProductCeramic::priceWayGroup(isset($v->price_way)?$v->price_way:'');


            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //异步搜索产品
    public function search_product(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $keyword = $request->input('keyword',null);

        $entry = ProductCeramic::query();

        $entry->select(['id','name','code']);
        $entry->limit(10);

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('code','like','%'.$keyword.'%');
            });
        }else{
            $entry->where('name','-1');
        }

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->get();

        $this->respData($datas);
    }


    //权限保存
    public function store(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //参数判断
        $pcu = new ParamConfigUseService($loginAdmin->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $config = $pcu->get_by_keyword('platform.product.ceramic.app_info');
        $rules = [
            'name' => $config['platform.product.ceramic.app_info.name.required']?'required':'',
            'type' => 'required',
            'code' => $config['platform.product.ceramic.app_info.code.required']?'required':'',
            'key_technology' => $config['platform.product.ceramic.app_info.key_technology.required']?'required':'',
            'physical_chemical_property' => $config['platform.product.ceramic.app_info.physical_chemical_property.required']?'required':'',
            'function_feature' => $config['platform.product.ceramic.app_info.function_feature.required']?'required':'',
            'customer_value' => $config['platform.product.ceramic.app_info.customer_value.required']?'required':'',
            'series' => 'required',
            'spec' => 'required',
            'apply_categories' => 'required',
            'technology_categories' => 'required',
            'colors' => 'required',
            'surface_features' => 'required',
            'styles' => 'required',
            'photo_product' => $config['platform.product.ceramic.app_info.photo_product.required']?'required':'',
            'photo_practicality' => $config['platform.product.ceramic.app_info.photo_practicality.required']?'required':'',
            'accessory_number' => $config['platform.product.ceramic.app_info.accessory.code.required']?'required':'',
            'accessory_length' => $config['platform.product.ceramic.app_info.accessory.spec.required']?'required':'',
            'accessory_width' => $config['platform.product.ceramic.app_info.accessory.spec.required']?'required':'',
            'accessory_technology' => $config['platform.product.ceramic.app_info.accessory.technology.required']?'required':'',
            'product_accessory_photo' => $config['platform.product.ceramic.app_info.accessory.photo.required']?'required':'',
            'collocation_note' => $config['platform.product.ceramic.app_info.collocation.note.required']?'required':'',
            'collocation_product' => $config['platform.product.ceramic.app_info.collocation.product.required']?'required':'',
            'space_application_title' => $config['platform.product.ceramic.app_info.space.title.required']?'required':'',
            'space_application_note' => $config['platform.product.ceramic.app_info.space.note.required']?'required':'',
            'space_application_photo' => $config['platform.product.ceramic.app_info.space.photo.required']?'required':'',
            'photo_video' => $config['platform.product.ceramic.app_info.photo_video.required']?'required':'',
        ];

        $messages = [
            'name.required' => '请填写产品名称',
            'code.required' => '请填写产品编号',
            'key_technology.required' => '请填写核心工艺',
            'physical_chemical_property.required' => '请填写理化性能',
            'function_feature.required' => '请填写功能特征',
            'customer_value.required' => '请填写顾客价值',
            'series.required' => '请选择系列',
            'spec.required' => '请选择产品规格',
            'apply_categories.required' => '请选择应用类别',
            'technology_categories.required' => '请选择工艺类别',
            'colors.required' => '请选择色系',
            'surface_features.required' => '请选择表面特征',
            'styles.required' => '请选择应用空间风格',
            'photo_product.required' => '请添加产品图',
            'photo_practicality.required' => '请添加实物图',
            'accessory_number.required' => '请添加并填写配件编号',
            'accessory_length.required' => '请添加并填写配件规格长度',
            'accessory_width.required' => '请添加并填写配件规格宽度',
            'accessory_technology.required' => '请添加并填写配件加工工艺',
            'product_accessory_photo.required' => '请添加配件图',
            'collocation_note.required' => '请添加并填写产品搭配应用说明',
            'collocation_product.required' => '请添加搭配产品',
            'space_application_title.required' => '请添加并填写空间应用摘要',
            'space_application_note.required' => '请添加并填写空间应用详细说明',
            'space_application_photo.required' => '请添加并填写空间应用图片',
            'photo_video.required' => '请添加产品视频',
        ];

        $validator = Validator::make($input_data,$rules,$messages);

        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->respFail($msg_result);
        }

        //判断产品Or配件
        $parent_id = 0;
        if(!in_array($input_data['type'],['0','1'])){
            $this->respFail('产品类型错误！');
        }
        if($input_data['type']==1){
            //是配件，则必须要有父产品id
            if(!isset($input_data['parent_id'])){
                $this->respFail('请选择父产品！');
            }else{
                $parent_id = $input_data['parent_id'];
            }
        }

        $exist_name = ProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->where('name',$input_data['name'])
            ->first();
        if($exist_name){$this->respFail('名称已存在！');}

        $exist_code = ProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->where('code',$input_data['code'])
            ->first();
        if($exist_code){$this->respFail('编号已存在！');}

        $exist_series = CeramicSeries::query()
            ->where('brand_id',$brand->id)
            ->where('id',$input_data['series'])
            ->count();
        if($exist_series<=0){$this->respFail('产品系列不存在！');}

        $exist_spec = CeramicSpec::query()
            ->where('id',$input_data['spec'])
            ->count();
        if($exist_spec<=0){$this->respFail('产品规格不存在！');}

        //产品图初始化
        if(!isset($input_data['photo_product'])){
            $input_data['photo_product'] = [];
        }

        //实物图初始化
        if(!isset($input_data['photo_practicality'])){
            $input_data['photo_practicality'] = [];
        }

        //产品视频初始化
        if(!isset($input_data['photo_video'])){
            $input_data['photo_video'] = [];
        }

        //检查限制字数
        $checkArray = [
            'platform.product.ceramic.app_info.name.character_limit'=>$input_data['name'],
            'platform.product.ceramic.app_info.code.character_limit'=>$input_data['code'],
            'platform.product.ceramic.app_info.key_technology.character_limit'=>$input_data['key_technology'],
            'platform.product.ceramic.app_info.customer_value.character_limit'=>$input_data['customer_value'],

        ];
        $rejectReason = ParamCheckService::check_length_param_config($checkArray);
        if($rejectReason<>''){
            DB::rollback();
            $this->respFail($rejectReason);
        }
        //产品配件文本字数判断
        for($i=0;$i<count($input_data['accessory_number']);$i++){
            $checkArray = [
                'platform.product.ceramic.app_info.accessory.code.character_limit'=>$input_data['accessory_number'][$i],
                'platform.product.ceramic.app_info.accessory.spec.character_limit'=>$input_data['accessory_length'][$i],
                'platform.product.ceramic.app_info.accessory.technology.character_limit'=>$input_data['accessory_technology'][$i],
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }
        }
        //产品搭配文本字数判断
        for($i=0;$i<count($input_data['collocation_note']);$i++){
            $checkArray = [
                'platform.product.ceramic.app_info.collocation.note.character_limit'=>$input_data['collocation_note'][$i],
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }
        }
        //理化性能文本字数判断
        for($i=0;$i<count($input_data['physical_chemical_property']);$i++){
            $checkArray = [
                'platform.product.ceramic.app_info.physical_chemical_property.character_limit'=>$input_data['physical_chemical_property'][$i],
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }
        }
        //功能特征文本字数判断
        for($i=0;$i<count($input_data['function_feature']);$i++){
            $checkArray = [
                'platform.product.ceramic.app_info.function_feature.character_limit'=>$input_data['function_feature'][$i],
            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }
        }
        //空间应用文本字数判断
        for($i=0;$i<count($input_data['space_application_title']);$i++){
            $checkArray = [
                'platform.product.ceramic.app_info.space.title.character_limit'=>$input_data['space_application_title'][$i],
                'platform.product.ceramic.app_info.space.note.character_limit'=>$input_data['space_application_note'][$i]            ];
            $rejectReason = ParamCheckService::check_length_param_config($checkArray);
            if($rejectReason<>''){
                DB::rollback();
                $this->respFail($rejectReason);
            }
        }


        //检查项目数量
        $checkArray = [
            'platform.product.ceramic.app_info.photo_product.limit'=>count($input_data['photo_product']),
            'platform.product.ceramic.app_info.photo_practicality.limit'=>count($input_data['photo_practicality']),
            'platform.product.ceramic.app_info.accessory.limit'=>count($input_data['accessory_length']),
            'platform.product.ceramic.app_info.accessory.photo.limit'=>count($input_data['product_accessory_photo']),
            'platform.product.ceramic.app_info.collocation.limit'=>count($input_data['collocation_note']),
            'platform.product.ceramic.app_info.space.limit'=>count($input_data['space_application_note']),
            'platform.product.ceramic.app_info.photo_video.limit'=>count($input_data['photo_video']),
        ];
        $rejectReason = ParamCheckService::check_array_count_range_param_config($checkArray);
        if($rejectReason<>''){
            DB::rollback();
            $this->respFail($rejectReason);
        }

        DB::beginTransaction();

        try{

            //新建
            $data = new ProductCeramic();
            $data->brand_id = $brand->id;
            $data->name = $input_data['name'];
            $data->code = $input_data['code'];
            $data->type = $input_data['type'];
            $data->parent_id = $parent_id;
            $data->sys_code = ProductCeramicService::get_sys_code($input_data['type'],$brand->id,$parent_id,$input_data['series']);
            $data->key_technology = $input_data['key_technology'];
            $data->physical_chemical_property = serialize($input_data['physical_chemical_property']);
            $data->function_feature = serialize($input_data['function_feature']);
            $data->customer_value = $input_data['customer_value'];
            $data->series_id = $input_data['series'];
            $data->spec_id = $input_data['spec'];
            $data->photo_product = serialize($input_data['photo_product']);
            $data->photo_practicality = serialize($input_data['photo_practicality']);
            $data->photo_video = serialize($input_data['photo_video']);

            $data->save();

            //应用类别
            if(isset($input_data['apply_categories']) && count($input_data['apply_categories'])>0){
                $data->apply_categories()->sync($input_data['apply_categories']);
            }

            //工艺类别
            if(isset($input_data['technology_categories']) && count($input_data['technology_categories'])>0){
                $data->technology_categories()->sync($input_data['technology_categories']);
            }

            //色系
            if(isset($input_data['colors']) && count($input_data['colors'])>0){
                $data->colors()->sync($input_data['colors']);
            }

            //表面特征
            if(isset($input_data['surface_features']) && count($input_data['surface_features'])>0){
                $data->surface_features()->sync($input_data['surface_features']);
            }

            //可应用空间风格
            if(isset($input_data['styles']) && count($input_data['styles'])>0){
                $data->styles()->sync($input_data['styles']);
            }

            //产品配件
            $product_accessory = [];
            for($i=0;$i<count($input_data['accessory_number']);$i++){
                $product_accessory[$i]['product_id'] = $data->id;
                $product_accessory[$i]['code'] = $input_data['accessory_number'][$i];
                $product_accessory[$i]['spec_length'] = $input_data['accessory_length'][$i];
                $product_accessory[$i]['spec_width'] = $input_data['accessory_width'][$i];
                $product_accessory[$i]['technology'] = $input_data['accessory_technology'][$i];
                $product_accessory[$i]['photo'] = serialize([]);
                if(isset($input_data['product_accessory_photo'][$i])){
                    $product_accessory[$i]['photo'] = serialize($input_data['product_accessory_photo'][$i]);
                }
            }
            $insert_accessories = DB::table('product_ceramic_accessories')->insert($product_accessory);

            //产品搭配
            $product_collocation = [];
            for($i=0;$i<count($input_data['collocation_note']);$i++){
                $product_collocation[$i]['product_id'] = $data->id;
                $product_collocation[$i]['collocation_id'] = $input_data['collocation_product'][$i];
                $product_collocation[$i]['note'] = $input_data['collocation_note'][$i];
            }
            $insert_collocations = DB::table('product_ceramic_collocations')->insert($product_collocation);

            //空间应用
            $space_application = [];
            for($i=0;$i<count($input_data['space_application_title']);$i++){
                $space_application[$i]['product_id'] = $data->id;
                $space_application[$i]['title'] = $input_data['space_application_title'][$i];
                $space_application[$i]['note'] = $input_data['space_application_note'][$i];
                $space_application[$i]['photo'] = $input_data['space_application_photo'][$i];
            }
            $insert_space_applications = DB::table('product_ceramic_spaces')->insert($space_application);


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

    //权限更新
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'key_technology' => 'required',
            'physical_chemical_property' => 'required',
            'function_feature' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = ProductCeramic::where('brand_id',$brand->id)->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        $exist_name = ProductCeramic::query()
            ->where('id','<>',$data->id)
            ->where('brand_id',$brand->id)
            ->where('name',$input_data['name'])
            ->first();
        if($exist_name){$this->respFail('名称已存在！');}

        $exist_code = ProductCeramic::query()
            ->where('id','<>',$data->id)
            ->where('brand_id',$brand->id)
            ->where('code',$input_data['code'])
            ->first();
        if($exist_code){$this->respFail('编号已存在！');}



        DB::beginTransaction();

        try{


            //更新
            $data->name = $input_data['name'];
            $data->code = $input_data['code'];
            $data->key_technology = $input_data['key_technology'];
            $data->physical_chemical_property = serialize($input_data['physical_chemical_property']);
            $data->function_feature = serialize($input_data['function_feature']);

            $data->save();


            DB::commit();

            $this->respData([]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！');

        }

    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $operation = $request->input('op','');
        if(!in_array($operation,['on','off'])){
            $this->respFail('操作错误');

        }

        $data = ProductCeramic::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        $new_status = ProductCeramic::STATUS_PLATFORM_OFF;

        if($data->status_platform == ProductCeramic::STATUS_PLATFORM_OFF){
            $this->respFail('无法操作');
        }

        if($data->status_platform == ProductCeramic::STATUS_PLATFORM_ON){
            if($operation=='on'){
                $this->respFail('不能重复操作');
            }
        }

        if($data->status_platform == ProductCeramic::STATUS_PLATFORM_VERIFYING){
            if($operation=='on'){
                $new_status = ProductCeramic::STATUS_PLATFORM_ON;
            }
        }

        DB::beginTransaction();

        try{

            //更新状态
            $data->status_platform = $new_status;

            $msg_content = '您的产品已允许在平台展示。产品名称：'.$data->name.'，产品编号：'.$data->code;
            if($new_status==ProductCeramic::STATUS_PLATFORM_OFF){
                $msg_content = '您的产品未被允许在平台展示。产品名称：'.$data->name.'，产品编号：'.$data->code;
            }

            //写入品牌产品通知
            $msg = new MsgProductCeramicBrandService();
            $msg->setBrandId($data->brand_id);
            $msg->setContent($msg_content);
            $msg->setType(MsgProductCeramicBrand::TYPE_SWITCH_BY_PLATFORM);
            $result1= $msg->add_msg();

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //异步获取父产品列表
    public function get_parent_product(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = ProductCeramic::select(['id','name','code'])
            ->where('type',ProductCeramic::TYPE_PRODUCT)
            ->where('status',ProductCeramic::STATUS_PASS)
            ->where('brand_id',$brand->id)
            ->orderBy('created_at','desc');
        //->where('is_menu',PrivilegeBrand::IS_SHOW_MENU_YES) //默认只显示菜单权限

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->orWhere('name','like',"%".$keyword."%");
                $query->orWhere('code','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'data'  =>$datas->items()
        ]);
    }

    //异步获取品牌列表
    public function get_brands(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = OrganizationBrand::select(['id','name','brand_name','organization_account','contact_name','contact_telephone'])
            ->orderBy('created_at','desc');

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->where('name','like',"%".$keyword."%");
                $query->orWhere('brand_name','like',"%".$keyword."%");
                $query->orWhere('short_name','like',"%".$keyword."%");
                $query->orWhere('organization_account','like',"%".$keyword."%");
                $query->orWhere('contact_name','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'data'  =>$datas->items()
        ]);
    }
}