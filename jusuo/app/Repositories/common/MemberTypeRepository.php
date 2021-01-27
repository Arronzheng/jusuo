<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/10
 * Time: 15:20
 */

namespace App\Http\Repositories\common;


use App\Models\DesignerAccountType;

class MemberTypeRepository
{
    public function get_normal_data()
    {
        return DesignerAccountType::all();
    }

    public function exist_name($name)
    {
        $count =  DesignerAccountType::where('name',$name)->count();

        if($count>0){
            return true;
        }else{
            return false;
        }
    }
}