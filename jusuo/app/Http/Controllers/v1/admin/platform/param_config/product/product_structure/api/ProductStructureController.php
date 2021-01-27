<?php

namespace App\Http\Controllers\v1\admin\platform\param_config\product\product_structure\api;

use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramicStructure;
use App\Models\ProductStructure;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductStructureController extends ApiController
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
        $keyword = $request->input('keyword',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = ProductCeramicStructure::query();

        if($keyword!==null){
            $entry->where('name','like','%'.$keyword.'%');
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //权限保存
    public function store(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $exist_name = ProductCeramicStructure::where('name',$input_data['name'])
            ->first();
        if($exist_name){$this->respFail('名称已存在！');}

        DB::beginTransaction();

        try{

            //新建
            $privilege = new ProductStructure();
            $privilege->name = $input_data['name'];
            $privilege->save();

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

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = ProductCeramicStructure::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        $exist_name = ProductCeramicStructure::where('name',$input_data['name'])
            ->where('id','<>',$data->id)
            ->first();
        if($exist_name){$this->respFail('名称已存在！');}


        DB::beginTransaction();

        try{


            //更新
            $data->name = $input_data['name'];
            $data->save();


            DB::commit();

            $this->respData([]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！');

        }



    }

    //权限删除
    public function destroy(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = ProductCeramicStructure::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        //删除
        $data->delete();

        $this->respData([]);

    }

}