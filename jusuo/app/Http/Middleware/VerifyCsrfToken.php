<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'wechat',
        'admin/api/upload_editor_img',
        'admin/integral/brand_recharge_notify',
        'wechat/native_notify',
    ];
}
