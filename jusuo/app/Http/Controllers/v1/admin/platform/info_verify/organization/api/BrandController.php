<?php

namespace App\Http\Controllers\v1\admin\platform\info_verify\organization\api;

use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AdministratorBrand;

use App\Models\Area;
use App\Models\CertificationBrand;
use App\Models\DetailBrand;
use App\Models\LogBrandCertification;

use App\Models\MsgSystemBrand;
use App\Models\OrganizationBrand;
use App\Models\RoleBrand;
use App\Services\v1\admin\MsgSystemBrandService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class BrandController extends ApiController
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
        $input = $request->all();

        $entry = LogBrandCertification::query()
            ->leftJoin('organization_brands as org', 'org.id','=', 'log_brand_certifications.target_brand_id')
            ->leftJoin('detail_brands as detail', 'org.id','=', 'detail.brand_id')
            ->leftJoin('administrator_brands as ad', 'ad.brand_id','=', 'org.id')
            ->orderBy('log_brand_certifications.created_at','desc')
            ->select([
                'log_brand_certifications.*',
                'org.id as brand_id',
                'ad.login_account as login_account',
            ]);


        if(isset($input['is_approved']) && $input['is_approved']!=-99){
            $entry->OfIsApproved($input['is_approved']);
        }
        if(isset($input['keyword']) && $input['keyword']!=-99){
            $entry = $entry->where('org.name','like','%'.$input['keyword'].'%');
        }
        if(isset($input['date_start']) && isset($input['date_end']) && $input['date_start']!=-99 && $input['date_end']!=-99){
            $entry->whereBetween('log_brand_certifications.created_at', array($input['date_start'], $input['date_end']));
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
            $content = unserialize($v->content);
            $v->name = isset($content['name'])?$content['name']:'暂无信息';
            $v->brand_name = isset($content['brand_name'])?$content['brand_name']:'暂无信息';
            $v->legal_person_name = isset($content['legal_person_name'])?$content['legal_person_name']:'暂无信息';
            $v->code_license = isset($content['code_license'])?$content['code_license']:'暂无信息';
            $v->code_idcard = isset($content['code_idcard'])?$content['code_idcard']:'暂无信息';
            $v->approve_text= LogBrandCertification::getIsApproved($v->is_approved);

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


        $log = LogBrandCertification::query()
            ->with('target_brand')
            ->find($id);

        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogBrandCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        //审核内容
        $verify_content = unserialize($log->content);

        $this->approve_brand_certification($log, $verify_content);
        
        $this->respData([]);

    }

    public function approve_brand_certification($log,$verify_content)
    {
        $loginAdmin = $this->authService->getAuthUser();


        DB::beginTransaction();

        try{

            //审核通过
            $organization = OrganizationBrand::query()
                ->find($log->target_brand_id);
            if (!$organization){$this->respFail('系统错误');}
            $detail = $organization->detail;
            if (!$detail){$this->respFail('品牌详情不存在');}
            $certification_brand = new CertificationBrand();
            $certification_brand->brand_id = $organization->id;
            if (!$certification_brand){$this->respFail('系统错误');}
            //公司名称
            if(isset($verify_content['name'])){
                $organization->name = $verify_content['name'];
            }
            //品牌名称
            if(isset($verify_content['brand_name'])){
                $organization->brand_name = $verify_content['brand_name'];
            }
            //统一社会信用代码
            if(isset($verify_content['code_license'])){
                $certification_brand->code_license = $verify_content['code_license'];
            }
            //法人姓名
            if(isset($verify_content['legal_person_name'])){
                $certification_brand->legal_person_name = $verify_content['legal_person_name'];
            }
            //法人身份证号
            if(isset($verify_content['code_idcard'])){
                $certification_brand->code_idcard = $verify_content['code_idcard'];
            }
            //法人身份证到期日期
            if(isset($verify_content['expired_at_idcard'])){
                $certification_brand->expired_at_idcard = $verify_content['expired_at_idcard'];
            }
            //法人身份证背面
            if(isset($verify_content['url_idcard_back'])){
                $certification_brand->url_idcard_back = $verify_content['url_idcard_back'];
            }
            //法人身份证正面
            if(isset($verify_content['url_idcard_front'])){
                $certification_brand->url_idcard_front = $verify_content['url_idcard_front'];
            }
            //营业执照
            if(isset($verify_content['url_license'])){
                $certification_brand->url_license = $verify_content['url_license'];
            }

            $certification_brand->save();

            $organization->status = OrganizationBrand::STATUS_ON;
            $organization->save();

            $detail->save();


            //更新审核记录信息
            $log->is_approved = LogBrandCertification::IS_APROVE_APPROVAL;
            $log->remark = '';
            $log->approve_administrator_id = $loginAdmin->id;
            $log->save();

            //赋予该品牌管理员正式超级管理员权限
            $super_admin = AdministratorBrand::query()
                ->where('brand_id',$organization->id)
                ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)
                ->first();
            if(!$super_admin){
                DB::rollback();
                $this->respFail('超级管理员信息不存在');
            }
            $role = RoleBrand::OfName('brand.super_admin')->first();
            if(!$role){
                DB::rollback();
                $this->respFail('更新超级管理员权限失败');
            }
            $super_admin->syncRoles([$role]);

            //写入品牌账号通知
            $msg = new MsgSystemBrandService();
            $msg->setBrandId($organization->id);
            $msg->setContent('您的资料审核已通过');
            $msg->setType(MsgSystemBrand::TYPE_CERTIFICATION);
            $result1= $msg->add_msg();

            if(!$result1){
                DB::rollback();
                $this->respFail('品牌账号通知失败');
            }



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

        $reason = $request->input('reason','');

        if(!$reason){$this->respFail('请填写驳回理由！');}

        $log = LogBrandCertification::query()
            ->with('target_brand')
            ->find($id);

        if(!$log){
            $this->respFail('权限不足！');
        }

        //判断状态
        if($log->is_approved != LogBrandCertification::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        DB::beginTransaction();

        try{

            //审核驳回
            $brand = OrganizationBrand::find($log->target_brand_id);
            if(!$brand){
                $this->respFail('品牌不存在！');
            }


            //更新审核记录信息
            $log->is_approved = LogBrandCertification::IS_APROVE_REJECT;
            $log->remark = $reason;
            $log->save();

            //写入品牌账号通知
            $msg = new MsgSystemBrandService();
            $msg->setBrandId($brand->id);
            $msg->setContent('您的资料审核已被驳回，请重新提交审核。驳回原因：'.$reason);
            $msg->setType(MsgSystemBrand::TYPE_CERTIFICATION);
            $result1= $msg->add_msg();

            if(!$result1){
                DB::rollback();
                $this->respFail('品牌账号通知失败');
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
