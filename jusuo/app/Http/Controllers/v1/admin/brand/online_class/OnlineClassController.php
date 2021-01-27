<?php

namespace App\Http\Controllers\v1\admin\brand\online_class;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\NewsBrand;
use App\Models\OnlineClassBrand;
use App\Models\OnlineClassDesigner;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class OnlineClassController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $admin = $this->authService->getAuthUser();
        $brand = $admin->brand;
        $class_brand = OnlineClassBrand::where('brand_id',$brand->id)->first();

        return $this->get_view('v1.admin_brand.online_class.index',compact('class_brand'));

    }


    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_brand.online_class.edit');
    }

    //编辑密码页
    public function edit($id)
    {
        $data  = OnlineClassDesigner::find($id);

        return $this->get_view('v1.admin_brand.online_class.edit',compact('data'));
    }

}
