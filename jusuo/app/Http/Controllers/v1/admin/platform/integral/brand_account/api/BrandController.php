<?php

namespace App\Http\Controllers\v1\admin\platform\integral\brand_account\api;

use App\Exports\IntegralBrandAccountLogExport;
use App\Http\Services\common\OrganizationService;
use App\Models\IntegralBrand;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\IntegralGood;
use App\Models\IntegralRechargeLog;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends ApiController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //表格异步获取数据
    public function integral_list(Request $request)
    {
        $export = $request->input('export',null);
        $limit = $request->input('limit',10);
        $keyword = $request->input('kw','');

        $entry = OrganizationBrand::query()
            ->select(['id','point_money','brand_name']);

        if($keyword){
            $entry->where('brand_name','like','%'.$keyword.'%');
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            $v->total_buy = 0;
            $v->last_buy_time = '';

            $recharges = IntegralRechargeLog::query()
                ->where('buyer_type',OrganizationService::ORGANIZATION_TYPE_BRAND)
                ->where('buyer_id',$v->id)
                ->where('pay_status',IntegralRechargeLog::PAY_STATUS_YES)
                ->orderBy('id','desc')
                ->get();

            if($recharges->count()>0){
                //累计购买积分
                $v->total_buy = $recharges->sum('integral');

                //最后一次购买时间
                $last = $recharges->first();
                $last = $last->toArray();
                if($last){
                    $v->last_buy_time = $last['created_at'];

                }
            }

            return $v;
        });

        if($export!=null){
            return $this->export_integral($datas);
        }


        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //导出表格
    private function export_integral($datas)
    {

        $result = [
            [
                'id','品牌名称','当前积分','累计购买积分','最后一次购买时间'
            ]
        ];


        foreach($datas as $v){
            $resultItem = [];
            $resultItem[] = $v->id;
            $resultItem[] = $v->brand_name;
            $resultItem[] = $v->point_money;
            $resultItem[] = $v->total_buy;
            $resultItem[] = $v->last_buy_time;

            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new IntegralBrandAccountLogExport($result),'品牌积分记录'.date('Y-m-d_H_i_s') . '.xls');
    }


    //表格异步获取数据
    public function recharge_list(Request $request)
    {
        $export = $request->input('export',null);
        $limit = $request->input('limit',10);
        $keyword = $request->input('kw','');
        $brand_id = $request->input('b',0);

        $entry = DB::table('integral_recharge_logs as l')
            ->where('l.buyer_type',OrganizationService::ORGANIZATION_TYPE_BRAND)
            ->join('organization_brands as b','b.id','=','l.buyer_id')
            ->leftJoin('integral_log_brands as l1','l1.id','=','l.integral_log_id')
            ->select(['l.id','b.brand_name','l.pay_time','l.money','l.integral','l1.available_integral']);

        if($keyword){
            $entry->where('b.brand_name','like','%'.$keyword.'%');
        }

        if($brand_id>0){
            $entry->where('buyer_id',$brand_id);
        }

        $entry->orderBy('l.id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){


            return $v;
        });

        if($export!=null){
            return $this->export_integral($datas);
        }


        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

}