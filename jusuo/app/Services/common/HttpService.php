<?php
namespace App\Http\Services\common;

use \GuzzleHttp\Client;

class HttpService{

    //文档：https://guzzle-cn.readthedocs.io/zh_CN/latest/quickstart.html

    public static function get($url)
    {
        $client = new Client();
        $res = $client->request('GET',$url,[
            'auth' => ['user', 'pass']
        ]);
        //返回信息包体
        return $res->getBody();
    }

    public static function post_json($url,$params)
    {
        $client = new Client();
        $res = $client->request('POST', $url,[
            'json' => $params
        ]);

        //返回信息包体
        $body = $res->getBody();
        $contents  = $body->getContents();
        $response = json_decode($contents, true);
        return $response;
    }


    public static function post_form($url,$params)
    {
        $client = new Client();
        $res = $client->request('POST', $url,[
            'form_params' => [
                'data' => $params
            ]
        ]);

        //返回信息包体
        $body = $res->getBody();
        $contents  = $body->getContents();
        $response = json_decode($contents, true);
        return $response;
    }

}