<?php

namespace App\Http\Controllers\v1\admin\brand\banner;

use App\Http\Controllers\v1\VersionController;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class BannerController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_brand.banner.index');
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_brand.banner.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = Banner::find($id);

        return $this->get_view('v1.admin_brand.banner.edit',compact('data'));
    }
}
