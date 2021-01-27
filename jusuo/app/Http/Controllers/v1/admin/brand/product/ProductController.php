<?php

namespace App\Http\Controllers\v1\admin\brand\product;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Area;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\LogProductCeramic;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicSpace;
use App\Models\ProductCeramicStructure;
use App\Models\ProductStructure;
use App\Models\Style;
use App\Models\TestData;
use App\Models\TmpProductCeramic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class ProductController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $vdata = [];
        //按应用类别、工艺类别、色系、规格、产品状态、产品结构进行组合筛选
        //应用类别
        $apply_categories = CeramicApplyCategory::all();
        $technology_categories = CeramicTechnologyCategory::all();
        $series = CeramicSeries::where('brand_id',$brand->id)->get();
        $colors = CeramicColor::all();
        $specs = CeramicSpec::all();
        $product_status = ProductCeramic::statusGroup();
        $visible_status = ProductCeramic::visibleGroup();
        $product_structures = ProductCeramicStructure::where('brand_id',$brand->id)->get();
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        $vdata['apply_categories'] = $apply_categories;
        $vdata['technology_categories'] = $technology_categories;
        $vdata['colors'] = $colors;
        $vdata['specs'] = $specs;
        $vdata['product_status'] = $product_status;
        $vdata['visible_status'] = $visible_status;
        $vdata['product_structures'] = $product_structures;
        $vdata['series'] = $series;
        $vdata['provinces'] = $provinces;

        return $this->get_view('v1.admin_brand.product.index',compact('vdata'));
    }

    //去前端预览产品详情
    public function preview_product_detail($web_id_code)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $preview_brand_id = $brand->id;
        session()->put('preview_brand_id',$preview_brand_id);

        return redirect('/product/sm/'.$web_id_code);
    }

    //查看详情
    public function detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $data = ProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->find($id);
        if(!$data){
            die('数据不存在');
        }


        //系列
        $data->series_text = '';
        $series = CeramicSeries::find($data->series_id);
        if($series){$data->series_text = $series->name;}
        //规格
        $data->spec_text = '';
        $spec = CeramicSpec::find($data->spec_id);
        if($spec){$data->spec_text = $spec->name;}
        //应用类别
        $data->apply_categories_text = '';
        $apply_categories = $data->apply_categories()->get()->pluck('id')->toArray();
        if(is_array($apply_categories) && count($apply_categories)>0){
            $data->apply_categories_text = implode(',',$apply_categories);
        }
        //工艺类别
        $data->technology_categories_text = '';
        $technology_categories = $data->technology_categories()->get()->pluck('id')->toArray();
        if(is_array($technology_categories) && count($technology_categories)>0){
            $data->technology_categories_text = implode(',',$technology_categories);
        }
        //表面特征
        $data->surface_features_text = '';
        $surface_features = $data->surface_features()->get()->pluck('id')->toArray();
        if(is_array($surface_features) && count($surface_features)>0){
            $data->surface_features_text = implode(',',$surface_features);
        }
        //色系
        $data->colors_text = '';
        $colors = $data->colors()->get()->pluck('id')->toArray();
        if(is_array($colors) && count($colors)>0){
            $data->colors_text = implode(',',$colors);
        }
        //可应用空间风格
        $data->styles_text = '';
        $styles = $data->styles()->get()->pluck('id')->toArray();
        if(is_array($styles) && count($styles)>0){
            $data->styles_text = implode(',',$styles);
        }


        //die(\GuzzleHttp\json_encode($data->spaces));


        return $this->get_view('v1.admin_brand.product.detail',compact(
            'data'
        ));

    }

    //新增页
    public function create()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $vdata = [];
        $vdata['ceramic_series'] = CeramicSeries::where('brand_id',$brand->id)->where('status',CeramicSeries::STATUS_ON)->get();
        $vdata['ceramic_specs'] = CeramicSpec::all();
        $vdata['ceramic_apply_categories'] = CeramicApplyCategory::all();
        $vdata['ceramic_technology_categories'] = CeramicTechnologyCategory::all();
        $vdata['ceramic_colors'] = CeramicColor::all();
        $vdata['ceramic_surface_features'] = CeramicSurfaceFeature::all();
        $vdata['styles'] = Style::all();
        $product_structures = ProductCeramicStructure::select(['id','name'])->get();
        $vdata['product_structures'] = $product_structures;


        $pcu = new ParamConfigUseService($brand->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $pcu->setIncludeSelf(true);
        $config_value = $pcu->get_by_keyword('platform.product.ceramic.app_info');

        $cf = array();
        $cf['name.required'] = $config_value['platform.product.ceramic.app_info.name.required'];
        $cf['code.required'] = $config_value['platform.product.ceramic.app_info.code.required'];
        $cf['key_technology.required'] = $config_value['platform.product.ceramic.app_info.key_technology.required'];
        $cf['physical_chemical_property.required'] = $config_value['platform.product.ceramic.app_info.physical_chemical_property.required'];
        $cf['function_feature.required'] = $config_value['platform.product.ceramic.app_info.function_feature.required'];
        $cf['customer_value.required'] = $config_value['platform.product.ceramic.app_info.customer_value.required'];
        $cf['photo_product.required'] = $config_value['platform.product.ceramic.app_info.photo_product.required'];
        $cf['photo_practicality.required'] = $config_value['platform.product.ceramic.app_info.photo_practicality.required'];
        $cf['accessory.code.required'] = $config_value['platform.product.ceramic.app_info.accessory.code.required'];
        $cf['accessory.spec.required'] = $config_value['platform.product.ceramic.app_info.accessory.spec.required'];
        $cf['accessory.technology.required'] = $config_value['platform.product.ceramic.app_info.accessory.technology.required'];
        $cf['accessory.photo.required'] = $config_value['platform.product.ceramic.app_info.accessory.photo.required'];
        $cf['collocation.note.required'] = $config_value['platform.product.ceramic.app_info.collocation.note.required'];
        $cf['collocation.product.required'] = $config_value['platform.product.ceramic.app_info.collocation.product.required'];
        $cf['collocation.technology_desc.required'] = $config_value['platform.product.ceramic.app_info.collocation.technology_desc.required'];
        $cf['collocation.photo.required'] = $config_value['platform.product.ceramic.app_info.collocation.photo.required'];
        $cf['space.title.required'] = $config_value['platform.product.ceramic.app_info.space.title.required'];
        $cf['space.note.required'] = $config_value['platform.product.ceramic.app_info.space.note.required'];
        $cf['space.photo.required'] = $config_value['platform.product.ceramic.app_info.space.photo.required'];
        $cf['photo_video.required'] = $config_value['platform.product.ceramic.app_info.photo_video.required'];
        $cf['name.character_limit'] = $config_value['platform.product.ceramic.app_info.name.character_limit'];
        $cf['code.character_limit'] = $config_value['platform.product.ceramic.app_info.code.character_limit'];
        $cf['key_technology.character_limit'] = $config_value['platform.product.ceramic.app_info.key_technology.character_limit'];
        $cf['accessory.code.character_limit'] = $config_value['platform.product.ceramic.app_info.accessory.code.character_limit'];
        $cf['accessory.technology.character_limit'] = $config_value['platform.product.ceramic.app_info.accessory.technology.character_limit'];
        $cf['collocation.note.character_limit'] = $config_value['platform.product.ceramic.app_info.collocation.note.character_limit'];
        $cf['collocation.technology_desc.character_limit'] = $config_value['platform.product.ceramic.app_info.collocation.technology_desc.character_limit'];
        $cf['physical_chemical_property.character_limit'] = $config_value['platform.product.ceramic.app_info.physical_chemical_property.character_limit'];
        $cf['function_feature.character_limit'] = $config_value['platform.product.ceramic.app_info.function_feature.character_limit'];
        $cf['customer_value.character_limit'] = $config_value['platform.product.ceramic.app_info.customer_value.character_limit'];
        $cf['space.title.character_limit'] = $config_value['platform.product.ceramic.app_info.space.title.character_limit'];
        $cf['space.note.character_limit'] = $config_value['platform.product.ceramic.app_info.space.note.character_limit'];
        $cf['photo_product.limit'] = $config_value['platform.product.ceramic.app_info.photo_product.limit'];
        $cf['photo_practicality.limit'] = $config_value['platform.product.ceramic.app_info.photo_practicality.limit'];
        $cf['accessory.limit'] = $config_value['platform.product.ceramic.app_info.accessory.limit'];
        $cf['accessory.photo.limit'] = $config_value['platform.product.ceramic.app_info.accessory.photo.limit'];
        $cf['collocation.limit'] = $config_value['platform.product.ceramic.app_info.collocation.limit'];
        $cf['space.limit'] = $config_value['platform.product.ceramic.app_info.space.limit'];
        $cf['photo_video.limit'] = $config_value['platform.product.ceramic.app_info.photo_video.limit'];

        return $this->get_view('v1.admin_brand.product.edit',compact('vdata','cf'));
    }

    //编辑页
    public function edit($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        $data = ProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->find($id);

        if(!$data){
            return back()->withErrors(['您没有相关权限']);
        }

        $vdata = [];
        $vdata['ceramic_series'] = CeramicSeries::where('brand_id',$brand->id)->where('status',CeramicSeries::STATUS_ON)->get();
        $vdata['ceramic_specs'] = CeramicSpec::all();
        $vdata['ceramic_apply_categories'] = CeramicApplyCategory::all();
        $vdata['ceramic_technology_categories'] = CeramicTechnologyCategory::all();
        $vdata['ceramic_colors'] = CeramicColor::all();
        $vdata['ceramic_surface_features'] = CeramicSurfaceFeature::all();
        $vdata['styles'] = Style::all();
        $product_structures = ProductCeramicStructure::select(['id','name'])->get();
        $vdata['product_structures'] = $product_structures;

        $pcu = new ParamConfigUseService($brand->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $config_value = $pcu->get_by_keyword('platform.product.ceramic.app_info');

        $cf = array();
        $cf['name.required'] = $config_value['platform.product.ceramic.app_info.name.required'];
        $cf['code.required'] = $config_value['platform.product.ceramic.app_info.code.required'];
        $cf['key_technology.required'] = $config_value['platform.product.ceramic.app_info.key_technology.required'];
        $cf['physical_chemical_property.required'] = $config_value['platform.product.ceramic.app_info.physical_chemical_property.required'];
        $cf['function_feature.required'] = $config_value['platform.product.ceramic.app_info.function_feature.required'];
        $cf['customer_value.required'] = $config_value['platform.product.ceramic.app_info.customer_value.required'];
        $cf['photo_product.required'] = $config_value['platform.product.ceramic.app_info.photo_product.required'];
        $cf['photo_practicality.required'] = $config_value['platform.product.ceramic.app_info.photo_practicality.required'];
        $cf['accessory.code.required'] = $config_value['platform.product.ceramic.app_info.accessory.code.required'];
        $cf['accessory.spec.required'] = $config_value['platform.product.ceramic.app_info.accessory.spec.required'];
        $cf['accessory.technology.required'] = $config_value['platform.product.ceramic.app_info.accessory.technology.required'];
        $cf['accessory.photo.required'] = $config_value['platform.product.ceramic.app_info.accessory.photo.required'];
        $cf['collocation.note.required'] = $config_value['platform.product.ceramic.app_info.collocation.note.required'];
        $cf['collocation.product.required'] = $config_value['platform.product.ceramic.app_info.collocation.product.required'];
        $cf['collocation.technology_desc.required'] = $config_value['platform.product.ceramic.app_info.collocation.technology_desc.required'];
        $cf['collocation.photo.required'] = $config_value['platform.product.ceramic.app_info.collocation.photo.required'];
        $cf['collocation.photo.limit'] = $config_value['platform.product.ceramic.app_info.collocation.photo.limit'];
        $cf['space.title.required'] = $config_value['platform.product.ceramic.app_info.space.title.required'];
        $cf['space.note.required'] = $config_value['platform.product.ceramic.app_info.space.note.required'];
        $cf['space.photo.required'] = $config_value['platform.product.ceramic.app_info.space.photo.required'];
        $cf['photo_video.required'] = $config_value['platform.product.ceramic.app_info.photo_video.required'];
        $cf['name.character_limit'] = $config_value['platform.product.ceramic.app_info.name.character_limit'];
        $cf['code.character_limit'] = $config_value['platform.product.ceramic.app_info.code.character_limit'];
        $cf['key_technology.character_limit'] = $config_value['platform.product.ceramic.app_info.key_technology.character_limit'];
        $cf['accessory.code.character_limit'] = $config_value['platform.product.ceramic.app_info.accessory.code.character_limit'];
        $cf['accessory.technology.character_limit'] = $config_value['platform.product.ceramic.app_info.accessory.technology.character_limit'];
        $cf['collocation.note.character_limit'] = $config_value['platform.product.ceramic.app_info.collocation.note.character_limit'];
        $cf['collocation.technology_desc.character_limit'] = $config_value['platform.product.ceramic.app_info.collocation.technology_desc.character_limit'];
        $cf['physical_chemical_property.character_limit'] = $config_value['platform.product.ceramic.app_info.physical_chemical_property.character_limit'];
        $cf['function_feature.character_limit'] = $config_value['platform.product.ceramic.app_info.function_feature.character_limit'];
        $cf['customer_value.character_limit'] = $config_value['platform.product.ceramic.app_info.customer_value.character_limit'];
        $cf['space.title.character_limit'] = $config_value['platform.product.ceramic.app_info.space.title.character_limit'];
        $cf['space.note.character_limit'] = $config_value['platform.product.ceramic.app_info.space.note.character_limit'];
        $cf['photo_product.limit'] = $config_value['platform.product.ceramic.app_info.photo_product.limit'];
        $cf['photo_practicality.limit'] = $config_value['platform.product.ceramic.app_info.photo_practicality.limit'];
        $cf['accessory.limit'] = $config_value['platform.product.ceramic.app_info.accessory.limit'];
        $cf['accessory.photo.limit'] = $config_value['platform.product.ceramic.app_info.accessory.photo.limit'];
        $cf['collocation.limit'] = $config_value['platform.product.ceramic.app_info.collocation.limit'];
        $cf['space.limit'] = $config_value['platform.product.ceramic.app_info.space.limit'];
        $cf['photo_video.limit'] = $config_value['platform.product.ceramic.app_info.photo_video.limit'];


        if($data->status == ProductCeramic::STATUS_PASS){
            return $this->edit_pass_product($data,$brand,$vdata,$cf);
        }else if($data->status == ProductCeramic::STATUS_TEMP){
            return $this->edit_temp_product($data,$brand,$vdata,$cf);
        }else{
            return $this->edit_nopass_product($data,$brand,$vdata,$cf);
        }

    }

    //编辑已通过的产品
    private function edit_pass_product($data,$brand,$vdata,$cf)
    {
        //应用类别
        $data->apply_categories_ids = [];
        $apply_categories = $data->apply_categories()->get()->pluck('id')->toArray();
        if(is_array($apply_categories) && count($apply_categories)>0){
            $data->apply_categories_ids = $apply_categories;
        }
        //工艺类别
        $data->technology_categories_ids = [];
        $technology_categories = $data->technology_categories()->get()->pluck('id')->toArray();
        if(is_array($technology_categories) && count($technology_categories)>0){
            $data->technology_categories_ids = $technology_categories;
        }
        //表面特征
        $data->surface_features_ids = [];
        $surface_features = $data->surface_features()->get()->pluck('id')->toArray();
        if(is_array($surface_features) && count($surface_features)>0){
            $data->surface_features_ids = $surface_features;
        }
        //色系
        $data->colors_ids = [];
        $colors = $data->colors()->get()->pluck('id')->toArray();
        if(is_array($colors) && count($colors)>0){
            $data->colors_ids = $colors;
        }
        //可应用空间风格
        $data->styles_ids = [];
        $styles = $data->styles()->get()->pluck('id')->toArray();
        if(is_array($styles) && count($styles)>0){
            $data->styles_ids = $styles;
        }
        //产品配件
        $data->accessories_data = [];
        $accessories = $data->accessories()->get();
        if($accessories->count()>0){
            $data->accessories_data = $accessories;
        }
        //产品搭配
        $data->collocations_product = [];
        $collocations = $data->collocations()->get();
        if($collocations->count()>0){
            $collocations->transform(function($v){
                $v->collocation_note = $v->note;
                $product = ProductCeramic::find($v->collocation_id);
                $v->name = '';
                if($product){
                    $v->name = $product->name;
                }
                return $v;
            });
            $data->collocations_product = $collocations;
        }
        //空间应用说明
        $data->spaces_data = [];
        $spaces = $data->spaces()->get();
        if($spaces->count()>0){
            $data->spaces_data = $spaces;
        }
        //所属父产品
        $data->parent_name = '';
        if($data->parent_id){
            $parent_product = ProductCeramic::query()
                ->where('type',ProductCeramic::TYPE_PRODUCT)
                ->find($data->parent_id);
            if($parent_product){
                $data->parent_name = $parent_product->name;
            }
        }
        return $this->get_view('v1.admin_brand.product.edit',
            compact('data','vdata','cf'));
    }

    //编辑未通过（待审核/已驳回）的产品
    private function edit_nopass_product($data,$brand,$vdata,$cf)
    {
        //获取log
        $log_status = $data->status==ProductCeramic::STATUS_VERIFYING?LogProductCeramic::IS_APROVE_VERIFYING:LogProductCeramic::IS_APROVE_REJECT;
        $log = LogProductCeramic::query()
            ->where('is_approved',$log_status)
            ->where('brand_id',$brand->id)
            ->where('target_product_id',$data->id)
            ->orderBy('id','desc')
            ->first();
        if(!$log){
            return '暂无相关信息';
        }

        $data->edit_type = 'nopass';

        $data = $this->get_edit_log_data($data,$log);

        return $this->get_view('v1.admin_brand.product.edit',
            compact('data','vdata','cf'));
    }

    //编辑暂存的产品
    private function edit_temp_product($data,$brand,$vdata,$cf)
    {
        //获取log
        $log = TmpProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->where('target_product_id',$data->id)
            ->orderBy('id','desc')
            ->first();
        if(!$log){
            return '暂无相关信息';
        }

        $data->edit_type = 'temp';


        $data = $this->get_edit_log_data($data,$log);

        return $this->get_view('v1.admin_brand.product.edit',
            compact('data','vdata','cf'));
    }

    //获取编辑 未通过（待审核/已驳回）、暂存 产品时的blade模板data
    private function get_edit_log_data($data,$log)
    {
        $content = unserialize($log->content);
        $data->name = isset($content['name'])?$content['name']:'';
        $data->log_id = $log->id;
        //所属父产品
        $data->parent_name = '';
        if(isset($content['parent_id'])){
            $parent_product = ProductCeramic::query()
                ->where('type',ProductCeramic::TYPE_PRODUCT)
                ->find($content['parent_id']);
            if($parent_product){
                $data->parent_name = $parent_product->name;
            }
        }
        //系列
        $data->series_id = isset($content['series_id'])?$content['series_id']:0;
        //产品结构
        $data->structure_id = isset($content['structure_id'])?$content['structure_id']:0;
        //规格
        $data->spec_id = isset($content['spec_id'])?$content['spec_id']:0;
        //应用类别
        $data->apply_categories_ids = isset($content['apply_categories'])?$content['apply_categories']:[];
        //产品编号
        $data->code = isset($content['code'])?$content['code']:[];
        //产品图
        $data->photo_product = isset($content['photo_product'])?$content['photo_product']:'';
        //实物图
        $data->photo_practicality = isset($content['photo_practicality'])?$content['photo_practicality']:'';
        //核心技术
        $data->key_technology = isset($content['key_technology'])?$content['key_technology']:'';
        //理化性能
        $data->physical_chemical_property = isset($content['physical_chemical_property'])?$content['physical_chemical_property']:'';
        //功能特征
        $data->function_feature = isset($content['function_feature'])?$content['function_feature']:'';
        //客户价值
        $data->customer_value = isset($content['customer_value'])?$content['customer_value']:'';
        //产品视频
        $data->photo_video = isset($content['photo_video'])?$content['photo_video']:'';

        //工艺类别
        $data->technology_categories_ids = isset($content['technology_categories'])?$content['technology_categories']:[];
        //表面特征
        $data->surface_features_ids = isset($content['surface_features'])?$content['surface_features']:[];
        //色系
        $data->colors_ids = isset($content['colors'])?$content['colors']:[];
        //可应用空间风格
        $data->styles_ids = isset($content['styles'])?$content['styles']:[];
        //产品配件
        $data->accessories_data = isset($content['accessories'])?$content['accessories']:[];
        //产品搭配
        $data->collocations_product = [];
        if(isset($content['collocations'])){
            $collocations = $content['collocations'];
            for($i=0;$i<count($collocations);$i++){
                if(isset($collocations[$i]['collocation_id'])){
                    $product = ProductCeramic::find($collocations[$i]['collocation_id']);
                    if(!$product){
                        $product = new \stdClass();
                    }
                    $product->collocation_note = isset($collocations[$i]['note'])?$collocations[$i]['note']:'';
                    $product->photo = isset($collocations[$i]['photo'])?$collocations[$i]['photo']:'';
                    $product->collocation_id = isset($collocations[$i]['collocation_id'])?$collocations[$i]['collocation_id']:'';
                }else{
                    $product = new \stdClass();
                    $product->collocation_note = isset($collocations[$i]['note'])?$collocations[$i]['note']:'';
                    $product->photo = isset($collocations[$i]['photo'])?$collocations[$i]['photo']:'';
                    $product->collocation_id = 0;
                    $product->name = '';
                }
                $collocations[$i] = $product;
            }
            $data->collocations_product = $collocations;
        }
        //空间应用说明
        $data->spaces_data = isset($content['spaces'])?$content['spaces']:[];

        return $data;
    }
}
