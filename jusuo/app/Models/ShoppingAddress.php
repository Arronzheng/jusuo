<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingAddress extends Model
{
    //
    const IS_DEFAULT_YES = 1;
    const IS_DEFAULT_NO = 0;

    public static $statusGroup = [
        self::IS_DEFAULT_YES => '默认',
        self::IS_DEFAULT_NO => '非默认',
    ];
}
