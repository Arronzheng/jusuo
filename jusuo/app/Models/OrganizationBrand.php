<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationBrand extends Model
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

    const TOP_STATUS_OFF = 0;
    const TOP_STATUS_ON = 1;

    public static function topStatusGroup($key=null){
        $group = [
            self::TOP_STATUS_OFF => '未置顶',
            self::TOP_STATUS_ON => '置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public function detail()
    {
        return $this->hasOne(DetailBrand::class,'brand_id');
    }

    public function designer()
    {
        return $this->morphMany(Designer::class, 'organization');
    }

    //积分商品授权
    public function integral_good_authorizations()
    {
        return $this->belongsToMany(IntegralGood::class,'integral_good_authorizations','brand_id','good_id');
    }
}
