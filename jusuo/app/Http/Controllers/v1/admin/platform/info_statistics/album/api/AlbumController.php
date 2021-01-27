<?php

namespace App\Http\Controllers\v1\admin\platform\info_statistics\album\api;

use App\Http\Controllers\Controller;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\PrivilegeSellerService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\AdministratorRoleBrand;
use App\Models\Album;
use App\Models\Area;
use App\Models\CertificationBrand;
use App\Models\Designer;
use App\Models\DetailBrand;
use App\Models\LogAlbumTop;
use App\Models\LogBrandCertification;
use App\Models\MsgAccountBrand;
use App\Models\MsgSystemBrand;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\PrivilegeDealer;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\RolePrivilegeBrand;
use App\Models\SpaceType;
use App\Services\v1\admin\MsgAccountBrandMultiService;
use App\Services\v1\admin\MsgAccountBrandService;
use App\Services\v1\admin\MsgSystemBrandService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class AlbumController extends ApiController
{
    private $globalService;
    private $roleOrganizationRepository;
    private $authService;
    private $infiniteTreeService;
    private $getNameServices;

    public function __construct(
        GlobalService $globalService,
        AuthService $authService,
        InfiniteTreeService $infiniteTreeService,
        GetNameServices $getNameServices
    ){
        $this->globalService = $globalService;
        $this->authService = $authService;
        $this->infiniteTreeService = $infiniteTreeService;
        $this->getNameServices = $getNameServices;
    }


    //列表数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $brand_id = $request->input('bi',null);
        $space_type_id = $request->input('spt',null);
        $style = $request->input('stl',null);
        $house_type_id = $request->input('ht',null);
        $area_start = $request->input('area_start',null);
        $area_end = $request->input('area_end',null);
        $title = $request->input('tle',null);
        $area_belong_province = $request->input('abp',null);
        $area_belong_city = $request->input('abc',null);
        $area_belong_district = $request->input('abd',null);

        $type = $request->input('type',null);
        //$color_id = $request->input('clr',null);

        $entry = Album::query()
            ->select([
                'albums.id','albums.id as album_id','albums.code','albums.type','albums.title','albums.count_area',
                'albums.address_province_id','albums.address_city_id','albums.address_area_id',
                'albums.count_visit','albums.count_praise','albums.count_fav','albums.count_use',
                'albums.count_share','albums.point_focus','albums.created_at as upload_time',
                'albums.top_status_platform','albums.designer_id','albums.period_status'
            ])
            ->with(['designer:id','designer.detail:id,designer_id,realname'])
            ->whereHas('designer',function($query)use($brand_id){
                //所属品牌
                if($brand_id>0){
                    $query->where(function($query1) use($brand_id){

                        //筛选品牌或销售商设计师
                        $query1->where(function($brand_designer)use($brand_id){
                            $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);

                            if($brand_id>0 ){
                                $brand_designer->whereHas('brand',function($organization)use($brand_id){
                                    $organization->where('id',$brand_id);
                                });
                            }
                        });

                        $query1->orWhere(function($seller_designer)use($brand_id){
                            $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                            if($brand_id>0){
                                $seller_designer->whereHas('seller',function($organization)use($brand_id){
                                    $organization->where('organization_dealers.p_brand_id',$brand_id);
                                });
                            }

                        });

                    });
                }
            });

        //筛选所在省份
        if($area_belong_province!==null){
            $entry = $entry->where('albums.address_province_id',$area_belong_province);
        }

        //筛选所在城市
        if($area_belong_city!==null){
            $entry = $entry->where('albums.address_city_id',$area_belong_city);
        }

        //筛选所在地区
        if($area_belong_district!==null){
            $entry = $entry->where('albums.address_area_id',$area_belong_district);
        }

        //空间类别
        if($space_type_id!==null){
            $entry->whereHas('space_types',function($query)use($space_type_id){
                $query->where('space_types.id',$space_type_id);
            });
        }

        if($title!==null){
            $entry->where('title','like',"%".$title."%");
        }

        if($area_start!==null && $area_end!==null){
            $area_start = intval($area_start);
            $area_end = intval($area_end);
            $entry->whereBetween('count_area',[$area_start,$area_end]);
        }

        if($house_type_id!==null){
            $entry->whereHas('house_types',function($query)use($house_type_id){
                $query->where('house_types.id',$house_type_id);
            });
        }

        /*if($color_id!==null){
            $entry->whereHas('album_sections',function($section)use($color_id){
                $section->whereHas('product_ceramics',function($product_ceramic)use($color_id){
                    $product_ceramic->whereHas('colors',function($color)use($color_id){
                        $color->where('ceramic_colors.id',$color_id);
                    });
                });
            });
        }*/

        if($style!==null){
            $entry->whereHas('style',function($query)use($style){
                $query->where('id',$style);
            });
        }


        if($type!==null){
            if(key_exists($type,Album::typeGroup())){
                $entry->where('type',$type);
            }
        }


        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('albums.created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderBy($sort,$order);
        }

        $entry->orderBy('albums.id','desc');
        $entry->groupBy('albums.id');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $v->type_text = Album::typeGroup($v->type);
            $v->house_type_text = implode(',',$v->house_types()->get()->pluck('name')->toArray());
            $v->style_text = implode(',',$v->style()->get()->pluck('name')->toArray());
            $v->count_area_text = $v->count_area."平方米";
            //章节数
            $v->space_count = $v->space_types()->count();
            //章节风格
            $album_section_space_type_ids = $v->album_sections()->get()->pluck('space_type_id')->toArray();
            $album_section_space_types = SpaceType::whereIn('id',$album_section_space_type_ids)->get()->pluck('name')->toArray();
            $v->album_section_space_type = implode('/',$album_section_space_types);
            //关联产品数
            $v->product_count = $v->product_ceramics()->count();
            $v->comment_count = $v->comments()->count();
            $v->status_text = Album::statusGroup($v->status);
            $province = Area::where('id',$v->address_province_id)->first();
            $city =  Area::where('id',$v->address_city_id)->first();
            $district =  Area::where('id',$v->address_area_id)->first();
            $v->area_text = ($province?$province->name:'').($city?$city->name:'').($district?$district->name:'');
            $v->realname = isset($v->designer->detail->realname)?$v->designer->detail->realname:'';

            $v->can_change_status = $v->period_status==Album::PERIOD_STATUS_FINISH?1:0;
            $v->changeStatusApiUrl = url('admin/platform/info_statistics/album/api/'.$v->id.'/top_status');

            unset($v->designer);
            return $v;
        });


        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //修改置顶状态
    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $album = Album::query()
            ->find($id);

        if(!$album){
            $this->respFail('数据不存在');
        }

        if($album->period_status != Album::PERIOD_STATUS_FINISH){
            $this->respFail('方案未通过，无法操作');
        }


        DB::beginTransaction();

        try{

            $op_type = LogAlbumTop::OP_TYPE_TOP;
            //更新状态
            if($album->top_status_platform==Album::TOP_PLATFORM_OFF){
                $album->top_status_platform = Album::TOP_PLATFORM_ON;
            }else{
                $album->top_status_platform = Album::TOP_PLATFORM_OFF;
                $op_type = LogAlbumTop::OP_TYPE_CANCEL;

            }

            $result = $album->save();


            //写入方案被置顶记录
            $top_log = new LogAlbumTop();
            $top_log->album_id = $album->id;
            $top_log->op_type = $op_type;
            $top_log->organization_type = LogAlbumTop::ORGANIZATION_TYPE_PLATFORM;
            $top_log->operator_id = $loginAdmin->id;
            $top_log->save();


            if(!$result){
                DB::rollback();
                $this->respFail('数据更新错误');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //异步获取品牌列表
    public function get_brands(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = OrganizationBrand::select(['id','name','brand_name','organization_account','contact_name','contact_telephone'])
            ->orderBy('created_at','desc');

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->where('name','like',"%".$keyword."%");
                $query->orWhere('brand_name','like',"%".$keyword."%");
                $query->orWhere('short_name','like',"%".$keyword."%");
                $query->orWhere('organization_account','like',"%".$keyword."%");
                $query->orWhere('contact_name','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'data'  =>$datas->items()
        ]);
    }


}
