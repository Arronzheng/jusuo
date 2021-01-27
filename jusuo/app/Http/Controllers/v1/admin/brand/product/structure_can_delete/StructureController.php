<?php

namespace App\Http\Controllers\v1\admin\brand\product\structure_can_delete;

use App\Http\Controllers\v1\VersionController;
use App\Models\ProductCeramicStructure;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class StructureController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_brand.product.structure.index');
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_brand.product.structure.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = ProductCeramicStructure::find($id);

        return $this->get_view('v1.admin_brand.product.structure.edit',compact('data'));
    }
}
