<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class RootService
{

    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;

    public $result;

    //错误结果
    protected function result_fail($error_msg){
        $result = array();
        $result['status'] = self::STATUS_FAIL;
        $result['msg'] = $error_msg;
        $this->result = $result;
        return false;
    }

    //成功结果
    protected function result_success($data=[]){
        $result = array();
        $result['status'] = self::STATUS_SUCCESS;
        $result['msg'] = 'success';
        $result['data'] = $data;
        $this->result = $result;
        return true;
    }

}