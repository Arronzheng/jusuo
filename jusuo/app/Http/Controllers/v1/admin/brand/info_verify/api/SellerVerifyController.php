<?php

namespace App\Http\Controllers\v1\admin\brand\info_verify\api;

use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\PrivilegeSellerService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorDealer;

use App\Models\Area;
use App\Models\CertificationDealer;
use App\Models\DetailDealer;
use App\Models\LogBrandCertification;
use App\Models\LogDealerCertification;

use App\Models\LogDesignerCertification;
use App\Models\MsgAccountDealer;
use App\Models\MsgSystemDealer;
use App\Models\OrganizationDealer;
use App\Models\RoleBrand;
use App\Models\RoleDealer;
use App\Services\v1\admin\MsgAccountSellerService;
use App\Services\v1\admin\MsgSystemSellerService;
use App\Services\v1\site\DealerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SellerVerifyController extends ApiController
{
    private $globalService;
    private $authorizationService;
    private $authService;

    public function __construct(GlobalService $globalService,
                                AuthService $authService
    ){
        $this->globalService = $globalService;
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand_id = $loginAdmin->brand->id;
        $input = $request->all();

        $entry = LogDealerCertification::query()
            ->leftJoin('organization_dealers as org', 'org.id','=', 'log_dealer_certifications.target_dealer_id')
            ->leftJoin('detail_dealers as detail', 'org.id','=', 'detail.dealer_id')
            ->leftJoin('administrator_dealers as ad', 'ad.dealer_id','=', 'org.id')
            ->where('org.p_brand_id', $brand_id)
            ->orderBy('log_dealer_certifications.created_at','desc')
            ->select([
                'log_dealer_certifications.*','log_dealer_certifications.target_dealer_id',
                'ad.id as admin_id','ad.login_username','ad.login_account','ad.login_mobile','org.name as dealer_name','org.id as dealer_id',
                'detail.area_belong_id','detail.area_serving_id','org.contact_name','org.contact_telephone','quota_designer','quota_designer_used',
                'org.created_at','org.expired_at','ad.status as account_status','org.level',
                'org.status as dealer_status'
            ]);


        if(isset($input['is_approved']) && $input['is_approved']!=-99){
            $entry->OfIsApproved($input['is_approved']);
        }
        if(isset($input['keyword']) && $input['keyword']!=-99){
            $entry = $entry->where('org.name','like','%'.$input['keyword'].'%');
        }
        if(isset($input['date_start']) && isset($input['date_end']) && $input['date_start']!=-99 && $input['date_end']!=-99){
            $entry->whereBetween('log_dealer_certifications.created_at', array($input['date_start'], $input['date_end']));
        }
        if(isset($input['area']) && $input['area']!=-99){
            $city = Area::OfLevel('2')->where('pid',$input['area'])->get(['id']);
            if ($city){
                $district = Area::OfLevel('3')->whereIn('pid',$city)->get(['id']);
                if ($district){
                    $entry->whereIn('org.area_belong_id',$district);
                }
            }

        }

        $datas = $entry->paginate(10);

        $datas->transform(function($v){
            //法人代表
            $legal_person_name = '暂无信息';
            $certificationDealer = CertificationDealer::where('dealer_id',$v->target_dealer_id)->first();
            if($certificationDealer){
                $legal_person_name = $certificationDealer->legal_person_name;
            }
            $v->legal_person_name = $legal_person_name;
            $v->isOn = $v->account_status==AdministratorDealer::STATUS_ON;
            //已授权/可授权设计师账号数
            $designerCount = $v->quota_designer_used ."/" .$v->quota_designer;
            $v->designer_count = $designerCount;
            $v->account_status_text = AdministratorDealer::statusGroup($v->account_status);
            $v->dealer_status_text = OrganizationDealer::statusGroup($v->dealer_status);
            $v->approve_text= LogDesignerCertification::getIsApproved($v->is_approved);
            if ($v->area_serving_id){
                $district = Area::where('id',$v->area_serving_id)->first();
                if ($district){
                    $city =  Area::where('id',$district->pid)->first();
                    if ($city){
                        $province =  Area::where('id',$city->pid)->first();

                        if ($province){
                            $v->area_serving = $province->name.'/'.$city->name.'/'.$district->name;
                        }
                    }
                }
            }
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //审核通过
    public function verify_approval($id)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;

        $log = LogDealerCertification::query()
            ->has('target_dealer')
            ->find($id);

        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogDealerCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        //审核内容
        $verify_content = unserialize($log->content);

        $this->approve_dealer_certification($log, $verify_content);
        
        $this->respData([]);

    }

    public function approve_dealer_certification($log,$verify_content)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;

        DB::beginTransaction();

        try{

            //审核通过
            $organization = OrganizationDealer::query()
                ->where('p_brand_id',$brand->id)
                ->find($log->target_dealer_id);
            if (!$organization){$this->respFail('系统错误');}
            $detail = $organization->detail;
            if (!$detail){$this->respFail('销售商详情不存在');}

            $certification_dealer = new CertificationDealer();
            $certification_dealer->dealer_id = $organization->id;
            if (!$certification_dealer){$this->respFail('系统错误');}
            //公司名称
            if(isset($verify_content['name'])){
                $organization->name = $verify_content['name'];
            }
            //品牌名称
            if(isset($verify_content['brand_name'])){
                $organization->brand_name = $verify_content['brand_name'];
            }
            //服务城市
            if(isset($verify_content['area_serving_id'])){
                $detail->area_serving_id = $verify_content['area_serving_id'];
            }
            //账号子域名
            if(isset($verify_content['dealer_domain'])){
                $detail->dealer_domain = $verify_content['dealer_domain'];
            }
            //统一社会信用代码
            if(isset($verify_content['code_license'])){
                $certification_dealer->code_license = $verify_content['code_license'];
            }
            //法人姓名
            if(isset($verify_content['legal_person_name'])){
                $certification_dealer->legal_person_name = $verify_content['legal_person_name'];
            }
            //法人身份证号
            if(isset($verify_content['code_idcard'])){
                $certification_dealer->code_idcard = $verify_content['code_idcard'];
            }
            //法人身份证到期日期
            if(isset($verify_content['expired_at_idcard'])){
                $certification_dealer->expired_at_idcard = $verify_content['expired_at_idcard'];
            }
            //法人身份证背面
            if(isset($verify_content['url_idcard_back'])){
                $certification_dealer->url_idcard_back = $verify_content['url_idcard_back'];
            }
            //法人身份证正面
            if(isset($verify_content['url_idcard_front'])){
                $certification_dealer->url_idcard_front = $verify_content['url_idcard_front'];
            }
            //营业执照
            if(isset($verify_content['url_license'])){
                $certification_dealer->url_license = $verify_content['url_license'];
            }

            $certification_dealer->save();
            $detail->save();

            $organization->status = OrganizationDealer::STATUS_ON;
            $organization->save();


            //更新审核记录信息
            $log->is_approved = LogDealerCertification::IS_APROVE_APPROVAL;
            $log->remark = '';
            $log->approve_administrator_id = $loginAdmin->id;
            $log->save();

            //赋予该销售商管理员正式超级管理员权限
            $super_admin = AdministratorDealer::query()
                ->where('dealer_id',$organization->id)
                ->where('is_super_admin',AdministratorDealer::IS_SUPER_ADMIN_YES)
                ->first();
            if(!$super_admin){
                DB::rollback();
                $this->respFail('超级管理员信息不存在');
            }
            $role = RoleDealer::OfName('seller.super_admin')->first();
            if(!$role){
                DB::rollback();
                $this->respFail('更新超级管理员权限失败');
            }
            $super_admin->syncRoles([$role]);

            //写入销售商账号通知
            $msg = new MsgSystemSellerService();
            $msg->setDealerId($organization->id);
            $msg->setContent('您的资料审核已通过。');
            $msg->setType(MsgSystemDealer::TYPE_CERTIFICATION);
            $result1= $msg->add_msg();

            if(!$result1){
                DB::rollback();
                $this->respFail('销售商账号通知失败');
            }

            /*----------根据品牌的一级权限，更新品牌旗下销售商的一级权限及其子孙权限--------*/
            PrivilegeSellerService::sync_brand_seller_privilege($brand->id);

            //累加品牌商的经销商已开通数量
            $seller_level = $organization->level;
            $quota_column = 'quota_dealer_lv'.$seller_level.'_used';
            $brand->$quota_column = $brand->$quota_column + 1;
            $brand->save();

            DealerService::addToSearch();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }
        
    }

    //审核驳回
    public function verify_reject($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;

        $reason = $request->input('reason','');

        if(!$reason){$this->respFail('请填写驳回理由！');}

        $log = LogDealerCertification::query()
            ->has('target_dealer')
            ->find($id);

        if(!$log){
            $this->respFail('权限不足！');
        }

        //判断状态
        if($log->is_approved != LogDealerCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        DB::beginTransaction();

        try{

            //审核驳回
            $seller = OrganizationDealer::find($log->target_dealer_id);
            if(!$seller){
                $this->respFail('销售商不存在！');
            }

            //更新审核记录信息
            $log->is_approved = LogDealerCertification::IS_APROVE_REJECT;
            $log->remark = $reason;
            $log->save();

            //写入销售商账号通知
            $msg = new MsgSystemSellerService();
            $msg->setDealerId($seller->id);
            $msg->setContent('您的资料审核已被驳回，请重新提交审核。驳回原因：'.$reason);
            $msg->setType(MsgSystemDealer::TYPE_CERTIFICATION);
            $result1= $msg->add_msg();

            if(!$result1){
                DB::rollback();
                $this->respFail('销售商账号通知失败');
            }

            DB::commit();

//            //发送手机短信
//            if ($log->approve_type==LogOrganizationDetail::APPROVE_TYPE_ORGANIZATION_REGISTER){
//                $msgService = new GetVerifiCodeService();
//                $msg_content = '您的注册申请被驳回，请重新提交申请，谢谢。';
//                $msgService->sendMobile($verify_content['login_telephone'],$msg_content);
//            }

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！'.$e->getMessage());
        }

    }


}
