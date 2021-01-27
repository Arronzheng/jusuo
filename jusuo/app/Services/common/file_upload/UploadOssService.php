<?php
namespace App\Http\Services\common\file_upload;

use App\Http\Services\common\AliyunOss\BusinessOssService;
use App\Http\Services\common\StrService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadOssService
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;

    private $config;
    private $file;
    private $oss_key;

    public $result;

    //根据不同模块设定上传对象的key（不能以/开头，必须要以/结尾）
    const KEY_DIR_PUBLIC = 'images/public/';  //网站素材类

    const KEY_DIR_CERT = 'images/cert/';  //证件类
    const KEY_DIR_AVATAR = 'images/avatar/';  //头像类
    const KEY_DIR_CERT_IDCARD = 'images/cert/idcard/';  //身份证类
    const KEY_DIR_CERT_BUSINESS_LICENCE = 'images/cert/business_licence/';  //营业执照类
    const KEY_DIR_PHOTO = 'images/photo/';  //照片类
    const KEY_DIR_VIDEO = 'videos/';  //视频类
    const KEY_DIR_PRODUCT = 'images/product/';  //产品类
    const KEY_DIR_DESIGN = 'images/design/';  //设计图类
    const KEY_DIR_EDITOR = 'images/editor/';  //富文本编辑器内容
    const KEY_DIR_AUTHORIZATION = 'images/authorization/'; //授权公函

    //平台模块
    const KEY_DIR_PLATFORM_PHOTO = self::KEY_DIR_PHOTO."platform/";

    //品牌商模块
    const KEY_DIR_BRAND_IDCARD = self::KEY_DIR_CERT_IDCARD."brand/";
    const KEY_DIR_BRAND_BUSINESS_LICENCE = self::KEY_DIR_CERT_BUSINESS_LICENCE."brand/";
    const KEY_DIR_BRAND_AVATAR = self::KEY_DIR_AVATAR."brand/";
    const KEY_DIR_BRAND_PHOTO = self::KEY_DIR_PHOTO."brand/";

    //销售商模块
    const KEY_DIR_SELLER_AVATAR = self::KEY_DIR_AVATAR."seller/";
    const KEY_DIR_SELLER_IDCARD = self::KEY_DIR_CERT_IDCARD."seller/";
    const KEY_DIR_SELLER_BUSINESS_LICENCE = self::KEY_DIR_CERT_BUSINESS_LICENCE."seller/";
    const KEY_DIR_SELLER_AUTHORIZATION = self::KEY_DIR_AUTHORIZATION."seller/";
    const KEY_DIR_SELLER_PHOTO = self::KEY_DIR_PHOTO."seller/";

    //设计机构模块
    /*const KEY_DIR_DESIGN_COMPANY_AVATAR = self::KEY_DIR_AVATAR."designCompany/";
    const KEY_DIR_DESIGN_COMPANY_IDCARD = self::KEY_DIR_CERT_IDCARD."designCompany/";*/

    //设计师模块
    const KEY_DIR_DESIGNER_IDCARD = self::KEY_DIR_CERT_IDCARD."designer/";
    const KEY_DIR_DESIGNER_BUSINESS_LICENCE = self::KEY_DIR_CERT_BUSINESS_LICENCE."designer/";
    const KEY_DIR_DESIGNER_AVATAR = self::KEY_DIR_AVATAR."designer/";
    const KEY_DIR_DESIGNER_CERT = self::KEY_DIR_CERT."designer/";
    const KEY_DIR_DESIGNER_PHOTO = self::KEY_DIR_PHOTO."designer/";

    //产品模块
    const KEY_DIR_PRODUCT_PHOTO = self::KEY_DIR_PRODUCT;
    const KEY_DIR_PRODUCT_VIDEO = self::KEY_DIR_VIDEO."product/";

    //方案模块
    const KEY_DIR_ALBUM_PHOTO = self::KEY_DIR_DESIGN;


    /**
     * UploadOssService constructor.
     * @param $oss_key
     * @param $file（表单文件或本地文件路径）
     * @param array $config
     */
    public function __construct($oss_key,$file,$config=[])
    {
        $this->config = $config;
        $this->file = $file;
        $this->oss_key = $oss_key;
    }

    //表单文件上传
    public function form_upload()
    {
        //文件上传服务
        $upload_file = new FormUploadService($this->config,$this->file);

        if($check = $upload_file->simple_check()){
            //对象key
            $ext = $upload_file->extension;     // 扩展名
            $file_name = StrService::strRandom(). '.' . $ext;
            //细分文件夹：年/月/文件名前两位/xxx.png
            $strRandom = StrService::strRandom(2);
            $object_key = $this->oss_key.date('y')."/".date('m')."/".$strRandom."/".$file_name;

            //OSS上传服务
            $oss_service = new BusinessOssService();

            if(!$oss_service->upload($object_key, $this->file)){
                return $this->result_fail($oss_service->result['msg']);
            }

            return $oss_service->result['data']['access_url'];

        }else{
            return $this->result_fail($upload_file->result['msg']);
        }
    }

    //本地路径文件上传（本地绝对路径）
    public function path_upload()
    {
        //文件是否存在
        if(!file_exists($this->file)){
            return $this->result_fail('本地文件不存在！');
        }

        //对象key
        $ext = File::extension($this->file);
        $file_name = StrService::strRandom(). '.' . $ext;
        $object_key = $this->oss_key.$file_name;

        //OSS上传服务
        $oss_service = new BusinessOssService();

        if(!$oss_service->upload($object_key, $this->file)){
            return $this->result_fail($oss_service->result['msg']);
        }

        return $oss_service->result['data']['access_url'];
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