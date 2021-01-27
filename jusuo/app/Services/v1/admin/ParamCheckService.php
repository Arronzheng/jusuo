<?php

namespace App\Http\Services\v1\admin;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\RootService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ParamConfigOrganization;
use App\ParamConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParamCheckService extends RootService {

    //表单验证专用Service

    /**
     * 用于间接的限制值检查，$array中的$v会先计算为长度，再作比较
     * @param $array 一个数组，键名为param_configs的name，键值为所需检验的表单项值
     * @return 如果超限，返回err_msg_prefix+value+err_msg_suffix，否则返回''
     */
    public static function check_length_param_config($array)
    {
        foreach($array as $k=>$v){
            $length = mb_strlen($v,'UTF-8');
            $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
            if ($length>$config['value']) {
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    /**
     * 用于表单项值为数组（包含多个值）的限制值检查
     * @param $array 一个包含表单项值数组，它们都会接受第二参数的校验
     * @param $config_name 字符串，用于校验的param_configs的name值
     * @return 如果超限，返回err_msg_prefix+value+err_msg_suffix，否则返回''
     */
    public static function check_multi_length_param_config($array,$config_name)
    {
        foreach($array as $v){
            $length = mb_strlen($v,'UTF-8');
            $config = ParamConfigUseService::find_root_msg_prefix_suffix($config_name);
            if ($length>$config['value']) {
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    /**
     * 用于判断参数是否必填检查，$array中的$v会直接作比较
     * @param $array 一个数组，键名为param_configs的name，键值为所需检验的表单项（数组）的长度（count）值
     * @return 如果超限，返回err_msg_prefix+value+err_msg_suffix，否则返回''
     */
    public static function check_array_required_param_config($array)
    {
        foreach($array as $k=>$v){
            $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
            //如果必填，则需要判断是否为空
            if($config['value'] && !$v){
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    /**
     * 用于直接的限制值检查，$array中的$v会直接与限制值作比较
     * @param $array 一个数组，键名为param_configs的name，键值为所需检验的表单项（数组）的长度（count）值
     * @return 如果超限，返回err_msg_prefix+value+err_msg_suffix，否则返回''
     */
    public static function check_array_count_param_config($array)
    {
        foreach($array as $k=>$v){
            $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
            if ($v>$config['value']) {
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    /**
     * 用于有数量区间的限制值检查，$array中的$v会直接与限制值作比较
     * @param $array 一个数组，键名为param_configs的name，键值为所需检验的表单项（数组）的长度（count）值
     * @return 如果超限，返回err_msg_prefix+value+err_msg_suffix，否则返回''
     */
    public static function check_array_count_range_param_config($array)
    {
        foreach($array as $k=>$v){
            $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
            if ($v>$config['value']['upper_limit'] || $v<$config['value']['lower_limit']) {
                return $config['msg_prefix'].$config['value']['lower_limit'].'与'.$config['value']['upper_limit'].$config['msg_suffix'];
            }
        }
        return '';
    }

    /**
     * 用于有最低数量的限制值检查，$array中的$v会直接与限制值作比较
     * @param $array 一个数组，键名为param_configs的name，键值为所需检验的表单项（数组）的长度（count）值
     * @return 如果超限，返回err_msg_prefix+value+err_msg_suffix，否则返回''
     */
    public static function check_array_count_min_limit_param_config($array)
    {
        foreach($array as $k=>$v){
            $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
            if ($v<$config['value']) {
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    public static function pcu_check_length_param_config($array,$pcu)
    {
        foreach($array as $k=>$v){
            $limit = $pcu->find($k);
            $length = mb_strlen($v,'UTF-8');
            if ($length>$limit) {
                $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    public static function pcu_check_array_value_length_param_config($array,$pcu)
    {
        foreach($array as $k=>$v){
            $limit = $pcu->find($k);
            foreach($v as $vv) {
                $length = mb_strlen($vv, 'UTF-8');
                if ($length > $limit) {
                    $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
                    return $config['msg_prefix'] . $config['value'] . $config['msg_suffix'];
                }
            }
        }
        return '';
    }

    public static function pcu_check_array_count_param_config($array,$pcu)
    {
        foreach($array as $k=>$v){
            $limit = $pcu->find($k);
            if ($v>$limit) {
                $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

    public static function pcu_need_param_config($array,$pcu)
    {
        foreach($array as $k=>$v){
            $limit = $pcu->find($k);
            if ($v>$limit) {
                $config = ParamConfigUseService::find_root_msg_prefix_suffix($k);
                return $config['msg_prefix'].$config['value'].$config['msg_suffix'];
            }
        }
        return '';
    }

}