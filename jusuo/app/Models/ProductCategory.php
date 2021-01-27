<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    //
    protected $guarded = [];

    public static function getProductCategoryText($productCategoryId){
        $productCategory = ProductCategory::find($productCategoryId);
        if($productCategory){
            return $productCategory->name;
        }
        else{
            return '';
        }
    }
}
