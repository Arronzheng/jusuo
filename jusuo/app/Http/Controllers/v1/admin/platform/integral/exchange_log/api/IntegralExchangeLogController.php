<?php

namespace App\Http\Controllers\v1\admin\platform\integral\exchange_log\api;

use App\Exports\IntegralGoodExport;
use App\Exports\IntegralLogBuyExport;
use App\Http\Services\common\OrganizationService;
use App\LogisticsCompany;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
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
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\IntegralRechargeLog;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\OrganizationDealerService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\DesignerService;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IntegralExchangeLogController extends ApiController
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
        $export = $request->input('export',null);

        $limit = $request->input('limit',20);

        //获取本次兑换列表
        $entry = IntegralLogBuy::query()
            ->select([
                'id','designer_id','goods_id','count','total','receiver_name','receiver_tel',
                'receiver_address','full_address','status','created_at','sent_at','logistics_company',
                'logistics_code'
            ])
            //筛选平台商品
            ->whereHas('good',function($query){
                $query->where('integral_goods.brand_id',0);
            });

        $datas =$entry->paginate(intval($limit));

        $data_designer_ids = collect($datas->items())->pluck('designer_id')->toArray();
        $data_designers = Designer::query()
            ->whereIn('id',$data_designer_ids)
            ->get();

        //获取本次查询的设计师详情信息
        $data_designer_details = DesignerDetail::query()
            ->whereIn('designer_id',$data_designer_ids)
            ->get();

        //获取本次查询的积分商品信息
        $data_good_ids = collect($datas->items())->pluck('goods_id')->toArray();
        $data_goods = IntegralGood::query()
            ->whereIn('id',$data_good_ids)
            ->get();

        //获取本次查询的物流公司信息
        $data_logistics_ids = collect($datas->items())->pluck('logistics_company')->toArray();
        $data_logistics = LogisticsCompany::query()
            ->whereIn('id',$data_logistics_ids)
            ->get();

        $datas->transform(function($v) use($data_designer_details,$data_goods,$data_logistics,$data_designers){


            $v->nickname = '';
            $v->realname = '';
            $designer_detail = $data_designer_details->where('designer_id',$v->designer_id)->first();
            if($designer_detail){
                $v->nickname = $designer_detail->nickname;
                $v->realname = $designer_detail->realname;
            }

            $v->mobile = '';
            $designer = $data_designers->where('id',$v->designer_id)->first();
            if($designer){
                $v->mobile = $designer->login_mobile;
            }

            $v->logistics_company_name = '';
            $logistics_company = $data_logistics->where('id',$v->logistics_company)->first();
            if($logistics_company){
                $v->logistics_company_name = $logistics_company->name;
            }

            //是否可处理
            $v->can_handle = false;
            $v->good_name = '';
            $v->rejectApiUrl = '';
            $v->sendApiUrl = '';
            $good = $data_goods->where('id',$v->goods_id)->first();
            if($good){
                $v->good_name = $good->name;
                //积分商品属于平台且兑换状态为待发货才能处理
                if($good->brand_id == 0 && $v->status == IntegralLogBuy::STATUS_TO_BE_SENT){
                    $v->can_handle = true;
                    $v->rejectApiUrl = url('/admin/platform/integral/exchange_log/api/'.$v->id.'/reject');
                    $v->sendApiUrl = url('/admin/platform/integral/exchange_log/api/'.$v->id.'/send');
                }
            }

            $v->status_text = IntegralLogBuy::statusGroup($v->status);

            return $v;
        });

        if($export!=null){
            return $this->export($datas);
        }

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //导出表格
    private function export($datas)
    {

        $result = [
            [
                'id','昵称','真实姓名','手机','商品','数量','消耗积分','兑换时间','收货人','收货电话',
                '地址','状态','发货物流公司','物流单号','发货时间'
            ]
        ];

        foreach($datas as $v){
            $resultItem = [];
            $resultItem[] = $v->id;
            $resultItem[] = $v->nickname;
            $resultItem[] = $v->realname;
            $resultItem[] = $v->mobile;
            $resultItem[] = $v->good_name;
            $resultItem[] = $v->count;
            $resultItem[] = $v->total;
            $resultItem[] = $v->created_at;
            $resultItem[] = $v->receiver_name;
            $resultItem[] = $v->receiver_tel;
            $resultItem[] = $v->full_address;
            $resultItem[] = $v->status_text;
            $resultItem[] = $v->logistics_company_name;
            $resultItem[] = $v->logistics_code;
            $resultItem[] = $v->sent_at;

            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new IntegralLogBuyExport($result),'积分商品兑换记录'.date('Y-m-d_H_i_s') . '.xls');
    }


    //拒绝兑换
    public function reject($id,Request $request)
    {
        $remark = $request->input('remark','');
        if(!$remark){
            $this->respFail('请填写理由');

        }

        $exchange_log = IntegralLogBuy::query()
            ->find($id);

        if(!$exchange_log){
            $this->respFail('信息不存在');
        }

        if($exchange_log->status != IntegralLogBuy::STATUS_TO_BE_SENT){
            $this->respFail('兑换记录状态异常，无法操作');
        }

        //设计师
        $designer = Designer::query()
            ->find($exchange_log->designer_id);
        if(!$designer){
            $this->respFail('权限不足');
        }

        //判断积分商品是否平台商品
        $good = IntegralGood::query()
            ->where('brand_id',0)
            ->find($exchange_log->goods_id);
        if(!$good){
            $this->respFail('权限不足1');
        }

        //订单积分
        $total = intval($exchange_log->total);

        //退还前用户积分
        $designer_detail = DesignerDetail::where('designer_id',$designer->id)->first();
        $before_integral = intval($designer_detail->point_money);

        //退还后用户剩余积分
        $after_integral = $before_integral + $total;

        try{

            DB::beginTransaction();

            //处理拒绝

            //退回用户积分
            $designer_detail->increment('point_money',$total);

            //记录用户积分改变
            $log = new IntegralLogDesigner();
            $log->designer_id = $designer->id;
            $log->type = IntegralLogDesigner::TYPE_ADMIN_ADD;
            $log->integral = $total;
            $log->available_integral = $after_integral;
            $log->remark = IntegralLogDesigner::REASONS[IntegralLogDesigner::TYPE_EXCHANGE];
            $log->save();

            //状态修改至已拒绝
            $exchange_log->status = IntegralLogBuy::STATUS_REJECTED;
            $exchange_log->remark = $remark;
            $exchange_log->save();

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误');

        }



    }

    //兑换发货
    public function send($id,Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'logistics_company' => 'required',
            'logistics_code' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $loginAdmin = $this->authService->getAuthUser();

        $exchange_log = IntegralLogBuy::query()
            ->find($id);

        if(!$exchange_log){
            $this->respFail('信息不存在');
        }

        //设计师
        $designer = Designer::query()
            ->find($exchange_log->designer_id);
        if(!$designer){
            $this->respFail('权限不足');
        }

        //检查该设计师所属品牌是否有足够积分扣减
        $order_total = intval($exchange_log->total);
        $organization_brand = null;
        $organization_brand_id = DesignerService::getDesignerBrandScope($designer->id);
        $organization_brand = OrganizationBrand::find($organization_brand_id);
        if(!$organization_brand){
            $this->respFail('品牌组织信息不存在');
        }
        $organization_brand_integral = intval($organization_brand->point_money);
        if($organization_brand_integral < $order_total){
            $this->respFail('品牌积分不足以扣减，无法发货。品牌积分为：'.$organization_brand_integral.'，所需积分：'.$order_total);
        }

        //物流公司是否存在
        $logistics_company = LogisticsCompany::find($input_data['logistics_company']);
        if(!$logistics_company){
            $this->respFail('物流公司信息不存在');
        }

        if($exchange_log->status != IntegralLogBuy::STATUS_TO_BE_SENT){
            $this->respFail('兑换记录状态异常，无法操作');
        }


        //判断积分商品是否平台商品
        $good = IntegralGood::query()
            ->where('brand_id',0)
            ->find($exchange_log->goods_id);
        if(!$good){
            $this->respFail('权限不足1');
        }

        try{

            DB::beginTransaction();

            //处理发货
            $before_integral = intval($organization_brand_integral);
            $add_integral = intval($order_total);
            $after_integral = $before_integral-$add_integral;

            //记录品牌积分变化
            $log = new IntegralLogBrand();
            $log->brand_id = $organization_brand->id;
            $log->type = IntegralLogBrand::TYPE_EXCHANGE;
            $log->integral = $add_integral;
            $log->available_integral = $after_integral;
            $log->remark = '兑换平台商品扣减';
            $log->exchange_id = $exchange_log->id;
            $result = $log->save();

            //扣减品牌积分
            DB::table('organization_brands')
                ->where('id',$organization_brand->id)
                ->decrement('point_money',$add_integral);

            //订单状态修改至已发货
            $exchange_log->status = IntegralLogBuy::STATUS_SENT;
            $exchange_log->logistics_company = $input_data['logistics_company'];
            $exchange_log->logistics_code = $input_data['logistics_code'];
            $exchange_log->sent_at = Carbon::now();
            $exchange_log->save();

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误');

        }



    }

}