<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestData extends Model
{
    //
    protected $guarded = [];

    public static $typeGroup = [
        1 => '一级用户',
        2 => '二级用户',
        3 => '三级用户'
    ];

    public static function typeGroup($key){
        $group = self::$typeGroup;
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    public static $statusGroup = [
        self::STATUS_ON => '启用',
        self::STATUS_OFF => '禁用',
    ];

    public static function statusGroup($key){
        $group = self::$statusGroup;
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static $hobbyGroup = [
        1 => '写作',
        2 => '阅读',
        3 => '发呆'
    ];

    public static function hobbyGroup($key){
        $group = self::$hobbyGroup;
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
