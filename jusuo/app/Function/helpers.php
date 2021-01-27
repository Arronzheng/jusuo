<?php

if (!function_exists('get_img_route')) {
    function get_img_route($path, $want_ratio=0 , $want_width = '', $want_height = '', $water = 0)
    {
        if (!$path) {
            return;
        }

        if (strstr($path, 'http')) {
            return;
        }


        $filename = public_path($path);


        if (!file_exists($filename)) {
            return;
        }

        try{
            list($file_width, $file_height) = getimagesize($filename);
        }catch(\Exception $e){
            return ;
        }

        $dir_name = \App\Services\common\ImageRouteService::get_dir_name($path);
        $file_name = \App\Services\common\ImageRouteService::get_file_name($path);
        $save_dir = \App\Services\common\ImageRouteService::get_save_dir($dir_name);
        //拆分file_name
        $file_array = \App\Services\common\ImageRouteService::get_file_array($file_name);
        $file_prefix = $file_array[0];
        $file_suffix = $file_array[1];
        $save_file_path = \App\Services\common\ImageRouteService::get_save_file_path($save_dir,$file_prefix,$want_ratio,$want_width,$want_height,$water,$file_suffix);
        $save_file_name = \App\Services\common\ImageRouteService::get_save_file_name($file_prefix,$want_ratio,$want_width,$want_height,$water,$file_suffix);

        $url = $dir_name;
        $param = $save_file_name;

        if (!file_exists($save_file_path)) {
            $url = 'getImg';
            $param = implode('_', [str_replace('/', '@', $path),$want_ratio,$want_width, $want_height, $water]);

        }

        return url($url,$param);
    }
}


/**
 * 计算几分钟前、几小时前、几天前、几月前、几年前。
 * $agoTime string Unix时间
 * @author tangxinzhuan
 * @version 2016-10-28
 */
if (!function_exists('time_ago')) {
    function time_ago($posttime){
        //当前时间的时间戳
        $nowtimes = strtotime(date('Y-m-d H:i:s'),time());
        //之前时间参数的时间戳
        $posttimes = strtotime($posttime);
        //相差时间戳
        $counttime = $nowtimes - $posttimes;

        //进行时间转换
        if($counttime<=10){

            return '刚刚';

        }else if($counttime>10 && $counttime<=30){

            return '刚才';

        }else if($counttime>30 && $counttime<=60){

            return '刚一会';

        }else if($counttime>60 && $counttime<=120){

            return '1分钟前';

        }else if($counttime>120 && $counttime<=180){

            return '2分钟前';

        }else if($counttime>180 && $counttime<3600){

            return intval(($counttime/60)).'分钟前';

        }else if($counttime>=3600 && $counttime<3600*24){

            return intval(($counttime/3600)).'小时前';

        }else if($counttime>=3600*24 && $counttime<3600*24*2){

            //return '昨天';
            return date('昨天 H:i',strtotime($posttime));

        }else if($counttime>=3600*24*2 && $counttime<3600*24*3){

            //return '前天';
            return date('Y年m月d日 H:i',strtotime($posttime));

        }else if($counttime>=3600*24*3 && $counttime<=3600*24*20){

            //return intval(($counttime/(3600*24))).'天前';
            return date('Y年m月d日 H:i',strtotime($posttime));

        }else{

            return date('Y年m月d日 H:i',strtotime($posttime));

        }
    }
}