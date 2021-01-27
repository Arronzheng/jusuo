<?php

namespace App\Http\Controllers\v1\admin\brand\album\comment;

use App\Http\Controllers\v1\VersionController;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\CeramicSeries;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class CommentController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_brand.album.comment.index');
    }

    //编辑页
    public function edit($id)
    {
        $data  = AlbumComments::find($id);

        return $this->get_view('v1.admin_brand.album.comment.edit',compact('data'));
    }
}
