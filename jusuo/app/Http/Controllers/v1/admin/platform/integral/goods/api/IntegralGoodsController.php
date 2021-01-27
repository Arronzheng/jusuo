<?php

namespace App\Http\Controllers\v1\admin\platform\integral\goods\api;

use App\Exports\IntegralGoodExport;
use App\Http\Services\common\StrService;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\IntegralGoodsCategory;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IntegralGoodsController extends ApiController
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
        $limit = $request->input('limit',20);
        $integral_brand_id = $request->input('bi',null);
        $category_id_2 = $request->input('cid',null);
        $keyword = $request->input('keyword',null);
        $integral_start = $request->input('integral_start',null);
        $integral_end = $request->input('integral_end',null);
        $export = $request->input('export',null);
        $limit = $request->input('limit',10);


        $entry = IntegralGood::query();

        if($integral_start!==null && $integral_end!==null && $integral_start>0 && $integral_end>0){
            $integral_start = intval($integral_start);
            $integral_end = intval($integral_end);
            $entry->whereBetween('integral',[$integral_start,$integral_end]);
        }

        if($keyword!==null && $keyword){
            $entry->where('name','like','%'.$keyword.'%');
        }

        if($integral_brand_id!==null && $integral_brand_id>0){
            $entry->where('integral_brand_id',$integral_brand_id);
        }

        if($category_id_2!==null && $category_id_2>0){
            $entry->where('category_id_2',$category_id_2);
        }

        $entry->select([
            'id','name','market_price','integral','integral_brand_id','category_id_1','category_id_2',
            'status','sort','created_at','exchange_amount','photo','top_status','cover']);
        $entry->where('brand_id',0);
        $entry->where('is_delete',IntegralGood::IS_DELETE_NO);
        $entry->orderBy('sort','desc');
        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            $v->brand_name = '';
            if($v->integral_brand_id){
                $brand = IntegralBrand::find($v->integral_brand_id);
                if($brand){ $v->brand_name = $brand->name; }
            }
            $v->category_text = '';
            if($v->category_id_2 && $v->category_id_1){
                $category_lv2 = IntegralGoodsCategory::query()
                    ->where('id',$v->category_id_2)
                    ->where('pid','<>',0)
                    ->first();
                $category_lv1 = IntegralGoodsCategory::query()
                    ->where('id',$v->category_id_1)
                    ->where('pid',0)
                    ->first();
                if($category_lv2 &&$category_lv1){
                    $v->category_text = $category_lv1->name.'/'.$category_lv2->name;
                }
            }
            $v->changeStatusApiUrl = url('admin/platform/integral/goods/api/'.$v->id.'/status');
            $v->changeTopApiUrl = url('admin/platform/integral/goods/api/'.$v->id.'/top');
            $v->status_text = IntegralGood::statusGroup($v->status);
            $v->top_status_text = IntegralGood::topStatusGroup($v->top_status);

            return $v;
        });

        if($export!=null){
            return $this->export($datas);
        }

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //保存
    public function store(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();

        $validator = Validator::make($input_data, [
            'photo' => 'required',
            'name' => 'required',
            'integral_brand_id' => 'required',
            'category_id_2' => 'required',
            'sort' => 'present',
            'market_price' => 'required',
            'integral' => 'required',
            'detail' => 'present',
            'photo_promote' => 'present',
            'photo_video' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
            /*$messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->respFail($msg_result);*/
        }

        //其他校验

        $brand = IntegralBrand::find($input_data['integral_brand_id']);
        if(!$brand){
            $this->respFail('品牌不存在！');
        }

        //商品参数
        $goods_param = [];
        if(isset($input_data['param_key']) && $input_data['param_key']) {
            for ($i = 0; $i < count($input_data['param_key']); $i++) {
                $goods_param[$i]['key'] = $input_data['param_key'][$i];
                $goods_param[$i]['value'] = $input_data['param_value'][$i];
            }
        }
        $goods_param = serialize($goods_param);

        //分类
        $category_lv2 = IntegralGoodsCategory::query()
            ->where('pid','<>',0)
            ->where('id',$input_data['category_id_2'])
            ->first();
        if(!$category_lv2){
            $this->respFail('分类不存在！');
        }
        $category_lv1 = IntegralGoodsCategory::query()
            ->where('id',$category_lv2->pid)
            ->first();
        if(!$category_lv1){
            $this->respFail('分类不存在!！');
        }

        //礼品图至少有以一个
        if(!isset($input_data['photo']) || count($input_data['photo']) <=0){
            $this->respFail('请至少上传一个礼品图片！');
        }
        

        DB::beginTransaction();

        try{

            //新建
            $data = new IntegralGood();
            $id_code = StrService::str_random_field_value('integral_goods','web_id_code',16,10);
            if($id_code['tryLeft']>0){
                $data->web_id_code = $id_code['string'];
            }else{
                $this->respFail('无法生成标识，请重新再试！');
            }
            $data->integral_brand_id = $input_data['integral_brand_id'];
            $data->photo = \Opis\Closure\serialize($input_data['photo']);
            //以第一个礼品图为封面图
            $data->cover = $input_data['photo'][0];
            $data->name = $input_data['name'];
            $data->sort = intval($input_data['sort']);
            $data->integral = intval($input_data['integral']);
            $data->market_price = floatval($input_data['market_price']);
            $data->detail = $input_data['detail'];
            $data->param = $goods_param;
            $data->category_id_1 = $category_lv1->id;
            $data->category_id_2 = $category_lv2->id;
            $data->photo_promote = isset($input_data['photo_promote'])?$input_data['photo_promote']:'';
            $data->photo_video = isset($input_data['photo_video'])?$input_data['photo_video']:'';
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;
            $data->status = IntegralGood::STATUS_ON;
            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

    //更新
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'photo' => 'required',
            'name' => 'required',
            'integral_brand_id' => 'required',
            'category_id_2' => 'required',
            'sort' => 'present',
            'market_price' => 'required',
            'integral' => 'required',
            'detail' => 'present',
            'photo_promote' => 'present',
            'photo_video' => 'present',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = IntegralGood::query()->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        //其他校验

        $brand = IntegralBrand::find($input_data['integral_brand_id']);
        if(!$brand){
            $this->respFail('品牌不存在！');
        }

        //商品参数
        $goods_param = [];
        if(isset($input_data['param_key']) && $input_data['param_key']) {
            for ($i = 0; $i < count($input_data['param_key']); $i++) {
                $goods_param[$i]['key'] = $input_data['param_key'][$i];
                $goods_param[$i]['value'] = $input_data['param_value'][$i];
            }
        }
        $goods_param = serialize($goods_param);


        //分类
        $category_lv2 = IntegralGoodsCategory::query()
            ->where('pid','<>',0)
            ->where('id',$input_data['category_id_2'])
            ->first();
        if(!$category_lv2){
            $this->respFail('分类不存在！');
        }
        $category_lv1 = IntegralGoodsCategory::query()
            ->where('id',$category_lv2->pid)
            ->first();
        if(!$category_lv1){
            $this->respFail('分类不存在!！');
        }

        DB::beginTransaction();

        try{


            //更新
            $data->integral_brand_id = $input_data['integral_brand_id'];
            $data->photo = \Opis\Closure\serialize($input_data['photo']);
            //以第一个礼品图为封面图
            $data->cover = $input_data['photo'][0];
            $data->name = $input_data['name'];
            $data->integral = intval($input_data['integral']);
            $data->sort = intval($input_data['sort']);
            $data->market_price = floatval($input_data['market_price']);
            $data->detail = $input_data['detail'];
            $data->param = $goods_param;
            $data->category_id_1 = $category_lv1->id;
            $data->category_id_2 = $category_lv2->id;
            $data->photo_promote = isset($input_data['photo_promote'])?$input_data['photo_promote']:'';
            $data->photo_video = isset($input_data['photo_video'])?$input_data['photo_video']:'';
            $data->sort = isset($input_data['sort'])?intval($input_data['sort']):0;

            $data->save();


            DB::commit();

            $this->respData([]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！');

        }

    }

    //更改状态
    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();


        $data = IntegralGood::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==IntegralGood::STATUS_OFF){
                $data->status = IntegralGood::STATUS_ON;
            }else{
                $data->status = IntegralGood::STATUS_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //更改置顶
    public function change_top($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();


        $data = IntegralGood::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        if($data->top_status==IntegralGood::TOP_STATUS_OFF && !$data->photo_promote){
            $this->respFail('请先设置推荐图再置顶');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->top_status==IntegralGood::TOP_STATUS_OFF){
                $data->top_status = IntegralGood::TOP_STATUS_ON;
                $data->top_time = Carbon::now();
            }else{
                $data->top_status = IntegralGood::TOP_STATUS_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }


    //软删除
    public function destroy(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = IntegralGood::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        //删除
        $data->is_delete = IntegralGood::IS_DELETE_YES;
        $data->delete_time = Carbon::now();
        $data->save();

        $this->respData([]);

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

    //相关视频上传
    public function upload_video(Request $request)
    {
        $file = $request->file('file');

        //本地上传
        $service = new FormUploadService([
            'size' => 1024 * 50000,
            'extension' => ['mp4']
        ],$file);

        $loginAdmin = $this->authService->getAuthUser();

        if($access_url = $service->simple_upload(UploadOssService::KEY_DIR_PRODUCT_VIDEO."/platform/")){
            $this->respData([
                'access_path'=>$service->result['data']['access_path'],
                'base_path'=>$service->result['data']['base_path'],
            ]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }

        //oss上传
        /*$service = new UploadOssService(UploadOssService::KEY_DIR_BRAND_PRODUCT,$file,[
            'size' => 1024 * 200,
            'extension' => ['jpg','png']
        ]);
        if($access_url = $service->form_upload()){
            $this->respData(['access_url'=>$access_url]);
        }else{
            $error_msg = $service->result['msg'];
            $this->respFail($error_msg);
        }*/

    }

    //导出表格
    private function export($datas)
    {

        $result = [
            [
                'id','名称','市场价','积分','品牌','分类','状态','排序','添加时间','兑换量'
            ]
        ];


        foreach($datas as $v){
            $resultItem = [];
            $resultItem[] = $v->id.'';
            $resultItem[] = $v->name.'';
            $resultItem[] = $v->market_price.'';
            $resultItem[] = $v->integral.'';
            $resultItem[] = $v->brand_name.'';
            $resultItem[] = $v->category_text.'';
            $resultItem[] = $v->status_text.'';
            $resultItem[] = $v->sort.'';
            $resultItem[] = $v->created_at.'';
            $resultItem[] = $v->exchange_amount.'';

            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new IntegralGoodExport($result),'积分商品记录'.date('Y-m-d_H_i_s') . '.xls');
    }
}