<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class AdministratorBrand extends Authenticatable
{
    use HasRoles,Notifiable;

    protected $guarded = [];

    protected $hidden = ['login_password'];

    protected $guard_name = 'brand';

    const STATUS_WAIT_VERIFY = '000';   //未审核
    const STATUS_ON = '200';   //正常
    const STATUS_OFF = '100';  //禁用

    public static function statusGroup($key=NULL){
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

    //如果字段名不是password，则需要加上这个让Auth知道自定义的password字段名
    public function getAuthPassword()
    {
        return $this->login_password;
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
        $this->roleClass = app(RoleBrand::class);
        return $this->roleClass;
    }

    public function getPermissionClass()
    {
        $this->permissionClass = app(PrivilegeBrand::class);
        return $this->permissionClass;
    }

    public function roles()
    {
        return $this->belongsToMany(RoleBrand::class, 'administrator_role_brands', 'administrator_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(PrivilegeBrand::class, 'administrator_privilege_brands', 'administrator_id', 'privilege_id');
    }

    /*-----------角色权限专用end------------*/

    public function brand()
    {
        return $this->belongsTo(OrganizationBrand::class,'brand_id');
    }


    public function scopeOfBrandId($query,$name)
    {
        return $query->where('brand_id', $name);
    }

}
