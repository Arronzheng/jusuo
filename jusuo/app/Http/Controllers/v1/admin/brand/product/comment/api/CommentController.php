<?php

namespace App\Http\Controllers\v1\admin\brand\product\comment\api;

use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\DesignerDetail;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\ProductQa;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends ApiController
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
        $asker = $request->input('asker',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $status = $request->input('status',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = ProductQa::query();

        $entry->whereHas('product',function($product)use($brand,$keyword){
            $product->where('brand_id',$brand->id);

            //筛选产品名称/编号
            if($keyword!==null){
                $product->where(function($query) use($keyword){
                    $query->where('name','like','%'.$keyword.'%');
                    $query->orWhere('code','like','%'.$keyword.'%');
                });
            }
        });

        //筛选提问者
        if($asker!==null){
            $entry->whereHas('designer.detail',function($designer) use($asker){
                $designer->where('nickname','like','%'.$asker.'%');
            });
        }

        //筛选状态
        if($status!==null){
            $entry->where('status',$status);
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

            $v->product_name = '';
            $v->product_web_id_code = '';
            $product = ProductCeramic::find($v->product_id);
            if($product){
                $v->product_name = $product->name."(".$product->code.")";
                $v->product_web_id_code = $product->web_id_code;
            }
            $designer = DesignerDetail::where('designer_id',$v->question_designer_id)->first();
            if($designer){
                $v->questioner_nickname = $designer->nickname;
            }

            $v->changeStatusApiUrl = url('admin/brand/product/comment/api/'.$v->id.'/status');
            $v->status_text = ProductQa::statusGroup($v->status);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //更新
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = ProductQa::query()
            ->whereHas('product',function($product)use($brand){
                $product->where('brand_id',$brand->id);
            })
            ->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }

        DB::beginTransaction();

        try{


            //更新
            $data->answer = $input_data['answer'];
            $data->answered_at = (string)Carbon::now();
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


        $data = ProductQa::query()
            ->whereHas('product',function($product)use($brand){
                $product->where('brand_id',$brand->id);
            })
            ->find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==ProductQa::STATUS_OFF){
                $data->status = ProductQa::STATUS_ON;
            }else{
                $data->status = ProductQa::STATUS_OFF;
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