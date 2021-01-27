<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAlbumDownload extends Model
{
    //
    const OP_TYPE_DOWNLOAD = 0;   //下载
    const OP_TYPE_COPY = 1;  //复制

    public static function opTypeGroup($key=NULL){
        $group = [
            self::OP_TYPE_DOWNLOAD => '下载',
            self::OP_TYPE_COPY => '复制'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
