<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsgSystemBrand extends Model
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
    const TYPE_INTEGRAL_GOOD_AUTHORIZE = 3;
    const TYPE_UPDATE_PRIVILEGE = 4;



    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_CERTIFICATION => '品牌资料认证',
            self::TYPE_MODIFY_PWD => '密码被修改',
            self::TYPE_INTEGRAL_GOOD_AUTHORIZE => '积分商品开放',
            self::TYPE_UPDATE_PRIVILEGE => '权限更新'

        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
