<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQa extends Model
{
    //
    const STATUS_ON = 1;   //正常
    const STATUS_OFF = 0;  //禁用

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_ON => '正常',
            self::STATUS_OFF => '禁用'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //所属产品
    public function product()
    {
        return $this->belongsTo(ProductCeramic::class, 'product_id');
    }

    //提问者
    public function designer()
    {
        return $this->belongsTo(Designer::class, 'question_designer_id');
    }
}
