<?php

namespace App\Http\Repositories\common;

use App\Models\SpaceType;
use Illuminate\Support\Facades\DB;

class SpaceTypeRepository
{
    const table = 'space_types';
    public function getName($id)
    {
      return DB::table(self::table)->where('id', $id)->pluck('name')[0];
    }

    public function get_normal_data()
    {
        return SpaceType::all();
    }

    public function exist_name($name)
    {
        $count =  SpaceType::where('name',$name)->count();

        if($count>0){
            return true;
        }else{
            return false;
        }
    }

}