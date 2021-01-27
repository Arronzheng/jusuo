<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission as SpatiePermission;

class PrivilegeDealer extends SpatiePermission
{

    const IS_SUPER_ADMIN_YES = 1;
    const IS_SUPER_ADMIN_NO = 0;

    const SHOWN_YES = 1;
    const SHOWN_NO = 0;

    const IS_MENU_YES = 1;
    const IS_MENU_NO = 0;

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

    public static function shownGroup($key=null){
        $group = [
            self::SHOWN_YES => '是',
            self::SHOWN_NO => '否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function isMenuGroup($key=null){
        $group = [
            self::IS_MENU_YES => '是',
            self::IS_MENU_NO => '否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    /*-----------角色权限专用----------*/
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable('privilege_dealers');
    }
    public static function bootHasPermissions()
    {
        return ;
    }
    protected static function getPermissions(array $params = []): Collection
    {
        $permissions = static::query()->with('roles')->get();

        foreach ($params as $attr => $value) {
            $permissions = $permissions->where($attr, $value);
        }

        return $permissions;
    }
    public function admins()
    {
        return $this->belongsToMany(AdministratorDealer::class, 'administrator_privilege_dealers', 'privilege_id', 'administrator_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            app(RoleDealer::class),
            'role_privilege_dealers',
            'privilege_id',
            'role_id'
        );
    }


    /*--------------角色权限专用---------------*/


    public static function getIsShowMenu($key=NULL)
    {
        $arr=[
            self::IS_MENU_YES=>'是',
            self::IS_MENU_NO=>'否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:'';
        }
        return $arr;
    }

    public static function getIsSuperAdmin($key=NULL)
    {
        $arr=[
            self::IS_SUPER_ADMIN_YES=>'是',
            self::IS_SUPER_ADMIN_NO=>'否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$arr)?$arr[$key]:'';
        }
        return $arr;
    }

    public function scopeOfName($query,$name)
    {
        $query->where('name',$name);
    }

    public function scopeOfIsSuperAdmin($query,$is_super_admin)
    {
        $query->where('is_super_admin',$is_super_admin);
    }

    public function scopeOrderByCreatedAt($query,$order)
    {
        $query->orderBy('created_at',$order);
    }

    public function scopeOrderByPath($query,$order)
    {
        $query->orderBy('path',$order);
    }
}
