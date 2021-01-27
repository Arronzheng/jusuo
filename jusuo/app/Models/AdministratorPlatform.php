<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class AdministratorPlatform extends Authenticatable
{
    use HasRoles,Notifiable;
    
    protected $guarded = [];

    protected $hidden = ['login_password'];

    //如果字段名不是password，则需要加上这个让Auth知道自定义的password字段名
    public function getAuthPassword()
    {
        return $this->login_password;
    }

    const STATUS_ON = 200;   //正常
    const STATUS_OFF = 100;  //禁用

    const KEYWORD_TYPE_LOGIN_USERNAME = 1;
    const KEYWORD_TYPE_REALNAME = 2;
    const KEYWORD_TYPE_DEPARTMENT = 3;
    const KEYWORD_TYPE_POSITION = 4;

    public static function getKeywordType($key=NULL)
    {
        $arr=[
            self::KEYWORD_TYPE_LOGIN_USERNAME => '账号',
            self::KEYWORD_TYPE_REALNAME=>'姓名',
            self::KEYWORD_TYPE_DEPARTMENT=>'部门',
            self::KEYWORD_TYPE_POSITION=>'职位',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:'';
        }

        return $arr;
    }

    const IS_SUPER_ADMIN_YES = 1;
    const IS_SUPER_ADMIN_NO = 0;

    public static function isSuperAdminGroup($key=null){
        $group = [
            self::IS_SUPER_ADMIN_YES => '是',
            self::IS_SUPER_ADMIN_NO => '否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }

    }

    /*-----------角色权限专用start------------*/
    public function getRoleClass()
    {
        $this->roleClass = app(RolePlatform::class);
        return $this->roleClass;
    }

    public function getPermissionClass()
    {
        $this->permissionClass = app(PrivilegePlatform::class);
        return $this->permissionClass;
    }

    public function roles()
    {
        return $this->belongsToMany(RolePlatform::class, 'administrator_role_platforms', 'administrator_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(PrivilegePlatform::class, 'administrator_privilege_platforms', 'administrator_id', 'privilege_id');
    }
    /*-----------角色权限专用end------------*/

    public function scopeOfIsSuperAdmin($query,$is_super_admin)
    {
        return $query->where('is_super_admin', $is_super_admin);
    }

    public function scopeOrderByCreatedAt($query,$order)
    {
        return $query->orderBy('created_at', $order);
    }

    public function scopeOrderByIsSuperAdmin($query,$is_super_admin)
    {
        return $query->orderBy('is_super_admin', $is_super_admin);
    }
}
