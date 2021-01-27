<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/10/8
 * Time: 14:00
 */

namespace App\Services\v1\admin;


use App\Models\AdministratorBrand;
use App\Models\MsgAccountBrand;
use App\Models\MsgSystemBrand;
use App\Models\OrganizationBrand;
use Illuminate\Support\Facades\DB;

class MsgSystemBrandMultiService
{

    public static function add($data)
    {
        $result = DB::table('msg_system_brands')->insert($data);

        return $result;
    }

}