<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralRechargeLog extends Model
{
    //
    const PAY_TYPE_WECHAT = 1;   //微信支付

    public static function statusGroup($key=NULL){
        $group = [
            self::PAY_TYPE_WECHAT => '微信支付',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }


    //
    const PAY_STATUS_YES = 1;   //已支付
    const PAY_STATUS_NO = 0;   //未支付

    public static function payStatusGroup($key=NULL){
        $group = [
            self::PAY_STATUS_YES => '已支付',
            self::PAY_STATUS_NO => '未支付',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function getOrderNo()
    {
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
