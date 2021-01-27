<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogIntegralGoodAuthorization extends Model
{
    //
    const LOG_TYPE_SHOW = 0;

    public static function logTypeGroup($key=null){
        $group = [
            self::LOG_TYPE_SHOW => '开放授权',
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
            self::LOG_TYPE_OPERATION_AUTHORIZE => '开放',
            self::LOG_TYPE_OPERATION_CANCEL => '取消开放',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
