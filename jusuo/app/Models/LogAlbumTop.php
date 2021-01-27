<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAlbumTop extends Model
{
    //
    const OP_TYPE_TOP = 1;
    const OP_TYPE_CANCEL = 0;

    public static function opTypeGroup($key=null){
        $group = [
            self::OP_TYPE_TOP => '置顶',
            self::OP_TYPE_CANCEL => '取消置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const ORGANIZATION_TYPE_PLATFORM = 0;
    const ORGANIZATION_TYPE_BRAND = 1;
    const ORGANIZATION_TYPE_SELLER = 2;
    const ORGANIZATION_TYPE_DESIGNER = 3;

    public static function organizationTypeGroup($key=null){
        $group = [
            self::OP_TYPE_TOP => '置顶',
            self::OP_TYPE_CANCEL => '取消置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
