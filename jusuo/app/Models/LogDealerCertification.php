<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDealerCertification extends Model
{
    protected $guarded = [];
    const IS_READ_YES = 1;
    const IS_READ_NO = 0;

    public static function isReadGroup($key=NULL){
        $group = [
            self::IS_READ_YES => '是',
            self::IS_READ_NO => '否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

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
}
