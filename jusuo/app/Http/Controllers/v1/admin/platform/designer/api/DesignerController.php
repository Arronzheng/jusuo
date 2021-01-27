<?php

namespace App\Http\Controllers\v1\admin\platform\designer\api;

use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DesignerController extends ApiController
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
        $login_name = $request->input('ln',null);
        $realname = $request->input('rn',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        //查询设计师列表
        $entry = Designer::join('designer_details as detail', 'detail.designer_id','=','designers.id')
            ->OrganizationType(Designer::ORGANIZATION_TYPE_NONE)
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
            $v->changeStatusApiUrl = url('admin/platform/designer/api/account/'.$v->id.'/status');
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?$v->self_designer_type:0);
            $self_organization_text = DesignerDetail::getSelfOrganization($v->id);
            $v->self_organization = $self_organization_text;
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //启用禁用
    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        //修改设计师
        $designer = Designer::query()
            ->with(['detail'=>function($query){
                $query->where('approve_realname','=',DesignerDetail::APPROVE_REALNAME_YES);
            }])
            ->OrganizationType(Designer::ORGANIZATION_TYPE_NONE)
            ->find($id);
        if (!$designer){
            $this->respFail('权限不足');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($designer->status==Designer::STATUS_OFF){
                $designer->status = Designer::STATUS_ON;
            }else{
                $designer->status = Designer::STATUS_OFF;
            }

            $designer->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }
}
