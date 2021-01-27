<?php

namespace App\Http\Controllers\v1\admin\seller\info_notify;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumNotifyController extends VersionController
{

    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //账号通知
    public function index(\Illuminate\Http\Request $request)
    {
        return $this->get_view('v1.admin_seller.info_notify.album_notify');
    }


}
