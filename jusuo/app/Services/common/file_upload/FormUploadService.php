<?php
namespace App\Http\Services\common\file_upload;

use App\Http\Services\common\StrService;
use Illuminate\Support\Facades\Storage;

class FormUploadService
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;

    private $config = [
        'extension' => ['jpg', 'jpeg', 'png'],  //文件扩展名限制
        'size' => 1024 * 1024 * 5,  //文件大小限制
    ];

    private $file;
    public $extension;
    public $size;
    public $result;



    public function __construct($config = array(),$file)
    {
        $this->result = [
            'status' => 1,
            'msg' => 'success',
            'data' => [],
        ];

        $this->config = array_merge($this->config,$config);

        $this->file = $file;
        $this->size           = $file->getClientSize();
        $this->extension      = $file->getClientOriginalExtension(); // 真实文件扩展名d
    }


    //默认检测文件函数
    public function simple_check()
    {

        if(!$this->file){
            return $this->result_fail('请上传文件!');
        }

        if (!$this->file->isValid()) {
            return $this->result_fail('文件上传失败!');
        }

        if (!$this->check_extension()) {
            return $this->result_fail('文件类型错误!');
        }

        if (!$this->check_size()) {
            return $this->result_fail('文件大小超过限制!');
        }

        return $this->result_success();

    }
    
    //上传文件
    public function simple_upload($save_dir)
    {

        if(!$this->file){
            return $this->result_fail('请上传文件!');
        }

        if (!$this->file->isValid()) {
            return $this->result_fail('文件上传失败!');
        }

        if (!$this->check_extension()) {
            return $this->result_fail('文件类型错误!');
        }

        if (!$this->check_size()) {
            return $this->result_fail('文件大小超过限制!');
        }

        //保存文件(返回相对于storage/app/public下的路径)
        //细分文件夹：年/月/文件名前两位/xxx.png
        $save_dir = trim($save_dir,'/');
        $strRandom = StrService::strRandom(2);
        $final_dir = $save_dir."/".date('y')."/".date('m')."/".$strRandom;
        $path = $this->file->store($final_dir,'public');

        return $this->result_success(
            [
                'base_path' => $path,
                'storage_path' => storage_path('app/public/'.$path),
                'access_path' => '/storage/'.$path
            ]
        );


    }

    //检测文件扩展名
    public function check_extension()
    {
        $ext = $this->config['extension'];
        if (is_string($ext)) {
            $ext = explode(',', $ext);
        }

        return in_array($this->extension, $ext);
    }

    //检测文件大小
    public function check_size() {
        return $this->size <= $this->config['size'];
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