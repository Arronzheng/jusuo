<?php

namespace App\Http\Controllers\v1\admin\platform\album\verify\hd\free_designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\AlbumSection;
use App\Models\Area;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\Color;
use App\Models\HouseType;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\SpaceType;
use App\Models\Style;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class AlbumController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        /*$data = [];
        $data['design'] = [];
        $data['design']['photos'] = [];
        $data['design']['photos'][0]['url'] = '/storage/images/photo/product/12/03/wv/jMUIFRCHzKtrrdYZDeQ6molbVzIiahCtoJ2NRKKk.jpeg';
        $data['design']['photos'][1]['url'] = '/storage/images/photo/product/12/03/wv/jMUIFRCHzKtrrdYZDeQ6molbVzIiahCtoJ2NRKKk.jpeg';
        $data['design']['description'] = '我是空间设计说明';
        $data['product'] = [];
        $data['product']['photos'] = [];
        $data['product']['photos'][0]['url'] = '/storage/images/photo/product/12/03/wv/jMUIFRCHzKtrrdYZDeQ6molbVzIiahCtoJ2NRKKk.jpeg';
        $data['product']['photos'][1]['url'] = '/storage/images/photo/product/12/03/wv/jMUIFRCHzKtrrdYZDeQ6molbVzIiahCtoJ2NRKKk.jpeg';
        $data['product']['description'] = '我是产品应用说明';
        $data['build'] = [];
        $data['build']['photos'] = [];
        $data['build']['photos'][0]['url'] = '/storage/images/photo/product/12/03/wv/jMUIFRCHzKtrrdYZDeQ6molbVzIiahCtoJ2NRKKk.jpeg';
        $data['build']['photos'][1]['url'] = '/storage/images/photo/product/12/03/wv/jMUIFRCHzKtrrdYZDeQ6molbVzIiahCtoJ2NRKKk.jpeg';
        $data['build']['description'] = '我是施工说明';
        $result = serialize($data);
        $row = AlbumSection::find(1);
        $row->content = $result;
        $row->save();
        die(\GuzzleHttp\json_encode($row));*/

        $vdata = [];
        $vdata['styles'] = Style::all();
        $vdata['colors'] = CeramicColor::all();
        $vdata['house_types'] = HouseType::all();

        return $this->get_view('v1.admin_platform.album.verify.hd.free_designer.index',
            compact('vdata'
            ));
    }

    //查看详情
    public function detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $data = Album::query()
            ->find($id);
        if(!$data){
            die('数据不存在');
        }
        $province = Area::where('id',$data->address_province_id)->first();
        $city =  Area::where('id',$data->address_city_id)->first();
        $district =  Area::where('id',$data->address_area_id)->first();
        $data->area_text = $province?$province->name:''.$city?$city->name:''.$district?$district->name:'';
        //户型
        $house_types = $data->house_types()->get()->pluck('name')->toArray();
        $data->house_type_text = implode('/',$house_types);
        //方案风格
        $styles = $data->style()->get()->pluck('name')->toArray();
        $data->style_text = implode('/',$styles);
        //空间图
        $album_sections = $data->album_sections()->get();
        $album_sections->transform(function($section){
            $content = unserialize($section->content);
            $styles = $section->styles()->get()->pluck('name')->toArray();
            $section->style_text = implode('/',$styles);
            $space_type = SpaceType::find($section->space_type_id);
            $section->space_type_text = $space_type?$space_type->name:'';
            $section->content = $content;
            return $section;
        });
        $data->album_sections = $album_sections;
        //产品清单
        $product_ceramics = $data->product_ceramics()->get();
        $product_ceramics->transform(function($v){
            $v->brand_name = $v->brand->name;
            return $v;
        });
        $data->product_ceramics = $product_ceramics;

        return $this->get_view('v1.admin_platform.album.verify.hd.free_designer.detail',compact(
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

        return $this->get_view('v1.admin_platform.album.verify.hd.free_designer.edit',compact('vdata','cf'));
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

        return $this->get_view('v1.admin_platform.album.verify.hd.free_designer.edit',compact('data'));
    }
}
