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

class IntegralRechargeController extends ApiController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //提交积分充值订单
    public function store(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $loginBrand = $loginAdmin->brand;

        $validator = Validator::make($input_data, [
            'integral' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //其他校验
        $integral = intval($input_data['integral']);

        DB::beginTransaction();

        try{

            //生成一个未支付的订单
            $order_no = IntegralRechargeLog::getOrderNo();
            $order = new IntegralRechargeLog();
            $order->buyer_type = OrganizationService::ORGANIZATION_TYPE_BRAND;
            $order->buyer_id = $loginBrand->id;
            $order->money = bcdiv($integral,100,2);
            $order->integral = $integral;
            $order->pay_type = IntegralRechargeLog::PAY_TYPE_WECHAT;
            $order->order_no = $order_no;
            $order->save();

            SystemLogService::simple('生成积分充值订单',array(
                \GuzzleHttp\json_encode($order)
            ));

            //生成微信支付码
            $config = config('wechat.payment.default');
            $app = Factory::payment($config);

            $unify_info = [
                'trade_type' => 'NATIVE',
                'product_id' => 'product_id', // $message['product_id'] 则为生成二维码时的产品 ID
                'body' => '积分充值',
                'out_trade_no' => $order_no,
                'total_fee' => $integral,
                'notify_url' => url("/admin/integral/brand_recharge_notify"), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            ];

            $result = $app->order->unify($unify_info);

            if(!isset($result['code_url'])){
                SystemLogService::simple('积分充值回调进入',array(
                    '$unify_info:'.\GuzzleHttp\json_encode($unify_info),
                    '$result:'.\GuzzleHttp\json_encode($result),
                ));

                $this->respFail('生成支付码失败！');
            }

            DB::commit();

            $this->respData([
                'code_url'=>$result['code_url'],
                'order_no' =>$order_no
            ]);

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！');

        }



    }

    //查询订单是否支付
    public function check_order(Request $request)
    {
        $order_no = $request->input('on','');

        if(!$order_no){
            $this->respFail('参数缺失');
        }

        $order = IntegralRechargeLog::where('order_no',$order_no)->first();
        if(!$order){
            $this->respFail('订单信息缺失');
        }

        if($order->pay_status == IntegralRechargeLog::PAY_STATUS_YES){
            $this->respData(['isPaid'=>true]);
        }else{
            $this->respData(['isPaid'=>false]);
        }
    }

    //积分充值订单回调
    public function recharge_notify(){

        SystemLogService::simple('积分充值回调进入',array(
            'test'
        ));

        $config = config('wechat.payment.default');
        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function($message, $fail)use($app){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单

            SystemLogService::simple('handlePaidNotify',array(
                '$message'.\GuzzleHttp\json_encode($message)
            ));

            $order = IntegralRechargeLog::where('order_no',$message['out_trade_no'])->first();;

            if (!$order || $order->status == IntegralRechargeLog::PAY_STATUS_YES) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            SystemLogService::simple('order',array(
                \GuzzleHttp\json_encode($order)
            ));

            //微信订单查询确认
            $wechat_order = $app->order->queryByOutTradeNumber($message['out_trade_no']);

            SystemLogService::simple('微信订单查询',array(
                \GuzzleHttp\json_encode($wechat_order)
            ));
            if ($wechat_order['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($wechat_order, 'result_code') === 'FAIL') {
                    return $fail('订单未支付成功，请稍后再通知我');
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {

                    SystemLogService::simple('用户支付成功',array(
                        \GuzzleHttp\json_encode($message)
                    ));

                    DB::beginTransaction();

                    try{

                        //修改订单信息
                        $order->pay_status = IntegralRechargeLog::PAY_STATUS_YES; // 更新支付时间为当前时间
                        $order->pay_no = $message['transaction_id']; // 微信支付订单号
                        $order->pay_time = Carbon::now();

                        //根据充值对象进行不同处理
                        switch($order->buyer_type){
                            case OrganizationService::ORGANIZATION_TYPE_BRAND:
                                //品牌充值积分
                                $brand_id = $order->buyer_id;
                                $brand = OrganizationBrand::find($brand_id);
                                if(!$brand){
                                    SystemLogService::simple('积分充值未找到品牌',array(
                                        '$order:'.\GuzzleHttp\json_encode($order),
                                        '$message:'.\GuzzleHttp\json_encode($message)
                                    ));
                                    return $fail('订单未处理成功，请稍后再通知我');
                                }

                                $before_integral = intval($brand->point_money);
                                $add_integral = intval($order->integral);
                                $after_integral = $before_integral+$add_integral;

                                //记录积分变化
                                $log = new IntegralLogBrand();
                                $log->brand_id = $brand_id;
                                $log->type = IntegralLogBrand::TYPE_DEPOSIT;
                                $log->integral = $add_integral;
                                $log->available_integral = $after_integral;
                                $log->remark = '充值成功';
                                $log->recharge_id = $order->id;
                                $result = $log->save();
                                if(!$result){
                                    DB::rollback();
                                    return $fail('订单未处理成功，请稍后再通知我');
                                }

                                //在充值订单上关联积分变动记录id
                                $order->integral_log_id = $log->id;

                                //真正增加品牌积分
                                DB::table('organization_brands')
                                    ->where('id',$brand_id)
                                    ->increment('point_money',$add_integral);
                                break;

                            default:break;
                        }


                        DB::commit();

                    }catch(\Exception $e){
                        DB::rollback();

                        SystemLogService::simple('积分充值抛出异常',array(
                            '$order:'.\GuzzleHttp\json_encode($order),
                            '$message:'.\GuzzleHttp\json_encode($message)
                        ));
                    }


                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {

                    //暂无处理行为
                    return $fail('支付未成功，请稍后再通知我');

                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            return true; // 返回处理完成
        });

        $response->send(); // return $response;
    }
}