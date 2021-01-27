<?php

namespace App\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Models\AdministratorOrganization;
use App\Models\CeramicSeries;
use App\Models\IntegralLogBuy;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use Illuminate\Support\Facades\Auth;

class IntegralLogBuyService{

    //获取某个用户兑换礼品数
    /**
     * @return int
     */
    public static function get_designer_exchange_count($designer_id)
    {

        $count = IntegralLogBuy::query()
            ->where('designer_id',$designer_id)
            ->whereIn('status',[IntegralLogBuy::STATUS_TO_BE_SENT,IntegralLogBuy::STATUS_SENT])
            ->count();

        return intval($count);
    }


}