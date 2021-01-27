<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    const INDEX_SHOW_ON = 1;
    const INDEX_SHOW_OFF = 0;

    public function scopeOfLevel($query, $level)
    {
        return $query->where('level', $level);
    }


}
