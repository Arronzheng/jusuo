<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsgAlbumBrand extends Model
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

    const TYPE_SWITCH_BY_PLATFORM = 1;

    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_SWITCH_BY_PLATFORM => '方案被平台上下架',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
