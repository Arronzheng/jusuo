<?php

namespace App\Http\Controllers\v1\admin\brand\product\series\api;

use App\Models\CeramicSeries;
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

class SeriesController extends ApiController
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

        $keyword = $request->input('keyword',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = CeramicSeries::query();

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('short_name','like','%'.$keyword.'%');
            });
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $product_count = ProductCeramic::where('series_id',$v->id)->count();
            $product_on_count = ProductCeramic::where('series_id',$v->id)->where('status',ProductCeramic::STATUS_PASS)->where('visible',ProductCeramic::VISIBLE_YES)->count();
            $product_off_count = ProductCeramic::where('series_id',$v->id)->where('status',ProductCeramic::STATUS_PASS)->where('visible',ProductCeramic::VISIBLE_NO)->count();
            $v->product_count = $product_count;
            $v->product_on_count = $product_on_count;
            $v->product_off_count = $product_off_count;
            $v->changeStatusApiUrl = url('admin/brand/product/series/api/'.$v->id.'/status');
            $v->status_text = CeramicSeries::statusGroup($v->status);

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
            'name' => 'required',
            'short_name' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $exist_name = CeramicSeries::where('name',$input_data['name'])
            ->first();
        if($exist_name){$this->respFail('名称已存在！');}
        

        DB::beginTransaction();

        try{

            //新建
            $data = new CeramicSeries();
            $data->brand_id = $brand->id;
            $data->series_code = CeramicSeries::get_series_code($brand->id);
            $data->name = $input_data['name'];
            $data->short_name = $input_data['short_name'];
            $data->description = $input_data['description'];
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
            'name' => 'required',
            'short_name' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = CeramicSeries::where('brand_id',$brand->id)->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        $exist_name = CeramicSeries::where('name',$input_data['name'])
            ->where('id','<>',$data->id)
            ->first();
        if($exist_name){$this->respFail('名称已存在！');}


        DB::beginTransaction();

        try{


            //更新
            $data->name = $input_data['name'];
            $data->short_name = $input_data['short_name'];
            $data->description = $input_data['description'];
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


        $data = CeramicSeries::where('brand_id',$brand->id)->find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==CeramicSeries::STATUS_OFF){
                $data->status = CeramicSeries::STATUS_ON;
            }else{
                $data->status = CeramicSeries::STATUS_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }
}