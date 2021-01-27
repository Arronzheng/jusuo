<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\api;

use App\Http\Controllers\ApiRootController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiController extends ApiRootController
{
    //本文件夹下的其他api相关Controller请继承本Controller

    //模块业务码
    //....

    public function __construct()
    {

    }

//    public function get_privilege(){
//        $privilege = DB::table('role_privilege_organizations')
//            ->leftJoin('privilege_organizations', 'role_privilege_organizations.privilege_id', '=', 'privilege_organizations.id')
//            ->where('privilege_organizations.parent_id','=',0)
//            ->where('role_privilege_organizations.role_id','=',Auth::guard('brand')->user()->role_id)
//            ->where('privilege_organizations.is_menu','=',0)
//            ->where('privilege_organizations.organization_type','=',1)
//            ->where('privilege_organizations.shown','=',1)
//            ->where('privilege_organizations.description','=','button_privilege')
//            ->pluck('privilege_id')
//            ->toArray();
//        return $privilege;
//    }

}
