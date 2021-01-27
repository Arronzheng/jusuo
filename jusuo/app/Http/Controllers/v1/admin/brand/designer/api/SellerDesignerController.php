<?php

namespace App\Http\Controllers\v1\admin\brand\designer\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\IntegralLogDesigner;
use App\Models\OrganizationDealer;
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
        $login_name = $request->input('ln',null);
        $realname = $request->input('rn',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        //查询设计师列表
        $entry = Designer::leftJoin('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->whereIn('organization_id',$sellerIds)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            //->where('self_designer_level', '>','-1')
            ;

        if($login_name!==null){
            $entry = $entry->where(function($query)use($login_name){
                $query->where('designers.login_username','like','%'.$login_name.'%');
                $query->orWhere('designers.designer_account','like','%'.$login_name.'%');
                $query->orWhere('designers.login_mobile','like','%'.$login_name.'%');
            });
        }

        if($realname!==null){
            $entry = $entry->where('detail.realname','like',"%".$realname."%");
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('designers.created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('designers.id','desc');

        $datas = $entry->select(
            'designers.id',
            'designers.designer_account',
            'detail.nickname',
            'detail.realname',
            'designers.login_mobile',
            'detail.gender',
            'detail.area_belong_province',
            'detail.area_belong_city',
            'detail.area_belong_district',
            'detail.self_designer_type',
            'detail.self_organization',
            'designers.created_at',
            'designers.status',
            'detail.approve_time',
            'detail.approve_realname',
            'detail.self_designer_level'
        )->paginate($limit);

        $datas->transform(function($v){
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->area_belong = '';
            $province =  Area::where('id',$v->area_belong_province)->first();
            $city =  Area::where('id',$v->area_belong_city)->first();
            $district =  Area::where('id',$v->area_belong_district)->first();
            if($province){$v->area_belong.= $province->name;}
            if($city){$v->area_belong.= $city->name;}
            if($district){$v->area_belong.= $district->name;}

            //方案数（待开发）
            $v->album_count = 0;
            $v->status_text = Designer::statusGroup($v->status);
            $v->approve_info = $v->approve_realname == DesignerDetail::APPROVE_REALNAME_YES?$v->approve_time:'未认证';
            $v->isOn = $v->status==Designer::STATUS_ON;
            $v->changeStatusApiUrl = url('admin/brand/brand_designer/api/account/'.$v->id.'/status');
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?:'');
            $self_organization_text = DesignerDetail::getSelfOrganization($v->id);
            $v->self_organization = $self_organization_text;
            $v->level_text = Designer::designerTitleCn($v->self_designer_level);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //修改等级
    public function modify_level(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $new_level = $input_data['level'];

        $user = $this->authService->getAuthUser();

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        $data = Designer::whereIn('organization_id',$sellerIds)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($input_data['id']);

        if(!$data){
            $this->respFail('信息不存在！');
        }

        $detail = $data->detail;
        if(!$detail){
            $this->respFail('信息不存在！');
        }

        //其他校验
        $exist = false;
        $level_group = DesignerDetail::designerLevelIdNameGroup(true);
        for($i=0;$i<count($level_group);$i++){
            if($level_group[$i]['id'] == $new_level){
                $exist = true;
            }
        }
        if(!$exist){
            $this->respFail('参数错误！');
        }

        DB::beginTransaction();

        try{
            $detail->self_designer_level = $input_data['level'];
            $result = $detail->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }

    //调整积分
    public function modify_integral(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'value' => 'required',
            'remark' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $value = intval($input_data['value']);
        if($value==0 || $value<>$input_data['value']){
            $this->respFail('请填写非零整数！');
        }

        $user = $this->authService->getAuthUser();

        $sellerIds = OrganizationDealer::where('p_brand_id',$user->brand->id)
            ->get()->pluck('id')->toArray();

        $data = Designer::whereIn('organization_id',$sellerIds)
            ->OrganizationType(Designer::ORGANIZATION_TYPE_SELLER)
            ->find($input_data['id']);

        if(!$data){
            $this->respFail('信息不存在！');
        }

        $detail = $data->detail;
        if(!$detail){
            $this->respFail('信息不存在！');
        }

        $original = $detail->point_money;
        $after = $original+$value;
        if($after<0){
            $this->respFail('调整后积分不可以为负值！');
        }

        DB::beginTransaction();

        try{
            $detail->point_money = $input_data['value'];
            $result = $detail->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            //增加后台操作记录
            $log = new IntegralLogDesigner();
            $log->designer_id = $input_data['id'];
            $log->type = $value>0?IntegralLogDesigner::TYPE_ADMIN_ADD:IntegralLogDesigner::TYPE_ADMIN_MINUS;
            $log->integral = $value;
            $log->available_integral = $after;
            $log->remark = $input_data['remark'];
            $result = $log->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }

}
