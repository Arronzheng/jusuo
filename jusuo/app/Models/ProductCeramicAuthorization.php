<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCeramicAuthorization extends Model
{
    //
    const STATUS_OFF = '000'; //下架
    const STATUS_ON = '100';   //正常
    const STATUS_NEW = '200';  //新授权

    const PRICE_WAY_TY = '0'; //统一
    const PRICE_WAY_FD = '1';   //浮动
    const PRICE_WAY_QD = '2';  //渠道
    const PRICE_WAY_BDJ = '3';  //不定价

    public static function statusGroup($key=null){
        $group = [
            self::STATUS_NEW => '新授权',
            self::STATUS_ON => '展示',
            self::STATUS_OFF => '不展示'
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const UNIT_PIECE = 1;
    const UNIT_SQUARE = 2;

    public static function unitGroup($key=null){
        $group = [
            self::UNIT_PIECE => '片',
            self::UNIT_SQUARE => '平方',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }


    public function structures()
    {
        return $this->belongsToMany(ProductCeramicStructure::class, 'product_ceramic_authorize_structures', 'authorization_id', 'structure_id');
    }
}
