<?php

namespace App\Http\Repositories\common;

use App\Models\HouseType;
use Illuminate\Support\Facades\DB;

class HouseTypeRepository
{
    const table = 'house_types';
    public function getName($id)
    {
      return DB::table(self::table)->where('id', $id)->pluck('name')[0];
    }

    public function get_normal_data()
    {
        return HouseType::all();
    }

    public function exist_name($name)
    {
        $count =  HouseType::where('name',$name)->count();

        if($count>0){
            return true;
        }else{
            return false;
        }
    }

}