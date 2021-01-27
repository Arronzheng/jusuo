<?php

namespace App\Http\Controllers\v1\admin\platform\info_statistics\activity\brand\api;

use App\Http\Controllers\Controller;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\PrivilegeSellerService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\AdministratorRoleBrand;
use App\Models\Area;
use App\Models\CertificationBrand;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\MsgAccountBrand;
use App\Models\MsgSystemBrand;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\PrivilegeDealer;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\RolePrivilegeBrand;
use App\Services\v1\admin\MsgAccountBrandMultiService;
use App\Services\v1\admin\MsgAccountBrandService;
use App\Services\v1\admin\MsgSystemBrandService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class AccountController extends ApiController
{
    private $globalService;
    private $roleOrganizationRepository;
    private $authService;
    private $infiniteTreeService;
    private $getNameServices;

    public function __construct(
        GlobalService $globalService,
        AuthService $authService,
        InfiniteTreeService $infiniteTreeService,
        GetNameServices $getNameServices
    ){
        $this->globalService = $globalService;
        $this->authService = $authService;
        $this->infiniteTreeService = $infiniteTreeService;
        $this->getNameServices = $getNameServices;
    }


    //账号列表数据
    public function account_index(Request $request)
    {

        $area_belong_id = $request->input('abi',null);
        $product_category_id = $request->input('pc',null);
        $name = $request->input('name',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = DB::table('administrator_brands as ab');

        //筛选经营品类
        if($product_category_id!==null){
            $entry = $entry->where('b.product_category',$product_category_id);
        }

        //筛选所在城市
        if($area_belong_id!==null){
            $entry = $entry->where('bd.area_belong_id',$area_belong_id);
        }

        //名称搜索
        if($name!==null){
            $entry = $entry->where('b.name','like',"%".$name."%");
        }

        //开通时间筛选
        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('lbc.updated_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderBy($sort,$order);
        }

        $entry->orderBy('b.id','desc');
        $entry->groupBy('b.id');

        $entry->join('organization_brands as b','ab.brand_id','=','b.id')
            ->join('log_brand_certifications as lbc','lbc.target_brand_id','=','b.id')
            ->join('detail_brands as bd','bd.brand_id','=','b.id')
            //->leftJoin('statistic_account_brands as stat','stat.brand_id','=','b.id')
            ->leftJoin('statistic_account_brands as stat','stat.id','=',DB::raw("
                 (select id from statistic_account_brands t where t.brand_id = b.id
                order by t.id desc limit 1)
            "))
            ->select([
                'ab.id as admin_id','ab.login_username','ab.login_account',
                'b.id as brand_id','b.brand_name','b.name as company_name','b.product_category',
                'b.created_at','ab.status as account_status',
                'b.status as brand_status','b.top_status',
                'stat.count_album_increase_day_7','stat.count_product_increase_day_7','stat.count_dealer_lv1_increase_day_7'
                ,'stat.count_dealer_lv2_increase_day_7','stat.count_designer_increase_day_7',
                'lbc.updated_at as start_at',
                'bd.area_belong_id'])
            ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)
            ->where('lbc.is_approved',LogBrandCertification::IS_APROVE_APPROVAL);

        $datas = $entry->paginate($limit);

        $datas->transform(function($v){
            if ($v->area_belong_id){
                $district = Area::where('id',$v->area_belong_id)->first();
                if ($district){
                    $city =  Area::where('id',$district->pid)->first();
                    if ($city){
                        $province =  Area::where('id',$city->pid)->first();
                        if ($province){
                            $v->area_belong_text = $province->name.'/'.$city->name.'/'.$district->name;
                        }
                    }
                }
            }
            $product_category_id = $v->product_category;
            $v->product_category_name = '';
            $product_category = ProductCategory::find($product_category_id);
            if($product_category){
                $v->product_category_name = $product_category->name;
            }
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //修改置顶状态
    public function change_status($id, Request $request)
    {
        $brand = OrganizationBrand::query()
            ->find($id);
        if(!$brand){
            die('数据不存在');
        }

        if($brand->status != OrganizationBrand::STATUS_ON){
            die('品牌未审核');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($brand->top_status==OrganizationBrand::TOP_STATUS_OFF){
                $brand->top_status = OrganizationBrand::TOP_STATUS_ON;
                $brand->top_time = Carbon::now();
            }else{
                $brand->top_status = OrganizationBrand::TOP_STATUS_OFF;
            }

            $result = $brand->save();

            if(!$result){
                DB::rollback();
                $this->respFail('数据更新错误');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }



}
