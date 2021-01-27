<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralLogBrand extends Model
{
    //
    const TYPE_DEPOSIT = 1;   //预存
    const TYPE_EXCHANGE = 2;  //商品兑换
    const TYPE_WITHDRAW = 3;  //提现
    const TYPE_ADMIN_ADD = 100;  //平台增加
    const TYPE_ADMIN_MINUS = 110;  //平台扣减

    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_DEPOSIT => '预存',
            self::TYPE_EXCHANGE => '商品兑换',
            self::TYPE_WITHDRAW => '提现',
            self::TYPE_ADMIN_ADD => '平台增加',
            self::TYPE_ADMIN_MINUS => '平台扣减',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
