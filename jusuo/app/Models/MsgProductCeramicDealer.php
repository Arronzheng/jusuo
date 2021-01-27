<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsgProductCeramicDealer extends Model
{
    //
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

    const TYPE_AUTHORIZATION = 1;
    const TYPE_AUTHORIZATION_STRUCTURE = 2;
    const TYPE_AUTHORIZATION_PRICE = 3;

    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_AUTHORIZATION => '产品被品牌授权',
            self::TYPE_AUTHORIZATION_STRUCTURE => '产品被授权产品结构',
            self::TYPE_AUTHORIZATION_PRICE => '产品被授权价格',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
