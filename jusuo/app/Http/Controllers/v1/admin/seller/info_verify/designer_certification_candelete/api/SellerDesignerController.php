<?php

namespace App\Http\Controllers\v1\admin\seller\info_verify\designer_certification_candelete\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\CertificationDesigner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerCertification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SellerDesignerController extends ApiController
{
    private $getNameServices;
    private $authService;
    public function __construct(GetNameServices $getNameServices,
                                AuthService $authService
    )
    {
        $this->getNameServices = $getNameServices;
        $this->authService  = $authService;
    }

    public function account_index(Request $request)
    {
        $user = $this->authService->getAuthUser();
        $input = $request->all();

        //查询品牌设计师资料审核提交列表
        $entry = LogDesignerCertification::query()
            ->join('designers','designers.id','=','log_designer_certifications.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_id',$user->dealer->id)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            //->where('self_designer_level', '>','-1')
            ->orderBy('id','DESC');

        if(isset($input['status']) && $input['status']!=-99){
            $entry->OfStatus($input['status']);
        }

        $datas = $entry->select(
            'log_designer_certifications.id',
            'designers.designer_account',
            'detail.nickname',
            'detail.realname',
            'designers.login_mobile',
            'detail.gender',
            'detail.area_belong_id',
            'detail.self_designer_type',
            'detail.self_organization',
            'designers.created_at',
            'designers.status',
            'detail.approve_time',
            'detail.approve_realname',
            'detail.self_designer_level',
            'detail.code_idcard',
            'log_designer_certifications.is_approved'
        )->paginate(10);

        $datas->transform(function($v){
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->local = '';
            if ($v->area_belong_id){
                $district = Area::where('id',$v->area_belong_id)->first();
                if ($district){
                    $city =  Area::where('id',$district->pid)->first();
                    if ($city){
                        $province =  Area::where('id',$city->pid)->first();
                        if ($province){
                            $v->local = $province->name.'/'.$city->name;
                        }
                    }
                }

            }
            //方案数（待开发）
            $v->album_count = 0;
            $v->status_text = Designer::statusGroup($v->status);
            $v->approve_info = $v->approve_realname == DesignerDetail::APPROVE_REALNAME_YES?$v->approve_time:'未认证';
            $v->isOn = $v->status==Designer::STATUS_ON;
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?:'');
            $v->approve_text= LogDesignerCertification::getIsApproved($v->is_approved);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //审核通过资料
    public function verify_approval($id)
    {
        $user = $this->authService->getAuthUser();
        $dealer = $user->dealer;
        $log = LogDesignerCertification::query()
            ->whereHas('target_designer',function($query)use($dealer){
                $query->has('detail');
                $query->where('organization_id',$dealer->id);
                $query->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
            })
            ->find($id);

        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogDesignerCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        //审核内容
        $verify_content = unserialize($log->content);

        DB::beginTransaction();

        try{
            $member = Designer::OrganizationId($user->dealer->id)
                ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
                ->find($log->target_designer_id);
            if (!$member){$this->respFail('设计师不存在');}

            $member_detail = DesignerDetail::where('designer_id',$member->id)->first();
            if (!$member_detail){$this->respFail('设计师详情不存在');}


            //更新设计师状态
            $member->status = Designer::STATUS_ON;
            $member->save();

            //写入认证记录表
            $certification_designers = new CertificationDesigner();
            $certification_designers->designer_id = $member->id;
            $certification_designers->legal_person_name = $verify_content['legal_person_name'];
            $certification_designers->code_idcard = $verify_content['code_idcard'];
            $certification_designers->expired_at_idcard = $verify_content['expired_at_idcard'];
            $certification_designers->url_idcard_front = $verify_content['url_idcard_front'];
            $certification_designers->url_idcard_back = $verify_content['url_idcard_back'];
            $certification_designers->save();


            //设计师类型
            if (isset($verify_content['designer_type'])){
                $member_detail->self_designer_type = $verify_content['designer_type'];
            }

            //昵称
            if (isset($verify_content['nickname'])){
                $member_detail->nickname = $verify_content['nickname'];
            }

            //性别
            if (isset($verify_content['gender'])){
                $member_detail->gender = $verify_content['gender'];
            }

            //所属地区
            if (isset($verify_content['district_id'])){
                $member_detail->area_belong_id = $verify_content['district_id'];
            }

            //更新设计师详情
            $member_detail->approve_time = Carbon::now();
            $member_detail->approve_realname = DesignerDetail::APPROVE_REALNAME_YES;
            $member_detail->save();

            //更新审核记录信息
            $model = LogDesignerCertification::find($id);
            $model->is_approved = LogDesignerCertification::IS_APROVE_APPROVAL;
            $model->remark = '';
            $model->approve_administrator_id = $user->id;
            $model->approve_time = Carbon::now();
            $model->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e->getMessage());
        }
        $this->respFail('系统错误');
    }



    //审核驳回资料
    public function verify_reject($id,Request $request)
    {
        $user = $this->authService->getAuthUser();
        $dealer = $user->dealer;
        $reason = $request->input('reason','');

        if(!$reason){$this->respFail('请填写驳回理由！');}

        $log = LogDesignerCertification::query()
            ->whereHas('target_designer',function($query)use($dealer){
                $query->has('detail');
                $query->where('organization_id',$dealer->id);
                $query->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
            })
            ->find($id);

        if(!$log){
            $this->respFail('权限不足！');
        }

        //判断状态
        if($log->is_approved != LogDesignerCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        //审核内容

        DB::beginTransaction();

        try{

            //审核驳回
            $model = LogDesignerCertification::find($id);

            //更新审核记录信息
            $model->is_approved = LogDesignerCertification::IS_APROVE_REJECT;
            $model->remark = $reason;
            $model->approve_administrator_id = $user->id;
            $model->save();

            DB::commit();

//            //发送手机短信
//            if ($log->approve_type == LogOrganizationDetail::APPROVE_TYPE_ORGANIZATION_REGISTER){
//                $msgService = new GetVerifiCodeService();
//                $msg_content = '您的注册申请被驳回，请重新提交申请，谢谢。';
//                $msgService->sendMobile($verify_content['login_telephone'],$msg_content);
//            }

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！');
        }

    }

}
