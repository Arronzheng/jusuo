<?php

namespace App\Http\Controllers\v1\admin\brand\banner\api;

use App\Models\Banner;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BannerController extends ApiController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //表格异步获取数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $limit = $request->input('limit',20);

        $entry = Banner::query();


        $entry->orderBy('position','asc');
        $entry->orderBy('sort','desc');
        $entry->orderBy('id','desc');

        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            $v->changeStatusApiUrl = url('admin/brand/banner/api/'.$v->id.'/status');
            $v->status_text = Banner::statusGroup($v->status);
            $v->position_text = Banner::positionGroup($v->position);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //权限保存
    public function store(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $validator = Validator::make($input_data, [
            'position' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        if(!key_exists($input_data['position'],Banner::positionGroup())){
            $this->respFail('位置错误！');
        }
        

        DB::beginTransaction();

        try{

            //新建
            $data = new Banner();
            $data->brand_id = $brand->id;
            $data->position = $input_data['position'];
            $data->photo = $input_data['photo'];
            $data->url = isset($input_data['url'])?$input_data['url']:'';
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;
            $data->status = Banner::STATUS_ON;
            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

    //权限更新
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'position' => 'required',
            'photo' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = Banner::where('brand_id',$brand->id)->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        if(!key_exists($input_data['position'],Banner::positionGroup())){
            $this->respFail('位置错误！');
        }

        DB::beginTransaction();

        try{


            //更新
            $data->position = $input_data['position'];
            $data->photo = $input_data['photo'];
            $data->url = isset($input_data['url'])?$input_data['url']:'';
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;

            $data->save();


            DB::commit();

            $this->respData([]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！');

        }

    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $data = Banner::where('brand_id',$brand->id)->find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==Banner::STATUS_OFF){
                $data->status = Banner::STATUS_ON;
            }else{
                $data->status = Banner::STATUS_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //上传图片
    public function upload_image(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 1024 * 2,
            'extension' => ['jpeg','jpg','png']
        ],$file);

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_BRAND_PHOTO)){
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