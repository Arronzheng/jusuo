<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationDealer extends Model
{
    //
    protected $guarded = [];
    const STATUS_WAIT_VERIFY = '000';
    const STATUS_ON = '200';   //正常
    const STATUS_OFF = '100';  //禁用

    public static function statusGroup($key=null){
        $group = [
            self::STATUS_WAIT_VERIFY => '未审核',
            self::STATUS_ON => '正常',
            self::STATUS_OFF => '禁用'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TOP_PLATFORM_STATUS_OFF = 0;
    const TOP_PLATFORM_STATUS_ON = 1;

    public static function topPlatformStatusGroup($key=null){
        $group = [
            self::TOP_PLATFORM_STATUS_OFF => '平台未置顶',
            self::TOP_PLATFORM_STATUS_ON => '平台置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TOP_BRAND_STATUS_OFF = 0;
    const TOP_BRAND_STATUS_ON = 1;

    public static function topBrandStatusGroup($key=null){
        $group = [
            self::TOP_BRAND_STATUS_OFF => '品牌未置顶',
            self::TOP_BRAND_STATUS_ON => '品牌置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public function parent_dealer()
    {
        return $this->belongsTo(OrganizationDealer::class,'p_dealer_id');
    }

    public function detail()
    {
        return $this->hasOne(DetailDealer::class,'dealer_id');
    }

    public function designer()
    {
        return $this->morphMany(Designer::class, 'organization');
    }

    public function brand()
    {
        return $this->belongsTo(OrganizationBrand::class,'p_brand_id');
    }

    //瓷砖产品授权
    public function product_ceramic_authorizations()
    {
        return $this->belongsToMany(ProductCeramic::class,'product_ceramic_authorizations','dealer_id','product_id');
    }

}
