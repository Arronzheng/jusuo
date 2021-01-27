<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParamConfigOrganization extends Model
{
    //
    protected $guarded = [];

    const CHILDREN_OBEY_YES = 1;
    const CHILDREN_OBEY_NO = 0;

    public function scopeOfConfigId($query,$config_id)
    {
        return $query->where('config_id', $config_id);
    }

    public function scopeOfOrganizationId($query,$organization_id)
    {
        return $query->where('organization_id', $organization_id);
    }

    public function scopeInOrganizationId($query,$organization_ids)
    {
        return $query->whereIn('organization_id', $organization_ids);
    }

    public function scopeNotOrganizationType($query,$organization_type)
    {
        return $query->where('organization_type','<>', $organization_type);
    }
}
