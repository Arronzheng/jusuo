<?php

namespace App\Http\Controllers\v1\admin\platform\integral\exchange_log;

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
        return $this->get_view('v1.admin_platform.integral.exchange_log.index');
    }

    public function send($id)
    {
        $companies = LogisticsCompany::get();

        $data = IntegralLogBuy::find($id);
        if(!$data){
            die('信息不存在');
        }

        //判断状态是否正确
        if($data->status != IntegralLogBuy::STATUS_TO_BE_SENT){
            die('兑换记录状态异常');
        }

        //判断积分商品是否平台商品
        $good = IntegralGood::query()
            ->where('brand_id',0)
            ->find($data->goods_id);
        if(!$good){
            die('权限不足');
        }

        return $this->get_view('v1.admin_platform.integral.exchange_log.send',compact('companies','data'));

    }
}
