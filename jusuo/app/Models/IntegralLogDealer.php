<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegralLogDealer extends Model
{
    //
    const TYPE_GIVEN_BY_BRAND = 1;   //品牌发放
    const TYPE_GIVE_TO_DESIGNER = 2;  //发放给设计师
}
