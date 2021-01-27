<?php

namespace App\Http\Controllers\v1\admin\api;


use App\Http\Repositories\common\HouseTypeRepository;
use App\Http\Repositories\common\MemberTypeRepository;

use App\Http\Repositories\common\SpaceRepository;
use App\Http\Repositories\common\SpaceTypeRepository;
use App\Http\Repositories\common\StyleRepository;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigService;
use App\Models\Area;
use App\Models\CeramicColor;
use App\Models\CeramicLifePhase;
use App\Models\CeramicProductStatus;
use App\Models\CeramicSurfaceFeature;
use App\Models\DesignerAccountType;
use App\Models\HouseType;

use App\Models\OrganizationDealer;
use App\Models\ParamConfigOrganization;
use App\Models\CeramicApplyCategory;
use App\Models\ProductCeramicCeramicColor;
use App\Models\CeramicSpec;
use App\Models\CeramicTechnologyCategory;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\Style;
use App\ParamConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Param;

class ParamConfigController extends ApiController
{
    //模块业务码
    //....
    private $educationRepository;
    private $styleRepository;
    private $spaceRepository;
    private $memberTypeRepository;
    private $optionBusinessCategoryOrganizationRepository;
    private $authService;
    private $houseTypeRepository;
    private $spaceTypeRepository;

    public function __construct(
        StyleRepository $styleRepository,
        SpaceRepository $spaceRepository,
        HouseTypeRepository $houseTypeRepository,
        SpaceTypeRepository $spaceTypeRepository,
        MemberTypeRepository $memberTypeRepository,
        AuthService $authService
    )
    {
        $this->styleRepository = $styleRepository;
        $this->spaceRepository = $spaceRepository;
        $this->houseTypeRepository = $houseTypeRepository;
        $this->spaceTypeRepository = $spaceTypeRepository;
        $this->memberTypeRepository = $memberTypeRepository;
        $this->authService = $authService;
    }

    //更新参数设置值
    public function update_param_config(Request $request)
    {
        $param_name = $request->input('param_name','');
        $param_value = $request->input('param_value','');

        //参数值是否存在
        $config_set = ParamConfig::OfName($param_name)->first();
        if(!$config_set){$this->respFail('参数不存在！');}

        $final_value = $param_value;

        $param_array = [];
        for($i=0;$i<count($param_value);$i++){
            $key = $param_value[$i]['name'];
            $value = $param_value[$i]['value'];
            if(!key_exists($key,$param_array)){
                $new_value_array = array();
                //如果前端提交的key是数组，则需要将value组装成数组
                if(strpos($key,'[]') !== false){
                    for($j=0;$j<count($param_value);$j++){
                        if($param_value[$j]['name'] == $key){
                            array_push($new_value_array,$param_value[$j]['value']);
                        }
                    }
                }
                if(count($new_value_array)>0){
                    $value = $new_value_array;
                }
                $param_array[$key] = $value;
            }

        }
        $final_value = serialize($param_array);

        $organization = OrganizationService::get_organization_by_admin_id($this->authService->getAuthId());

        $guardName = $this->authService->getAuthUserGuardName();
        if($guardName == 'platform'){
            $organization_id = 0;
        }else{
            $organization_id = $organization->id;
        }

        //更新参数值
        $organzationTypeName = OrganizationService::get_type_value_by_name($guardName);
        $param_service = new ParamConfigService($param_name,$organzationTypeName,$organization_id);
        $update = $param_service->update_param_config($final_value);

        //如果参数的数据类型是可继承的，并且本级从严，则需要判断下级是否比上级宽松，
        //若下级比上级宽松，则要修改下级的值跟上级一致。
        switch ($config_set->value_type){
            case ParamConfig::VALUE_TYPE_REQUIRED_RADIO:
                //必填/选填单选
                $parent_value = $param_array['value'];
                if($parent_value==0){
                    $child_config_sets = [];
                    //如果设置了必填（从严），则需要考虑下级
                    if($organzationTypeName == 'platform'){
                        //如果是平台，则需要遍历其他组织的本设置值
                        $child_config_sets = ParamConfigOrganization::NotOrganizationType(OrganizationService::ORGANIZATION_TYPE_PLATFORM)
                            ->OfConfigId($config_set->id)
                            ->get();
                    }else if($organzationTypeName == OrganizationService::ORGANIZATION_TYPE_BRAND){
                        //如果是品牌商，则需要遍历旗下的销售商的本设置值
                        $child_sellers_ids = OrganizationDealer::where('p_brand_id',$organization->id)->get()->pluck('id');
                        $child_config_sets = ParamConfigOrganization::NotOrganizationType(OrganizationService::ORGANIZATION_TYPE_PLATFORM)
                            ->OfConfigId($config_set->id)
                            ->InOrganizationId($child_sellers_ids)
                            ->get();
                    }
                    foreach($child_config_sets as $set){
                        $format = unserialize($set->value);
                        $child_value = $format['value'];
                        if($child_value>$parent_value){
                            //更新子参数值
                            $param_service = new ParamConfigService($param_name,$set->organization_id);
                            $update = $param_service->update_param_config($final_value);
                        }
                    }
                }
                break;
            case ParamConfig::VALUE_TYPE_PUBLIC_OBJ_RADIO:
                //公开对象参数设置
                $parent_value = $param_array['value'];
                $child_config_sets = [];
                if($organzationTypeName == 'platform'){
                    //如果是平台，则需要遍历其他组织的本设置值
                    $child_config_sets = ParamConfigOrganization::NotOrganizationType(OrganizationService::ORGANIZATION_TYPE_PLATFORM)
                        ->OfConfigId($config_set->id)
                        ->get();
                }else if($organzationTypeName == OrganizationService::ORGANIZATION_TYPE_BRAND){
                    //如果是品牌商，则需要遍历旗下的销售商的本设置值
                    $child_sellers_ids = OrganizationDealer::where('p_brand_id',$organization->id)->get()->pluck('id');
                    $child_config_sets = ParamConfigOrganization::NotOrganizationType(OrganizationService::ORGANIZATION_TYPE_PLATFORM)
                        ->OfConfigId($config_set->id)
                        ->InOrganizationId($child_sellers_ids)
                        ->get();
                }
                foreach($child_config_sets as $set){
                    $format = unserialize($set->value);
                    $child_value = $format['value'];
                    if($child_value>$parent_value){
                        //更新子参数值
                        $param_service = new ParamConfigService($param_name,$set->organization_id);
                        $update = $param_service->update_param_config($final_value);
                    }
                }
                break;
            default:break;
        }

        if(!$update){
            $this->respFail($param_service->result['msg']);
        }

        $this->respData([],'修改成功！');

    }

    //添加多项选项
    public function add_multiple_option(Request $request)
    {
        $option = $request->input('option','');
        $type = $request->input('type','');

        if(!$option){$this->respFail('参数缺失');}

        switch ($type){
            case 'education':
                /*$is_exist = $this->educationRepository->exist_name($option);

                if($is_exist){$this->respFail('名称已存在');}

                //新增选项
                $data = new Education();
                $data->name = $option;
                $data->status = Education::STATUS_ON;
                $data->save();*/
                break;
            case 'style_options':
                $is_exist = $this->styleRepository->exist_name($option);

                if($is_exist){$this->respFail('名称已存在');}

                //新增选项
                $data = new Style();
                $data->name = $option;
                $data->save();
                break;
            case 'house_types':
                $is_exist = $this->houseTypeRepository->exist_name($option);

                if($is_exist){$this->respFail('名称已存在');}

                //新增选项
                $data = new HouseType();
                $data->name = $option;
                $data->save();
                break;
            case 'space_types':
                $is_exist = $this->spaceTypeRepository->exist_name($option);

                if($is_exist){$this->respFail('名称已存在');}

                //新增选项
                $data = new SpaceType();
                $data->name = $option;
                $data->save();
                break;
            case 'space_options':
                $is_exist = $this->spaceRepository->exist_name($option);

                if($is_exist){$this->respFail('名称已存在');}

                //新增选项
                $data = new Space();
                $data->name = $option;
                $data->save();
                break;
            case 'member_type':
                $is_exist = $this->memberTypeRepository->exist_name($option);

                if($is_exist){$this->respFail('名称已存在');}

                //新增选项
                $data = new DesignerAccountType();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_spec_options':
                $is_exist = CeramicSpec::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicSpec();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_color_options':
                $is_exist = CeramicColor::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicColor();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_apply_category_options':
                $is_exist = CeramicApplyCategory::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicApplyCategory();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_technology_category_options':
                $is_exist = CeramicTechnologyCategory::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicTechnologyCategory();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_surface_feature_options':
                $is_exist = CeramicSurfaceFeature::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicSurfaceFeature();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_life_phase_options':
                $is_exist = CeramicLifePhase::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicLifePhase();
                $data->name = $option;
                $data->save();
                break;
            case 'ceramic_product_status_options':
                $is_exist = CeramicProductStatus::where('name',$option)->first();
                if($is_exist){$this->respFail('名称已存在');}
                //新增选项
                $data = new CeramicProductStatus();
                $data->name = $option;
                $data->save();
                break;
            default:
                $this->respFail('权限不足');
                break;
        }

        $this->respData(['id'=>$data->id]);


    }

    //修改多项选项
    public function modify_multiple_option(Request $request)
    {
        $id = $request->input('id',0);
        $type = $request->input('type',0);
        $new_value = $request->input('value',0);

        if(!$id){$this->respFail('参数缺失');}

        switch ($type){
            case 'education':
                /*$data = Education::find($id);
                if($data){
                   $data->value = $new_value;
                    $data->save();
                }*/
                break;
            case 'style_options':
                $data = Style::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'house_types':
                $data = HouseType::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'space_types':
                $data = SpaceType::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'space_options':
                $data = Space::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'member_type':
                $data = DesignerAccountType::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_spec_options':
                $data = CeramicSpec::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_color_options':
                $data = CeramicColor::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_apply_category_options':
                $data = CeramicApplyCategory::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_technology_category_options':
                $data = CeramicTechnologyCategory::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_surface_feature_options':
                $data = CeramicSurfaceFeature::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_life_phase_options':
                $data = CeramicLifePhase::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            case 'ceramic_product_status_options':
                $data = CeramicProductStatus::find($id);
                if($data){
                    $data->name = $new_value;
                    $data->save();
                }
                break;
            default:
                $this->respFail('权限不足');
                break;
        }


        $this->respData([]);

    }


    //删除多项选项
    public function delete_multiple_option(Request $request)
    {
        $id = $request->input('id',0);
        $type = $request->input('type',0);

        if(!$id){$this->respFail('参数缺失');}

        switch ($type){
            case 'education':
                //删除选项
                /*$data = Education::find($id);
                if($data){
                    $data->status = Education::STATUS_OFF;
                    $data->save();
                }*/
                break;
            case 'style_options':
                //删除选项
                $data = Style::find($id);
                if($data){
                    $data->delete();
                }
                break;
            case 'house_types':
                //删除选项
                $data = HouseType::find($id);
                if($data){
                    $data->delete();
                }
                break;
            case 'space_types':
                //删除选项
                $data = SpaceType::find($id);
                if($data){
                    $data->delete();
                }
                break;
            case 'space_options':
                //删除选项
                $data = Space::find($id);
                if($data){
                    $data->delete();
                }
                break;
            case 'member_type':
                //删除选项
                $data = DesignerAccountType::find($id);
                if($data){
                    $data->delete();
                }
                break;
            case 'product_category':
                /*//删除选项
                $data = OptionBusinessCategoryOrganization::find($id);
                if($data){
                    $data->status = OptionBusinessCategoryOrganization::STATUS_OFF;
                    $data->save();
                }*/
                break;
            default:
                $this->respFail('权限不足');
                break;
        }


        $this->respData([]);

    }



}
