<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 12:54
 */

namespace App\Http\Repositories\common;


use App\Models\Area;

class AreaRepository
{

    /**
     * @param $district_id
     * @param $onlyCity
     * @return string
     */
    public function getLocationByDistrictId($district_id=0,$onlyCity=false)
    {
        if ($district_id==0){
            return '空';
        }

        $district = Area::OfLevel(3)->find($district_id);
        $city = Area::OfLevel(2)->find($district->pid);
        $province = Area::OfLevel(1)->find($city->pid);

        if ($onlyCity){
            return $city->name;
        }

        $province_name = '';
        $city_name = '';
        $district_name = '';

        if($province){$province_name = $province->name;}
        if($city){$city_name = $city->name;}
        if($district){$district_name = $district->name;}

        return $province_name.' '.$city_name.' '.$district_name;


    }

    public function getLocationByAreaId($province_id,$city_id,$district_id)
    {

        $district = Area::OfLevel(3)->find($district_id);
        $city = Area::OfLevel(2)->find($city_id);
        $province = Area::OfLevel(1)->find($province_id);

        $province_name = '';
        $city_name = '';
        $district_name = '';

        if($province){$province_name = $province->name;}
        if($city){$city_name = $city->name;}
        if($district){$district_name = $district->name;}

        return $province_name.' '.$city_name.' '.$district_name;

    }

    public function getServiceAreaName($area_serving_id)
    {
        if (!$area_serving_id){
            return '空';
        }

        $result = '';

        $district = Area::where('id',$area_serving_id)->first();
        if ($district){
            $city =  Area::where('id',$district->pid)->first();
            if ($city){
                $province =  Area::where('id',$city->pid)->first();

                if ($province){
                    $result = $province->shortname.'/'.$city->shortname.'/'.$district->shortname;
                }
            }
        }

        return $result;
    }
}