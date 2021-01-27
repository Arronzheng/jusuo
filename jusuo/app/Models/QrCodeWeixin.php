<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class QrCodeWeixin extends Authenticatable
{
    use HasRoles,Notifiable;

    protected $guarded = [];

    protected $hidden = ['login_password'];

    const STATUS_WAIT_VERIFY = '000';   //待用
    const STATUS_LOCKED = '300';   //锁定（信息收集完成，可迁移至用户表）
    const STATUS_ON = '200';   //正常（已占用，其他用户使用无效）
    const STATUS_OFF = '100';  //禁用

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_WAIT_VERIFY => '待用',
            self::STATUS_LOCKED => '锁定',
            self::STATUS_ON => '正常',
            self::STATUS_OFF => '禁用'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //如果字段名不是password，则需要加上这个让Auth知道自定义的password字段名
    public function getAuthPassword()
    {
        return $this->login_password;
    }

}
