<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\basic_info;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\HouseTypeRepository;
use App\Http\Repositories\common\MemberTypeRepository;
use App\Http\Repositories\common\SpaceRepository;
use App\Http\Repositories\common\SpaceTypeRepository;
use App\Http\Repositories\common\StyleRepository;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SellerController extends VersionController
{

    private $authService;

    public function __construct(

        AuthService $authService
    )
    {
        $this->authService = $authService;


    }

    public function index()
    {

        return $this->get_view('v1.admin_platform.param_config.basic_info.seller');
    }

}