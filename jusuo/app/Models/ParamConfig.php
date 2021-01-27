<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParamConfig extends Model
{
    //
    protected $guarded = [];

    const VALUE_TYPE_SINGLE_NUMBER = 110;  //单个值
    const VALUE_TYPE_FUNC_PRIVILEGE_SWITCH = 120;  //功能权限开关（0关1开）
    const VALUE_TYPE_WHETHER_RADIO = 121;  //是/否单选（0否1是）
    const VALUE_TYPE_REQUIRED_RADIO = 122;  //必/选填单选（0必填1选填）
    const VALUE_TYPE_PUBLIC_OBJ_RADIO = 123;  //公开对象单选（10合作对象20当地用户30所有用户）
    const VALUE_TYPE_LIMIT_RADIO = 124;  //有/无限制单选（0有限制1无限制）
    const VALUE_TYPE_MULTIPLE_TEXT = 130;  //多个字符串text
    const VALUE_TYPE_MULTIPLE_SELECT = 140;  //自定义多选项（暂时没用，20190117）
    const VALUE_TYPE_MULTIPLE_ITEM= 150;  //公式

    public function scopeOfName($query,$name)
    {
        return $query->where('name', $name);
    }
}
