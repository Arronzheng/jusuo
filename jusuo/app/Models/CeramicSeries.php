<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CeramicSeries extends Model
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

    //生成系列码
    public static function get_series_code($brand_id)
    {
        $brand_series_count = CeramicSeries::where('brand_id',$brand_id)->count();

        $code = $brand_series_count+1;
        //数字3位，不够则补0
        $code = sprintf("%03d",$code);
        return $code;
    }
}
