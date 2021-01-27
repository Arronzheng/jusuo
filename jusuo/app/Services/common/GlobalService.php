<?php
namespace App\Http\Services\common;

class GlobalService{

    //多级数据转树状结构
    public function array_to_tree($array, $pid = 0, $level = 0,$parent_name = 'pid')
    {
        $tree = array();
        if (count($array)) {
            foreach ($array as $k => $v) {
                if ($v[$parent_name] == $pid) {
                    $v['level'] = $level;
                    $tree[$v['id']] = $v;
                    $tree[$v['id']]['child'] = $this->array_to_tree($array, $v['id'], $level + 1,$parent_name);
                }
            }
        }
        $tree = array_values($tree);

        return $tree;
    }

    /**
     * 计算几分钟前、几小时前、几天前、几月前、几年前。
     * $agoTime string Unix时间
     * @author tangxinzhuan
     * @version 2016-10-28
     */
    public static function time_ago($posttime){
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
            return date('m-d H:i',strtotime($posttime));

        }else if($counttime>=3600*24*2 && $counttime<3600*24*3){

            //return '前天';
            return date('m-d H:i',strtotime($posttime));

        }else if($counttime>=3600*24*3 && $counttime<=3600*24*20){

            //return intval(($counttime/(3600*24))).'天前';
            return date('m-d H:i',strtotime($posttime));

        }else{

            return date('m-d H:i',strtotime($posttime));

        }
    }

    public static function time_ago_string($posttime){
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

            return '昨天';

        }else if($counttime>=3600*24*2 && $counttime<3600*24*3){

            return '前天';

        }else if($counttime>=3600*24*3 && $counttime<3600*24*30){

            return intval(($counttime/(3600*24))).'天前';

        }else if($counttime>=3600*24*30 && $counttime<3600*24*365){

            return intval(($counttime/(3600*24*30))).'个月前';

        }
        else{
            return intval(($counttime/(3600*24*365))).'年前';
            //return $nowtimes.'-'.$posttimes.'='.$counttime;
        }
    }

}