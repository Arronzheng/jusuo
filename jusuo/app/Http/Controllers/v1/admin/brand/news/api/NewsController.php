<?php

namespace App\Http\Controllers\v1\admin\brand\news\api;

use App\Http\Services\common\StrService;
use App\Models\Banner;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\NewsBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use EasyWeChat\Work\GroupRobot\Messages\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewsController extends ApiController
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

        $entry = NewsBrand::query();


        $entry->orderBy('sort','desc');
        $entry->orderBy('id','desc');

        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v)use($brand){

            $v->changeStatusApiUrl = url('admin/brand/news/api/'.$v->id.'/status');
            $v->status_text = NewsBrand::statusGroup($v->status);
            $v->url = url('/news/'.$v->web_id_code.'?__bs='.$brand->web_id_code);

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
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        DB::beginTransaction();

        try{

            //新建
            $data = new NewsBrand();
            $data->brand_id = $brand->id;
            $id_code = StrService::str_random_field_value('news_brands','web_id_code',16,10);
            if($id_code['tryLeft']>0){
                $data->web_id_code = $id_code['string'];
            }else{
                $this->respFail('无法生成标识，请重新再试！');
            }
            $data->title = $input_data['title'];
            $data->content = $input_data['content'];
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;
            $data->status = NewsBrand::STATUS_ON;
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
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = NewsBrand::where('brand_id',$brand->id)->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }


        DB::beginTransaction();

        try{


            //更新
            $data->title = $input_data['title'];
            $data->content = $input_data['content'];
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


        $data = NewsBrand::where('brand_id',$brand->id)->find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==NewsBrand::STATUS_OFF){
                $data->status = NewsBrand::STATUS_ON;
            }else{
                $data->status = NewsBrand::STATUS_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //删除
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
            $data = NewsBrand::find($inputData['id']);
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

}