<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignerDetail extends Model
{
    //

    protected $guarded = [];

    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    //是否已实名认证
    const APPROVE_REALNAME_NO = 0;
    const APPROVE_REALNAME_YES = 1;

    //是否打开“我是设计师”相关内容
    const PRIVILEGE_SHOW_IM_DESIGNER_YES = 1;
    const PRIVILEGE_SHOW_IM_DESIGNER_NO = 0;

    //是否打开“我是设计师”相关内容
    const PRIVILEGE_ACCOUNT_LOCK_YES = 1;
    const PRIVILEGE_ACCOUNT_LOCK_NO = 0;

    const DESIGNER_TYPE_NOT_CHOOSE = 0;
    const DESIGNER_TYPE_APPLICATION = 1;
    const DESIGNER_TYPE_SPACE = 2;

    //是否已置顶
    const TOP_STATUS_NO = 0;
    const TOP_STATUS_YES = 1;

    public static function designerLevelIdNameGroup($need_zero = false)
    {
        $data = [];

        $i=1;
        if($need_zero==true){
            $i=0;
        }

        for(;$i<=5;$i++){

            $temp = array();
            $temp['id'] = $i;
            $temp['name'] = Designer::designerTitleCn($i);

            array_push($data,$temp);
        }

        return $data;
    }


    public static function designerLevelGroup($key=null){

        if($key==-1){
            return '临时账号';
        }else if($key>=0){
            return $key.'级设计师';
        }else{
            return '';
        }
    }

    public static function getSelfOrganization($designer_id)
    {
        $result = '';
        $designer = Designer::find($designer_id);
        if($designer){
            $detail = $designer->detail;
            if($designer->organization_id>0){
                //如果是组织设计师
                switch($designer->organization_type){
                    case Designer::ORGANIZATION_TYPE_BRAND:
                        $organization = OrganizationBrand::find($designer->organization_id);
                        if($organization){
                            $result = $organization->name;
                        }
                        break;
                    case Designer::ORGANIZATION_TYPE_SELLER:
                        $organization = OrganizationDealer::find($designer->organization_id);
                        if($organization){
                            $result = $organization->name;
                        }
                        break;
                    default:break;
                }
            }else{
                $result = $detail->self_organization;
            }
        }

        return $result;
    }

    public static function designerTypeGroup($key=NULL){
        $data = DesignerAccountType::all();
        $keyed = $data->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });
        $group = $keyed->all();
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }

    }

    public static function privilegeShowImDesignerGroup($key=NULL)
    {
        $group=[
            self::PRIVILEGE_SHOW_IM_DESIGNER_YES=>'是',
            self::PRIVILEGE_SHOW_IM_DESIGNER_NO=>'否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }    }

    public static function privilegeAccountLockGroup($key=NULL)
    {
        $group=[
            self::PRIVILEGE_ACCOUNT_LOCK_YES=>'是',
            self::PRIVILEGE_ACCOUNT_LOCK_NO=>'否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }    }


    public static function genderGroup($key=NULL)
    {
        $group=[
            self::GENDER_UNKNOWN=>'未知',
            self::GENDER_MALE=>'男',
            self::GENDER_FEMALE=>'女'
        ];

        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }
    }

    public static function approveRealnameGroup($key=NULL)
    {
        $group=[
            self::APPROVE_REALNAME_YES=>'是',
            self::APPROVE_REALNAME_NO=>'否',
        ];
        if(!is_null($key)){
            return array_key_exists($key,$group)?$group[$key]:'';
        }else{
            return $group;
        }    }
}
