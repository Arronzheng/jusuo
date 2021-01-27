<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogProductCeramic extends Model
{
    //

    const IS_APROVE_CANCEL = -2; // 作废
    const IS_APROVE_REJECT = -1; // 驳回
    const IS_APROVE_VERIFYING = 0; // 待审核
    const IS_APROVE_APPROVAL = 1; // 通过

    public static function getIsApproved($key=NULL)
    {
        $arr=[
            self::IS_APROVE_CANCEL=>'作废',
            self::IS_APROVE_REJECT=>'驳回',
            self::IS_APROVE_VERIFYING=>'待审核',
            self::IS_APROVE_APPROVAL=>'通过',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:'';
        }

        return $arr;
    }

    const TYPE_FIRST_VERIFY = 0; // 首次审核
    const TYPE_MODIFY_VERIFY = 1; // 修改审核

    public static function typeGroup($key=NULL)
    {
        $arr=[
            self::TYPE_FIRST_VERIFY=>'首次审核',
            self::TYPE_MODIFY_VERIFY=>'修改审核'
        ];

        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:'';
        }

        return $arr;
    }

    public function target_product()
    {
        return $this->belongsTo(ProductCeramic::class,'target_product_id');
    }
}
