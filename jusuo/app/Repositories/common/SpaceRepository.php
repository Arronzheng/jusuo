<?php

namespace App\Http\Repositories\common;
use App\Models\OptionHouseCategoryMember;
use App\Models\Space;
use Illuminate\Support\Facades\DB;


/**
 * Class SpaceRepository
 * @package App\Http\Repositories\common
 */
class SpaceRepository
{
    /**
     *
     */
    const table = 'spaces';

    /**
     * @param $spaceId
     * @return mixed
     */
    public function getSpaceName($spaceId)
    {
        return DB::table(self::table)->where('id', $spaceId)->pluck('name')[0];
    }

    public function get_normal_data()
    {
        return Space::all();
    }

    public function exist_name($name)
    {
        $count =  Space::where('name',$name)->count();

        if($count>0){
            return true;
        }else{
            return false;
        }
    }
}