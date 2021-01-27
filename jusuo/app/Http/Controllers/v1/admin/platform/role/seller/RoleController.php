<?php

namespace App\Http\Controllers\v1\admin\platform\role\seller;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\PrivilegeDealer;
use App\Models\RoleDealer;
use App\Models\TestData;
use App\Traits\AdminAuthTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class RoleController extends VersionController
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
        return $this->get_view('v1.admin_platform.role.seller.index');
    }

    //新增角色页
    public function create()
    {
        //获取角色可选权限（当前管理员所含权限)
        $loginAdmin = $this->authService->getAuthUser();
        $privileges = $loginAdmin->getAllPermissions()
            ->toArray();

        $privileges_tree = $this->globalService->array_to_tree($privileges,0,0,'parent_id');
        //die(json_encode($privileges_tree));

        return $this->get_view('v1.admin_platform.role.seller.edit');
    }

    //编辑页模板
    public function edit($id)
    {
        $data  = RoleDealer::find($id);

        //获取角色可选权限（当前管理员所含权限)
        $loginAdmin = $this->authService->getAuthUser();
        $privileges = $loginAdmin->getAllPermissions()
            ->toArray();

        $privileges_tree = $this->globalService->array_to_tree($privileges,0,0,'parent_id');

        if(!$data){
            return back();
        }

        $role_privileges = $data->permissions()->get()->pluck('id')->toArray();

        return $this->get_view('v1.admin_platform.role.seller.edit',compact(
            'data','privileges_tree','role_privileges'
        ));
    }
}
