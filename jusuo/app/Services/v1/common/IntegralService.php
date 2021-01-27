<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/17 0017
 * Time: 16:03
 */

namespace App\Services\v1\common;


use App\Models\DesignerDetail;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use Illuminate\Support\Facades\DB;

class IntegralService
{

    //用户主动取消兑换逻辑
    public function cancel_exchange($order_id,$designer_id)
    {

        $order = IntegralLogBuy::query()
            ->where('designer_id',$designer_id)
            ->find($order_id);

        if(!$order){
            return $this->respFail('信息不存在！');
        }

        if($order->status != IntegralLogBuy::STATUS_TO_BE_SENT){
            return $this->respFail('订单状态异常，无法取消！');
        }

        //判断用户积分
        $designer_detail = DesignerDetail::query()->where('designer_id',$designer_id)->first();
        if(!$designer_detail){
            return $this->respFail('用户信息不存在');
        }


        $before_integral = intval($designer_detail->point_money);

        //需返还的积分
        $add_integral = intval($order->total);

        try{

            DB::beginTransaction();

            //返还后用户剩余积分
            $after_integral = $before_integral + $add_integral;

            //返还用户积分
            $designer_detail->increment('point_money',$add_integral);

            //记录用户积分改变
            $log = new IntegralLogDesigner();
            $log->designer_id = $designer_id;
            $log->type = IntegralLogDesigner::TYPE_ADMIN_ADD;
            $log->integral = $add_integral;
            $log->available_integral = $after_integral;
            $log->remark = IntegralLogDesigner::REASONS[IntegralLogDesigner::TYPE_CANCEL_EXCHANGE];
            $log->save();

            //取消订单
            $order->status = IntegralLogBuy::STATUS_CANCELED;
            $order->save();

            DB::commit();


            return $this->respData([]);

        }catch(\Exception $e){

            DB::rollback();
            return $this->respFail('系统错误！'.$e->getMessage());
        }
    }


    private function respFail($msg)
    {
        return [
            'status' => 0,
            'msg' => $msg,
        ];
    }

    private function respData($msg)
    {
        return [
            'status' => 1,
            'msg' => 'success',
        ];
    }

}