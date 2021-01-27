<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDesignerDetail extends Model
{
    //
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

    public function target_designer()
    {
        return $this->belongsTo(Designer::class,'target_designer_id');
    }


}
