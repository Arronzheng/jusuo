<?php

namespace App\Http\Repositories\common;

use App\Models\OptionDecorationMannerMember;
use App\Models\Style;
use Illuminate\Support\Facades\DB;

class StyleRepository
{
    const table = 'styles';
    public function getStyleName($styleId)
    {
      return DB::table(self::table)->where('id', $styleId)->pluck('name')[0];
    }

    public function get_normal_data()
    {
        return Style::all();
    }

    public function exist_name($name)
    {
        $count =  Style::where('name',$name)->count();

        if($count>0){
            return true;
        }else{
            return false;
        }
    }

}