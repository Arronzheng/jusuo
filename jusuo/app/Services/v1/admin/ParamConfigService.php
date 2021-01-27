<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\RootService;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ParamConfigOrganization;
use App\ParamConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParamConfigService extends RootService {

    //参数设置专用Service
    private $organization_id = 0;
    private $organization_type = OrganizationService::ORGANIZATION_TYPE_PLATFORM;
    private $param_config = null;
    private $param_config_organization = null;


    /**
     * ParamConfigService constructor.
     * @param $param_name
     * @param int $organization_id  //0默认是平台管理员
     */
    public function __construct($param_name,$organization_type,$organization_id=0)
    {
        $this->organization_id = $organization_id;
        $this->organization_type = $organization_type;
        $this->param_config = ParamConfig::OfName($param_name)->first();
        if($this->param_config){

            $entry = ParamConfigOrganization::OfConfigId($this->param_config->id);

            if($organization_id){
                $entry->OfOrganizationId($organization_id);
            }

            $this->param_config_organization = $entry->first();
        }
    }

    //更新参数值
    public function update_param_config($value)
    {
        if(!$this->param_config){
            return $this->result_fail('参数不存在');
        }

        $param_config_organization = $this->param_config_organization;
        if(!$param_config_organization){

            $param_config_organization = new ParamConfigOrganization();
            $param_config_organization->config_id = $this->param_config->id;
            $param_config_organization->organization_id = $this->organization_id;
            $param_config_organization->organization_type = $this->organization_type;
        }

        $param_config_organization->value = $value;

        $param_config_organization->save();
        $this->param_config_organization = $param_config_organization;

        return $this->result_success();
    }

    //根据关键词获取config的基本信息（不是值的信息）
    public static function get_configs_by_keyword($keyword)
    {
        $entry = DB::table('param_configs as pc')
            ->select(['pc.*'])
            ->where('name','like','%'.$keyword.'%');

        $data = $entry->get();

        return $data;

    }

    //根据name获取config的值的设置（单条记录）
    public static function get_config_set_by_name($name,$organization_type=null,$organization_id=null)
    {
        $entry = DB::table('param_config_organizations as pco')
            ->select(['pco.*','pc.name','pc.display_name','pc.value_type'])
            ->join('param_configs as pc','pc.id','=','pco.config_id')
            ->where('name',$name);

        if($organization_id!==null){
            $entry->where('pco.organization_id',$organization_id);
        }

        if($organization_type!==null){
            $entry->where('pco.organization_type',$organization_type);
        }

        $data = $entry->first();

        if($data){
            if($data->value){
                try{
                    $data->value = unserialize($data->value);
                }catch(\Exception $e){

                }
            }
        }


        return $data;

    }

    //根据关键词获取config的值的设置（单条记录）
    public static function get_config_set_by_keyword($keyword,$organization_type=null,$organization_id=null)
    {
        $entry = DB::table('param_config_organizations as pco')
            ->select(['pco.*','pc.name','pc.display_name','pc.value_type'])
            ->join('param_configs as pc','pc.id','=','pco.config_id')
            ->where('name','like','%'.$keyword.'%');

        if($organization_id!==null){
            $entry->where('pco.organization_id',$organization_id);
        }

        if($organization_id!==null){
            $entry->where('pco.organization_type',$organization_type);
        }

        $data = $entry->first();

        if($data){
            if($data->value){
                try{
                    $data->value = unserialize($data->value);
                }catch(\Exception $e){

                }
            }
        }

        return $data;

    }

    //根据关键词获取config的值的设置（多条记录）
    public static function get_config_sets_by_keyword($keyword,$organization_type=null,$organization_id=null)
    {
        $entry = DB::table('param_config_organizations as pco')
            ->select(['pco.*','pc.name','pc.display_name','pc.value_type'])
            ->join('param_configs as pc','pc.id','=','pco.config_id')
            ->where('name','like','%'.$keyword.'%');

        if($organization_id!==null){
            $entry->where('pco.organization_id',$organization_id);
        }

        if($organization_type!==null){
            $entry->where('pco.organization_type',$organization_type);
        }else{
            $entry->where('pco.organization_type',OrganizationService::ORGANIZATION_TYPE_PLATFORM);
        }


        $data = $entry->get();

        $data->transform(function($v){
            switch($v->value_type){
                default:
                    if($v->value){
                        try{
                            $v->value = unserialize($v->value);
                        }catch(\Exception $e){

                        }
                    }
                    break;
            }
            return $v;
        });

        return $data;

    }




    /*--------------------------下级参数设置专用--------------------------*/
    //获取平台某参数的设置值
    public static function get_platform_config_set($config_name)
    {

        $parent_value = null;

        //默认取平台的值
        $platform_config = ParamConfigService::get_config_set_by_name($config_name,OrganizationService::ORGANIZATION_TYPE_PLATFORM,0);
        if($platform_config){
            $parent_value = $platform_config->value;
        }

        return $parent_value;
    }

    //获取销售商上级设置
    public static function get_seller_parent_config_set($config_name,$seller_id)
    {

        $parent_value = null;

        //默认取平台的值
        $platform_config = ParamConfigService::get_config_set_by_name($config_name,OrganizationService::ORGANIZATION_TYPE_PLATFORM,0);
        if($platform_config){
            $parent_value = $platform_config->value;
        }

        if($seller_id){
            $seller = OrganizationDealer::OfStatus(OrganizationDealer::STATUS_ON)->find($seller_id);
            if($seller){
                if($seller->belong_organization_id){
                    $brand = OrganizationBrand::OfStatus(OrganizationBrand::STATUS_ON)->find($seller->p_brand_id);
                    if($brand){
                        $brand_config = ParamConfigService::get_config_set_by_name($config_name,OrganizationService::ORGANIZATION_TYPE_BRAND,$brand->id);
                        if($brand_config){
                            $parent_value = $brand_config->value;
                        }
                    }
                }
            }
        }

        return $parent_value;
    }
    /*--------------------------下级参数设置专用--------------------------*/


    //内部生成权限开关.设计师.平台功能权限
    public static function generate_level_experience()
    {
        $number_array = [0,1,2,3,4,5,6,7,8,9];

        for($j=0;$j<count($number_array);$j++){
            $privilege_name = 'switch.designer.level_experience.'.$j;
            $privilege_display_name = '权限开关.设计师.等级经验值.'.$j.'级';
            $exist = ParamConfig::where('name',$privilege_name)->first();
            if(!$exist){
                $model = new ParamConfig();
                $model->display_name = $privilege_display_name;
                $model->name = $privilege_name;
                $model->value_type = ParamConfig::VALUE_TYPE_SINGLE_NUMBER;
                $model->save();
            }
        }

        die('abc');
    }//内部生成权限开关.设计师.平台功能权限
    public static function generate_switch_func()
    {
        $number_array = ['temp',0,1,2,3,4,5,6,7,8,9];
        $privilege_array = [
            'switch.designer.func_privilege.make_appointment.',
            'switch.designer.func_privilege.comment.',
            'switch.designer.func_privilege.read_design_tutorial.',
            'switch.designer.func_privilege.use_design_tool.',
            'switch.designer.func_privilege.upload_works.',
            'switch.designer.func_privilege.forward_designer_index.',
            'switch.designer.func_privilege.attach_organization.',
            'switch.designer.func_privilege.apply_design_company.',
            'switch.designer.func_privilege.apply_business_cooperation.',
            'switch.designer.func_privilege.apply_lecturer.',
            'switch.designer.func_privilege.accept_appointment.',
        ];

        $privilege_china_array = [
            '预约他人.',
            '留言.',
            '查看设计教程.',
            '使用设计工具.',
            '上传作品.',
            '转发设计师主页.',
            '归属单位组织.',
            '申请注册设计机构.',
            '申请与商家合作.',
            '申请成为讲师.',
            '接受预约.',
        ];

        $privilege_china_prefix = '权限开关.设计师.平台功能权限.';

        for($i=0;$i<count($privilege_array);$i++){
            for($j=0;$j<count($number_array);$j++){
                $privilege_name = $privilege_array[$i].$number_array[$j];
                $level_name = $number_array[$j]==='temp'?'临时账号':$number_array[$j].'级';
                $privilege_display_name = $privilege_china_prefix.$privilege_china_array[$i].$level_name;
                $exist = ParamConfig::where('name',$privilege_name)->first();
                if(!$exist){
                    $model = new ParamConfig();
                    $model->display_name = $privilege_display_name;
                    $model->name = $privilege_name;
                    $model->value_type = ParamConfig::VALUE_TYPE_SINGLE_NUMBER;
                    $model->save();
                }
            }
        }

        die('abc');
    }

    //内部生成权限开关.设计师.平台优惠特权
    public static function generate_switch_discount()
    {
        $number_array = ['temp',0,1,2,3,4,5,6,7,8,9];
        $privilege_array = [
            'switch.designer.discount_privilege.upload_works_extra_integral.',
            'switch.designer.discount_privilege.shop_discount.',
            'switch.designer.discount_privilege.exchange_integral_discount.',
        ];

        $privilege_china_array = [
            '上传作品获得的积分奖励.',
            '商城折扣优惠.',
            '积分兑换现金优惠.',
        ];

        $privilege_china_prefix = '权限开关.设计师.平台优惠特权.';

        for($i=0;$i<count($privilege_array);$i++){
            for($j=0;$j<count($number_array);$j++){
                $privilege_name = $privilege_array[$i].$number_array[$j];
                $level_name = $number_array[$j]==='temp'?'临时账号':$number_array[$j].'级';
                $privilege_display_name = $privilege_china_prefix.$privilege_china_array[$i].$level_name;
                $exist = ParamConfig::where('name',$privilege_name)->first();
                if(!$exist){
                    $model = new ParamConfig();
                    $model->display_name = $privilege_display_name;
                    $model->name = $privilege_name;
                    $model->value_type = ParamConfig::VALUE_TYPE_MULTIPLE_ITEM;
                    $model->save();
                }
            }
        }

        die('abc');
    }

}