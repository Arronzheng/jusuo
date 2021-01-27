<?php
/**
 * Created by PhpStorm.
 * User: libin
 * Date: 2019/10/8
 * Time: 14:00
 */

namespace App\Services\v1\admin;


use App\Models\MsgAccountPlatform;
use Illuminate\Support\Facades\DB;

class MsgAccountPlatformMultiService
{

    public static function add($data)
    {
        $result = DB::table('msg_account_platforms')->insert($data);

        return $result;
    }

}