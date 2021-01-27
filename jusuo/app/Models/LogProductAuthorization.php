<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogProductAuthorization extends Model
{
    //
    const PRODUCT_TYPE_CERAMIC = 0;

    public static function productTypeGroup($key=null){
        $group = [
            self::PRODUCT_TYPE_CERAMIC => '瓷砖',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const LOG_TYPE_SHOW = 0;
    const LOG_TYPE_STRUCTURE = 1;
    const LOG_TYPE_PRICE = 2;

    public static function logTypeGroup($key=null){
        $group = [
            self::LOG_TYPE_SHOW => '产品显示',
            self::LOG_TYPE_STRUCTURE => '产品结构',
            self::LOG_TYPE_PRICE => '产品价格',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const LOG_TYPE_OPERATION_AUTHORIZE = 0;
    const LOG_TYPE_OPERATION_CANCEL = 1;

    public static function logTypeOperationGroup($key=null){
        $group = [
            self::LOG_TYPE_OPERATION_AUTHORIZE => '授权',
            self::LOG_TYPE_OPERATION_CANCEL => '取消授权',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }


}
