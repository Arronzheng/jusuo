<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileCaptcha extends Model
{
    //
    protected $guarded = [];

    const TYPE_REGISTER = 0;
    const TYPE_LOGIN = 1;
    const TYPE_BIND = 2;
    const TYPE_RESET_PWD = 3;
    const TYPE_RESET_PHONE = 4;
    const TYPE_M_BIND_MOBILE = 5; //手机端绑定手机
}
