<?php
/**
 * Created by PhpStorm.
 * User: xlb
 * Date: 2017/9/12
 * Time: 16:40
 */

namespace App\Http\Services\common;


class SystemLogService
{

    public static function simple($description,$data)
    {
        $date = date('Y-m-d',time());
        $file = 'laravel-'.$date.'.log';

        $log_path = storage_path('logs/'.$file);

        if(file_exists($log_path)){
            @chmod($log_path,0777);

            \Log::info("=========== ".$description." start===========");
            foreach($data as $key=>$val){
                \Log::info($val);
            }
            \Log::info("=========== ".$description." end=============");
        }


    }

}