<?php

namespace App\Services\v1\admin;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Models\AdministratorOrganization;
use App\Models\CeramicSeries;
use App\Models\IntegralLogBuy;
use App\Models\IntegralRechargeLog;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use Illuminate\Support\Facades\Auth;

class IntegralRechargeLogService{

    //获取某个用户兑换礼品数
    /**
     * @return int
     */
    public static function get_designer_recharge_count($designer_id)
    {

        $count = IntegralRechargeLog::query()
            ->where('buyer_type',OrganizationService::ORGANIZATION_TYPE_DESIGNER)
            ->where('buyer_id',$designer_id)
            ->where('pay_status',IntegralRechargeLog::PAY_STATUS_YES)
            ->count();

        return intval($count);
    }


}