<?php

namespace App\Http\Controllers\v1\admin\brand\info_statistics\activity\designer\api;

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

    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        $brand_id = $brand->id;

        $area_belong_province = $request->input('abp',null);
        $area_belong_city = $request->input('abc',null);
        $area_belong_district = $request->input('abd',null);
        $area_serving_province = $request->input('asp',null);
        $area_serving_city = $request->input('asc',null);
        $area_serving_district = $request->input('asd',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        //查询设计师列表
        $entry = Designer::query()
            //->join('statistic_designers as stat','stat.designer_id','=','designers.id')
            ->join('statistic_designers as stat','stat.id','=',DB::raw("
                 (select id from statistic_designers t where t.designer_id = designers.id
                order by t.id desc limit 1)
            "))
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
            //->where('self_designer_level', '>','-1')
            ;

        $entry->where(function($query1) use($brand_id){

            //筛选品牌或销售商设计师
            $query1->where(function($brand_designer)use($brand_id){
                $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                $brand_designer->whereHas('brand',function($organization)use($brand_id){
                    $organization->where('id',$brand_id);
                });
            });

            $query1->orWhere(function($seller_designer)use($brand_id){
                $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                $seller_designer->whereHas('seller',function($organization)use($brand_id){
                    $organization->where('organization_dealers.p_brand_id',$brand_id);
                });

            });

        });

        //筛选所在省份
        if($area_belong_province!==null){
            $entry = $entry->where('detail.area_belong_province',$area_belong_province);
        }

        //筛选所在城市
        if($area_belong_city!==null){
            $entry = $entry->where('detail.area_belong_city',$area_belong_city);
        }

        //筛选所在地区
        if($area_belong_district!==null){
            $entry = $entry->where('detail.area_belong_district',$area_belong_district);
        }

        //筛选服务省份
        if($area_serving_province!==null){
            $entry = $entry->where('detail.area_serving_province',$area_serving_province);
        }

        //筛选服务城市
        if($area_serving_city!==null){
            $entry = $entry->where('detail.area_serving_city',$area_serving_city);
        }

        //筛选服务地区
        if($area_serving_district!==null){
            $entry = $entry->where('detail.area_serving_district',$area_serving_district);
        }

        if($sort && $order){
            $entry->orderBy($sort,$order);
        }

        $entry->orderBy('designers.id','desc');
        $entry->groupBy('designers.id');

        $datas = $entry->select(
            'designers.id',
            'designers.designer_account',
            'designers.created_at',
            'designers.status',
            'designers.login_mobile',
            'detail.nickname',
            'detail.realname',
            'detail.gender',
            'detail.area_belong_province',
            'detail.area_belong_city',
            'detail.area_belong_district',
            'detail.area_serving_province',
            'detail.area_serving_city',
            'detail.area_serving_district',
            'detail.self_designer_type',
            'detail.self_organization',
            'stat.count_upload_album',
            'stat.count_fav_album',
            'stat.count_praise_album',
            'stat.count_download_album',
            'stat.count_copy_album',
            'stat.count_fav_designer'
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

            $v->area_serving = '';
            $province =  Area::where('id',$v->area_serving_province)->first();
            $city =  Area::where('id',$v->area_serving_city)->first();
            $district =  Area::where('id',$v->area_serving_district)->first();
            if($province){$v->area_serving.= $province->name;}
            if($city){$v->area_serving.= $city->name;}
            if($district){$v->area_serving.= $district->name;}

            $v->status_text = Designer::statusGroup($v->status);
            $v->designer_type_text= DesignerDetail::designerTypeGroup($v->self_designer_type?$v->self_designer_type:0);
            $self_organization_text = DesignerDetail::getSelfOrganization($v->id);
            $v->self_organization = $self_organization_text;
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

}
