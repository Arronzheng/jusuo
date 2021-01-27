<?php

namespace App\Http\Controllers\v1\admin\platform\info_notify\api;

use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorDealer;

use App\Models\Area;
use App\Models\CertificationDealer;
use App\Models\DetailDealer;
use App\Models\LogDealerCertification;

use App\Models\MsgAccountPlatform;
use App\Models\OrganizationDealer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AccountNotifyController extends ApiController
{
    private $globalService;
    private $authService;

    public function __construct(GlobalService $globalService,
                                AuthService $authService
    ){
        $this->globalService = $globalService;
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $entry = MsgAccountPlatform::query()->where('administrator_id',$loginAdmin->id);


        $entry->orderBy('id','desc');

        $datas = $entry->paginate(10);

        $datas->transform(function($v) use($loginAdmin){
            $v->type_text = MsgAccountPlatform::typeGroup($v->type);
            $v->is_read_text = MsgAccountPlatform::isReadGroup($v->is_read);
            $v->setReadApiUrl = url('admin/platform/info_notify/api/account_notify/'.$v->id.'/set_read');
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    public function set_read($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        DB::beginTransaction();

        try{

            //更新状态
            $entry = MsgAccountPlatform::query()->where('administrator_id',$loginAdmin->id);
            $msg = $entry->find($id);
            $msg->is_read = MsgAccountPlatform::IS_READ_YES;
            $msg->save();


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

}
