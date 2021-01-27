<?php

namespace App\Http\Controllers\v1\admin\brand;

use App\Console\Commands\OrganizationBrandColumnStatistic;
use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\common\OrganizationService;
use App\Models\AdministratorBrand;
use App\Models\Album;
use App\Models\CertificationBrand;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailBrand;
use App\Models\IntegralLogBuy;
use App\Models\LogBrandCertification;
use App\Models\LogBrandSiteConfig;
use App\Models\LogDetailBrand;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Services\v1\admin\DesignerColumnStatisticService;
use App\Services\v1\admin\OrganizationBrandColumnStatisticService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\v1\admin\ParamConfigUseService;

use Illuminate\Support\Facades\DB;

class BrandController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //后台首页
    public function index()
    {

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        $brand_id = $brand->id;

        //配额使用
        $data['quota_dealer_lv1_used']=$brand->quota_dealer_lv1_used;
        $data['quota_dealer_lv1']=$brand->quota_dealer_lv1;
        $data['quota_dealer_lv2_used']=$brand->quota_dealer_lv2_used;
        $data['quota_dealer_lv2']=$brand->quota_dealer_lv2;
        $data['quota_designer_brand_used']=$brand->quota_designer_brand_used;
        $data['quota_designer_brand']=$brand->quota_designer_brand;
        $data['quota_designer_dealer_used']=$brand->quota_designer_dealer_used;
        $data['quota_designer_dealer']=$brand->quota_designer_dealer;
        $data['account_expired_at']=date('Y-m-d',strtotime($brand->expired_at));

        $yesterday = Carbon::now()->addDay(-1)->toDateString();
        $today = Carbon::now()->toDateString();

        //方案总数，昨日新增方案数，待审核数
        $data['album_count_total'] = SearchAlbum::where('brand_id',$brand_id)->count();
        $data['album_count_yesterday'] = SearchAlbum::where('brand_id',$brand_id)
            ->whereBetween('created_at',[$yesterday, $today])->count();
        $data['album_count_to_do'] = DB::table('albums as a')
            ->leftJoin('search_albums as sa','a.id','=','sa.album_id')
            ->where(['sa.brand_id'=>$brand_id,'sa.dealer_id'=>0,'a.status'=>Album::STATUS_VERIFYING])
            ->count();

        //设计师总数，昨日新增设计师数，待审核数
        $data['designer_count_total'] = Designer::where(['organization_type'=>Designer::ORGANIZATION_TYPE_BRAND,'organization_id'=>$brand_id])->count();
        $data['designer_count_yesterday'] = Designer::where(['organization_type'=>Designer::ORGANIZATION_TYPE_BRAND,'organization_id'=>$brand_id])
            ->whereBetween('created_at',[$yesterday, $today])->count();
        $data['designer_count_to_do'] = DB::table('designers as d')
            ->leftJoin('designer_details as dd','d.id','=','dd.designer_id')
            ->where(['dd.brand_id'=>$brand_id,'dd.dealer_id'=>0,'d.status'=>Designer::STATUS_VERIFYING])
            ->count();

        //产品总数，昨日新增产品数，待审核数
        $data['product_count_total'] = ProductCeramic::where('brand_id',$brand_id)->count();
        $data['product_count_yesterday'] = ProductCeramic::where('brand_id',$brand_id)
            ->whereBetween('created_at',[$yesterday, $today])->count();
        $data['product_count_to_do'] = ProductCeramic::where(['brand_id'=>$brand_id,'status'=>ProductCeramic::STATUS_VERIFYING])
            ->count();

        //销售商总数，昨日新增销售商数，待审核数
        $data['dealer_count_total'] = OrganizationDealer::where([
            'p_brand_id'=>$brand_id
        ])->count();
        $data['dealer_count_yesterday'] = OrganizationDealer::where([
            'p_brand_id'=>$brand_id
        ])->whereBetween('created_at',[$yesterday, $today])->count();
        $data['dealer_count_to_do'] = OrganizationDealer::where([
            'p_brand_id'=>$brand_id,
            'status'=>OrganizationDealer::STATUS_WAIT_VERIFY
        ])->count();

        //积分金额
        $data['money'] = $brand->point_money;
        $data['designer_money'] = DesignerDetail::where('brand_id',$brand_id)->sum('point_money');
        $data['sheet_to_do'] = DB::table('integral_log_buys as b')
            ->leftJoin('integral_goods as g','g.id','=','b.goods_id')
            ->where(['g.brand_id'=>$brand_id,'b.status'=>IntegralLogBuy::STATUS_TO_BE_SENT])
            ->count();

        $date = [];
        for($i=30;$i>=0;$i--){
            $date[] = Carbon::now()->addDay(0-$i)->toDateString();
        }
        $date[] = Carbon::now()->toDateTimeString();
        $count = [];
        for($i=0;$i<31;$i++){
            $count[] = SearchAlbum::where('brand_id',$brand_id)
                ->whereBetween('created_at',[$date[$i], $date[$i+1]])->count();
        }
        $date1 = [];
        for($i=0;$i<31;$i++){
            $date1[] = date('m/d',strtotime($date[$i]));
        }
        $chartDataX = json_encode($date1);
        $chartDataY = json_encode($count);

        return $this->get_view('v1.admin_brand.index',compact('data','chartDataX', 'chartDataY'));
    }

    //品牌基本信息
    public function basic_info()
    {

        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;
        $brand_id = $brand->id;
        $detail_info = DetailBrand::find($brand_id);
        $approving_log = [];
        $certification = [];

        $pcu = new ParamConfigUseService($brand->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $config['name'] = $pcu->get_by_keyword('platform.basic_info.brand.name.character_limit');//公司名称字数
        $config['brand_name'] = $pcu->get_by_keyword('platform.basic_info.brand.brand_name.character_limit');//品牌名称字数

        if($brand->status == OrganizationBrand::STATUS_ON){
            //审核通过
            $log_status = -1;
            $certification = CertificationBrand::where('brand_id',$brand_id)->orderBy('id','desc')->first();
        }else{
            //参数配置
            $log = LogBrandCertification::where(['target_brand_id'=>$brand_id,'is_approved'=>LogBrandCertification::IS_APROVE_VERIFYING])->first();
            if($log){
                //待审核
                $log_status = 1;
            }else{
                $approving_log = LogBrandCertification::where(['target_brand_id'=>$brand_id,'is_read'=>0,'is_approved'=>LogBrandCertification::IS_APROVE_REJECT])->first();
                if($approving_log){
                    //审核拒绝
                    $log_status = 2;
                }else{
                    //暂未有审核信息
                    $log_status = 0;
                }
            }
        }

        return $this->get_view('v1.admin_brand.info_manage.basic_info',
            compact('detail_info','approving_log','log_status','brand',
                'config','certification'));
    }

    //品牌应用信息
    public function app_info()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        $brandDetail = DetailBrand::where('brand_id',$brand->id)->first();
        if(!$brandDetail){
            return back();
        }

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        $cities = [];
        $districts = [];
        $brandDetail->area_belong_province_id = 0;
        $brandDetail->area_belong_city_id = 0;
        $brandDetail->area_belong_district_id = 0;
        if($brandDetail->area_belong_id){
            $area_belong_district = Area::select(['*'])->where('id',$brandDetail->area_belong_id)->first();
            if($area_belong_district){
                $area_belong_city_id = $area_belong_district->pid;
                $area_belong_city = Area::select(['pid'])->where('id',$area_belong_city_id)->first();
                $area_belong_province_id = $area_belong_city->pid;
                $cities = Area::select(['id','name'])
                    ->where('pid',$area_belong_province_id)
                    ->get();
                $districts = Area::select(['id','name'])
                    ->where('pid',$area_belong_city_id)
                    ->get();
                $brandDetail->area_belong_province_id = $area_belong_province_id;
                $brandDetail->area_belong_city_id = $area_belong_city_id;
                $brandDetail->area_belong_district_id = $area_belong_district->id;
            }
        }

        $limit = [
            'brand.self_award.limit'=>ParamConfigUseService::find_root('platform.app_info.global.brand.self_award.limit'),
            'brand.self_staff.limit'=>ParamConfigUseService::find_root('platform.app_info.global.brand.self_staff.limit')
        ];

        //参数设置
        $pcu = new ParamConfigUseService($loginAdmin->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $required_config['avatar_required'] = $pcu->find('platform.app_info.brand.avatar.required');
        $required_config['brand_domain_required'] = $pcu->find('platform.app_info.brand.brand_domain.required');
        $required_config['area_belong_required'] = $pcu->find('platform.app_info.brand.area_belong.required');
        $required_config['contact_name_required'] = $pcu->find('platform.app_info.brand.contact_name.required');
        $required_config['contact_telephone_required'] = $pcu->find('platform.app_info.brand.contact_telephone.required');
        $required_config['contact_zip_code_required'] = $pcu->find('platform.app_info.brand.contact_zip_code.required');
        $required_config['company_address_required'] = $pcu->find('platform.app_info.brand.company_address.required');
        $required_config['self_introduction_scale_required'] = $pcu->find('platform.app_info.brand.self_introduction_scale.required');
        $required_config['self_introduction_brand_required'] = $pcu->find('platform.app_info.brand.self_introduction_brand.required');
        $required_config['self_introduction_product_required'] = $pcu->find('platform.app_info.brand.self_introduction_product.required');
        $required_config['self_introduction_service_required'] = $pcu->find('platform.app_info.brand.self_introduction_service.required');
        $required_config['self_award_required'] = $pcu->find('platform.app_info.brand.self_award.required');
        $required_config['self_staff_required'] = $pcu->find('platform.app_info.brand.self_staff.required');
        $required_config['self_introduction_plan_required'] = $pcu->find('platform.app_info.brand.self_introduction_plan.required');

        return $this->get_view('v1.admin_brand.info_manage.app_info',
            compact(
                'brand','brandDetail','provinces','cities','districts','limit','required_config'
            ));
    }

    //品牌主页设置
    public function site_config()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //主页设置信息
        $info = LogBrandSiteConfig::query()
            ->where('target_brand_id',$brand->id)
            ->first();

        if(!$info){
            $info = new LogBrandSiteConfig();
            $info->target_brand_id = $brand->id;
            $info->save();
        }


        //销售商注册提交的信息
        $content = @unserialize($info->content);
        $content['id'] = $info->id;


        return $this->get_view('v1.admin_brand.info_manage.site_config',
            compact(
                'brand','content'
            ));
    }

}
