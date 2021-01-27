<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Models\Album;
use App\Models\CeramicSeries;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use Illuminate\Support\Facades\Auth;

class AlbumService{

    //设计方案系统编号生成
    /**
     * @return string
     */
    public static function get_sys_code()
    {
        $str = date('Ymd');

        //随机识别码6位
        $random_code = str_pad(random_int(100000,999999),5,0,STR_PAD_LEFT);
        $str.= $random_code;

        $exist = Album::query()->where('code',$str)->first();
        if($exist){
            return self::get_sys_code();
        }

        return $str;

    }

    public static function get_unname_title($designer_id)
    {
        $str = '未命名';

        //随机识别码6位
        $random_code = str_pad(random_int(10000,99999),5,0,STR_PAD_LEFT);
        $str.= $random_code;

        $exist = Album::query()->where('designer_id',$designer_id)->where('title',$str)->first();
        if($exist){
            return self::get_unname_title($designer_id);
        }

        return $str;

    }


}