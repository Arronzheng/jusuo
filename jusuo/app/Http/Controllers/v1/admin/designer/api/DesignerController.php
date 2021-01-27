<?php

namespace App\Http\Controllers\v1\admin\designer\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Models\TestData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DesignerController extends ApiController
{
    //表格异步获取数据
    public function index(Request $request)
    {
        $keyword = $request->input('keyword',null);
        $status = $request->input('status',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = TestData::query();

        if($keyword!==null){
            $entry = $entry->where('name','like','%'.$keyword.'%');
        }

        if($status!==null){
            $entry = $entry->where('status',$status);
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $hobbyArray = json_decode($v->hobby);
            $hobbyResult = [];
            for($i=0;$i<count($hobbyArray);$i++){
                array_push($hobbyResult,TestData::$hobbyGroup[$hobbyArray[$i]]);
            }
            $v->hobby = implode('，',$hobbyResult);
            $v->type_text = TestData::typeGroup($v->type);
            $v->status_text = TestData::statusGroup($v->status);

            unset($v->type);
            unset($v->status);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //新增数据提交示例
    public function store(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'name' => 'required',
            'intro' => 'required',
            'type' => 'required',
            'status' => 'required',
            'avatar' => 'required',
            'desc' => 'required',
            'hobby' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //处理数据
        $hobby = json_encode($inputData['hobby']);

        DB::beginTransaction();

        try{

            //新建
            $data = new TestData();
            $data->name = $inputData['name'];
            $data->intro = $inputData['intro'];
            $data->type = $inputData['type'];
            $data->status = $inputData['status'];
            $data->avatar = $inputData['avatar'];
            $data->desc = $inputData['desc'];
            $data->hobby = $hobby;
            $saveResult = $data->save();

            if(!$saveResult){
                $this->respFail('操作错误！');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！');
        }

    }

    //更新数据提交示例
    public function update(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'id' => 'required',
            'name' => 'required',
            'intro' => 'required',
            'type' => 'required',
            'status' => 'required',
            'avatar' => 'required',
            'desc' => 'required',
            'hobby' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //处理数据
        $hobby = json_encode($inputData['hobby']);

        DB::beginTransaction();

        try{

            //更新
            $data = TestData::find($inputData['id']);
            if(!$data){
                $this->respFail('信息不存在！');
            }
            $data->name = $inputData['name'];
            $data->intro = $inputData['intro'];
            $data->type = $inputData['type'];
            $data->status = $inputData['status'];
            $data->avatar = $inputData['avatar'];
            $data->desc = $inputData['desc'];
            $data->hobby = $hobby;
            $saveResult = $data->save();

            if(!$saveResult){
                $this->respFail('操作错误！');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！');
        }

    }

    //删除数据提交示例
    public function destroy(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('参数错误！');
        }

        DB::beginTransaction();

        try{

            //更新
            $data = TestData::find($inputData['id']);
            if(!$data){
                $this->respFail('信息不存在！');
            }

            $result = $data->delete();

            if(!$result){
                $this->respFail('操作错误！');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！');
        }

    }

    //上传图片示例
    public function upload_img(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload('test/temp')){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }

        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_IDCARD,$file,[
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ]);

        if($access_url = $service->form_upload()){
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }
}