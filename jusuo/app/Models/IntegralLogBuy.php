<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralLogBuy extends Model
{
    //
    const STATUS_TO_BE_SENT = 0;   //待发货
    const STATUS_SENT = 1;  //已发货
    const STATUS_REJECTED = 2;  //已拒绝
    const STATUS_CANCELED = 3;  //已取消

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_TO_BE_SENT => '待发货',
            self::STATUS_SENT => '已发货',
            self::STATUS_REJECTED => '已拒绝',
            self::STATUS_CANCELED => '已取消'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //商品
    public function good()
    {
        return $this->belongsTo(IntegralGood::class, 'goods_id');
    }
}
