<?php

namespace App\Http\Controllers\v1\admin\brand\info_statistics\activity\designer;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Models\Space;
use App\Models\Style;
use App\Services\v1\admin\StatisticDesignerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerController extends VersionController
{

    private $authService;
    private $getNameServices;
    private $areaRepository;


    public function __construct(
        AuthService $authService,
        GetNameServices $getNameServices,
        AreaRepository $areaRepository
    )
    {
        $this->authService = $authService;
        $this->getNameService = $getNameServices;
        $this->areaRepository = $areaRepository;
    }

    //账号列表
    public function index(\Illuminate\Http\Request $request)
    {

        $vdata = [];
        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $vdata['provinces'] = $provinces;

        return $this->get_view('v1.admin_brand.info_statistics.activity.designer.account_index',compact('vdata'));
    }

}
