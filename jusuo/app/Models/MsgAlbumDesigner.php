<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsgAlbumDesigner extends Model
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

    const TYPE_VERIFICATION = 1;

    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_VERIFICATION => '方案审核',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
