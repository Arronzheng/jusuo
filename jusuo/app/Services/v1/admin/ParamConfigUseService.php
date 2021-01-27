<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\StrService;
use App\Http\Services\RootService;
use App\Models\AdministratorOrganization;
use App\Models\Designer;
use App\Models\Member;
use App\Models\Organization;
use App\Http\Services\common\OrganizationService;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\ParamConfig;
use App\ParamConfigOrganization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParamConfigUseService extends RootService {

    //参数设置 实际使用专用service

    private $object_id;
    private $organization_type;
    private $include_self=false;

    /**
     * ParamConfigUseService constructor.
     * @param $object_id integer 对象id（个人用户、品牌商、经销商、装饰公司等的id）
     * @param null $organization_type 组织类型（默认null是个人用户类型）
     */
    public function __construct($object_id,$organization_type=null)
    {
        $this->object_id = $object_id;
        $this->organization_type = $organization_type;
    }

    public function setIncludeSelf($value)
    {
        $this->include_self = (boolean)$value;
    }

    /** 根据name获取config的值的设置
     * @param $name string 参数设置名称
     * @return null|array|boolean|string  若参数无值，则返回null。其他有可能返回数组、boolean、string等
     */
    public function find($name)
    {
        $final_config_set = $this->get_final_config_set($name);

        return $final_config_set;

    }

    //根据关键词获取config的值的设置（多条记录）
    public function get_by_keyword($keyword)
    {
        $entry = DB::table('param_configs as pc')
            ->select(['pc.*'])
            ->where('name','like','%'.$keyword.'%');

        $data = $entry->get();

        $result = [];

        foreach($data as $v){
            $final_config_set = $this->get_final_config_set($v->name);
            $param_name = $v->name;
            $value = $final_config_set;
            $result[$param_name] = $value;
        }

        return $result;

    }

    //直接获取参数在平台设置的值
    public static function find_root($name)
    {
        $this_obj = new ParamConfigUseService(0);
        $config_set = $this_obj->get_config_set_row($name,OrganizationService::ORGANIZATION_TYPE_PLATFORM);
        if($config_set!==false){
            //有参数设置值数据
            //进入工厂处理数据
            $final_config_set = $this_obj->config_set_factory($config_set->value_type,$config_set->value);
            return $final_config_set;
        }
        return null;
    }

    //直接获取参数在平台设置的值、报错前缀、后缀
    public static function find_root_msg_prefix_suffix($name)
    {
        $this_obj = new ParamConfigUseService(0);
        $config_set = $this_obj->get_config_set_row($name,OrganizationService::ORGANIZATION_TYPE_PLATFORM);
        if($config_set!==false){
            //有参数设置值数据
            //进入工厂处理数据
            $final_config_set = $this_obj->config_set_factory($config_set->value_type,$config_set->value);
            return [
                'value'=>$final_config_set,
                'msg_prefix'=>$config_set->err_msg_prefix,
                'msg_suffix'=>$config_set->err_msg_suffix,
            ];
        }
        return null;
    }

    //根据关键词获取参数在平台设置的值（多条记录）
    public static function get_root_by_keyword($keyword)
    {
        $this_obj = new ParamConfigUseService(0);

        $entry = DB::table('param_configs as pc')
            ->join('param_config_organizations as pco','pc.id','=','pco.config_id')
            ->select(['pc.name','pc.value_type','pco.value'])
            ->where('name','like','%'.$keyword.'%')
            ->where('pco.organization_type',OrganizationService::ORGANIZATION_TYPE_PLATFORM);

        $data = $entry->get();

        $result = [];

        foreach($data as $config_set){
            try{
                $config_set->value = unserialize($config_set->value);
            }catch(\Exception $e){

            }
            $final_config_set = $this_obj->config_set_factory($config_set->value_type,$config_set->value);
            $param_name = $config_set->name;
            $value = $final_config_set;
            $result[$param_name] = $value;
        }

        return $result;

    }


    /** 获取经过处理的参数设置值（单条）
     * @param $name
     * @return bool|null
     */
    private function get_final_config_set($name)
    {

        $relation_chain = [];

        $object_id=$this->object_id;
        $organization_type=$this->organization_type;
        $level1 = null;
        $level2 = null;
        $level3 = null;
        $level4 = null;

        //查找关系链条
        $self_chain = null;
        if($organization_type==null){
            //使用对象是个人账户
            $level1 = Designer::find($object_id);
            if(!$level1){return $this->result_fail('个人账号不存在');}
            if($level1->organization_id){
                //个人账户有上级字段
                $level2 = $level1->organization;
                $level2_organization_type = Designer::organizationTypeToId($level1->organization_type);
                if($level2){
                    array_push($relation_chain,[
                        'organization_type'=>$level2_organization_type,
                        'organization_id' => $level2->id
                    ]);
                    switch($level2_organization_type){
                        case OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY:
                            //lv2是设计机构，则lv3一定是平台
                            array_push($relation_chain,[
                                'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                            ]);
                            break;
                        case OrganizationService::ORGANIZATION_TYPE_SELLER:
                            //lv2是销售商，则检查lv3是否是品牌商
                            $level3 = OrganizationBrand::find($level2->p_brand_id);
                            if(!$level3){
                                //如果lv3品牌商找不到，则添加平台进链条
                                array_push($relation_chain,[
                                    'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                                ]);
                            }else{
                                //如果lv3是品牌商，则lv4是平台
                                array_push($relation_chain,[
                                    'organization_type'=>OrganizationService::ORGANIZATION_TYPE_BRAND,
                                    'organization_id' => $level3->id
                                ]);
                                array_push($relation_chain,[
                                    'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                                ]);
                            }
                            break;
                        case OrganizationService::ORGANIZATION_TYPE_BRAND:
                            //如果lv2是品牌商，则lv3一定是平台
                            array_push($relation_chain,[
                                'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                            ]);
                            break;
                        default:break;
                    }

                }else{
                    //lv2是平台
                    array_push($relation_chain,[
                        'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                    ]);
                }
            }else{
                //lv2是平台
                array_push($relation_chain,[
                    'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                ]);
            }

        }else{
            switch($organization_type){

                //使用对象是销售商
                case OrganizationService::ORGANIZATION_TYPE_SELLER:
                    $level1  = OrganizationDealer::find($object_id);
                    $self_chain = [
                        'organization_type'=>OrganizationService::ORGANIZATION_TYPE_SELLER,
                        'organization_id' => $object_id
                    ];
                    if(!$level1){return null;}
                    $level2 = OrganizationBrand::find($level1->p_brand_id);
                    if(!$level2){
                        //如果lv2品牌商找不到，则添加平台进链条
                        array_push($relation_chain,[
                            'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                        ]);
                    }else{
                        //如果lv2是品牌商，则lv3是平台
                        array_push($relation_chain,[
                            'organization_type'=>OrganizationService::ORGANIZATION_TYPE_BRAND,
                            'organization_id' => $level2->id,
                        ]);
                        array_push($relation_chain,[
                            'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                        ]);
                    }
                    break;
                case OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY:
                    //使用对象是设计机构
                    $self_chain = [
                        'organization_type'=>OrganizationService::ORGANIZATION_TYPE_DESIGN_COMPANY,
                        'organization_id' => $object_id
                    ];
                    array_push($relation_chain,[
                        'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                    ]);
                    break;
                case OrganizationService::ORGANIZATION_TYPE_BRAND:
                    //使用对象是品牌商
                    $self_chain = [
                        'organization_type'=>OrganizationService::ORGANIZATION_TYPE_BRAND,
                        'organization_id' => $object_id
                    ];
                    array_push($relation_chain,[
                        'organization_type'=>OrganizationService::ORGANIZATION_TYPE_PLATFORM
                    ]);
                    break;
                default:break;
            }
        }

        //关系链条是否需要包含自己
        if($this->include_self && $self_chain){
            array_push($relation_chain,$self_chain);
        }

        //根据关系链条查找相应的参数值
        $final_config_set = null;
        $chain_value_result = [];  //存放每个关系链条的设置值结果
        for($i=0;$i<count($relation_chain);$i++){
            $organization_type = $relation_chain[$i]['organization_type'];
            $organization_id = isset($relation_chain[$i]['organization_id'])?$relation_chain[$i]['organization_id']:null;
            $config_set = $this->get_config_set_row($name,$organization_type,$organization_id);
            if($config_set!==false){
                //有参数设置值数据
                //进入工厂处理数据
                $final_config_set = $this->config_set_factory($config_set->value_type,$config_set->value);
                //如果参数值是布尔类型（即必填/选填参数项），则需要往下判断服从关系
                if(is_bool($final_config_set)){
                    $chain_value_result[] = $final_config_set;
                }else{
                    //非布尔类型可以直接返回
                    return $final_config_set;
                }
            }
        }

        //如果参数值是布尔类型（即必填/选填参数项），则判断服从关系
        if(isset($chain_value_result[0])){
            if(is_bool($chain_value_result[0])){
                for($i=0;$i<count($chain_value_result);$i++){
                    if($final_config_set===null){
                        //如果最终结果为空，则先填充了最终结果
                        $final_config_set = $chain_value_result[$i];
                    }else{
                        //如果最终结果不为空，则判断当前参数值是否应该被服从
                        if((int)$chain_value_result[$i] > $final_config_set){
                            $final_config_set = $chain_value_result[$i];
                        }
                    }
                }
            }
        }

        return $final_config_set;
    }

    /** 根据name获取config的值的原始数据（单条记录）
     * @param $name
     * @param $organization_type
     * @param null $organization_id
     * @return bool
     */
    private function get_config_set_row($name,$organization_type,$organization_id=null)
    {
        $entry = DB::table('param_config_organizations as pco')
            ->select(['pco.*','pc.name','pc.display_name','pc.value_type','pc.err_msg_prefix','pc.err_msg_suffix'])
            ->join('param_configs as pc','pc.id','=','pco.config_id')
            ->where('pc.name',$name)
            ->where('pco.organization_type',$organization_type);

        if($organization_id!==null){
            $entry->where('pco.organization_id',$organization_id);
        }
        $data = $entry->first();

        if($data){
            try{
                $data->value = unserialize($data->value);
            }catch(\Exception $e){

            }
            return $data;
        }else{
            return false;
        }


    }

    /** 数据处理工厂，根据数据类型不同处理数据
     * @param $value_type
     * @param $value
     * @return bool
     */
    private function config_set_factory($value_type,$value)
    {
        switch($value_type){
            case ParamConfig::VALUE_TYPE_SINGLE_NUMBER:
                //单个值，直接返回value值
                return $value['value'];
                break;
            case ParamConfig::VALUE_TYPE_FUNC_PRIVILEGE_SWITCH:
                //功能权限开关，返回value值的boolean值。返回true就是开，false就是关。
                return (boolean)$value;
                break;
            case ParamConfig::VALUE_TYPE_WHETHER_RADIO:
                //是/否单选，返回value值的boolean值。返回true就是是，false就是否。
                return (boolean)$value['value'];
                break;
            case ParamConfig::VALUE_TYPE_REQUIRED_RADIO:
                //必/选填单选，返回value值的boolean值的相反值。返回true就是必填，false就是选填。
                return !(boolean)$value['value'];
                break;
            case ParamConfig::VALUE_TYPE_PUBLIC_OBJ_RADIO:
                //公开对象单选，返回value值。返回10就是合作对象，20当地用户，30所有人
                return $value['value'];
                break;
            case ParamConfig::VALUE_TYPE_LIMIT_RADIO:
                //有/无限制单选，返回value值的boolean值的相反值。返回true就是有限制，false就是无限制。
                return !(boolean)$value['value'];
                break;
            case ParamConfig::VALUE_TYPE_MULTIPLE_TEXT:
                //多个字符串text，返回value值。
                return $value['value[]'];
                break;
            case ParamConfig::VALUE_TYPE_MULTIPLE_ITEM:
                //公式，返回value值
                return $value;
                break;
            default:
                return $value;
                break;
        }
    }

}