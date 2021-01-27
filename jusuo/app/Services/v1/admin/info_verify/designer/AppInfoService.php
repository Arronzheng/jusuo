<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24
 * Time: 12:36
 */

namespace App\Http\Services\v1\admin\info_verify\designer;



use App\Models\CertificationDesigner;
use App\Models\LogDesignerCertification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerDetail;



/**
 * Class AuthService
 * @package App\Http\Services\v1\admin
 */
class AppInfoService
{

    public function approve_app_info($organization_id,$organization_type,$administrator_id,$log,$verify_content)
    {

        $result = [
            'status' => 1,
            'msg' => 'success',
        ];

        try{

            DB::beginTransaction();

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

            //写入认证记录表
            $certification_designers = new CertificationDesigner();
            $certification_designers->designer_id = $member->id;
            $certification_designers->legal_person_name = isset($verify_content['legal_person_name'])?$verify_content['legal_person_name']:'';
            $certification_designers->code_idcard = isset($verify_content['code_idcard'])?$verify_content['code_idcard']:'';
            $certification_designers->expired_at_idcard = isset($verify_content['expired_at_idcard'])?$verify_content['expired_at_idcard']:'';
            $certification_designers->url_idcard_front = isset($verify_content['url_idcard_front'])?$verify_content['url_idcard_front']:'';
            $certification_designers->url_idcard_back = isset($verify_content['url_idcard_back'])?$verify_content['url_idcard_back']:'';
            $certification_designers->save();

            //更新设计师详情表中的是否已实名认证
            $member_detail->realname = $verify_content['legal_person_name'];
            $member_detail->approve_realname = DesignerDetail::APPROVE_REALNAME_YES;
            $member_detail->code_idcard = isset($verify_content['code_idcard'])?$verify_content['code_idcard']:'';
            //$member_detail->expired_at_idcard = isset($verify_content['expired_at_idcard'])?$verify_content['expired_at_idcard']:'';
            $member_detail->url_idcard_front = isset($verify_content['url_idcard_front'])?$verify_content['url_idcard_front']:'';
            $member_detail->url_idcard_back = isset($verify_content['url_idcard_back'])?$verify_content['url_idcard_back']:'';
            $member_detail->approve_time = Carbon::now();
            //dd($member_detail);
            $member_detail->save();

            //更新审核记录信息
            $model = LogDesignerCertification::find($log->id);
            $model->is_approved = LogDesignerCertification::IS_APROVE_APPROVAL;
            $model->remark = '';
            $model->approve_administrator_id =$administrator_id;
            $model->approve_time = Carbon::now();
            $model->save();

            DB::commit();

            return $result;

        }catch (\Exception $e){

            DB::rollback();

            $result['status'] = 0;
            $result['msg'] = '系统错误'.$e->getTraceAsString();
            return $result;

        }

   }


}