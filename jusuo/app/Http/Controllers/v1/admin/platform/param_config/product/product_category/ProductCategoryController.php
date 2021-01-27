<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\product\product_category;

use App\Http\Controllers\v1\VersionController;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class ProductCategoryController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_platform.param_config.product.product_category.index');
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_platform.param_config.product.product_category.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = ProductCategory::find($id);

        return $this->get_view('v1.admin_platform.param_config.product.product_category.edit',compact('data'));
    }
}
