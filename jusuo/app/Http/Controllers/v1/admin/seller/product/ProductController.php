<?php

namespace App\Http\Controllers\v1\admin\seller\product;

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
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\Style;
use App\Models\TestData;
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
        $vdata = [];
        //按应用类别、工艺类别、色系、规格、产品状态、产品结构进行组合筛选
        //应用类别
        $apply_categories = CeramicApplyCategory::all();
        $technology_categories = CeramicTechnologyCategory::all();
        $colors = CeramicColor::all();
        $specs = CeramicSpec::all();
        $product_status = ProductCeramic::statusGroup();
        $visible_status = ProductCeramic::visibleGroup();
        $vdata['apply_categories'] = $apply_categories;
        $vdata['technology_categories'] = $technology_categories;
        $vdata['colors'] = $colors;
        $vdata['specs'] = $specs;
        $vdata['product_status'] = $product_status;
        $vdata['visible_status'] = $visible_status;

        return $this->get_view('v1.admin_seller.product.index',compact('vdata'));
    }


    //去前端预览产品详情
    public function preview_product_detail($web_id_code)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->dealer->brand;

        $preview_brand_id = $brand->id;
        session()->put('preview_brand_id',$preview_brand_id);

        return redirect('/product/sm/'.$web_id_code);
    }

    //查看详情
    public function detail($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        $brand = $seller->brand;
        if(!$seller){
            die('销售商信息错误');
        }

        $parent_seller_id = 0;
        if($seller->level==2){
            $parent_seller = $seller->parent_dealer;
            $parent_seller_id = $parent_seller->id;
        }

        $data = ProductCeramic::query()
            ->whereHas('dealer',function($query)use($seller,$parent_seller_id){
                $query->where('organization_dealers.id',$seller->id);
                if($parent_seller_id){
                    $query->orWhere('organization_dealers.id',$parent_seller_id);
                }
            })
            ->where('brand_id',$brand->id)
            ->find($id);
        if(!$data){
            die('数据不存在');
        }

        //系列
        $data->spec_text = '';
        $series = CeramicSeries::find($data->series_id);
        if($series){$data->series_text = $series->name;}
        //规格
        $data->spec_text = '';
        $spec = CeramicSpec::find($data->spec_id);
        if($spec){$data->spec_text = $spec->name;}
        //应用类别
        $data->apply_categories_text = '';
        $apply_categories = $data->apply_categories()->get()->pluck('name')->toArray();
        if(is_array($apply_categories) && count($apply_categories)>0){
            $data->apply_categories_text = implode(',',$apply_categories);
        }
        //工艺类别
        $data->technology_categories_text = '';
        $technology_categories = $data->technology_categories()->get()->pluck('name')->toArray();
        if(is_array($technology_categories) && count($technology_categories)>0){
            $data->technology_categories_text = implode(',',$technology_categories);
        }
        //表面特征
        $data->surface_features_text = '';
        $surface_features = $data->surface_features()->get()->pluck('name')->toArray();
        if(is_array($surface_features) && count($surface_features)>0){
            $data->surface_features_text = implode(',',$surface_features);
        }
        //色系
        $data->colors_text = '';
        $colors = $data->colors()->get()->pluck('name')->toArray();
        if(is_array($colors) && count($colors)>0){
            $data->colors_text = implode(',',$colors);
        }
        //可应用空间风格
        $data->styles_text = '';
        $styles = $data->styles()->get()->pluck('name')->toArray();
        if(is_array($styles) && count($styles)>0){
            $data->styles_text = implode(',',$styles);
        }

        //die(\GuzzleHttp\json_encode($data->spaces));


        return $this->get_view('v1.admin_seller.product.detail',compact(
            'data'
        ));

    }

}
