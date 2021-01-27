<?php

namespace App\Http\Controllers;

class ApiRootController extends Controller
{
    //PC端api的root class

    //全局业务码
    const API_CODE_SUCCESS = 1000;
    const API_CODE_FAIL = 2000;

    public function __construct()
    {

    }

    //respXXX 会直接exit程序，请按需使用
    public function respData($data = array(), $msg = '操作成功') {
        $result = [
            'code' => self::API_CODE_SUCCESS,
            'status' => 1,
            'data' => $data,
            'msg'  => $msg
        ];
        echo json_encode($result);
        exit;
    }

    public function respFail($msg = '操作失败', $code = self::API_CODE_FAIL, $data = []) {
        echo json_encode([
            'status' => 0,
            'code' => $code,
            'data'  => $data,
            'msg'  => $msg
        ]);
        exit;
    }

    //respXXXReturn 没有exit程序，而是返回结果，请按需使用。
    public function respDataReturn($data = array(), $msg = '操作成功') {
        $result = [
            'code' => self::API_CODE_SUCCESS,
            'status' => 1,
            'data' => $data,
            'msg'  => $msg
        ];
        return response($result);
    }

    public function respFailReturn($msg = '操作失败', $code = self::API_CODE_FAIL, $data = []) {
        return response([
            'status' => 0,
            'code' => $code,
            'data'  => $data,
            'msg'  => $msg
        ]);
    }

}
