<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralGoodAuthorization extends Model
{
    //
    const STATUS_BRAND_CAN_USE = 100;  //预开放（品牌可用）
    const STATUS_BRAND_USED = 101;  //品牌已选入
    const STATUS_DELETED = 200;  //删除（仅当状态为100时可彻底删除，否则删除后仍保留记录）

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_BRAND_CAN_USE => '已开放',
            self::STATUS_BRAND_USED => '已引入',
            self::STATUS_DELETED => '删除',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //上下架
    const VISIBLE_STATUS_ON = 1;  //上架
    const VISIBLE_STATUS_OFF = 0;  //下架

    public static function visibleStatusGroup($key=NULL){
        $group = [
            self::VISIBLE_STATUS_ON => '上架',
            self::VISIBLE_STATUS_OFF => '下架',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }


    //置顶
    const TOP_STATUS_ON = 1;   //置顶
    const TOP_STATUS_OFF = 0;  //不置顶

    public static function topStatusGroup($key=NULL){
        $group = [
            self::TOP_STATUS_ON => '置顶',
            self::TOP_STATUS_OFF => '不置顶'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
