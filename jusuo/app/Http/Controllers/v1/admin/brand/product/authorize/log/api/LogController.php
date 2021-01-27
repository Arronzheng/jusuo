<?php

namespace App\Http\Controllers\v1\admin\brand\product\authorize\log\api;

use App\Exports\LogProductAuthorizationExport;
use App\Exports\ProductExport;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Models\AlbumProductCeramic;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\CeramicSpec;
use App\Models\LogProductAuthorization;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\OrganizationDealerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LogController extends ApiController
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
        $export = $request->input('export',null);
        $limit = $request->input('limit',10);
        $log_type = $request->input('log_type',null);
        $log_type_operation = $request->input('log_type_operation',null);
        $seller_name = $request->input('sn',null);
        $product_name = $request->input('pn',null);
        $param = $request->input('param',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $export = $request->input('export',null);

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $entry = LogProductAuthorization::query()
            ->where('brand_id',$brand->id);

        //记录类型
        if($log_type!=null){
            $entry->where('log_type',$log_type);
        }

        //操作类型
        if($log_type_operation!=null){
            $entry->where('log_type_operation',$log_type_operation);
        }

        //销售商名称
        if($seller_name!=null){
            $seller = OrganizationDealer::where('name','like','%'.$seller_name.'%')
                ->orWhere('web_id_code',$product_name)
                ->first();
            $entry->where(function($query)use($seller){
                $query->whereRaw(" find_in_set('".$seller->id."',object_ids) ");
                $query->orWhere('object_ids','all');
            });
        }

        //产品名称
        if($product_name!=null){
            $product = ProductCeramic::where('name','like','%'.$product_name.'%')
                ->orWhere('code','like','%'.$product_name.'%')
                ->orWhere('web_id_code','like','%'.$product_name.'%')
                ->first();
            $entry->whereRaw(" find_in_set('".$product->id."',product_ids) ");
        }

        //授权内容关键词
        if($param!=null){
            $entry->where('log_type_param','like','%'.$param.'%');

        }

        //时间
        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }


        $entry->orderBy('id','desc');

        $datas =$entry->paginate($limit);

        $datas->transform(function($v){
            $v->admin_name = '';
            $admin = AdministratorBrand::find($v->administrator_id);
            if($admin){
                $v->admin_name = $admin->login_username;
            }
            $v->log_type_text = LogProductAuthorization::logTypeGroup(isset($v->log_type)?$v->log_type:'');
            $v->log_type_operation_text = LogProductAuthorization::logTypeOperationGroup(isset($v->log_type_operation)?$v->log_type_operation:'');
            $content = '';
            $product_ids = explode(',',$v->product_ids);
            $products = ProductCeramic::whereIn('id',$product_ids)->get()->pluck('code')->toArray();
            $v->products = implode('、',$products);
            $object_ids = explode(',',$v->object_ids);
            $objects = OrganizationDealer::whereIn('id',$object_ids)->select(['organization_account','name'])->get()->toArray();
            $objects_array = [];
            for($i=0;$i<count($objects);$i++){
                $text = $objects[$i]['name']."(".$objects[$i]['organization_account'].")";
                array_push($objects_array,$text);
            }
            $v->objects = implode('、',$objects_array);
            switch($v->log_type){
                case LogProductAuthorization::LOG_TYPE_SHOW:
                    $content = "授权产品";
                break;
                case LogProductAuthorization::LOG_TYPE_STRUCTURE:
                    $content = $v->log_type_param;
                    break;
                case LogProductAuthorization::LOG_TYPE_PRICE:
                    $content = $v->log_type_param;
                    break;
                default:break;
            }
            $v->content = $content;
            return $v;
        });

        if($export!=null){
            return $this->export($datas,$log_type);
        }

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //导出表格
    private function export($datas,$log_type)
    {

        $result = [
            [
                'id','操作管理员','记录类型','操作类型','相关产品','相关销售商','授权内容','操作时间'
            ]
        ];

        $log_type_name = LogProductAuthorization::logTypeGroup($log_type);


        foreach($datas as $v){
            $resultItem = [];
            $resultItem[] = $v->id;
            $resultItem[] = $v->admin_name;
            $resultItem[] = $v->log_type_text;
            $resultItem[] = $v->log_type_operation_text;
            $resultItem[] = $v->products;
            $resultItem[] = $v->objects;
            $resultItem[] = $v->content;
            $resultItem[] = $v->created_at;

            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new LogProductAuthorizationExport($result),$log_type_name.'记录'.date('Y-m-d_H_i_s') . '.xls');
    }


}