<?php

namespace App\Models;

use App\SearchProduct;
use Illuminate\Database\Eloquent\Model;

class ProductCeramic extends Model
{
    //是否已有审核通过的版本
    const HAS_FIRST_APPROVED_YES = 1;
    const HAS_FIRST_APPROVED_NO = 0;

    const TOP_PLATFORM_ON = 1;
    const TOP_PLATFORM_OFF = 0;

    //平台站置顶
    public static function topPlatformGroup($key=NULL){
        $group = [
            self::TOP_PLATFORM_ON => '置顶',
            self::TOP_PLATFORM_OFF => '未置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //品牌站状态
    const STATUS_REJECT = 0;
    const STATUS_VERIFYING = 1;
    const STATUS_PASS = 2;
    const STATUS_TEMP = -1;  //暂存

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_REJECT => '不通过',
            self::STATUS_VERIFYING => '正在审核',
            self::STATUS_PASS => '已通过',
            self::STATUS_TEMP => '暂存',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //平台站状态
    const STATUS_PLATFORM_VERIFYING = 1;
    const STATUS_PLATFORM_ON = 2;
    const STATUS_PLATFORM_OFF = 0;

    public static function statusPlatformGroup($key=NULL){
        $group = [
            self::STATUS_PLATFORM_VERIFYING => '申请审核中',
            self::STATUS_PLATFORM_ON => '展示',
            self::STATUS_PLATFORM_OFF => '下架',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const VISIBLE_NO = 0;
    const VISIBLE_YES = 1;

    public static function visibleGroup($key=NULL){
        $group = [
            self::VISIBLE_NO => '不展示',
            self::VISIBLE_YES => '展示',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TYPE_PRODUCT = 0;
    const TYPE_ACCESSORY = 1;

    public static function typeGroup($key=NULL){
        $group = [
            self::VISIBLE_NO => '产品',
            self::VISIBLE_YES => '配件',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //定价方式
    const PRICE_WAY_UNIFIED = 0; //统一
    const PRICE_WAY_FLOAT = 1;  //浮动
    const PRICE_WAY_CHANNEL = 2; //渠道
    const PRICE_WAY_NOT_ALLOW = 3; //不允许定价

    public static function priceWayGroup($key=NULL){
        $group = [
            self::PRICE_WAY_UNIFIED => '统一定价',
            self::PRICE_WAY_FLOAT => '浮动定价',
            self::PRICE_WAY_CHANNEL => '渠道定价',
            self::PRICE_WAY_NOT_ALLOW => '不允许定价',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //产品问答
    public function qas()
    {
        return $this->hasMany(ProductQa::class, 'product_id')->orderBy('created_at','desc')->where('status',1);
    }

    //记录产品与在售城市
    public function sale_areas()
    {
        return $this->hasMany(LogProductSaleArea::class, 'product_id');
    }

    //关联方案
    public function albums(){
        return $this->belongsToMany(Album::class,'album_product_ceramics','product_ceramic_id','album_id')->withTimestamps();
    }

    //销售商
    public function dealer()
    {
        return $this->belongsToMany(OrganizationDealer::class, 'product_ceramic_authorizations','product_id','dealer_id');
    }

    //销售商授权记录
    public function authorize_dealer()
    {
        return $this->hasMany(ProductCeramicAuthorization::class, 'product_id');
    }
    public function authorize_dealer_on()
    {
        return $this->hasMany(ProductCeramicAuthorization::class, 'product_id')->where('status',ProductCeramicAuthorization::STATUS_ON);
    }

    //品牌
    public function brand()
    {
        return $this->belongsTo(OrganizationBrand::class, 'brand_id');
    }

    //规格
    public function spec()
    {
        return $this->belongsTo(CeramicSpec::class, 'spec_id');
    }

    //系列
    public function series()
    {
        return $this->belongsTo(CeramicSeries::class, 'series_id');
    }

    //应用类别
    public function apply_categories()
    {
        return $this->belongsToMany(CeramicApplyCategory::class, 'product_ceramic_apply_categories', 'product_id', 'apply_category_id');
    }

    //工艺类别
    public function technology_categories()
    {
        return $this->belongsToMany(CeramicTechnologyCategory::class, 'product_ceramic_technology_categories', 'product_id', 'technology_category_id');
    }

    //色系
    public function colors()
    {
        return $this->belongsToMany(CeramicColor::class, 'product_ceramic_colors', 'product_id', 'color_id');
    }

    //表面特征
    public function surface_features()
    {
        return $this->belongsToMany(CeramicSurfaceFeature::class, 'product_ceramic_surface_features', 'product_id', 'surface_feature_id');
    }

    //可应用空间风格
    public function styles()
    {
        return $this->belongsToMany(Style::class, 'product_ceramic_styles', 'product_id', 'style_id');
    }

    //产品配件
    public function accessories()
    {
        return $this->hasMany(ProductCeramicAccessory::class, 'product_id');
    }

    //产品搭配
    public function collocations()
    {
        return $this->hasMany(ProductCeramicCollocation::class, 'product_id');
    }

    //产品搭配关联表
    public function collocation_rels()
    {
        return $this->hasMany(ProductCeramicCollocation::class, 'product_id');

    }

    //空间应用说明
    public function spaces()
    {
        return $this->hasMany(ProductCeramicSpace::class, 'product_id');
    }

    //产品授权销售商表
    public function authorizations()
    {
        return $this->hasMany(ProductCeramicAuthorization::class, 'product_id');
    }

    //产品价格---产品价格直接在authorization表设置
    /*public function productCeramicAuthorizePrice(){
        return $this->hasOne(ProductCeramicAuthorizePrice::class,'product_id');
    }*/

    public function searchProduct(){
        return $this->hasOne(SearchProduct::class,'product_id');
    }


}
