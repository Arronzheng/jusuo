<?php

namespace App\Http\Controllers\v1\admin\brand\integral\goods_import\api;

use App\Exports\IntegralGoodExport;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\IntegralGoodAuthorization;
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
        $loginAdmin = $this->authService->getAuthUser();
        $loginBrand = $loginAdmin->brand;

        $limit = $request->input('limit',20);
        $integral_brand_id = $request->input('bi',null);
        $category_id_2 = $request->input('cid',null);
        $keyword = $request->input('keyword',null);
        $integral_start = $request->input('integral_start',null);
        $integral_end = $request->input('integral_end',null);
        $export = $request->input('export',null);
        $limit = $request->input('limit',10);


        $entry = DB::table('integral_goods as ig')
            ->select([
                'ig.*',
                'ig.id','ig.name','ig.web_id_code','ig.market_price','ig.integral','ig.integral_brand_id',
                'ig.category_id_1','ig.category_id_2','ig.photo',
                'iga.status','iga.sort','iga.created_at','iga.exchange_amount',
                'iga.id as authorization_id'
            ])
            ->join('integral_good_authorizations as iga','iga.good_id','=','ig.id')
            ->where('iga.brand_id',$loginBrand->id);


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

        $entry->orderBy('iga.sort','desc');
        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));



        $datas->transform(function($v)use($loginBrand){

            $photoArray = unserialize($v->photo);
            if(is_array($photoArray)&&count($photoArray)>0){
                $v->photo = $photoArray[0];
            }
            else{
                $v->photo = null;
            }

            //判断当前商品是否已引入
            $v->is_import = false;
            if($v->status == IntegralGoodAuthorization::STATUS_BRAND_USED){
                $v->is_import = true;
            }

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
            $v->changeImportApiUrl = url('admin/brand/integral/goods_import/api/'.$v->authorization_id.'/import');
            $v->status_text = IntegralGoodAuthorization::statusGroup($v->status);

            //引入商品，兑换量、排序在authorization表
            $v->exchange_amount = 0;
            $v->sort = 0;
            $authorization = IntegralGoodAuthorization::where('brand_id',$loginBrand->id)
                ->where('good_id',$v->id)
                ->first();
            if($authorization){
                $v->exchange_amount = $authorization->exchange_amount;
                $v->sort = $authorization->sort;
            }
            return $v;
        });

        if($export!=null){
            return $this->export($datas);
        }

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //更改引入状态
    public function change_import($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $loginBrand = $loginAdmin->brand;


        $data = IntegralGoodAuthorization::where('brand_id',$loginBrand->id)
            ->find($id);

        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==IntegralGoodAuthorization::STATUS_BRAND_USED){
                $data->status = IntegralGoodAuthorization::STATUS_BRAND_CAN_USE;
            }else{
                $data->status = IntegralGoodAuthorization::STATUS_BRAND_USED;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //导出表格
    private function export($datas)
    {

        $result = [
            [
                'id','名称','市场价','积分','品牌','分类','状态','排序','兑换量'
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
            $resultItem[] = $v->exchange_amount.'';

            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new IntegralGoodExport($result),'积分商品引入记录'.date('Y-m-d_H_i_s') . '.xls');
    }
}