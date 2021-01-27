<?php

namespace App\Http\Services\common\AliyunOss;

use JohnLui\AliyunOSS;

use Exception;
use DateTime;
use OSS\Core\OssException;
use OSS\OssClient;

class BusinessOssService {

    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;

    private $bucketName;
    private $accessKeyId;
    private $accessKeySecret;
    private $endpoint;
    private $businessDomain;

    private $ossClient;

    public $result;

    public function __construct()
    {

        //初始化oss配置
        $this->bucketName = 'jinduo-business';
        $this->accessKeyId = 'LTAIMEIJCOyh1go5';
        $this->accessKeySecret = 'MJ8HhjpjpE4CtXljDKtjSGqnPFH3PX';
        if(env('APP_ENV')=='local'){
            $this->endpoint = 'oss-cn-shenzhen.aliyuncs.com';  //外网
        }else{
            $this->endpoint = 'oss-cn-shenzhen-internal.aliyuncs.com';  //内网
        }
        $this->businessDomain = 'http://img.sjyuan.cn';

        //初始化oss
        try {
            $this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        } catch (OssException $e) {
            return $this->result_fail($e->getMessage());

        }
    }

    /**
     * 上传文件
     * @param  string 上传之后的 OSS object 名称
     * @param  string 上传文件路径
     */
    public function upload($ossKey, $filePath, $options = [])
    {
        /*  成功上传的回调
        body: ""
        connection: "keep-alive"
        content-length: "0"
        content-md5: "TVVWWVEwY4POU2QEqckOAA=="
        date: "Tue, 18 Dec 2018 08:54:47 GMT"
        etag: ""4D55565951306383CE536404A9C90E00""
        info: {url: "http://jinduo-business.oss-cn-shenzhen.aliyuncs.com/admin/brand/gUvpmGkt6x38Rxrd.jpeg",…}
        oss-redirects: 0
        oss-request-url: "http://jinduo-business.oss-cn-shenzhen.aliyuncs.com/admin/brand/gUvpmGkt6x38Rxrd.jpeg"
        oss-requestheaders: {Accept-Encoding: "", Content-Type: "image/jpeg", Date: "Tue, 18 Dec 2018 08:54:52 GMT",…}
        oss-stringtosign: "PUT↵↵image/jpeg↵Tue, 18 Dec 2018 08:54:52 GMT↵/jinduo-business/admin/brand/gUvpmGkt6x38Rxrd.jpeg"
        server: "AliyunOSS"
        x-oss-hash-crc64ecma: "2550926038388709230"
        x-oss-request-id: "5C18B5D79ACC07BB6A88D19B"
        x-oss-server-time: "103"
        */

        try {
            $upload = $this->ossClient->uploadFile($this->bucketName,$ossKey, $filePath, $options);
            $data = array();
            $data['access_url'] = $this->get_public_object_url($ossKey);
            return $this->result_success($data);
        } catch (OssException $e) {
            return $this->result_fail($e->getMessage());
        }

    }


    /**
     * 删除文件
     * @param  string 目标 OSS object 名称
     * @return boolean 删除是否成功
     */
    public function delete_object($ossKey)
    {
        return $this->ossClient->deleteObject($this->bucketName, $ossKey);
    }

    // 获取公开文件的 URL
    public function get_public_object_url($ossKey)
    {
        //OSS的Object地址由域名、bucketName、object组成，具体格式为：bucketName.endpoint/object。
        //如果Bucket绑定了自定义域名例如abc.example.com，客户希望通过自定义域名来访问，
        //只需要将“bucketName.endpoint”更改为abc.example.com即可。
        //具体的自定义域名可以在下图中的域名管理页面中查看。

        return $this->businessDomain."/".$ossKey;
    }

    //错误结果
    private function result_fail($error_msg){
        $result = array();
        $result['status'] = self::STATUS_FAIL;
        $result['msg'] = $error_msg;
        $this->result = $result;
        return false;
    }

    //成功结果
    private function result_success($data=[]){
        $result = array();
        $result['status'] = self::STATUS_SUCCESS;
        $result['msg'] = 'success';
        $result['data'] = $data;
        $this->result = $result;
        return true;
    }


}