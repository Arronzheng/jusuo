<?php

namespace App\Http\Controllers\v1\site\center\info_notify;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use App\Models\MsgAlbumDesigner;
use App\Services\v1\site\DesignerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoNotifyController extends VersionController
{

    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //方案通知
    public function index(\Illuminate\Http\Request $request)
    {

        $designer = Auth::user();
        $brandId = DesignerService::getDesignerBrandScope($designer->id);
        $__BRAND_SCOPE = $this->compressBrandScope($brandId);

        return $this->get_view('v1.site.center.info_notify.index',compact('__BRAND_SCOPE'));
    }


}
