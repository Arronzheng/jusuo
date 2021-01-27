<?php

namespace App\Http\Controllers\v1\admin\platform\integral\brand;

use App\Http\Controllers\v1\VersionController;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\IntegralBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class BrandController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_platform.integral.brand.index');
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_platform.integral.brand.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = IntegralBrand::find($id);

        return $this->get_view('v1.admin_platform.integral.brand.edit',compact('data'));
    }
}
