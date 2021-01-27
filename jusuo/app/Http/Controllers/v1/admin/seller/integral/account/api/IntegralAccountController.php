<?php

namespace App\Http\Controllers\v1\admin\seller\integral\account\api;

use App\Exports\IntegralGoodExport;
use App\Exports\IntegralLogBuyExport;
use App\Http\Services\common\OrganizationService;
use App\LogisticsCompany;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
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
use App\Models\IntegralLogBrand;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\IntegralRechargeLog;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\IntegralLogBuyService;
use App\Services\v1\admin\IntegralRechargeLogService;
use App\Services\v1\admin\OrganizationDealerService;
use App\Services\v1\site\DealerService;
use App\Services\v1\site\LocationService;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IntegralAccountController extends ApiController
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
        $user = $this->authService->getAuthUser();
        $input = $request->all();

        $login_name = $request->input('ln',null);
        $realname = $request->input('rn',null);
        //$mobile = $request->input('mobile',null);//在login_name中可以用mobile输入
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);


        //查询设计师列表
        $entry = Designer::leftJoin('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->OrganizationId($user->dealer->id)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            //->where('self_designer_level', '>','-1')
            ->orderBy('id','DESC');

        if($login_name!==null){
            $entry = $entry->where(function($query)use($login_name){
                $query->where('detail.nickname','like','%'.$login_name.'%');
                $query->orWhere('designers.login_mobile','like','%'.$login_name.'%');
            });
        }

        if($realname!==null){
            $entry = $entry->where('detail.realname','like',"%".$realname."%");
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('designers.created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('designers.id','desc');

        $datas = $entry->select(
            'designers.id',
            'designers.designer_account',
            'detail.nickname',
            'detail.realname',
            'designers.login_mobile',
            'detail.gender',
            'detail.area_belong_province',
            'detail.area_belong_city',
            'detail.area_belong_district',
            'detail.self_designer_type',
            'detail.self_organization',
            'designers.created_at',
            'designers.status',
            'detail.approve_time',
            'detail.approve_realname',
            'detail.self_designer_level',
            'detail.self_birth_time',
            'detail.point_focus',
            'detail.point_experience',
            'detail.point_money',
            'detail.count_album'
        )->paginate($limit);

        $datas->transform(function($v){
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->area_belong = '';
            $province =  Area::where('id',$v->area_belong_province)->first();
            $city =  Area::where('id',$v->area_belong_city)->first();
            $district =  Area::where('id',$v->area_belong_district)->first();
            if($province){$v->area_belong.= $province->name;}
            if($city){$v->area_belong.= $city->name;}
            if($district){$v->area_belong.= $district->name;}

            $v->status_text = Designer::statusGroup($v->status);
            $v->approve_info = $v->approve_realname == DesignerDetail::APPROVE_REALNAME_YES?$v->approve_time:'未认证';
            //兑换礼品数
            $v->exchange_count = IntegralLogBuyService::get_designer_exchange_count($v->id);
            //充值次数
            $v->charge_count = IntegralRechargeLogService::get_designer_recharge_count($v->id);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }


}