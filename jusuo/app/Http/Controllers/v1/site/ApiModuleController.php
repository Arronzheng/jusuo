<?php

namespace App\Http\Controllers\v1\site;

use App\Http\Controllers\ApiRootController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiModuleController extends ApiRootController
{
    //子模块下的api控制器（比如控制整个用户端模块的api功能，再往上就是整个项目的ApiRootController）

    //本文件夹下的其他api相关Controller请继承本Controller

    //模块业务码
    //....

    public function __construct()
    {

    }
    
}
