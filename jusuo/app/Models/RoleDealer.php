<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleDealer extends SpatieRole
{
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable('role_dealers');
    }

    /*----------角色权限专用start----------*/
    public function getPermissionClass()
    {
        return app(PrivilegeDealer::class);
    }

    public function admins()
    {
        return $this->belongsToMany(AdministratorDealer::class, 'administrator_role_dealers', 'role_id', 'administrator_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            app(PrivilegeDealer::class),
            'role_privilege_dealers',
            'role_id',
            'privilege_id'
        );
    }
    /*----------角色权限专用end----------*/


    public function scopeOfName($query,$name)
    {
        return $query->where('name', $name);
    }

    public function scopeOfDisplayName($query,$name)
    {
        return $query->where('display_name', $name);
    }

    public function scopeOrderByCreatedAt($query,$order)
    {
        return $query->orderBy('created_at', $order);
    }

    public function scopeOrderBySort($query,$order)
    {
        return $query->orderBy('sort', $order);
    }

    public function scopeOrderByIsSuperAdmin($query,$is_super_admin)
    {
        return $query->orderBy('is_super_admin', $is_super_admin);
    }

}
