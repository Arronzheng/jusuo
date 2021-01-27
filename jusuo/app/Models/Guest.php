<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Guest extends Authenticatable
{
    //
    use Notifiable;


    const STATUS_ON = '200';   //正常
    const STATUS_OFF = '100';  //禁用
    const STATUS_VERIFYING = '000';  //待审核


    public static function statusGroup($key=NULL)
    {
        $group=[
            self::STATUS_ON=>'正常',
            self::STATUS_OFF=>'禁用',
            self::STATUS_VERIFYING=>'待审核'
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }
}
