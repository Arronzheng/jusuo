<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDetailDealer extends Model
{
    //
    protected $guarded = [];

    const IS_APROVE_REJECT = -1; // 驳回
    const IS_APROVE_VERIFYING = 0; // 待审核
    const IS_APROVE_APPROVAL = 1; // 通过

    public static function getIsApproved($key=NULL)
    {
        $arr=[
            self::IS_APROVE_REJECT=>'驳回',
            self::IS_APROVE_VERIFYING=>'待审核',
            self::IS_APROVE_APPROVAL=>'通过',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:'';
        }

        return $arr;
    }

    //被审核者关联
    public function target_dealer()
    {
        return $this->belongsTo(OrganizationDealer::class,'target_dealer_id');
    }

    //被审核单位id
    public function scopeOfTargetDealerId($query,$organization_id)
    {
        return $query->where('dealer_id',$organization_id);
    }

    public function scopeOfIsApproved($query,$is_approved)
    {
        return $query->where('is_approved',$is_approved);
    }

    public function scopeOrderByCreatedAt($query,$order)
    {
        return $query->orderBy('created_at',$order);
    }

}
