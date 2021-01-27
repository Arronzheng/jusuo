<?php

namespace App\Http\Controllers\v1\admin\platform\online_class;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\HttpService;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\OnlineClassBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class OnlineClassController extends VersionController
{

    public function index(Request $request)
    {


        return $this->get_view('v1.admin_platform.online_class.index');
    }



    //编辑密码页
    public function edit($id)
    {
        $data  = OnlineClassBrand::find($id);

        return $this->get_view('v1.admin_platform.online_class.edit',compact('data'));
    }
}
