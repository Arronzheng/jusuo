<?php

namespace App\Http\Controllers\v1\admin\brand\integral\goods;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\InfiniteTreeService;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class IntegralGoodsController extends VersionController
{
    private $infiniteTreeService;

    function __construct(InfiniteTreeService $infiniteTreeService )
    {
        $this->infiniteTreeService = $infiniteTreeService;
    }

    public function index(Request $request)
    {
        $brands = IntegralBrand::query()->get();

        $categories = IntegralGoodsCategory::query()->get();
        $categories = $this->infiniteTreeService->getFlatTree($categories,'pid');
        $categories = collect($categories)->transform(function($v){
            $v->name = str_repeat('|-- ',$v->level).$v->name;
            return $v;
        });

        return $this->get_view('v1.admin_brand.integral.goods.index',compact('brands','categories'));
    }

    //新增页
    public function create()
    {
        $brands = IntegralBrand::query()->get();

        $categories = IntegralGoodsCategory::query()->get();
        $categories = $this->infiniteTreeService->getFlatTree($categories,'pid');
        $categories = collect($categories)->transform(function($v){
            $v->name = str_repeat('|-- ',$v->level).$v->name;
            return $v;
        });
        return $this->get_view('v1.admin_brand.integral.goods.edit',compact('brands','categories'));
    }

    //编辑页
    public function edit($id)
    {
        $data  = IntegralGood::find($id);

        if(!$data){ die('暂无相关信息') ;}

        $brand = IntegralBrand::find($data->brand_id);

        $data->param_data = \Opis\Closure\unserialize($data->param);

        $data->brand_name = '';
        if($brand){
            $data->brand_name = $brand->brand_name;
        }

        $brands = IntegralBrand::query()->get();

        $categories = IntegralGoodsCategory::query()->get();
        $categories = $this->infiniteTreeService->getFlatTree($categories,'pid');
        $categories = collect($categories)->transform(function($v){
            $v->name = str_repeat('|-- ',$v->level).$v->name;
            return $v;
        });

        return $this->get_view('v1.admin_brand.integral.goods.edit',compact('data','brands','categories'));
    }
}
