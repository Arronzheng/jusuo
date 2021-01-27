<?php

namespace App\Services\v1\site;

class ApiService{

    //PC端api的root class

    //全局业务码
    const API_CODE_SUCCESS = 1000;
    const API_CODE_FAIL = 2000;

    //未登录
    const API_CODE_AUTH_FAIL = 2001;
    //操作、页面不可见
    const API_CODE_INVISIBLE = 2002;

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

    //返回空数据，用于品牌首页发起的无意义查询
    public function respBrandReturn($data = array(), $msg = '操作成功') {
        $result = [
            'code' => self::API_CODE_SUCCESS,
            'status' => 2,
            'data' => $data,
            'msg'  => $msg
        ];
        return response($result);
    }

    public function respFailReturn($msg = '操作失败', $code = self::API_CODE_FAIL, $data = []) {
        return response([
            'success' => 0,
            'code' => $code,
            'data'  => $data,
            'msg'  => $msg
        ]);
    }


}

?>