<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class AdministratorDealer extends Authenticatable
{
    use HasRoles,Notifiable;

    protected $guarded = [];

    protected $hidden = ['login_password'];

    protected $guard_name = 'seller';

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

    public function dealer()
    {
        return $this->belongsTo(OrganizationDealer::class,'dealer_id');
    }


    public function scopeOfDealerId($query,$name)
    {
        return $query->where('dealer_id', $name);
    }

    /*-----------角色权限专用start------------*/
    public function getRoleClass()
    {
        $this->roleClass = app(RoleDealer::class);
        return $this->roleClass;
    }

    public function getPermissionClass()
    {
        $this->permissionClass = app(PrivilegeDealer::class);
        return $this->permissionClass;
    }

    public function roles()
    {
        return $this->belongsToMany(RoleDealer::class, 'administrator_role_dealers', 'administrator_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(PrivilegeDealer::class, 'administrator_privilege_dealers', 'administrator_id', 'privilege_id');
    }
    /*-----------角色权限专用end------------*/

}
