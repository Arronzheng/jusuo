<?php

namespace App\Http\Controllers\v1\admin\brand\info_statistics\activity\seller\api;

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
use App\Models\LogDealerCertification;
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
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        $brand_id = $brand->id;

        $area_belong_id = $request->input('abi',null);
        $product_category_id = $request->input('pc',null);
        $name = $request->input('name',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = DB::table('administrator_dealers as as');

        $entry = $entry->where('d.p_brand_id',$brand_id);


        //筛选经营品类
        if($product_category_id!==null){
            $entry = $entry->where('b.product_category',$product_category_id);
        }

        //筛选所在城市
        if($area_belong_id!==null){
            $entry = $entry->where('dd.area_belong_id',$area_belong_id);
        }

        //名称搜索
        if($name!==null){
            $entry = $entry->where('d.name','like',"%".$name."%");
        }

        //开通时间筛选
        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('ldc.updated_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderBy($sort,$order);
        }

        $entry->orderBy('d.id','desc');

        $entry->join('organization_dealers as d','as.dealer_id','=','d.id')
            ->join('organization_brands as b','d.p_brand_id','=','b.id')
            ->join('log_dealer_certifications as ldc','ldc.target_dealer_id','=','d.id')
            ->join('detail_dealers as dd','dd.dealer_id','=','d.id')
            //->leftJoin('statistic_account_dealers as stat','stat.dealer_id','=','d.id')
            ->leftJoin('statistic_account_dealers as stat','stat.id','=',DB::raw("
                 (select id from statistic_account_dealers t where t.dealer_id = d.id
                order by t.id desc limit 1)
            "))
            ->select([
                'as.id as admin_id','as.login_username','as.login_account','as.status as account_status',
                'd.id as seller_id','d.name as company_name','d.p_brand_id','dd.area_belong_id',
                'd.status as seller_status',
                'b.product_category','b.name as brand_company_name','b.id as brand_id',
                'stat.count_product_increase_day_7','stat.count_album_increase_day_7','stat.count_designer_increase_day_7',
                'ldc.updated_at as start_at'])
            ->where('is_super_admin',AdministratorDealer::IS_SUPER_ADMIN_YES)
            ->where('ldc.is_approved',LogDealerCertification::IS_APROVE_APPROVAL)
            ->groupBy('d.id');

        $datas = $entry->paginate($limit);


        $datas->transform(function($v){
            $product_category_id = $v->product_category;
            $v->product_category_name = '';
            $product_category = ProductCategory::find($product_category_id);
            if($product_category){
                $v->product_category_name = $product_category->name;
            }
            $v->account_status_text = AdministratorDealer::statusGroup($v->account_status);
            $v->seller_status_text = OrganizationDealer::statusGroup($v->seller_status);
            $v->area_belong_text = '';
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
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //异步获取品牌列表
    public function get_brands(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = OrganizationBrand::select(['id','name','brand_name','organization_account','contact_name','contact_telephone'])
            ->orderBy('created_at','desc');

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->where('name','like',"%".$keyword."%");
                $query->orWhere('brand_name','like',"%".$keyword."%");
                $query->orWhere('short_name','like',"%".$keyword."%");
                $query->orWhere('organization_account','like',"%".$keyword."%");
                $query->orWhere('contact_name','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'data'  =>$datas->items()
        ]);
    }

}
