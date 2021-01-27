<?php

namespace App\Http\Controllers\v1\admin\brand\info_statistics\product\ceramic;

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
use App\Services\v1\admin\LogProductSaleAreaService;
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

        $vdata = [];
        //按应用类别、工艺类别、色系、规格、产品状态、产品结构进行组合筛选
        //应用类别
        $apply_categories = CeramicApplyCategory::all();
        $technology_categories = CeramicTechnologyCategory::all();
        $colors = CeramicColor::all();
        $specs = CeramicSpec::all();
        $product_status = ProductCeramic::statusGroup();
        $visible_status = ProductCeramic::visibleGroup();
        $product_categories = ProductCategory::all();
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        $vdata['product_categories'] = $product_categories;
        $vdata['apply_categories'] = $apply_categories;
        $vdata['technology_categories'] = $technology_categories;
        $vdata['colors'] = $colors;
        $vdata['specs'] = $specs;
        $vdata['product_status'] = $product_status;
        $vdata['visible_status'] = $visible_status;
        $vdata['provinces'] = $provinces;
        //$vdata['series'] = $series;

        return $this->get_view('v1.admin_brand.info_statistics.product.ceramic.index',compact('vdata'));
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


}
