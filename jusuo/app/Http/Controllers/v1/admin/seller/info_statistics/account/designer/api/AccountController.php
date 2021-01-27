<?php

namespace App\Http\Controllers\v1\admin\seller\info_statistics\account\designer\api;

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
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;
use App\Models\LogDesignerCertification;
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
use App\Services\v1\admin\StatisticDesignerService;
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
        $seller = $loginAdmin->dealer;

        $style_id = $request->input('stl',null);
        $space_id = $request->input('spc',null);
        $name = $request->input('name',null);
        $level = $request->input('lv',null);
        $reg_start = $request->input('reg_start',null);
        $reg_end = $request->input('reg_end',null);
        $cert_start = $request->input('cert_start',null);
        $cert_end = $request->input('cert_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);
        $area_belong_province = $request->input('abp',null);
        $area_belong_city = $request->input('abc',null);
        $area_belong_district = $request->input('abd',null);
        $area_serving_province = $request->input('asp',null);
        $area_serving_city = $request->input('asc',null);
        $area_serving_district = $request->input('asd',null);

        $entry = DB::table('designers as dsn');

        $entry->where('dsn.organization_type',Designer::ORGANIZATION_TYPE_SELLER);
        $entry = $entry->where('dsn.organization_id',$seller->id);


        //筛选等级
        if($level!==null){
            $entry = $entry->where('detail.self_designer_level',$level);
        }

        //筛选所在省份
        if($area_belong_province!==null){
            $entry = $entry->where('detail.area_belong_province',$area_belong_province);
        }

        //筛选所在城市
        if($area_belong_city!==null){
            $entry = $entry->where('detail.area_belong_city',$area_belong_city);
        }

        //筛选所在地区
        if($area_belong_district!==null){
            $entry = $entry->where('detail.area_belong_district',$area_belong_district);
        }

        //筛选服务省份
        if($area_serving_province!==null){
            $entry = $entry->where('detail.area_serving_province',$area_serving_province);
        }

        //筛选服务城市
        if($area_serving_city!==null){
            $entry = $entry->where('detail.area_serving_city',$area_serving_city);
        }

        //筛选服务地区
        if($area_serving_district!==null){
            $entry = $entry->where('detail.area_serving_district',$area_serving_district);
        }

        //名称搜索
        if($name!==null){
            $entry = $entry->where(function($query)use($name){
                $query->where('dsn.login_username','like',"%".$name."%");
                $query->orWhere('dsn.login_mobile','like',"%".$name."%");
                $query->orWhere('detail.nickname','like',"%".$name."%");
                $query->orWhere('detail.realname','like',"%".$name."%");
            });
        }

        //注册时间筛选
        if($reg_start!==null && $reg_end!==null){
            $entry->whereBetween('dsn.created_at', array($reg_start.' 00:00:00', $reg_end.' 23:59:59'));
        }

        //认证时间筛选
        if($cert_start!==null && $cert_end!==null){
            $entry->where('ldc.is_approved',LogDesignerCertification::IS_APROVE_APPROVAL);
            $entry->whereBetween('ldc.approve_time', array($cert_start.' 00:00:00', $cert_end.' 23:59:59'));
        }

        if($sort && $order){
            if($sort=='created_at'){
                $sort = 'dsn.created_at';
            }
            $entry->orderBy($sort,$order);
        }

        $entry->orderBy('dsn.id','desc');
        $entry->groupBy('dsn.id');

        //列表显示序号/账号ID/昵称/真实姓名/注册手机号/类型/性别/所在省/市/区/服务省/市/区/
        // 擅长风格/擅长空间/服务专长/级别/关注度/经验值/方案数/[-预约数]/方案置顶次数/主页浏览量/
        //账号点赞数/账号状态/注册时间/认证状态/认证时间/账号创建时间/所属组织
        $entry->join('designer_details as detail','detail.designer_id','=','dsn.id')
            ->leftJoin('log_designer_certifications as ldc','ldc.target_designer_id','=','dsn.id')
            //->leftJoin('statistic_designers as stat','stat.designer_id','=','dsn.id')
            ->leftJoin('statistic_designers as stat','stat.id','=',DB::raw("
                 (select id from statistic_designers t where t.designer_id = dsn.id
                order by t.id desc limit 1)
            "))
            ->select([
                'dsn.id as designer_id','dsn.top_dealer_status','detail.nickname','detail.realname','dsn.login_mobile',
                'detail.self_designer_type','detail.gender','detail.area_belong_province',
                'detail.area_belong_city','detail.area_belong_district','detail.area_serving_province',
                'detail.area_serving_city','detail.area_serving_district','detail.self_expert',
                'detail.self_designer_level','detail.point_focus','detail.point_experience',
                'stat.count_upload_album','stat.count_top_album','detail.count_visit',
                'detail.count_praise','dsn.status as account_status','dsn.created_at',
                'ldc.is_approved as cert_status','ldc.approve_time','dsn.organization_type',
                'dsn.organization_id']);


        //关联擅长风格
        if($style_id!=null){
            $entry->join('designer_styles as dstyle','dstyle.designer_id','=','dsn.id');
            $entry->where('dstyle.style_id',$style_id);
        }

        //关联擅长空间
        if($space_id!=null){
            $entry->join('designer_spaces as dspace','dspace.designer_id','=','dsn.id');
            $entry->where('dspace.space_id',$space_id);
        }

        $datas = $entry->paginate($limit);

        $datas->transform(function($v){
            $designer = Designer::find($v->designer_id);
            //擅长风格
            $v->style_text = '';
            $styles = $designer->styles()->get()->pluck('name')->toArray();
            if(count($styles)>0){
                $v->style_text = implode('/',$styles);
            }
            //擅长空间
            $v->space_text = '';
            $spaces = $designer->spaces()->get()->pluck('name')->toArray();
            if(count($spaces)>0){
                $v->space_text = implode('/',$spaces);
            }
            //所在城市
            $v->area_belong = '';
            $province =  Area::where('id',$v->area_belong_province)->first();
            $city =  Area::where('id',$v->area_belong_city)->first();
            $district =  Area::where('id',$v->area_belong_district)->first();
            if($province){$v->area_belong.= $province->name;}
            if($city){$v->area_belong.= $city->name;}
            if($district){$v->area_belong.= $district->name;}
            //服务城市
            $v->area_serving = '';
            $province =  Area::where('id',$v->area_serving_province)->first();
            $city =  Area::where('id',$v->area_serving_city)->first();
            $district =  Area::where('id',$v->area_serving_district)->first();
            if($province){$v->area_serving.= $province->name;}
            if($city){$v->area_serving.= $city->name;}
            if($district){$v->area_serving.= $district->name;}
            $v->account_status_text = AdministratorBrand::statusGroup($v->account_status);
            $v->cert_status_text = LogDesignerCertification::getIsApproved(isset($v->cert_status)?$v->cert_status:'');
            $v->changeStatusApiUrl = url('admin/seller/info_statistics/account/designer/api/'.$v->designer_id.'/top_status');
            //所属组织
            $v->organization = '无';
            if($v->organization_type != Designer::ORGANIZATION_TYPE_NONE){
                $organization_type = $v->organization_type;
                $organization_type_text = Designer::organizationTypeGroup($v->organization_type);
                $organization = null;
                switch($organization_type){
                    case Designer::ORGANIZATION_TYPE_BRAND:$organization = OrganizationBrand::find($v->organization_id); break;
                    case Designer::ORGANIZATION_TYPE_SELLER:$organization = OrganizationDealer::find($v->organization_id); break;
                    default:break;
                }
                if($organization){
                    $v->organization = "（".$organization_type_text."）".$organization->name;
                }else{
                    $v->organization = "（".$organization_type_text."）"."组织信息丢失";
                }
            }
            if($v->self_designer_level==-1){
                $v->self_designer_level_text = '临时账号';
            }else{
                $v->self_designer_level_text = $v->self_designer_level;

            }
            //设计师类型
            $v->self_designer_type_text = DesignerDetail::designerTypeGroup(isset($v->self_designer_type)?$v->self_designer_type:'');
            //性别
            $v->gender_text = DesignerDetail::genderGroup($v->gender);
            //认证时间
            $v->cert_time = '';
            if($v->cert_status == LogDesignerCertification::IS_APROVE_APPROVAL){
                $v->cert_time = $v->approve_time;
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
        $designer = Designer::query()
            ->find($id);
        if(!$designer){
            $this->respFail('数据不存在');
        }

        if($designer->status != Designer::STATUS_ON){
            $this->respFail('设计师未审核');

        }

        DB::beginTransaction();

        try{

            //更新状态
            if($designer->top_dealer_status==Designer::TOP_DEALER_STATUS_OFF){
                $designer->top_dealer_status = Designer::TOP_DEALER_STATUS_ON;
                $designer->top_dealer_time = Carbon::now();

                //累加设计师方案被置顶次数
                $statistic = StatisticDesignerService::get_statistic_log($designer->id);
                $statistic->count_top_album = $statistic->count_top_album+1;
                $statistic->save();
            }else{
                $designer->top_dealer_status = Designer::TOP_DEALER_STATUS_OFF;
            }

            $result = $designer->save();

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

    //异步获取销售商列表
    public function get_sellers(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = OrganizationDealer::select(['id','name','brand_name','organization_account','contact_name','contact_telephone'])
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
