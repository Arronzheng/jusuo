<?php

namespace App\Http\Controllers\v1\admin\seller;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Repositories\common\AreaRepository;

use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\PrivilegeSellerService;
use App\Models\AdministratorDealer;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\CertificationDealer;
use App\Models\LogDealerCertification;
use App\Models\LogDetailDealer;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramicAuthorization;
use App\Models\SearchAlbum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategory;


class SellerController extends VersionController
{
    private $authService;
    private $areaRepository;

    public function __construct(
        AuthService $authService,
        AreaRepository $areaRepository
    )
    {
        $this->authService = $authService;
        $this->areaRepository = $areaRepository;
    }

    //后台首页
    public function index()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $dealer = $loginAdmin->dealer;
        $dealer_id = $dealer->id;

        $yesterday = Carbon::now()->addDay(-1)->toDateString();
        $today = Carbon::now()->toDateString();

        //方案总数，昨日新增方案数，待审核数
        $data['album_count_total'] = SearchAlbum::where('dealer_id',$dealer_id)->count();
        $data['album_count_yesterday'] = SearchAlbum::where('dealer_id',$dealer_id)
            ->whereBetween('created_at',[$yesterday, $today])->count();
        $data['album_count_to_do'] = DB::table('albums as a')
            ->leftJoin('search_albums as sa','a.id','=','sa.album_id')
            ->where(['sa.dealer_id'=>$dealer_id,'a.status'=>Album::STATUS_VERIFYING])
            ->count();

        //设计师总数，昨日新增设计师数，待审核数
        $data['designer_count_total'] = Designer::where(['organization_type'=>Designer::ORGANIZATION_TYPE_SELLER,'organization_id'=>$dealer_id])->count();
        $data['designer_count_yesterday'] = Designer::where(['organization_type'=>Designer::ORGANIZATION_TYPE_SELLER,'organization_id'=>$dealer_id])
            ->whereBetween('created_at',[$yesterday, $today])->count();
        $data['designer_count_to_do'] = DB::table('designers as d')
            ->leftJoin('designer_details as dd','d.id','=','dd.designer_id')
            ->where(['dd.dealer_id'=>$dealer_id,'d.status'=>Designer::STATUS_VERIFYING])
            ->count();

        if($dealer->p_dealer_id>0){
            $p_dealer_id = $dealer->p_dealer_id;
        }
        else{
            $p_dealer_id = $dealer_id;
        }
        //dd($p_dealer_id);

        //产品总数，昨日新增产品数
        $data['product_count_total'] = ProductCeramicAuthorization::where('dealer_id',$p_dealer_id)->count();
        $data['product_count_yesterday'] = ProductCeramicAuthorization::where('dealer_id',$p_dealer_id)
            ->whereBetween('created_at',[$yesterday, $today])->count();

        //积分金额
        $data['money'] = $dealer->point_money;
        $data['designer_money'] = DesignerDetail::where('dealer_id',$dealer_id)->sum('point_money');
        //昨日活跃设计师数
        $data['active_count_yesterday'] = Designer::where(['organization_type'=>Designer::ORGANIZATION_TYPE_SELLER,'organization_id'=>$dealer_id])
            ->whereBetween('last_active_time',[$yesterday, $today])->count();

        $date = [];
        for($i=30;$i>=0;$i--){
            $date[] = Carbon::now()->addDay(0-$i)->toDateString();
        }
        $date[] = Carbon::now()->toDateTimeString();
        $count = [];
        for($i=0;$i<31;$i++){
            $count[] = SearchAlbum::where('dealer_id',$dealer_id)
                ->whereBetween('created_at',[$date[$i], $date[$i+1]])->count();
        }
        $date1 = [];
        for($i=0;$i<31;$i++){
            $date1[] = date('m/d',strtotime($date[$i]));
        }
        $chartDataX = json_encode($date1);
        $chartDataY = json_encode($count);

        return $this->get_view('v1.admin_seller.index',compact('data','chartDataX', 'chartDataY'));
    }

    //基本信息
    public function basic_info()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        $seller_id = $seller->id;
        $detail_info = $seller->detail;
        $approving_log = [];
        $certification = [];

        $seller->product_category_name = '';
        $product_category = ProductCategory::find($seller->brand->product_category);

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        if($product_category){
            $seller->product_category_name = $product_category->name;
        }

        $detail_info->area_serving='';
        if($detail_info->area_serving_id){
            $detail_info->area_serving = $this->areaRepository->getServiceAreaName($detail_info->area_serving_id);
        }

        if($seller->status == OrganizationDealer::STATUS_ON){
            //审核通过
            $log_status = -1;
            $certification = CertificationDealer::where('dealer_id',$seller_id)->orderBy('id','desc')->first();

        }else{
            //参数配置
            $log = LogDealerCertification::where(['target_dealer_id'=>$seller_id,'is_approved'=>LogDealerCertification::IS_APROVE_VERIFYING])->first();
            if($log){
                //待审核
                $log_status = 1;
            }else{
                $approving_log = LogDealerCertification::where(['target_dealer_id'=>$seller_id,'is_read'=>0,'is_approved'=>LogDealerCertification::IS_APROVE_REJECT])->first();
                if($approving_log){
                    //审核拒绝
                    $log_status = 2;
                }else{
                    //暂未有审核信息
                    $log_status = 0;
                }
            }
        }


        $pcu = new ParamConfigUseService($seller->id,OrganizationService::ORGANIZATION_TYPE_BRAND);
        $config['name'] = $pcu->get_by_keyword('platform.basic_info.brand.name.character_limit');//公司名称字数
        $config[' '] = $pcu->get_by_keyword('platform.basic_info.brand.brand_name.character_limit');//品牌名称字数


        return $this->get_view('v1.admin_seller.info_manage.basic_info',
            compact('detail_info','approving_log',
                'certification','log_status','seller','provinces'));
    }

    //应用信息
    public function app_info()
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        $sellerDetail = DetailDealer::where('dealer_id',$seller->id)->first();
        if(!$sellerDetail){
            return back();
        }

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();

        $cities = [];
        $districts = [];
        $sellerDetail->area_belong_province_id = 0;
        $sellerDetail->area_belong_city_id = 0;
        $sellerDetail->area_belong_district_id = 0;
        if($sellerDetail->area_belong_id){
            $area_belong_district = Area::select(['*'])->where('id',$sellerDetail->area_belong_id)->first();
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
                $sellerDetail->area_belong_province_id = $area_belong_province_id;
                $sellerDetail->area_belong_city_id = $area_belong_city_id;
                $sellerDetail->area_belong_district_id = $area_belong_district->id;
            }
        }

        $limit = [
            'seller.self_photo.limit'=>ParamConfigUseService::find_root('platform.app_info.global.seller.self_photo.limit')
        ];

        //参数设置
        $pcu = new ParamConfigUseService($loginAdmin->id,OrganizationService::ORGANIZATION_TYPE_SELLER);
        $required_config['avatar_required'] = $pcu->find('platform.app_info.seller.avatar.required');
        $required_config['dealer_domain_required'] = $pcu->find('platform.app_info.seller.dealer_domain.required');
        $required_config['area_belong_required'] = $pcu->find('platform.app_info.seller.area_belong.required');
        $required_config['contact_name_required'] = $pcu->find('platform.app_info.seller.contact_name.required');
        $required_config['contact_telephone_required'] = $pcu->find('platform.app_info.seller.contact_telephone.required');
        $required_config['contact_zip_code_required'] = $pcu->find('platform.app_info.seller.contact_zip_code.required');
        $required_config['company_address_required'] = $pcu->find('platform.app_info.seller.company_address.required');
        $required_config['self_introduction_required'] = $pcu->find('platform.app_info.seller.self_introduction.required');
        $required_config['self_promise_required'] = $pcu->find('platform.app_info.seller.self_promise.required');
        $required_config['self_address_required'] = $pcu->find('platform.app_info.seller.self_address.required');
        $required_config['self_photo_required'] = $pcu->find('platform.app_info.seller.self_photo.required');
        $required_config['self_promotion_required'] = $pcu->find('platform.app_info.seller.self_promotion.required');


        return $this->get_view('v1.admin_seller.info_manage.app_info',
            compact(
                'seller','sellerDetail','provinces','cities','districts','limit','required_config'
            ));
    }

}
