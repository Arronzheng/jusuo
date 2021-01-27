<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumSection extends Model
{
    //
    protected $guarded = [];

    public function styles(){
        return $this->belongsToMany(Style::class,'album_section_styles','album_section_id','style_id')
            ->withTimestamps()->withPivot('album_id');
    }
}
