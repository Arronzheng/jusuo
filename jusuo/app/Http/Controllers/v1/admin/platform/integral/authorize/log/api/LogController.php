<?php

namespace App\Http\Controllers\v1\admin\platform\integral\authorize\log\api;

use App\Exports\LogIntegralGoodAuthorizationExport;
use App\Exports\ProductExport;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Models\AdministratorPlatform;
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
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\LogIntegralGoodAuthorization;
use App\Models\OrganizationBrand;
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
        $log_type = $request->input('log_type','');
        $log_type_operation = $request->input('log_type_operation',null);
        $brand_name = $request->input('bn',null);
        $good_name = $request->input('gn',null);
        $param = $request->input('param',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $export = $request->input('export',null);
        $limit = $request->input('limit',10);

        $loginAdmin = $this->authService->getAuthUser();

        $entry = LogIntegralGoodAuthorization::query();

        //记录类型
        if($log_type!=null){
            $entry->where('log_type',$log_type);
        }

        //操作类型
        if($log_type_operation!=null){
            $entry->where('log_type_operation',$log_type_operation);
        }

        //品牌名称
        if($brand_name!=null){
            $brand = OrganizationBrand::where('name','like','%'.$brand_name.'%')
                ->orWhere('web_id_code',$brand_name)
                ->first();
            $entry->where(function($query)use($brand){
                $query->whereRaw(" find_in_set('".$brand->id."',object_ids) ");
            });
        }

        //商品名称
        if($good_name!=null){
            $integral_good = IntegralGood::where('name','like','%'.$good_name.'%')
                ->orWhere('web_id_code','like','%'.$good_name.'%')
                ->first();
            $entry->whereRaw(" find_in_set('".$integral_good->id."',good_ids) ");
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
            $admin = AdministratorPlatform::find($v->administrator_id);
            if($admin){
                $v->admin_name = $admin->login_username;
            }
            $v->log_type_text = LogIntegralGoodAuthorization::logTypeGroup(isset($v->log_type)?$v->log_type:'');
            $v->log_type_operation_text = LogIntegralGoodAuthorization::logTypeOperationGroup(isset($v->log_type_operation)?$v->log_type_operation:'');
            $good_ids = explode(',',$v->good_ids);
            $goods = IntegralGood::whereIn('id',$good_ids)->get()->pluck('name')->toArray();
            $v->goods = implode('、',$goods);
            $object_ids = explode(',',$v->object_ids);
            $objects = OrganizationBrand::whereIn('id',$object_ids)->select(['brand_name','organization_account','name'])->get()->toArray();
            $objects_array = [];
            for($i=0;$i<count($objects);$i++){
                $text = $objects[$i]['brand_name']."(".$objects[$i]['organization_account'].")";
                array_push($objects_array,$text);
            }
            $v->objects = implode('、',$objects_array);
            $v->content = LogIntegralGoodAuthorization::logTypeGroup($v->log_type);;
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
                'id','操作管理员','记录类型','操作类型','相关商品','相关品牌','授权内容','操作时间'
            ]
        ];

        $log_type_name = LogIntegralGoodAuthorization::logTypeGroup($log_type==null?'':$log_type);


        foreach($datas as $v){
            $resultItem = [];
            $resultItem[] = $v->id;
            $resultItem[] = $v->admin_name;
            $resultItem[] = $v->log_type_text;
            $resultItem[] = $v->log_type_operation_text;
            $resultItem[] = $v->goods;
            $resultItem[] = $v->objects;
            $resultItem[] = $v->content;
            $resultItem[] = $v->created_at;

            array_push($result,$resultItem);
        }

        //die(json_encode($result));

        // download 方法直接下载，store 方法可以保存。具体的导出类型等看官方的文档吧
        return Excel::download(new LogIntegralGoodAuthorizationExport($result),'积分商品开放记录'.date('Y-m-d_H_i_s') . '.xls');
    }


}