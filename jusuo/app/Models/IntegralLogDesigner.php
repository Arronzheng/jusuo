<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralLogDesigner extends Model
{
    //
    const TYPE_RULE = 0;   //规则自动触发
    const TYPE_DEPOSIT = 1;  //充值
    const TYPE_EXCHANGE = 2;  //商品兑换
    const TYPE_ADMIN_ADD = 100;  //后台增加
    const TYPE_ADMIN_MINUS = 110;  //后台扣减
    const TYPE_CANCEL_EXCHANGE = 120;  //取消商品兑换

    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_RULE => '规则自动触发',
            self::TYPE_DEPOSIT => '充值',
            self::TYPE_EXCHANGE => '商品兑换',
            self::TYPE_ADMIN_ADD => '后台增加',
            self::TYPE_ADMIN_MINUS => '后台扣减',
            self::TYPE_CANCEL_EXCHANGE => '取消商品兑换',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const REASONS = [
        IntegralLogDesigner::TYPE_RULE => '规则自动触发',
        IntegralLogDesigner::TYPE_DEPOSIT => '充值',
        IntegralLogDesigner::TYPE_EXCHANGE => '商品兑换',
        IntegralLogDesigner::TYPE_ADMIN_ADD => '后台增加',
        IntegralLogDesigner::TYPE_ADMIN_MINUS => '后台扣减',
        IntegralLogDesigner::TYPE_CANCEL_EXCHANGE => '取消商品兑换',
    ];
}
