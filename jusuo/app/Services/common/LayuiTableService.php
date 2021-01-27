<?php
namespace App\Http\Services\common;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LayuiTableService{

    public static function getTableResponseNoPage($data)
    {
        /*
         {
           "code": 0,
           "msg": "",
           "count": 1000,
           "data": [{}, {}]
         }
      */
        $result = [];
        $result['code'] = 0;
        $result['msg'] = 'success';
        $result['count'] = count($data);
        if($data instanceof Collection){
            $data  -> toArray();
        }
        $result['data'] = $data;
        return $result;
    }

    public static function getTableResponse($data,$code=0,$message='success')
    {
        /*
         {
           "code": 0,
           "msg": "",
           "count": 1000,
           "data": [{}, {}]
         }
      */
        $result = [];
        $result['code'] = $code;
        $result['msg'] = $message;
        $result['count'] = $data->total();
        $result['data'] = $data -> values() -> toArray();
        return $result;
    }


}