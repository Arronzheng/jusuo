<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Models\AdministratorOrganization;
use App\Models\CeramicSeries;
use App\Models\Organization;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use Illuminate\Support\Facades\Auth;

class ProductCeramicService{

    //产品系统编号生成
    /**
     * @param $type  //是否配件
     * @param $series_id  //系列id
     * @return string
     */
    public static function get_sys_code($type,$brand_id,$parent_id,$series_id)
    {
        //P+品牌码（3）+系列码（3）+随机识别码（5）+配件码（1）  [20191205]
        $str = 'P';

        //品牌码
        $brand_code = '0001';
        $brand = OrganizationBrand::find($brand_id);
        if($brand){
            $brand_code = $brand->organization_id_code;
        }
        $str.= $brand_code;

        //系列码
        $series_code = '001';
        $series = CeramicSeries::query()->find($series_id);
        if($series){
            $series_code = $series->series_code;
        }
        $str.= $series_code;

        //随机识别码
        $random_code = str_pad(random_int(10000,99999),5,0,STR_PAD_LEFT);
        $str.= $random_code;

        //配件码
        $accessory_code = 0;
        if($type==1){
            //勾选是，则输入父产品编码，查得此父已有N配件，得出配件码为N+1，往前的码拷贝父
            $parent_product = ProductCeramic::find($parent_id);
            if($parent_product){
                $child_accessory_count = ProductCeramic::query()
                    ->where('brand_id',$brand_id)
                    ->where('type',ProductCeramic::TYPE_ACCESSORY)
                    ->where('parent_id',$parent_id)
                    ->count();
                $accessory_code = $child_accessory_count + 1;
            }
        }
        $str.= $accessory_code;

        return $str;

    }


}