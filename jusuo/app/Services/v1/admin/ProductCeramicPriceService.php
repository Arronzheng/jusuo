<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Models\AdministratorOrganization;
use App\Models\CeramicSeries;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use Illuminate\Support\Facades\Auth;

class ProductCeramicPriceService{

    //获取浮动价格区间
    /**
     * @return array
     */
    public static function get_float_price_range($product_ceramic_authorization)
    {
        $data = null;
        $result = array();
        $result['top'] = 0;
        $result['bottom'] = 0;
        if($product_ceramic_authorization instanceof ProductCeramicAuthorization){
            $data = $product_ceramic_authorization;
        }else{
            $authorization_id = $product_ceramic_authorization;
            $data = ProductCeramicAuthorization::find($authorization_id);
        }

        if($data){
            $float_space=floor($data->float_space*100)/100;
            $final_space = $data->price_set * ($float_space/100) ;
            $result['top'] = $data->price_set + $final_space;
            $result['bottom'] = $data->price_set - $final_space;
        }

        return $result;

    }


}