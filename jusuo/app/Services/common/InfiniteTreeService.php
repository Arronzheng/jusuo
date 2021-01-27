<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/6/24
 * Time: 17:04
 */

namespace App\Http\Services\common;


class InfiniteTreeService
{
    //获取一维树（带level），即一维数组
    public function getFlatTree($data,$parentColumn='parent_id',$pid = 0,$level = 0){
        static $arr = array();
        foreach ($data as $key=>$value){
//            var_dump($value);
            if($value->$parentColumn == $pid){
                $value->level = $level;
                $arr[] = $value;
                $this->getFlatTree($data,$parentColumn,$value->id,$level+1);
//                var_dump($arr);die;
            }
        }
        return $arr;
    }
}