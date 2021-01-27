<?php

namespace App\Http\Controllers\v1\admin\platform\integral\category;

use App\Http\Controllers\v1\VersionController;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\IntegralBrand;
use App\Models\IntegralGoodAuthorization;
use App\Models\IntegralGoodsCategory;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class CategoryController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_platform.integral.category.index');
    }

    //新增页
    public function create()
    {

        //一级分类
        $cat_lv1 = IntegralGoodsCategory::query()->where('pid',0)->get();

        return $this->get_view('v1.admin_platform.integral.category.edit',compact('cat_lv1'));
    }

    //编辑页
    public function edit($id)
    {
        $data  = IntegralGoodsCategory::find($id);

        //一级分类
        $cat_lv1 = IntegralGoodsCategory::query()->where('pid',0)->get();

        return $this->get_view('v1.admin_platform.integral.category.edit',compact('data','cat_lv1'));
    }
}
