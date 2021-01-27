<?php

namespace App\Http\Controllers\v1\admin\api;


use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Models\Area;
use Illuminate\Http\Request;

class CommonController extends ApiController
{
    //模块业务码
    //....

    public function __construct()
    {

    }

    //获取地区数据
    public function get_area_children(Request $request)
    {
        $province_id = $request->input('pi',0);

        $data = Area::orderBy('id','asc')->where('pid',$province_id)->select(['id','name'])->get();

        return $this->respDataReturn($data);
    }

    //上传编辑器图片
    public function upload_editor_img(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($service->simple_upload('images/editor')){
            return response([
                'code' => 0,
                'msg' => 0,
                'data' => [
                    'src' => url($service->result['data']['access_path'])
                ],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            return response([
                'code' => 1,
                'msg' => $error_msg,

            ]);
        }


        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_EDITOR,$file,[
            'size' => 1024 * 200,
            'extension' => ['jpeg','jpg','png']
        ]);
        if($access_url = $service->form_upload()){
            return response([
                'code' => 0,
                'msg' => 0,
                'data' => [
                    'src' => $access_url
                ],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            return response([
                'code' => 1,
                'msg' => $error_msg,

            ]);
        }*/

    }
}
