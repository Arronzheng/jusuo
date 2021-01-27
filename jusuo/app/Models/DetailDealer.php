<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailDealer extends Model
{
    //
    //
    protected $guarded = [];

    const PRIVILEGE_AREA_SERVING_CITY = 0;
    const PRIVILEGE_AREA_SERVING_PROVINCE = 1;
    const PRIVILEGE_AREA_SERVING_COUNTRY = 2;

    const IS_TOP_NO = 0;
    const IS_TOP_YES = 1;

    public static function isTopGroup($key=null){
        $group = [
            self::IS_TOP_NO => '否',
            self::IS_TOP_YES => '是',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function privilegeAreaServingGroup($key=null){
        $group = [
            self::PRIVILEGE_AREA_SERVING_CITY => '本市',
            self::PRIVILEGE_AREA_SERVING_PROVINCE => '本省',
            self::PRIVILEGE_AREA_SERVING_COUNTRY => '全国'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public function dealer()
    {
        return $this->belongsTo(OrganizationDealer::class,'dealer_id');
    }


}
