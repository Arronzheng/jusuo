<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    //审核状态
    const STATUS_REJECT = '000';
    const STATUS_DELETE = '010';
    //const STATUS_HIDDEN = '020';
    const STATUS_TEMP = '050';
    const STATUS_VERIFYING = '100';
    const STATUS_PASS = '200';

    //加入搜索表
    const STATUS_SEARCH_ON = 1;
    const STATUS_SEARCH_OFF = 0;

    //是否代表作
    const IS_REPRESENTATIVE_WORK_ON = 1;
    const IS_REPRESENTATIVE_WORK_OFF = 0;

    public static function statusGroup($key=NULL){
        $group = [
            self::STATUS_REJECT => '不通过',
            self::STATUS_DELETE => '已删除',
            //self::STATUS_HIDDEN => '已隐藏',
            self::STATUS_TEMP => '未审核',
            self::STATUS_VERIFYING => '正在审核',
            self::STATUS_PASS => '已通过',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //阶段状态
    const PERIOD_STATUS_EDIT = '000';
    const PERIOD_STATUS_VERIFY = '100';
    const PERIOD_STATUS_FINISH = '200';

    public static function periodStatusGroup($key=NULL){
        $group = [
            self::PERIOD_STATUS_EDIT => '编辑阶段',
            self::PERIOD_STATUS_VERIFY => '审核阶段',
            self::PERIOD_STATUS_FINISH => '完成阶段',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    //可见状态
    const VISIBLE_STATUS_ON = '100';
    const VISIBLE_STATUS_OFF = '200';

    public static function visibleStatusGroup($key=NULL){
        $group = [
            self::VISIBLE_STATUS_ON => '显示',
            self::VISIBLE_STATUS_OFF => '已下架',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    const TYPE_HD_PHOTO = 0;
    const TYPE_KUJIALE_SOURCE = 1;

    public static function typeGroup($key=NULL){
        $group = [
            self::TYPE_HD_PHOTO => '高清图',
            //20200704暂时停止酷家乐方案源
            //self::TYPE_KUJIALE_SOURCE => '酷家乐方案源',
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

    //品牌站状态
    const STATUS_BRAND_VERIFYING = 0;//原值1，2020.03.18改为0
    const STATUS_BRAND_ON = -2;//原值2，2020.03.18废弃，改为直接写入品牌id
    const STATUS_BRAND_OFF = -1;//原值0，2020.03.18改为-1

    public static function statusBrandGroup($key=NULL){
        $group = [
            self::STATUS_BRAND_VERIFYING => '申请审核中',
            self::STATUS_BRAND_ON => '展示',
            self::STATUS_BRAND_OFF => '下架',
        ];
        if(!is_null($key)){
            if($key>0){
                return $group[self::STATUS_BRAND_ON];
            }else{
                return array_key_exists($key,$group)?$group[$key]:'';

            }
        }else{
            return $group;
        }
    }

    //销售商站状态
    const STATUS_DEALER_ON = 1;
    const STATUS_DEALER_OFF = 0;

    public static function statusDealerGroup($key=NULL){
        $group = [
            self::STATUS_DEALER_ON => '展示',
            self::STATUS_DEALER_OFF => '下架',
        ];
        if(!is_null($key)){
            if($key>0){
                return $group[self::STATUS_DEALER_ON];
            }else{
                return array_key_exists($key,$group)?$group[$key]:'';

            }
        }else{
            return $group;
        }
    }

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

    const TOP_BRAND_ON = 1;
    const TOP_BRAND_OFF = 0;

    const TOP_DESIGNER_ON = 1;
    const TOP_DESIGNER_OFF = 0;

    public function product_ceramics(){
        return $this->belongsToMany(ProductCeramic::class,'album_product_ceramics','album_id','product_ceramic_id')->withTimestamps();
    }

    public function album_sections(){
        return $this->hasMany(AlbumSection::class,'album_id');
    }

    public function designer(){
        return $this->belongsTo(Designer::class,'designer_id');
    }

    public function designerDetail(){
        return $this->belongsTo(DesignerDetail::class,'designer_id','designer_id');
    }

    public function house_types(){
        return $this->belongsToMany(HouseType::class,'album_house_types','album_id','house_type_id')->withTimestamps();
    }

    public function style(){
        return $this->belongsToMany(Style::class,'album_styles','album_id','style_id')->withTimestamps();
    }

    public function space_types(){
        return $this->belongsToMany(SpaceType::class,'album_space_types','album_id','space_type_id')->withTimestamps();
    }

    public function comments(){
        return $this->hasMany(AlbumComments::class,'album_id');
    }

    public function searchAlbums(){
        return $this->hasOne(SearchAlbum::class,'album_id');
    }


}
