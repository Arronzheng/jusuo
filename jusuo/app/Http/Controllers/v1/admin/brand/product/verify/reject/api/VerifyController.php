<?php

namespace App\Http\Controllers\v1\admin\brand\product\verify\reject\api;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Http\Services\v1\admin\SubAdminService;
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
use App\Models\LogProductCeramic;
use App\Models\MsgProductCeramicBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\MsgProductCeramicBrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VerifyController extends ApiController
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

        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = LogProductCeramic::query()
            ->where('is_approved',LogProductCeramic::IS_APROVE_REJECT); //显示待审核的产品


        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $product = $v->target_product;
            if($product) {
                $v->name = $product->name;
            }
            else{
                $v->name = '（此产品已删除）';
            }
            $admin = SubAdminService::getBrandAdminName($v->created_administrator_id);
            if($admin['name']<>'')
                $v->created_by = $admin['name'].'('.$admin['department'].','.$admin['position'].')';
            else
                $v->created_by = '';
            $admin = SubAdminService::getBrandAdminName($v->created_administrator_id);
            if($admin['name']<>'')
                $v->approved_by = $admin['name'].'('.$admin['department'].','.$admin['position'].')';
            else
                $v->approved_by = '';
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

}