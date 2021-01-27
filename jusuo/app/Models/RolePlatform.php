<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasPermissions;

class RolePlatform extends SpatieRole
{
    const IS_SUPER_ADMIN_YES = 1;
    const IS_SUPER_ADMIN_NO = 0;

    use HasPermissions;

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
        $this->setTable('role_platforms');
    }

    /*----------角色权限专用start----------*/
    public function getPermissionClass()
    {
        return app(PrivilegePlatform::class);
    }

    public function admins()
    {
        return $this->belongsToMany(AdministratorPlatform::class, 'administrator_role_platforms', 'role_id', 'administrator_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            app(PrivilegePlatform::class),
            'role_privilege_platforms',
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
