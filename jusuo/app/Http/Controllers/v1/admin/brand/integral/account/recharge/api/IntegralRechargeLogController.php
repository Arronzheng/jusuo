<?php

namespace App\Http\Controllers\v1\admin\brand\integral\account\recharge\api;

use App\Exports\IntegralGoodExport;
use App\Http\Services\common\OrganizationService;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\IntegralGoodAuthorization;
use App\Models\IntegralGoodsCategory;
use App\Models\IntegralLogBrand;
use App\Models\IntegralRechargeLog;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IntegralRechargeLogController extends ApiController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }


    //表格异步获取数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $limit = $request->input('limit',20);

        $entry = DB::table('integral_log_brands as log')
            ->select(['log.id','order.pay_time','order.money','log.integral','log.available_integral'])
            ->where('log.brand_id',$brand->id)
            ->where('log.type',IntegralLogBrand::TYPE_DEPOSIT)
            ->join('integral_recharge_logs as order','order.id','=','log.recharge_id');


        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

}