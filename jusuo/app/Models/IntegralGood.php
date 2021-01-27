<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralGood extends Model
{
    //
    const STATUS_ON = 1;   //上架
    const STATUS_OFF = 0;  //下架

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_ON => '上架',
            self::STATUS_OFF => '下架'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

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

    const IS_DELETE_YES = 1;   //软删除
    const IS_DELETE_NO = 0;  //不删除

    public static function isDeleteGroup($key=NULL){
        $group = [
            self::IS_DELETE_YES => '正常',
            self::IS_DELETE_NO => '禁用'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //有哪些品牌已引入本商品
    public function used_brands()
    {
        return $this->belongsToMany(OrganizationBrand::class, 'integral_good_authorizations', 'good_id', 'brand_id')
            ->where('integral_good_authorizations.status',IntegralGoodAuthorization::STATUS_BRAND_USED);
    }

    //有哪些品牌被开放本商品
    public function authorized_brands()
    {
        return $this->belongsToMany(OrganizationBrand::class, 'integral_good_authorizations', 'good_id', 'brand_id');
    }
}
