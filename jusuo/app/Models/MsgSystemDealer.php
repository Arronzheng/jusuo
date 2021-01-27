<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsgSystemDealer extends Model
{
    //
    const IS_READ_YES = 1;
    const IS_READ_NO = 0;

    public static function isReadGroup($key=NULL){
        $group = [
            self::IS_READ_YES => '是',
            self::IS_READ_NO => '否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TYPE_CERTIFICATION = 1;
    const TYPE_MODIFY_PWD = 2;


    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_CERTIFICATION => '销售商资料认证',
            self::TYPE_MODIFY_PWD => '密码被修改'

        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
