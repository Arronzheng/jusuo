<?php

namespace App\Http\Controllers\v1\admin\brand\product\api;

use App\Exports\ProductExport;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Models\AlbumProductCeramic;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\LogProductCeramic;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\Style;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductController_bak extends ApiController
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
        $brand = $loginAdmin->brand;

        $keyword = $request->input('pn',null);
        $export = $request->input('export',null);
        $apply_category_id = $request->input('ac',null);
        $technology_category_id = $request->input('tc',null);
        $series_id = $request->input('srs',null);
        $color_id = $request->input('clr',null);
        $spec_id = $request->input('spec',null);
        $product_status = $request->input('status',null);
        $visible_status = $request->input('vstatus',null);
        $product_structure_id = $request->input('psid',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = ProductCeramic::query();

        //应用类别
        if($product_structure_id!==null){
            $entry->whereHas('authorizations',function($query)use($product_structure_id){
                $query->whereHas('structures',function($query1)use($product_structure_id){
                    $query1->where('product_ceramic_structures.id',$product_structure_id);
                });
            });
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

        //系列
        if($series_id!==null){
            $entry->where('series_id',$series_id);
        }

        //规格
        if($spec_id!==null){
            $entry->where('spec_id',$spec_id);
        }

        //可用状态
        if($product_status!==null){
            $entry->where('status',$product_status);
        }

        //可见状态
        if($visible_status!==null){
            $entry->where('visible',$visible_status);
        }

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('code','like','%'.$keyword.'%');
            });
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v)use($brand){
            $v->type_text = ProductCeramic::typeGroup($v->type);
            $v->price_way_text = ProductCeramic::priceWayGroup(isset($v->price_way)?$v->price_way:'');
            $v->status_text = ProductCeramic::statusGroup(isset($v->status)?$v->status:'');
            $v->visible_text = ProductCeramic::visibleGroup(isset($v->visible)?$v->visible:'');

            //方案审核通过
            if($v->status == ProductCeramic::STATUS_PASS){
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

                $v->changeStatusApiUrl = url('admin/brand/product/api/'.$v->id.'/status');
                $v->can_switch = $v->status==ProductCeramic::STATUS_PASS?1:0;
                if($v->type==ProductCeramic::TYPE_ACCESSORY){
                    $parent_product = ProductCeramic::find($v->parent_id);
                    if($parent_product){
                        $v->type_text.= "(父产品:".$parent_product->name.")";
                    }
                }
                //平台站状态
                $v->status_platform_title = '';
                $v->status_platform_style = 'primary';
                $v->changeStatusPlatformApiUrl = '';
                switch($v->status_platform){
                    case ProductCeramic::STATUS_PLATFORM_VERIFYING:
                        //申请平台展示中
                        $v->status_platform_title = '平台展示申请中';
                        break;
                    case ProductCeramic::STATUS_PLATFORM_OFF:
                        //下架中
                        $v->status_platform_title = '申请平台展示';
                        $v->status_platform_style = 'warm';
                        $v->changeStatusPlatformApiUrl = url('admin/brand/product/api/'.$v->id.'/status_platform');
                        break;
                    case ProductCeramic::STATUS_PLATFORM_ON:
                        //平台展示中
                        $v->status_platform_title = '平台展示下架';
                        $v->status_platform_style = 'danger';
                        $v->changeStatusPlatformApiUrl = url('admin/brand/product/api/'.$v->id.'/status_platform');
                        break;
                }
            }else{
                //方案未审核通过或待审核
                $log_status = $v->status==ProductCeramic::STATUS_VERIFYING?LogProductCeramic::IS_APROVE_VERIFYING:LogProductCeramic::IS_APROVE_REJECT;
                $log = LogProductCeramic::query()
                    ->where('is_approved',$log_status)
                    ->where('brand_id',$brand->id)
                    ->orderBy('id','desc')
                    ->first();
                $content = unserialize($log->content);
                $v->name = isset($content['name'])?$content['name']:'';
                $v->code = isset($content['code'])?$content['code']:'';
                $v->product_type_text = ProductCeramic::typeGroup($content['type']);

                $v->spec = '';
                if(isset($content['spec_id'])){
                    $spec = CeramicSpec::find($content['spec_id']);
                    if($spec){
                        $v->spec = $spec->name;
                    }
                }

                $v->series = '';
                if(isset($content['series_id'])){
                    $series = CeramicSeries::find($content['series_id']);
                    if($series){
                        $v->series = $series->name;
                    }
                }

                $v->image = '';
                //获取第一个产品图作为缩略图
                if(isset($content['photo_product'])){
                    $photo_product = unserialize($content['photo_product']);
                    if(is_array($photo_product) && isset($photo_product[0]) && $photo_product[0]){
                        $v->image = $photo_product[0];
                    }
                }

                //应用类别
                $v->apply_categories_text = '';
                if(isset($content['apply_categories'])){
                    $apply_category_ids = $content['apply_categories'];
                    $apply_categories = CeramicApplyCategory::whereIn('id',$apply_category_ids)->get()->pluck('name')->toArray();
                    if(is_array($apply_categories) && count($apply_categories)>0){
                        $v->apply_categories_text = implode(',',$apply_categories);
                    }
                }

                //工艺类别
                $v->technology_categories_text = '';
                if(isset($content['technology_categories'])){
                    $technology_category_ids = $content['technology_categories'];
                    $technology_categories = CeramicTechnologyCategory::whereIn('id',$technology_category_ids)->get()->pluck('name')->toArray();
                    if(is_array($technology_categories) && count($technology_categories)>0){
                        $v->technology_categories_text = implode(',',$technology_categories);
                    }
                }

                //表面特征
                $v->surface_features_text = '';
                if(isset($content['surface_features'])){
                    $surface_features_ids = $content['surface_features'];
                    $surface_features = CeramicSurfaceFeature::whereIn('id',$surface_features_ids)->get()->pluck('name')->toArray();
                    if(is_array($surface_features) && count($surface_features)>0){
                        $v->surface_features_text = implode(',',$surface_features);
                    }
                }

                //色系
                $v->colors_text = '';
                if(isset($content['colors'])){
                    $colors_ids = $content['colors'];
                    $colors = CeramicColor::whereIn('id',$colors_ids)->get()->pluck('name')->toArray();
                    if(is_array($colors) && count($colors)>0){
                        $v->colors_text = implode(',',$colors);
                    }
                }

                //可应用空间风格
                $v->styles_text = '';
                if(isset($content['styles'])){
                    $styles_ids = $content['styles'];
                    $styles = Style::whereIn('id',$styles_ids)->get()->pluck('name')->toArray();
                    if(is_array($styles) && count($styles)>0){
                        $v->styles_text = implode(',',$styles);
                    }
                }

                //使用量
                //一个方案算一次使用量（即使在多个空间都使用到这个产品，也只算1次）
                $v->usage = 0;

                $v->is_approve_text = LogProductCeramic::getIsApproved($v->is_approved);
            }



            return $v;
        });

        if($export!=null){
            return $this->export($datas);
        }

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //导出表格
    private function export($datas)
    {

        $result = [
            [
                'id','名称','类型','产品编号','系统编号','规格','系列','应用类别','工艺类别','表面特征',
                '色系','定价方式','可应用空间风格','浏览量','使用量','收藏量','关注度','创建时间','显示状态',
                '品牌站可用状态'
            ]
        ];

        foreach($datas as $v){
            $resultItem = [];
            $resultItem[] = $v->id;
            $resultItem[] = $v->name;
            $resultItem[] = $v->type_text;
            $resultItem[] = $v->code;
            $resultItem[] = $v->sys_code;
            $resultItem[] = $v->spec;
            $resultItem[] = $v->series;
            $resultItem[] = $v->apply_categories_text;
            $resultItem[] = $v->technology_categories_text;
            $resultItem[] = $v->surface_features_text;
            $resultItem[] = $v->colors_text;
            $resultItem[] = $v->price_way_text;
            $resultItem[] = $v->styles_text;
            $resultItem[] = $v->count_visit;
            $resultItem[] = $v->usage;
            $resultItem[] = $v->count_fav;
            $resultItem[] = $v->point_focus;
            $resultItem[] = $v->created_at;
            $resultItem[] = $v->visible_text;
            $resultItem[] = $v->status_text;
            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new ProductExport($result),'产品列表'.date('Y-m-d_H_i_s') . '.xls');
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

    //产品保存
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
            'product_collocation_photo' => $config['platform.product.ceramic.app_info.collocation.photo.required']?'required':'',
            'collocation_note' => $config['platform.product.ceramic.app_info.collocation.note.required']?'required':'',
            'collocation_product' => $config['platform.product.ceramic.app_info.collocation.product.required']?'required':'',
            'space_application_title' => $config['platform.product.ceramic.app_info.space.title.required']?'required':'',
            'space_application_note' => $config['platform.product.ceramic.app_info.space.note.required']?'required':'',
            'space_application_photo' => $config['platform.product.ceramic.app_info.space.photo.required']?'required':'',
            'photo_video' => $config['platform.product.ceramic.app_info.photo_video.required']?'required':'',
        ];

        if($request->input('type') == ProductCeramic::TYPE_PRODUCT){
            array_merge($rules,[
                'accessory_number' => $config['platform.product.ceramic.app_info.accessory.code.required']?'required':'',
                'accessory_length' => $config['platform.product.ceramic.app_info.accessory.spec.required']?'required':'',
                'accessory_width' => $config['platform.product.ceramic.app_info.accessory.spec.required']?'required':'',
                'accessory_technology' => $config['platform.product.ceramic.app_info.accessory.technology.required']?'required':'',
                'product_accessory_photo' => $config['platform.product.ceramic.app_info.accessory.photo.required']?'required':'',
            ]);
        }

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
            'product_accessory_photo.required' => '请添加产品配件配图',
            'collocation_note.required' => '请添加并填写产品搭配应用说明',
            'collocation_product.required' => '请添加搭配产品',
            'product_collocation_photo.required' => '请添加产品搭配配图',
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
        if(isset($input_data['accessory_number'])){
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
        }

        //产品搭配文本字数判断
        if(isset($input_data['collocation_note'])){
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
        }

        //理化性能文本字数判断
        if(isset($input_data['physical_chemical_property'])){
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
        }

        //功能特征文本字数判断
        if(isset($input_data['function_feature'])){
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
        }

        //空间应用文本字数判断
        if($input_data['space_application_title']){
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
        }

        //检查项目数量
        $checkArray = [
            'platform.product.ceramic.app_info.accessory.limit'=>isset($input_data['accessory_length'])?count($input_data['accessory_length']):0,
            'platform.product.ceramic.app_info.collocation.limit'=>isset($input_data['collocation_note'])?count($input_data['collocation_note']):0,
            'platform.product.ceramic.app_info.space.limit'=>isset($input_data['space_application_note'])?count($input_data['space_application_note']):0,
        ];
        if(isset($input_data['product_accessory_photo']) && count($input_data['product_accessory_photo'])>0){
            $checkArray['platform.product.ceramic.app_info.accessory.photo.limit'] = count($input_data['product_accessory_photo']);
        }
        if(isset($input_data['product_collocation_photo']) && count($input_data['product_collocation_photo'])>0){
            $checkArray['platform.product.ceramic.app_info.collocation.photo.limit'] = count($input_data['product_collocation_photo']);
        }
        if(isset($input_data['photo_product']) && count($input_data['photo_product'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_product.limit'] = count($input_data['photo_product']);
        }
        if(isset($input_data['photo_practicality']) && count($input_data['photo_practicality'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_practicality.limit'] = count($input_data['photo_practicality']);
        }
        if(isset($input_data['photo_practicality']) && count($input_data['photo_practicality'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_practicality.limit'] = count($input_data['photo_practicality']);
        }
        if(isset($input_data['photo_video']) && count($input_data['photo_video'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_video.limit'] = count($input_data['photo_video']);
        }
        $rejectReason = ParamCheckService::check_array_count_range_param_config($checkArray);
        if($rejectReason<>''){
            DB::rollback();
            $this->respFail($rejectReason);
        }

        DB::beginTransaction();

        try{

            $data = new ProductCeramic();
            $data->brand_id = $brand->id;
            $data->name = $input_data['name'];
            $data->create_by_administrator_id = $loginAdmin->id;
            $data->type = $input_data['type'];
            $data->status = ProductCeramic::STATUS_VERIFYING;
            $data->visible = ProductCeramic::VISIBLE_YES;
            $data->save();

            $log_content = array();
            $log_content['name'] = $input_data['name'];
            $log_content['code'] = $input_data['code'];
            $log_content['type'] = $input_data['type'];
            $log_content['parent_id'] = $parent_id;
            $log_content['sys_code'] = ProductCeramicService::get_sys_code($input_data['type'],$brand->id,$parent_id,$input_data['series']);
            $log_content['key_technology'] = isset($input_data['key_technology'])?$input_data['key_technology']:'';
            if(isset($input_data['physical_chemical_property'])){
                $log_content['physical_chemical_property'] = serialize($input_data['physical_chemical_property']);
            }
            if(isset($input_data['function_feature'])){
                $log_content['function_feature'] = serialize($input_data['function_feature']);
            }
            $log_content['customer_value'] = isset($input_data['customer_value'])?$input_data['customer_value']:'';
            $log_content['series_id'] = isset($input_data['series'])?$input_data['series']:0;
            $log_content['spec_id'] = isset($input_data['spec'])?$input_data['spec']:0;
            $log_content['photo_product'] = isset($input_data['photo_product'])?serialize($input_data['photo_product']):[];
            $log_content['photo_practicality'] = isset($input_data['photo_practicality'])?serialize($input_data['photo_practicality']):[];
            $log_content['photo_video'] = isset($input_data['photo_video'])?serialize($input_data['photo_video']):[];
            $log_content['apply_categories'] = isset($input_data['apply_categories'])?$input_data['apply_categories']:[];
            $log_content['technology_categories'] = isset($input_data['technology_categories'])?$input_data['technology_categories']:[];
            $log_content['colors'] = isset($input_data['colors'])?$input_data['colors']:[];
            $log_content['surface_features'] = isset($input_data['surface_features'])?$input_data['surface_features']:[];
            $log_content['styles'] = isset($input_data['styles'])?$input_data['styles']:[];
            //产品配件
            $product_accessory = [];
            if(isset($input_data['accessory_number'])){
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
            }

            $log_content['accessories'] = $product_accessory;
            //产品搭配
            $product_collocation = [];
            if(isset($input_data['collocation_note'])){
                for($i=0;$i<count($input_data['collocation_note']);$i++){
                    $product_collocation[$i]['product_id'] = $data->id;
                    if(isset($input_data['collocation_product'][$i])){
                        $product_collocation[$i]['collocation_id'] = $input_data['collocation_product'][$i];
                    }
                    $product_collocation[$i]['note'] = $input_data['collocation_note'][$i];
                    if(isset($input_data['product_collocation_photo'][$i])){
                        $product_collocation[$i]['photo'] = serialize($input_data['product_collocation_photo'][$i]);
                    }
                }
            }
            $log_content['collocations'] = $product_collocation;
            //空间应用
            $space_application = [];
            if($input_data['space_application_title']){
                for($i=0;$i<count($input_data['space_application_title']);$i++){
                    $space_application[$i]['product_id'] = $data->id;
                    $space_application[$i]['title'] = $input_data['space_application_title'][$i];
                    $space_application[$i]['note'] = $input_data['space_application_note'][$i];
                    $space_application[$i]['photo'] = $input_data['space_application_photo'][$i];
                }
            }

            $log_content['spaces'] = $space_application;
            $log_content = serialize($log_content);

            $log = new LogProductCeramic();
            $log->brand_id = $brand->id;
            $log->created_administrator_id = $loginAdmin->id;
            $log->type = LogProductCeramic::TYPE_FIRST_VERIFY;
            $log->target_product_id = $data->id;
            $log->content = $log_content;
            $log->is_approved = LogProductCeramic::IS_APROVE_VERIFYING;
            $log->save();

            if(!$log){
                DB::rollback();
                $this->respFail('提交失败,请重试');
            }



            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

    //产品更新
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //check
        $check_result = $this->update_check($input_data);
        if($check_result['status']==0){
            $this->respFail($check_result['msg']);
        }
        $input_data['parent_id'] = $check_result['data']['parent_id'];

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



        DB::beginTransaction();

        try{

            $product = $check_result['data']['product'];

            //如果产品已有待审核的修改审核log，则需要作废，使用新的log
            $log = LogProductCeramic::query()
                ->where('target_product_id',$product->id)
                ->where('type',LogProductCeramic::TYPE_MODIFY_VERIFY)
                ->where('is_approved',LogProductCeramic::IS_APROVE_VERIFYING)
                ->where('brand_id',$brand->id)
                ->first();
            if($log){
                $log->is_approved = LogProductCeramic::IS_APROVE_CANCEL;
                $log->save();
            }


            //生成新的修改审核待审核log
            $new_log = $this->new_product_ceramic_log($input_data,$product,LogProductCeramic::TYPE_MODIFY_VERIFY);

            if(!$new_log){
                DB::rollback();
                $this->respFail('提交失败,请重试');
            }


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

    //更新未审核通过的产品（待审核/已驳回）
    public function update_nopass(Request $request,$log_id)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //check
        $check_result = $this->update_check($input_data);
        if($check_result['status']==0){
            $this->respFail($check_result['msg']);
        }
        $input_data['parent_id'] = $check_result['data']['parent_id'];
        $product = $check_result['data']['product'];

        $log = LogProductCeramic::query()
            ->where('target_product_id',$product->id)
            ->where('type',LogProductCeramic::TYPE_FIRST_VERIFY)
            ->where('is_approved','<>',LogProductCeramic::IS_APROVE_APPROVAL)
            ->where('brand_id',$brand->id)
            ->find($log_id);
        if(!$log){
            $this->respFail('权限不足');
        }

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



        DB::beginTransaction();

        try{


            //如果原来的log为待审核的，则需要作废，使用新的log
            if($log->is_approved == LogProductCeramic::IS_APROVE_VERIFYING){
                $log->is_approved = LogProductCeramic::IS_APROVE_CANCEL;
                $log->save();
            }

            $new_log = $this->new_product_ceramic_log($input_data,$product);

            if(!$new_log){
                DB::rollback();
                $this->respFail('提交失败,请重试');
            }

            $product->status = ProductCeramic::STATUS_VERIFYING;
            $product->save();



            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }
    }

    //update方法的check参数
    private function update_check($input_data,$is_edit=false)
    {
        $result = [
            'status' => 1,
            'msg' => 'success',
            'data' => [],
        ];
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
            'collocation_note' => $config['platform.product.ceramic.app_info.collocation.note.required']?'required':'',
            'collocation_product' => $config['platform.product.ceramic.app_info.collocation.product.required']?'required':'',
            'space_application_title' => $config['platform.product.ceramic.app_info.space.title.required']?'required':'',
            'space_application_note' => $config['platform.product.ceramic.app_info.space.note.required']?'required':'',
            'space_application_photo' => $config['platform.product.ceramic.app_info.space.photo.required']?'required':'',
            'photo_video' => $config['platform.product.ceramic.app_info.photo_video.required']?'required':'',
        ];

        if($is_edit){
            array_merge($rules,[
                'id' => 'required',
            ]);
        }

        if($input_data['type'] == ProductCeramic::TYPE_PRODUCT){
            array_merge($rules,[
                'accessory_number' => $config['platform.product.ceramic.app_info.accessory.code.required']?'required':'',
                'accessory_length' => $config['platform.product.ceramic.app_info.accessory.spec.required']?'required':'',
                'accessory_width' => $config['platform.product.ceramic.app_info.accessory.spec.required']?'required':'',
                'accessory_technology' => $config['platform.product.ceramic.app_info.accessory.technology.required']?'required':'',
                'product_accessory_photo' => $config['platform.product.ceramic.app_info.accessory.photo.required']?'required':'',
            ]);
        }

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
            $result['status'] = 0;
            $result['msg'] = $msg_result;
            return $result;
        }

        if($is_edit){
            $product_id = $input_data['id'];
            $product = ProductCeramic::query()
                ->where('brand_id',$brand->id)
                ->find($product_id);
            if(!$product){
                $result['status'] = 0;
                $result['msg'] = '权限不足';
                return $result;
            }
            $result['data']['product'] = $product;
        }

        //判断产品Or配件
        $result['data']['parent_id'] = 0;
        if(!in_array($input_data['type'],['0','1'])){
            $result['status'] = 0;
            $result['msg'] = '产品类型错误';
            return $result;
        }
        if($input_data['type']==1){
            //是配件，则必须要有父产品id
            if(!isset($input_data['parent_id'])){
                $result['status'] = 0;
                $result['msg'] = '请选择父产品！';
                return $result;
            }else{
                $result['data']['parent_id'] = $input_data['parent_id'];
            }
        }

        $exist_name_entry = ProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->where('name',$input_data['name']);
        if($is_edit){
            $exist_name_entry->where('id','<>',$product_id);
        }
        $exist_name = $exist_name_entry->first();
        if($exist_name){
            $result['status'] = 0;
            $result['msg'] = '名称已存在';
            return $result;
        }

        $exist_code_entry = ProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->where('code',$input_data['code']);
        if($is_edit){
            $exist_code_entry->where('id','<>',$product_id);
        }
        $exist_code = $exist_code_entry->first();
        if($exist_code){
            $result['status'] = 0;
            $result['msg'] = '编号已存在';
            return $result;
        }

        $exist_series = CeramicSeries::query()
            ->where('brand_id',$brand->id)
            ->where('id',$input_data['series'])
            ->count();
        if($exist_series<=0){
            $result['status'] = 0;
            $result['msg'] = '产品系列不存在';
            return $result;
        }

        $exist_spec = CeramicSpec::query()
            ->where('id',$input_data['spec'])
            ->count();
        if($exist_spec<=0){
            $result['status'] = 0;
            $result['msg'] = '产品规格不存在';
            return $result;
        }

        if(!$is_edit){
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
            $result['status'] = 0;
            $result['msg'] = $rejectReason;
            return $result;
        }
        //产品配件文本字数判断
        if(isset($input_data['accessory_number'])){
            for($i=0;$i<count($input_data['accessory_number']);$i++){
                $checkArray = [
                    'platform.product.ceramic.app_info.accessory.code.character_limit'=>$input_data['accessory_number'][$i],
                    'platform.product.ceramic.app_info.accessory.spec.character_limit'=>$input_data['accessory_length'][$i],
                    'platform.product.ceramic.app_info.accessory.technology.character_limit'=>$input_data['accessory_technology'][$i],
                ];
                $rejectReason = ParamCheckService::check_length_param_config($checkArray);
                if($rejectReason<>''){
                    $result['status'] = 0;
                    $result['msg'] = $rejectReason;
                    return $result;
                }
            }
        }

        //产品搭配文本字数判断
        if(isset($input_data['collocation_note'])){
            for($i=0;$i<count($input_data['collocation_note']);$i++){
                $checkArray = [
                    'platform.product.ceramic.app_info.collocation.note.character_limit'=>$input_data['collocation_note'][$i],
                ];
                $rejectReason = ParamCheckService::check_length_param_config($checkArray);
                if($rejectReason<>''){
                    $result['status'] = 0;
                    $result['msg'] = $rejectReason;
                    return $result;
                }
            }
        }

        //理化性能文本字数判断
        if(isset($input_data['physical_chemical_property'])){
            for($i=0;$i<count($input_data['physical_chemical_property']);$i++){
                $checkArray = [
                    'platform.product.ceramic.app_info.physical_chemical_property.character_limit'=>$input_data['physical_chemical_property'][$i],
                ];
                $rejectReason = ParamCheckService::check_length_param_config($checkArray);
                if($rejectReason<>''){
                    $result['status'] = 0;
                    $result['msg'] = $rejectReason;
                    return $result;
                }
            }
        }

        //功能特征文本字数判断
        if(isset($input_data['function_feature'])){
            for($i=0;$i<count($input_data['function_feature']);$i++){
                $checkArray = [
                    'platform.product.ceramic.app_info.function_feature.character_limit'=>$input_data['function_feature'][$i],
                ];
                $rejectReason = ParamCheckService::check_length_param_config($checkArray);
                if($rejectReason<>''){
                    $result['status'] = 0;
                    $result['msg'] = $rejectReason;
                    return $result;
                }
            }
        }

        //空间应用文本字数判断
        if(isset($input_data['space_application_title'])){
            for($i=0;$i<count($input_data['space_application_title']);$i++){
                $checkArray = [
                    'platform.product.ceramic.app_info.space.title.character_limit'=>$input_data['space_application_title'][$i],
                    'platform.product.ceramic.app_info.space.note.character_limit'=>$input_data['space_application_note'][$i]            ];
                $rejectReason = ParamCheckService::check_length_param_config($checkArray);
                if($rejectReason<>''){
                    $result['status'] = 0;
                    $result['msg'] = $rejectReason;
                    return $result;
                }
            }
        }

        //检查项目数量
        $checkArray = [
            'platform.product.ceramic.app_info.accessory.limit'=>isset($input_data['accessory_length'])?count($input_data['accessory_length']):0,
            'platform.product.ceramic.app_info.collocation.limit'=>isset($input_data['collocation_note'])?count($input_data['collocation_note']):0,
            'platform.product.ceramic.app_info.space.limit'=>isset($input_data['space_application_note'])?count($input_data['space_application_note']):0,
        ];
        if(isset($input_data['product_accessory_photo']) && count($input_data['product_accessory_photo'])>0){
            $checkArray['platform.product.ceramic.app_info.accessory.photo.limit'] = count($input_data['product_accessory_photo']);
        }
        if(isset($input_data['product_collocation_photo']) && count($input_data['product_collocation_photo'])>0){
            $checkArray['platform.product.ceramic.app_info.collocation.photo.limit'] = count($input_data['product_collocation_photo']);
        }
        if(isset($input_data['photo_product']) && count($input_data['photo_product'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_product.limit'] = count($input_data['photo_product']);
        }
        if(isset($input_data['photo_practicality']) && count($input_data['photo_practicality'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_practicality.limit'] = count($input_data['photo_practicality']);
        }
        if(isset($input_data['photo_practicality']) && count($input_data['photo_practicality'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_practicality.limit'] = count($input_data['photo_practicality']);
        }
        if(isset($input_data['photo_video']) && count($input_data['photo_video'])>0){
            $checkArray['platform.product.ceramic.app_info.photo_video.limit'] = count($input_data['photo_video']);
        }
        $rejectReason = ParamCheckService::check_array_count_range_param_config($checkArray);
        if($rejectReason<>''){
            $result['status'] = 0;
            $result['msg'] = $rejectReason;
            return $result;
        }

        return $result;
    }

    //生成一个新的待审核log
    private function new_product_ceramic_log($input_data,$product,$type=LogProductCeramic::TYPE_FIRST_VERIFY)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //生成一个新的待审核log
        $log_content = array();
        $log_content['name'] = $input_data['name'];
        $log_content['code'] = $input_data['code'];
        $log_content['type'] = $input_data['type'];
        $log_content['parent_id'] = $input_data['parent_id'];
        $log_content['sys_code'] = ProductCeramicService::get_sys_code($input_data['type'],$brand->id,$input_data['parent_id'],$input_data['series']);
        $log_content['key_technology'] = $input_data['key_technology'];
        $log_content['physical_chemical_property'] = serialize($input_data['physical_chemical_property']);
        $log_content['function_feature'] = serialize($input_data['function_feature']);
        $log_content['customer_value'] = $input_data['customer_value'];
        $log_content['series_id'] = $input_data['series'];
        $log_content['spec_id'] = $input_data['spec'];
        $log_content['photo_product'] = serialize($input_data['photo_product']);
        $log_content['photo_practicality'] = serialize($input_data['photo_practicality']);
        $log_content['photo_video'] = serialize($input_data['photo_video']);
        $log_content['apply_categories'] = $input_data['apply_categories'];
        $log_content['technology_categories'] = $input_data['technology_categories'];
        $log_content['colors'] = $input_data['colors'];
        $log_content['surface_features'] = $input_data['surface_features'];
        $log_content['styles'] = $input_data['styles'];
        //产品配件
        $product_accessory = [];
        for($i=0;$i<count($input_data['accessory_number']);$i++){
            $product_accessory[$i]['product_id'] = $product->id;
            $product_accessory[$i]['code'] = $input_data['accessory_number'][$i];
            $product_accessory[$i]['spec_length'] = $input_data['accessory_length'][$i];
            $product_accessory[$i]['spec_width'] = $input_data['accessory_width'][$i];
            $product_accessory[$i]['technology'] = $input_data['accessory_technology'][$i];
            $product_accessory[$i]['photo'] = serialize([]);
            if(isset($input_data['product_accessory_photo'][$i])){
                $product_accessory[$i]['photo'] = serialize($input_data['product_accessory_photo'][$i]);
            }
        }
        $log_content['accessories'] = $product_accessory;
        //产品搭配
        $product_collocation = [];
        for($i=0;$i<count($input_data['collocation_note']);$i++){
            $product_collocation[$i]['product_id'] = $product->id;
            if(isset($input_data['collocation_product'][$i])){
                $product_collocation[$i]['collocation_id'] = $input_data['collocation_product'][$i];
            }
            $product_collocation[$i]['note'] = $input_data['collocation_note'][$i];
            $product_collocation[$i]['photo'] = serialize([]);
            if(isset($input_data['product_collocation_photo'][$i])){
                $product_collocation[$i]['photo'] = serialize($input_data['product_collocation_photo'][$i]);
            }
        }
        $log_content['collocations'] = $product_collocation;
        //空间应用
        $space_application = [];
        for($i=0;$i<count($input_data['space_application_title']);$i++){
            $space_application[$i]['product_id'] = $product->id;
            $space_application[$i]['title'] = $input_data['space_application_title'][$i];
            $space_application[$i]['note'] = $input_data['space_application_note'][$i];
            $space_application[$i]['photo'] = $input_data['space_application_photo'][$i];
        }
        $log_content['spaces'] = $space_application;
        $log_content = serialize($log_content);

        $log = new LogProductCeramic();
        $log->brand_id = $brand->id;
        $log->type = $type;
        $log->target_product_id = $product->id;
        $log->created_administrator_id = $loginAdmin->id;
        $log->content = $log_content;
        $log->is_approved = LogProductCeramic::IS_APROVE_VERIFYING;
        $log->save();

        return $log;
    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $data = ProductCeramic::where('brand_id',$brand->id)->find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->visible==ProductCeramic::VISIBLE_NO){
                $data->visible = ProductCeramic::VISIBLE_YES;
            }else{
                $data->visible = ProductCeramic::VISIBLE_NO;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    public function change_status_platform($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $data = ProductCeramic::where('brand_id',$brand->id)->find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status_platform==ProductCeramic::STATUS_PLATFORM_OFF){
                $data->status_platform = ProductCeramic::STATUS_PLATFORM_VERIFYING;
            }else{
                $data->status_platform = ProductCeramic::STATUS_PLATFORM_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }


    //产品相关图片上传
    public function upload_product_image(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 2000,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        $loginAdmin = $this->authService->getAuthUser();
        $brand_id = $loginAdmin->brand->id;

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_PRODUCT_PHOTO.$brand_id."/")){
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

    //产品相关视频上传
    public function upload_product_video(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 50000,
            'extension' => ['mp4']
        ],$file);

        $loginAdmin = $this->authService->getAuthUser();
        $brand_id = $loginAdmin->brand->id;

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_PRODUCT_VIDEO.$brand_id."/")){
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

    //异步获取父产品列表
    public function get_parent_product(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = ProductCeramic::select(['id','name','code'])
            ->where('type',ProductCeramic::TYPE_PRODUCT)
            ->where('brand_id',$brand->id)
            ->orderBy('created_at','desc');
        //->where('is_menu',PrivilegeBrand::IS_SHOW_MENU_YES) //默认只显示菜单产品

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

}