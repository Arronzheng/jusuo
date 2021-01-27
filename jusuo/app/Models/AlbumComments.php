<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumComments extends Model
{
    //
    protected $table = 'album_comments';

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

    public function album(){
        return $this->belongsTo(Album::class,'album_id');
    }

    //评论者
    public function designer(){
        return $this->belongsTo(Designer::class,'designer_id');
    }


}
