<?php

namespace App\Http\Controllers\v1\admin\platform;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\PrivilegeService;
use App\Models\AdministratorPlatform;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\IntegralLogBuy;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\SiteConfigPlatform;
use App\Models\StatisticAccountBrand;
use App\Services\v1\admin\StatisticAccountBrandService;
use App\Services\v1\admin\StatisticAccountDealerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PlatformController extends VersionController
{


    //后台首页
    public function index()
    {

        PrivilegeService::sync_super_admin_role_privileges('platform');
        session()->forget('admin.menu_info');

        $yesterday = Carbon::now()->addDay(-1)->toDateString();
        $today = Carbon::now()->toDateString();

        $data['brand_count_verified']=OrganizationBrand::where('status',OrganizationBrand::STATUS_ON)->count();
        $data['brand_count_to_be_verified']=OrganizationBrand::where('status',OrganizationBrand::STATUS_WAIT_VERIFY)->count();

        $data['dealer_count_verified']=OrganizationDealer::where('status',OrganizationDealer::STATUS_ON)->count();
        $data['dealer_count_verified_yesterday']=OrganizationDealer::whereBetween('created_at',[$yesterday, $today])->count();

        $data['designer_count_verified']=Designer::where('status',Designer::STATUS_ON)->count();
        $data['designer_count_verified_yesterday']=DesignerDetail::whereBetween('approve_time',[$yesterday, $today])->count();

        $data['product_count_verified']=ProductCeramic::where('status',ProductCeramic::STATUS_PASS)->count();
        $data['product_count_verified_yesterday']=ProductCeramic::whereBetween('verify_time',[$yesterday, $today])->count();

        $data['album_count_verified']=Album::where('status',Album::STATUS_PASS)->count();
        $data['album_count_verified_yesterday']=Album::whereBetween('verify_time',[$yesterday, $today])->count();

        $data['money_brand']=OrganizationBrand::where('status',OrganizationBrand::STATUS_ON)->sum('point_money');
        $data['count_order']=IntegralLogBuy::leftJoin('integral_goods as g','g.id','=','integral_log_buys.goods_id')
            ->where([
                'g.brand_id'=>0,
                'integral_log_buys.status'=>IntegralLogBuy::STATUS_TO_BE_SENT
            ])->count();
        $data['money_designer']=DesignerDetail::leftJoin('designers as d','d.id','=','designer_details.designer_id')
            ->where('d.status',Designer::STATUS_ON)->sum('designer_details.point_money');

        return $this->get_view('v1.admin_platform.index',compact('data'));
    }


}
