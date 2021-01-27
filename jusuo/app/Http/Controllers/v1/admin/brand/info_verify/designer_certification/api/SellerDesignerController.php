<?php

namespace App\Http\Controllers\v1\admin\brand\info_verify\designer_certification\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\info_verify\designer\AppInfoService;
use App\Models\Area;
use App\Models\CertificationDesigner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerCertification;
use App\Models\OrganizationDealer;
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
    private $appInfoService;

    public function __construct(GetNameServices $getNameServices,
                                AuthService $authService,
                                AppInfoService $appInfoService
    )
    {
        $this->getNameServices = $getNameServices;
        $this->authService  = $authService;
        $this->appInfoService  = $appInfoService;

    }

    public function account_index(Request $request)
    {
        $user = $this->authService->getAuthUser();
        $input = $request->all();

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();


        //查询品牌设计师资料审核提交列表
        $entry = LogDesignerCertification::query()
            ->join('designers','designers.id','=','log_designer_certifications.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->whereIn('designers.organization_id',$sellerIds)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            //->where('self_designer_level', '>','-1')
            ->orderBy('id','DESC');

        if(isset($input['status']) && $input['status']!=-99){
            $entry->OfStatus($input['status']);
        }

        $datas = $entry->select(
            'log_designer_certifications.id',
            'designers.designer_account',
            'designers.id as designer_id',
            'detail.nickname',
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

            //获取实名信息资料
            $v->realname = '';
            $v->code_idcard = '';
            $certification = CertificationDesigner::query()
                ->where('designer_id',$v->designer_id)
                ->first();
            if($certification){
                $v->realname = $certification->legal_person_name;
                $v->code_idcard = $certification->code_idcard;
            }

            unset($v->designer_id);
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

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        $log = LogDesignerCertification::query()
            ->whereHas('target_designer',function($query)use($sellerIds){
                $query->has('detail');
                $query->whereIn('organization_id',$sellerIds);
                $query->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
            })
            ->find($id);


        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogDesignerCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        $target_designer = Designer::find($log->target_designer_id);
        $seller_id = $target_designer->organization_id;

        //审核内容
        $verify_content = unserialize($log->content);

        $result = $this->appInfoService->approve_app_info($seller_id,Designer::ORGANIZATION_TYPE_SELLER,$user->id,$log,$verify_content);
        if($result['status']==1){
            $this->respData([]);
        }else{
            $this->respFail($result['msg']);
        }
    }



    //审核驳回资料
    public function verify_reject($id,Request $request)
    {
        $user = $this->authService->getAuthUser();

        $reason = $request->input('reason','');

        if(!$reason){$this->respFail('请填写驳回理由！');}

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();


        $log = LogDesignerCertification::query()
            ->whereHas('target_designer',function($query)use($sellerIds){
                $query->has('detail');
                $query->whereIn('organization_id',$sellerIds);
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
