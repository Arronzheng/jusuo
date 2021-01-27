<?php

namespace App\Services\v1\site;


class PageService
{

    const ErrorNoResult = 1;
    const ErrorNoAuthority = 2;
    const ErrorNoService = 3;
    const ErrorNoLogin = 4;

    public static function showPage($error=0,$__BRAND_SCOPE=''){
        return view('v1.site.common.error', compact('error','__BRAND_SCOPE'));
    }

    public static function showPageMobile($error=0,$__BRAND_SCOPE=''){
        return view('v1.mobile.common.error', compact('error','__BRAND_SCOPE'));
    }

}