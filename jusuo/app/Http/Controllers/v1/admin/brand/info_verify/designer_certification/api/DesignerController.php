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
use Carbon\Carbon;
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
        $brand_id = $user->brand->id;
        $input = $request->all();
        $designer_type = $request->input('dsntype',null);
        $keyword = $request->input('keyword',null);
        $province_id = $request->input('province_id',null);
        $city_id = $request->input('city_id',null);
        $date_start = $request->input('date_start',null);
        $date_end = $request->input('date_end',null);


        //查询品牌设计师资料审核提交列表
        $entry = LogDesignerCertification::query()
            ->whereHas('target_designer',function($query)use($brand_id,$designer_type,$input){

                //查询账号/真实姓名
                if(isset($input['keyword']) && $input['keyword']!=null){
                    $query->where(function($keywordQuery)use($input){
                        $keywordQuery ->where('designer_account','like','%'.$input['keyword'].'%');
                        $keywordQuery ->orWhereHas('detail',function($designerDetail)use($input){
                            $designerDetail->where('realname','like','%'.$input['keyword'].'%');
                        });
                    });
                }

                //筛选省份
                if(isset($input['province_id']) && $input['province_id']!=null){
                    $query->where(function($areaQuery)use($input){
                        $areaQuery ->whereHas('detail',function($designerDetail)use($input){
                            $designerDetail->where('area_belong_province',$input['province_id']);
                        });
                    });
                }

                //筛选城市
                if(isset($input['city_id']) && $input['city_id']!=null){
                    $query->where(function($areaQuery)use($input){
                        $areaQuery ->whereHas('detail',function($designerDetail)use($input){
                            $designerDetail->where('area_belong_city',$input['city_id']);
                        });
                    });
                }

                //筛选注册时间
                if(isset($input['date_start']) && isset($input['date_end'])){
                    $query->where(function($dateQuery)use($input){
                        $dateQuery ->whereBetween('designers.created_at',array($input['date_start'].' 00:00:00', $input['date_end'].' 23:59:59'));
                    });
                }

                $query->where(function($query1) use($brand_id,$designer_type){
                    if($designer_type!==null){
                        if($designer_type==1){
                            $query1->where(function($brand_designer)use($brand_id){
                                $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                                $brand_designer->where('organization_id',$brand_id);
                            });                    }
                        if($designer_type==2){
                            $query1->where(function($seller_designer)use($brand_id){
                                $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                                $seller_designer->whereHas('seller',function($organization)use($brand_id){
                                    $organization->where('organization_dealers.p_brand_id',$brand_id);
                                });
                            });
                        }
                    }else{
                        //筛选品牌或销售商设计师
                        $query1->where(function($brand_designer)use($brand_id){
                            $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);
                            $brand_designer->where('organization_id',$brand_id);
                        });
                        $query1->orWhere(function($seller_designer)use($brand_id){
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            $seller_designer->whereHas('seller',function($organization)use($brand_id){
                                $organization->where('organization_dealers.p_brand_id',$brand_id);
                            });
                        });
                    }
                });
            })
            ->join('designers','designers.id','=','log_designer_certifications.target_designer_id')
            ->join('designer_details as detail', 'detail.designer_id','=','designers.id')
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
            'designers.organization_type',
            'detail.gender',
            'detail.area_belong_district',
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
            $v->designer_type = '品牌设计师';
            $v->org_type = 'brand';
            if($v->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                $v->org_type = 'seller';
                $v->designer_type = '销售商设计师';

            }
            $v->genderText = DesignerDetail::genderGroup($v->gender);
            $v->local = '';
            if ($v->area_belong_district){
                $district = Area::where('id',$v->area_belong_district)->first();
                if ($district){
                    $city =  Area::where('id',$district->pid)->first();
                    if ($city){
                        $province =  Area::where('id',$city->pid)->first();
                        if ($province){
                            $v->local = $province->shortname.'/'.$city->shortname;
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

}
