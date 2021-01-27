<?php

namespace App\Http\Controllers\v1\admin\brand\product\comment;

use App\Http\Controllers\v1\VersionController;
use App\Models\AlbumComments;
use App\Models\CeramicSeries;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductQa;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class CommentController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_brand.product.comment.index');
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_brand.product.comment.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = ProductQa::find($id);

        return $this->get_view('v1.admin_brand.product.comment.edit',compact('data'));
    }
}
