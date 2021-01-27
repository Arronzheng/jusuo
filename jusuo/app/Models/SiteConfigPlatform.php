<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfigPlatform extends Model
{
    //
    const TYPE_TEXT = 1;
    const TYPE_TEXTAREA = 2;
    const TYPE_RADIO = 3;
    const TYPE_EDITOR = 4;
    const TYPE_SERIALIZE = 5;

    public static $typeGroups = [
        self::TYPE_TEXT=>'文本框',
        self::TYPE_TEXTAREA=>'文本域',
        self::TYPE_RADIO=>'单选框',
        self::TYPE_EDITOR=>'富文本框',
        self::TYPE_SERIALIZE=>'序列化字符串',
    ];
}
