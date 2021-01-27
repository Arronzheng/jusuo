<?php

namespace App\Http\Controllers\v1\admin\platform\integral\category\api;

use App\Http\Services\common\InfiniteTreeService;
use App\Models\IntegralGoodsCategory;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\IntegralGood;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiController
{
    private $authService;
    private $infiniteTreeService;

    public function __construct(
        AuthService $authService,InfiniteTreeService $infiniteTreeService
    )
    {
        $this->authService = $authService;
        $this->infiniteTreeService = $infiniteTreeService;
    }

    //表格异步获取数据
    public function index(Request $request)
    {

        $entry = IntegralGoodsCategory::query();


        $entry->orderBy('sort','desc');
        $entry->orderBy('id','desc');

        $datas =$entry->get();

        $datas = $this->infiniteTreeService->getFlatTree($datas,'pid');

        $datas = collect($datas)->transform(function($v){
            $v->name = str_repeat('|-- ',$v->level).$v->name;
            $v->changeStatusApiUrl = url('admin/platform/integral/category/api/'.$v->id.'/status');

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponseNoPage($datas);

        return json_encode($datas);
    }

    //权限保存
    public function store(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();

        $validator = Validator::make($input_data, [
            'name' => 'required',
            'pid' => 'present',
            'photo' => 'present',
            'sort' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }


        DB::beginTransaction();

        try{

            //新建
            $data = new IntegralGoodsCategory();
            $data->name = isset($input_data['name'])?$input_data['name']:'';
            $data->pid = isset($input_data['pid'])?$input_data['pid']:0;
            $data->photo = isset($input_data['photo'])?$input_data['photo']:'';
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;
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


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'name' => 'required',
            'photo' => 'present',
            'sort' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = IntegralGoodsCategory::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }


        DB::beginTransaction();

        try{


            //更新
            $data->name = isset($input_data['name'])?$input_data['name']:'';
            $data->photo = isset($input_data['photo'])?$input_data['photo']:'';
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;
            $data->save();


            DB::commit();

            $this->respData([]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！');

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

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_PLATFORM_PHOTO)){
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

    //更改状态
    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();


        $data = IntegralGoodsCategory::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==IntegralGoodsCategory::STATUS_OFF){
                $data->status = IntegralGoodsCategory::STATUS_ON;
            }else{
                $data->status = IntegralGoodsCategory::STATUS_OFF;
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
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = IntegralGoodsCategory::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }


        //判断是否有商品绑定了本分类或子分类
        $id = $input_data['id'];

        $exist_child = IntegralGoodsCategory::where('pid',$id)->first();
        if($exist_child){
            $this->respFail('该分类有子分类，无法删除！');
        }

        $exist = IntegralGood::query()
            ->where(function($query) use($id){
                $query->where('category_id_1',$id);
            })
            ->orWhere(function($query) use($id){
                $query->where('category_id_2',$id);
            })
            ->count();
        if($exist>0){
            $this->respFail('已有商品绑定了本分类，无法删除！');
        }

        $data->delete();

        $this->respData([]);

    }
}