<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24
 * Time: 12:36
 */

namespace App\Http\Services\v1\admin\info_verify\designer;



use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerDetail;



/**
 * Class AuthService
 * @package App\Http\Services\v1\admin
 */
class BasicInfoService
{

    public function approve_basic_info($organization_id,$organization_type,$administrator_id,$log,$verify_content)
    {

        $result = [
            'status' => 1,
            'msg' => 'success',
        ];

        DB::beginTransaction();

        try{
            $member = Designer::OrganizationId($organization_id)
                ->OrganizationType($organization_type)
                ->find($log->target_designer_id);
            if (!$member){
                $result['status'] = 0;
                $result['msg'] = '设计师不存在';
                return $result;
            }
            $member_detail = DesignerDetail::where('designer_id',$member->id)->first();
            if (!$member_detail){
                $result['status'] = 0;
                $result['msg'] = '设计师详情不存在';
                return $result;
            }

            //设计师类型
            if (isset($verify_content['self_designer_type'])){
                $member_detail->self_designer_type = $verify_content['self_designer_type'];
            }

            //服务城市
            if (isset($verify_content['area_serving_district'])){
                $member_detail->area_serving_district = $verify_content['area_serving_district'];
            }
            if (isset($verify_content['area_serving_city'])){
                $member_detail->area_serving_city = $verify_content['area_serving_city'];
            }
            if (isset($verify_content['area_serving_province'])){
                $member_detail->area_serving_province = $verify_content['area_serving_province'];
            }

            //工作单位
            if (isset($verify_content['self_organization'])){
                $member_detail->self_organization = $verify_content['self_organization'];
            }

            //更新擅长风格
            $style_ids = $verify_content['style'];
            $member->styles()->sync($style_ids);

            //更新擅长空间
            $space_ids = $verify_content['space'];
            $member->spaces()->sync($space_ids);

            //服务专长
            if (isset($verify_content['self_expert'])){
                $member_detail->self_expert = $verify_content['self_expert'];
            }

            //更新设计师详情
            $member_detail->approve_realname = DesignerDetail::APPROVE_REALNAME_YES;
            $member_detail->approve_time = Carbon::now();
            $member_detail->save();

            //更新审核记录信息
            $model = LogDesignerDetail::find($log->id);
            $model->is_approved = LogDesignerDetail::IS_APROVE_APPROVAL;
            $model->remark = '';
            $model->approve_administrator_id = $administrator_id;
            $model->save();

            //更新设计师状态
            $designer = Designer::find($model->target_designer_id);
            $designer->status = Designer::STATUS_ON;
            $designer->save();

            DB::commit();

            return $result;

        }catch (\Exception $e){

            DB::rollback();

            $result['status'] = 0;
            $result['msg'] = '系统错误'.$e->getMessage();
            return $result;
        }

   }


}