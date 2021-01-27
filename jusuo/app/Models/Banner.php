<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    //
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

    const POSITION_INDEX_TOP = 1;   //首页宽屏轮播图
    const POSITION_PRODUCT_INDEX_TOP = 2;   //产品库列表轮播图
    const POSITION_DESIGNER_INDEX_TOP = 3;   //设计师列表轮播图
    const POSITION_DEALER_INDEX_TOP = 4;   //材料商家列表轮播图
    const POSITION_INTEGRAL_INDEX = 5;   //积分商城主页轮播图

    public static function positionGroup($key=NULL){
        $group = [
            self::POSITION_INDEX_TOP => '首页宽屏轮播图(1920*480)',
            self::POSITION_PRODUCT_INDEX_TOP => '产品库列表轮播图(1100*400)',
            self::POSITION_DESIGNER_INDEX_TOP => '设计师列表轮播图(1100*400)',
            self::POSITION_DEALER_INDEX_TOP => '材料商家列表轮播图(1100*400)',
            self::POSITION_INTEGRAL_INDEX => '积分商城主页轮播图(1100*400)',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

}
