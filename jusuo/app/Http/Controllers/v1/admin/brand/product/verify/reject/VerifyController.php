<?php

namespace App\Http\Controllers\v1\admin\brand\product\verify\reject;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
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
use App\Models\Style;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class VerifyController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $vdata = [];


        return $this->get_view('v1.admin_brand.product.verify.reject.index',compact('vdata'));
    }

    //查看详情
    public function detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            die('品牌信息错误');
        }

        $data = LogProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->find($id);
        if(!$data){
            die('数据不存在');
        }

        $content = unserialize($data->content);
        $product = $data->target_product;
        $data->name = $product->name;
        $data->status = $product->status;
        $data->visible = $product->visible;
        $data->code = $content['code'];

        //系列
        $data->spec_text = '';
        if(isset($content['series_id'])){
            $series = CeramicSeries::find($content['series_id']);
            if($series){$data->series_text = $series->name;}
        }
        //规格
        $data->spec_text = '';
        if(isset($content['spec_id'])){
            $spec = CeramicSpec::find($content['spec_id']);
            if($spec){$data->spec_text = $spec->name;}
        }

        //应用类别
        $data->apply_categories_text = '';
        if(isset($content['apply_categories'])){
            $apply_category_ids = $content['apply_categories'];
            $apply_categories = CeramicApplyCategory::whereIn('id',$apply_category_ids)->get()->pluck('name')->toArray();
            if(is_array($apply_categories) && count($apply_categories)>0){
                $data->apply_categories_text = implode(',',$apply_categories);
            }
        }

        //工艺类别
        $data->technology_categories_text = '';
        if(isset($content['technology_categories'])){
            $technology_category_ids = $content['technology_categories'];
            $technology_categories = CeramicTechnologyCategory::whereIn('id',$technology_category_ids)->get()->pluck('name')->toArray();
            if(is_array($technology_categories) && count($technology_categories)>0){
                $data->technology_categories_text = implode(',',$technology_categories);
            }
        }

        //表面特征
        $data->surface_features_text = '';
        if(isset($content['surface_features'])){
            $surface_features_ids = $content['surface_features'];
            $surface_features = CeramicSurfaceFeature::whereIn('id',$surface_features_ids)->get()->pluck('name')->toArray();
            if(is_array($surface_features) && count($surface_features)>0){
                $data->surface_features_text = implode(',',$surface_features);
            }
        }

        //色系
        $data->colors_text = '';
        if(isset($content['colors'])){
            $colors_ids = $content['colors'];
            $colors = CeramicColor::whereIn('id',$colors_ids)->get()->pluck('name')->toArray();
            if(is_array($colors) && count($colors)>0){
                $data->colors_text = implode(',',$colors);
            }
        }

        //可应用空间风格
        $data->styles_text = '';
        if(isset($content['styles'])){
            $styles_ids = $content['styles'];
            $styles = Style::whereIn('id',$styles_ids)->get()->pluck('name')->toArray();
            if(is_array($styles) && count($styles)>0){
                $data->styles_text = implode(',',$styles);
            }
        }

        $data->physical_chemical_property = isset($content['physical_chemical_property'])?$content['physical_chemical_property']:[];
        $data->function_feature = isset($content['function_feature'])?$content['function_feature']:[];
        $data->photo_product = isset($content['photo_product'])?$content['photo_product']:[];
        $data->photo_practicality = isset($content['photo_practicality'])?$content['photo_practicality']:[];
        $data->accessories = isset($content['accessories'])?$content['accessories']:[];
        $data->collocations = [];
        if(isset($content['collocations'])){
            $collocations = $content['collocations'];
            for($i=0;$i<count($collocations);$i++){
                $product = ProductCeramic::find($collocations[$i]['collocation_id']);
                $collocations[$i]['name'] = $product->name;
                $collocations[$i]['code'] = $product->code;
                $collocations[$i]['photo_product'] = $product->photo_product;
            }
            $data->collocations = $collocations;
        }
        $data->spaces = isset($content['spaces'])?$content['spaces']:[];
        $data->photo_video = isset($content['photo_video'])?$content['photo_video']:[];

        //die(\GuzzleHttp\json_encode($data->spaces));


        return $this->get_view('v1.admin_brand.product.verify.reject.detail',compact(
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

        return $this->get_view('v1.admin_brand.product.verify.reject.edit',compact('vdata','cf'));
    }

    //编辑页
    public function edit($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $data  = ProductCeramic::where('brand_id',$brand->id)->find($id);

        if(!$data){
            return back()->withErrors(['您没有相关权限']);
        }

        $physical_chemical_property = unserialize($data->physical_chemical_property);
        $data->physical_chemical_property = is_array($physical_chemical_property)?$physical_chemical_property:[];

        $function_feature = unserialize($data->function_feature);
        $data->function_feature = is_array($function_feature)?$function_feature:[];

        return $this->get_view('v1.admin_brand.product.verify.reject.edit',compact('data'));
    }
}
