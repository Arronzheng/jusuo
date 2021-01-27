<?php

namespace App\Models;

use App\Http\Services\common\OrganizationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Designer extends Authenticatable
{
    use Notifiable;

    const TOP_DEALER_STATUS_OFF = 0;
    const TOP_DEALER_STATUS_ON = 1;

    public static function topDealerStatusGroup($key=null){
        $group = [
            self::TOP_DEALER_STATUS_OFF => '销售商未置顶',
            self::TOP_DEALER_STATUS_ON => '销售商置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TOP_PLATFORM_STATUS_OFF = 0;
    const TOP_PLATFORM_STATUS_ON = 1;

    public static function topPlatformStatusGroup($key=null){
        $group = [
            self::TOP_PLATFORM_STATUS_OFF => '平台未置顶',
            self::TOP_PLATFORM_STATUS_ON => '平台置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TOP_BRAND_STATUS_OFF = 0;
    const TOP_BRAND_STATUS_ON = 1;

    public static function topBrandStatusGroup($key=null){
        $group = [
            self::TOP_BRAND_STATUS_OFF => '品牌未置顶',
            self::TOP_BRAND_STATUS_ON => '品牌置顶',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //
    const STATUS_ON = '200';   //正常
    const STATUS_OFF = '100';  //禁用
    const STATUS_VERIFYING = '000';  //待审核

    //归属单位的类型
    const ORGANIZATION_TYPE_NONE = '';
    const ORGANIZATION_TYPE_BRAND = 'App\Models\OrganizationBrand';
    const ORGANIZATION_TYPE_SELLER = 'App\Models\OrganizationDealer';
    const ORGANIZATION_TYPE_DESIGNER_COMPANY = 3;

    protected $hidden = ['login_password'];

    protected $fillable = [
        'login_username',
        'login_mobile' ,
        'login_password',
        'remember_token',
        'designer_account',
        'designer_id_code',
        'login_wx_openid',
    ];

    public function styles()
    {
        return $this->belongsToMany(Style::class, 'designer_styles', 'designer_id', 'style_id');
    }

    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'designer_spaces', 'designer_id', 'space_id');
    }

    public function getAuthPassword() {
        return $this->login_password;
    }

    public static function organizationTypeGroup($key=NULL)
    {
        $group=[
            self::ORGANIZATION_TYPE_NONE=>'自由',
            self::ORGANIZATION_TYPE_BRAND=>'品牌',
            self::ORGANIZATION_TYPE_SELLER=>'销售商',
            self::ORGANIZATION_TYPE_DESIGNER_COMPANY=>'装饰公司',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //设计师称号
    const TITLE_NONE = 0;
    const TITLE_XINSHOU = 1;
    const TITLE_JIANXI = 2;
    const TITLE_ZHUANYE = 3;
    const TITLE_ZISHEN = 4;
    const TITLE_JINPAI = 5;

    public static function designerTitle($key=NULL)
    {
        $group=[
            self::TITLE_NONE=>'',
            self::TITLE_XINSHOU=>'xinshou',
            self::TITLE_JIANXI=>'jianxi',
            self::TITLE_ZHUANYE=>'zhuanye',
            self::TITLE_ZISHEN=>'zishen',
            self::TITLE_JINPAI=>'jinpai',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function designerTitleCn($key=NULL)
    {
        $group=[
            self::TITLE_NONE=>'无',
            self::TITLE_XINSHOU=>'新手',
            self::TITLE_JIANXI=>'见习',
            self::TITLE_ZHUANYE=>'专业',
            self::TITLE_ZISHEN=>'资深',
            self::TITLE_JINPAI=>'金牌',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function designerTitleCnFull($key=NULL)
    {
        $group=[
            self::TITLE_NONE=>'',
            self::TITLE_XINSHOU=>'新手设计师',
            self::TITLE_JIANXI=>'见习设计师',
            self::TITLE_ZHUANYE=>'专业设计师',
            self::TITLE_ZISHEN=>'资深设计师',
            self::TITLE_JINPAI=>'金牌设计师',
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function organizationTypeToId($key=NULL)
    {

        $group=[
            self::ORGANIZATION_TYPE_NONE=>'',
            self::ORGANIZATION_TYPE_BRAND=>OrganizationService::ORGANIZATION_TYPE_BRAND,
            self::ORGANIZATION_TYPE_SELLER=>OrganizationService::ORGANIZATION_TYPE_SELLER,
            self::ORGANIZATION_TYPE_DESIGNER_COMPANY=>OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY,
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function statusGroup($key=NULL)
    {
        $group=[
            self::STATUS_ON=>'正常',
            self::STATUS_OFF=>'禁用',
            self::STATUS_VERIFYING=>'待审核'
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public function scopeOfStatus($query,$status)
    {
        return $query->where('status', $status);
    }

    public function detail()
    {
        return $this->hasOne(DesignerDetail::class,'designer_id');
    }

    public function scopeOrganizationId($query,$organization_id)
    {
        return $query->where('organization_id', $organization_id);
    }

    public function scopeOrganizationType($query,$type)
    {
        return $query->where('organization_type', $type);
    }

    public function scopeOrderByCreatedAt($query,$order)
    {
        return $query->orderBy('created_at', $order);
    }

    public function organization()
    {
        return $this->morphTo();
    }

    public function albums(){
        return $this->hasMany(Album::class,'designer_id');
    }

    public function brand()
    {
        return $this->belongsTo(OrganizationBrand::class, 'organization_id')/*
            ->where('designers.organization_type', Designer::ORGANIZATION_TYPE_BRAND)*/;
    }

    public function album()
    {
        return $this->hasMany(Album::class, 'designer_id');
    }

    public function seller()
    {
        return $this->belongsTo(OrganizationDealer::class, 'organization_id')/*
            ->where('designers.organization_type', Designer::ORGANIZATION_TYPE_SELLER)*/;
    }

    public function fav_albums(){
        return $this->belongsToMany(Album::class,'fav_albums','designer_id','album_id');
    }

    public function fav_designers(){
        return $this->belongsToMany(Designer::class,'fav_designers','designer_id','target_designer_id')->withTimestamps();
    }

    public function fav_products(){
        return $this->belongsToMany(ProductCeramic::class,'fav_products','designer_id','product_id');
    }

}
