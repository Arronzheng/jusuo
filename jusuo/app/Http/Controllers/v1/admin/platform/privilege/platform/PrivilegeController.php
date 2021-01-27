<?php

namespace App\Http\Controllers\v1\admin\platform\privilege\platform;

use App\Http\Controllers\v1\VersionController;
use App\Models\PrivilegePlatform;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class PrivilegeController extends VersionController
{

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_platform.privilege.platform.index');
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_platform.privilege.platform.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = PrivilegePlatform::find($id);

        if($data){
            if($data->parent_id){
                $parent_privilege = PrivilegePlatform::find($data->parent_id);

                if($parent_privilege){
                    $data->parent_display_name = $parent_privilege->display_name;
                }
            }
        }

        return $this->get_view('v1.admin_platform.privilege.platform.edit',compact('data'));
    }
}
