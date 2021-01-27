<?php

namespace App\Http\Controllers\v1\admin\brand\integral\exchange_log;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\v1\admin\AuthService;
use App\LogisticsCompany;
use App\Models\Banner;
use App\Models\CeramicSeries;
use App\Models\Designer;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\IntegralLogBuy;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\TestData;
use App\Services\v1\admin\OrganizationDealerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class IntegralExchangeLogController extends VersionController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        return $this->get_view('v1.admin_brand.integral.exchange_log.index');
    }

    public function send($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $companies = LogisticsCompany::get();

        $data = IntegralLogBuy::find($id);
        if(!$data){
            die('信息不存在');
        }

        //判断状态是否正确
        if($data->status != IntegralLogBuy::STATUS_TO_BE_SENT){
            die('兑换记录状态异常');
        }

        //判断设计师是否自己旗下
        $designer = Designer::query()
            ->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand->id)
            ->first();
        if(!$designer){
            $brand_seller_ids = OrganizationDealerService::getBrandAllLegalSellerEntry($brand->id)
                ->get()->pluck('id')->toArray();
            if(!in_array($data->designer_id,$brand_seller_ids)){
                die('权限不足');
            }
        }

        //判断积分商品是否自己旗下
        $good = IntegralGood::query()
            ->where('brand_id',$brand->id)
            ->find($data->goods_id);
        if(!$good){
            die('权限不足1');
        }



        return $this->get_view('v1.admin_brand.integral.exchange_log.send',compact('companies','data'));

    }
}
