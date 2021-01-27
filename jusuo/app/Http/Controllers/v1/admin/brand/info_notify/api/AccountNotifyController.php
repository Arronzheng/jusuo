<?php

namespace App\Http\Controllers\v1\admin\brand\info_notify\api;

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

use App\Models\MsgAccountBrand;
use App\Models\MsgSystemBrand;
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
        $brand_id = $loginAdmin->brand->id;
        $input = $request->all();

        if($loginAdmin->is_super_admin){
            $entry = MsgSystemBrand::query();
        }else{
            $entry = MsgAccountBrand::query()->where('administrator_id',$loginAdmin->id);
        }

        $entry->where('brand_id',$brand_id)
            ->orderBy('id','desc');

        $datas = $entry->paginate(10);

        $datas->transform(function($v) use($loginAdmin){
            if($loginAdmin->is_super_admin){
                $v->type_text = MsgSystemBrand::typeGroup($v->type);
                $v->is_read_text = MsgSystemBrand::isReadGroup($v->is_read);
            }else{
                $v->type_text = MsgAccountBrand::typeGroup($v->type);
                $v->is_read_text = MsgAccountBrand::isReadGroup($v->is_read);
            }
            $v->setReadApiUrl = url('admin/brand/info_notify/api/account_notify/'.$v->id.'/set_read');
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
            if($loginAdmin->is_super_admin){
                $entry = MsgSystemBrand::query();
                $msg = $entry->find($id);
                $msg->is_read = MsgSystemBrand::IS_READ_YES;
                $msg->save();
            }else{
                $entry = MsgAccountBrand::query()->where('administrator_id',$loginAdmin->id);
                $msg = $entry->find($id);
                $msg->is_read = MsgAccountBrand::IS_READ_YES;
                $msg->save();
            }


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

}
