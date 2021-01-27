<?php

namespace App\Http\Controllers\v1\admin\platform\privilege\brand;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\PrivilegeBrand;
use App\Models\TestData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class PrivilegeController extends VersionController
{
    private $authService;
    private $globalService;

    public function __construct(
        AuthService $authService,
        GlobalService $globalService
    )
    {
        $this->authService = $authService;
        $this->globalService = $globalService;
    }


    public function index(Request $request)
    {
        return $this->get_view('v1.admin_platform.privilege.brand.index');
    }

    public function nestable(Request $request)
    {
        $privileges = PrivilegeBrand::query()
            ->orderBy('level','asc')
            ->orderBy('path','asc')
            ->orderBy('sort','asc')
            ->where('shown',PrivilegeBrand::SHOWN_YES)
            ->get();

        $privileges_tree = $this->globalService->array_to_tree($privileges,0,0,'parent_id');


        return $this->get_view('v1.admin_platform.privilege.brand.nestable',compact('privileges_tree'));
    }

    //新增页
    public function create()
    {
        return $this->get_view('v1.admin_platform.privilege.brand.edit');
    }

    //编辑页
    public function edit($id)
    {
        $data  = PrivilegeBrand::find($id);

        if($data){
            if($data->parent_id){
                $parent_privilege = PrivilegeBrand::find($data->parent_id);

                if($parent_privilege){
                    $data->parent_display_name = $parent_privilege->display_name;
                }
            }
        }

        return $this->get_view('v1.admin_platform.privilege.brand.edit',compact('data'));
    }
}
