<?php

namespace App\Http\Controllers\v1\admin\platform\info_statistics\product\ceramic\api;

use App\Exports\ProductExport;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Models\Album;
use App\Models\AlbumProductCeramic;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\CeramicSpec;
use App\Models\LogAlbumTop;
use App\Models\LogProductCeramic;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends ApiController
{
    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //表格异步获取数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $product_category_id = $request->input('pc',null);
        $brand_id = $request->input('bi',null);
        $sale_province_id = $request->input('sp',null);
        $sale_city_id = $request->input('sc',null);
        $sale_district_id = $request->input('sd',null);


        $keyword = $request->input('pn',null);
        $apply_category_id = $request->input('ac',null);
        $technology_category_id = $request->input('tc',null);
        $series_id = $request->input('srs',null);
        $color_id = $request->input('clr',null);
        $spec_id = $request->input('spec',null);
        $product_status = $request->input('status',null);
        $visible_status = $request->input('vstatus',null);
        $product_structure_id = $request->input('psid',null);

        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = ProductCeramic::query();

        $entry->select(['id','type','parent_id','name','brand_id','code','sys_code',
            'series_id','spec_id','count_visit','count_fav','visible','point_focus',
            'created_at','status','top_status_platform'
        ]);

        //筛选在售地区
        if($sale_province_id!==null || $sale_city_id!==null || $sale_district_id!==null){
            $entry->whereHas('sale_areas',function($query)use($sale_province_id,$sale_city_id,$sale_district_id){
                if($sale_province_id!==null){
                    $query->where('province_id',$sale_province_id);
                }

                if($sale_city_id!==null){
                    $query->where('city_id',$sale_city_id);
                }

                if($sale_district_id!==null){
                    $query->where('district_id',$sale_district_id);
                }
            });
        }

        //筛选经营产品类别/品牌
        if($product_category_id!==null || $brand_id!==null){
            $entry->whereHas('brand',function($query)use($product_category_id,$brand_id){
                if($product_category_id!==null){
                    $query->where('organization_brands.product_category',$product_category_id);
                }

                if($brand_id!==null){
                    $query->where('organization_brands.id',$brand_id);
                }
            });
        }

        //应用类别
        if($product_structure_id!==null){
            $entry->whereHas('authorizations',function($query)use($product_structure_id){
                $query->whereHas('structures',function($query1)use($product_structure_id){
                    $query1->where('product_ceramic_structures.id',$product_structure_id);
                });
            });
        }

        //应用类别
        if($apply_category_id!==null){
            $entry->whereHas('apply_categories',function($query)use($apply_category_id){
                $query->where('ceramic_apply_categories.id',$apply_category_id);
            });
        }

        //工艺类别
        if($technology_category_id!==null){
            $entry->whereHas('technology_categories',function($query)use($technology_category_id){
                $query->where('ceramic_technology_categories.id',$technology_category_id);
            });
        }

        //色系
        if($color_id!==null){
            $entry->whereHas('colors',function($query)use($color_id){
                $query->where('ceramic_colors.id',$color_id);
            });
        }

        //系列
        if($series_id!==null){
            $entry->where('series_id',$series_id);
        }

        //规格
        if($spec_id!==null){
            $entry->where('spec_id',$spec_id);
        }

        //可用状态
        if($product_status!==null){
            $entry->where('status',$product_status);
        }

        //可见状态
        if($visible_status!==null){
            $entry->where('visible',$visible_status);
        }

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('code','like','%'.$keyword.'%');
            });
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderBy($sort,$order);
        }

        $entry->orderBy('product_ceramics.id','desc');
        $entry->orderBy('product_ceramics.id');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $v->brand_name = '';
            if($v->brand){
                $v->brand_name = $v->brand->name;
            }
            $v->spec = '';
            $spec = CeramicSpec::find($v->spec_id);
            if($spec){
                $v->spec = $spec->name;
            }
            $v->series = '';
            $series = CeramicSeries::find($v->series_id);
            if($series){
                $v->series = $series->name;
            }
            //应用类别
            $v->apply_categories_text = '';
            $apply_categories = $v->apply_categories()->get()->pluck('name')->toArray();
            if(is_array($apply_categories) && count($apply_categories)>0){
                $v->apply_categories_text = implode(',',$apply_categories);
            }
            //工艺类别
            $v->technology_categories_text = '';
            $technology_categories = $v->technology_categories()->get()->pluck('name')->toArray();
            if(is_array($technology_categories) && count($technology_categories)>0){
                $v->technology_categories_text = implode(',',$technology_categories);
            }
            //表面特征
            $v->surface_features_text = '';
            $surface_features = $v->surface_features()->get()->pluck('name')->toArray();
            if(is_array($surface_features) && count($surface_features)>0){
                $v->surface_features_text = implode(',',$surface_features);
            }
            //色系
            $v->colors_text = '';
            $colors = $v->colors()->get()->pluck('name')->toArray();
            if(is_array($colors) && count($colors)>0){
                $v->colors_text = implode(',',$colors);
            }
            //可应用空间风格
            $v->styles_text = '';
            $styles = $v->styles()->get()->pluck('name')->toArray();
            if(is_array($styles) && count($styles)>0){
                $v->styles_text = implode(',',$styles);
            }

            $v->changeStatusApiUrl = url('admin/platform/info_statistics/product/ceramic/api/'.$v->id.'/top_status');
            $v->status_text = ProductCeramic::statusGroup(isset($v->status)?$v->status:'');
            $v->visible_text = ProductCeramic::visibleGroup(isset($v->visible)?$v->visible:'');
            $v->can_switch = $v->status==ProductCeramic::STATUS_PASS?1:0;
            $v->type_text = ProductCeramic::typeGroup($v->type);
            if($v->type==ProductCeramic::TYPE_ACCESSORY){
                $parent_product = ProductCeramic::find($v->parent_id);
                if($parent_product){
                    $v->type_text.= "(父产品:".$parent_product->name.")";
                }
            }
            $v->album_counts = $v->albums()->count();

            return $v;
        });

        /*if($export!=null){
            return $this->export($datas);
        }*/

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //修改置顶状态
    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $data = ProductCeramic::query()
            ->find($id);

        if(!$data){
            $this->respFail('数据不存在');
        }

        if($data->status != ProductCeramic::STATUS_PASS){
            $this->respFail('产品未通过，无法操作');
        }


        DB::beginTransaction();

        try{

            //更新状态
            if($data->top_status_platform==ProductCeramic::TOP_PLATFORM_OFF){
                $data->top_status_platform = ProductCeramic::TOP_PLATFORM_ON;
            }else{
                $data->top_status_platform = ProductCeramic::TOP_PLATFORM_OFF;
            }

            $result = $data->save();




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