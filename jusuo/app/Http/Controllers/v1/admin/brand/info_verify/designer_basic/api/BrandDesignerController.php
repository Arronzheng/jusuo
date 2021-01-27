<?php

namespace App\Http\Controllers\v1\admin\brand\info_verify\designer_basic\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\LogDesignerDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\v1\admin\info_verify\designer\BasicInfoService;


class BrandDesignerController extends ApiController
{
    private $getNameServices;
    private $authService;
    private $basicInfoService;

    public function __construct(GetNameServices $getNameServices,
                                AuthService $authService,
                                BasicInfoService $basicInfoService

    )
    {
        $this->getNameServices = $getNameServices;
        $this->authService  = $authService;
        $this->basicInfoService  = $basicInfoService;

    }

    public function account_index(Request $request)
    {
        $user = $this->authService->getAuthUser();
        $input = $request->all();

        //查询品牌设计师资料审核提交列表
        $entry = LogDesignerDetail::query()
            ->join('designers','designers.id','=','log_designer_details.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_id',$user->brand->id)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            //->where('self_designer_level', '>','-1')
            ->orderBy('id','DESC');

        if(isset($input['status']) && $input['status']!=-99){
            $entry->OfStatus($input['status']);
        }

        $datas = $entry->select(
            'log_designer_details.id',
            'designers.designer_account',
            'detail.nickname',
            'detail.realname',
            'designers.login_mobile',
            'detail.gender',
            'detail.area_belong_district',
            'detail.self_designer_type',
            'detail.self_organization',
            'designers.created_at',
            'designers.status',
            'detail.approve_time',
            'detail.approve_realname',
            'detail.self_designer_level',
            'log_designer_details.is_approved'
        )->paginate(10);

        $datas->transform(function($v){
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->local = '';
            if ($v->area_belong_district){
                $district = Area::where('id',$v->area_belong_district)->first();
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
            $v->changeStatusApiUrl = url('admin/brand/brand_designer/api/account/'.$v->id.'/status');
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?:'');
            $v->approve_text= LogDesignerDetail::getIsApproved($v->is_approved);
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

        $log = LogDesignerDetail::query()
            ->select(['log_designer_details.*'])
            ->join('designers','designers.id','=','log_designer_details.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_id',$user->brand->id)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->find($id);

        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogDesignerDetail::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        //审核内容
        $verify_content = unserialize($log->content);

        $this->approve_designer($log,$verify_content);

        $this->respFail('系统错误');
    }

    //审核通过处理方法
    public function approve_designer($log, $verify_content)
    {
        $user = $this->authService->getAuthUser();

        $result = $this->basicInfoService->approve_basic_info($user->brand->id,Designer::ORGANIZATION_TYPE_BRAND,$user->id,$log,$verify_content);
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

        $log = LogDesignerDetail::query()
            ->join('designers','designers.id','=','log_designer_details.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->where('designers.organization_id',$user->brand->id)
            ->where('designers.organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->find($id);

        if(!$log){
            $this->respFail('权限不足！');
        }

        //判断状态
        if($log->is_approved != LogDesignerDetail::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        //审核内容

        DB::beginTransaction();

        try{

            //审核驳回
            $model = LogDesignerDetail::find($id);

            //更新审核记录信息
            $model->is_approved = LogDesignerDetail::IS_APROVE_REJECT;
            $model->remark = $reason;
            $model->approve_administrator_id = $user->id;
            $model->save();

            //更新设计师状态
            $designer = Designer::find($model->target_designer_id);
            $designer->status = Designer::STATUS_VERIFYING;
            $designer->save();

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
