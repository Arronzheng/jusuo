<?php

namespace App\Http\Controllers\v1\admin\brand\album\index\hd\seller\api;

use App\Http\Services\common\LayuiTableService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\MsgAlbumDesigner;
use App\Models\OrganizationBrand;
use App\Models\SearchAlbum;
use App\Services\v1\admin\MsgAlbumDesignerService;
use App\Services\v1\site\AlbumService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlbumController extends ApiController
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
        $brand = $loginAdmin->brand;

        //$keyword = $request->input('keyword',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $type = $request->input('type',null);
        $style = $request->input('stl',null);
        $color_id = $request->input('clr',null);
        $house_type_id = $request->input('ht',null);
        $area_start = $request->input('area_start',null);
        $area_end = $request->input('area_end',null);
        $space_count = $request->input('sc',null);
        $brand_id = $request->input('bi',null);

        $title = $request->input('tle',null);
        $realname = $request->input('rn',null);
        $nickname = $request->input('nn',null);
        $product_name = $request->input('pn',null);

        $entry = Album::query()
            ->select([
                DB::raw('(select count(*) from album_space_types as abs where abs.album_id = albums.id) as space_count'),
                'albums.id','type','title','code','status','status_brand','designer_id','updated_at','count_area','visible_status','status_platform'
            ])
            /*->whereHas('designer',function($query)use($brand){
                $query->whereHas('seller',function($organization)use($brand){
                    //所属品牌
                    $organization->where('organization_dealers.p_brand_id',$brand->id);
                });
            })//品牌设计师*/
            ->where('status',Album::STATUS_PASS) //必须是已审核通过的
            ->where('status_brand',$brand->id);//必须是申请品牌展示已通过的



        if($product_name!=null){
            $entry->whereHas('album_sections',function($section)use($product_name){
                $section->whereHas('product_ceramics',function($product_ceramic)use($product_name){
                    $product_ceramic->where('name','like','%'.$product_name.'%');
                });
            });
        }

        if($nickname!==null){
            $entry->whereHas('designer',function($designer)use($nickname){
                $designer->whereHas('detail',function($detail)use($nickname){
                    $detail->where('nickname','like','%'.$nickname  .'%');
                });
            });
        }

        if($realname!==null){
            $entry->whereHas('designer',function($designer)use($realname){
                $designer->whereHas('detail',function($detail)use($realname){
                    $detail->where('realname','like','%'.$realname  .'%');
                });
            });
        }

        if($title!==null){
           $entry->where('title','like',"%".$title."%");
        }

        if($brand_id>0){
            $entry->whereHas('album_sections',function($section)use($brand_id){
                $section->whereHas('product_ceramics',function($product_ceramic)use($brand_id){
                    $product_ceramic->where('brand_id',$brand_id);
                });
            });
        }

        if($space_count!==null){
            $entry->has('space_types','=',$space_count);
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

        if($color_id!==null){
            $entry->whereHas('album_sections',function($section)use($color_id){
                $section->whereHas('product_ceramics',function($product_ceramic)use($color_id){
                    $product_ceramic->whereHas('colors',function($color)use($color_id){
                        $color->where('ceramic_colors.id',$color_id);
                    });
                });
            });
        }

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
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            switch($sort){
                case 'count_area_text':
                    $entry->orderBy('count_area',$order);
                    break;
                case 'space_count':
                    $entry->orderBy('space_count',$order);
                    break;
                case 'updated_at':
                    $entry->orderBy('updated_at',$order);
                    break;
                default:break;
            }
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $v->designer_account = $v->designer->designer_account;
            $v->realname = $v->designer->detail->realname;
            $v->nickname = $v->designer->detail->nickname;
            $v->type_text = Album::typeGroup($v->type);
            $v->style_text = implode(',',$v->style()->get()->pluck('name')->toArray());
            $v->count_area_text = $v->count_area."平方米";
            $v->space_count = $v->space_types()->count();
            $v->house_type_text = implode(',',$v->house_types()->get()->pluck('name')->toArray());

            $v->status_text = Album::statusGroup($v->status);
            //$v->changeStatusApiUrl = url('admin/brand/album/index/hd/seller/api/'.$v->id.'/status');
            //平台站状态
            $v->changeStatusPlatformApiUrl = '';
            $v->status_platform_title = '';
            $v->status_platform_style = 'primary';
            $v->changeStatusPlatformApiUrl = '';
            $v->can_switch = $v->status==Album::STATUS_PASS?1:0;
            switch($v->status_platform){
                case Album::STATUS_PLATFORM_VERIFYING:
                    //申请平台展示中
                    $v->status_platform_title = '平台展示申请中';
                    break;
                case Album::STATUS_PLATFORM_OFF:
                    //下架中
                    $v->status_platform_title = '申请平台展示';
                    $v->status_platform_style = 'warm';
                    $v->changeStatusPlatformApiUrl = url('admin/brand/album/index/hd/seller/api/'.$v->id.'/status_platform');
                    break;
                case Album::STATUS_PLATFORM_ON:
                    //平台展示中
                    $v->status_platform_title = '平台展示下架';
                    $v->status_platform_style = 'danger';
                    $v->changeStatusPlatformApiUrl = url('admin/brand/album/index/hd/seller/api/'.$v->id.'/status_platform');
                    break;
            }
            unset($v->designer);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //方案展示/下架（弃用，销售商需主动提交申请品牌展示，然后品牌来审核）
    public function ____change_status($id, Request $request)
    {

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $data = Album::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        $designer = Designer::query()
            ->whereHas('seller',function($organization)use($brand){
                //所属品牌
                $organization->where('organization_dealers.p_brand_id',$brand->id);
            })
            ->find($data->designer_id);
        if(!$designer){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status_brand>0){
                //下架
                $data->status_brand = Album::STATUS_BRAND_OFF;
                $search_album = SearchAlbum::where('album_id',$data->id)->first();
                if($search_album){
                    $data->status_search = Album::STATUS_SEARCH_OFF;
                    $search_album->delete();
                }
            }else{
                //上架
                $data->status_brand = Album::STATUS_BRAND_ON;
                AlbumService::addToSearchSingle($data->id);
                $data->status_search = Album::STATUS_SEARCH_ON;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //方案申请平台展示
    public function change_status_platform($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $data = Album::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        $designer = Designer::query()
            ->whereHas('seller',function($organization)use($brand){
                //所属品牌
                $organization->where('organization_dealers.p_brand_id',$brand->id);
            })
            ->find($data->designer_id);
        if(!$designer){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status_platform==Album::STATUS_PLATFORM_OFF){
                $data->status_platform = Album::STATUS_PLATFORM_VERIFYING;
            }else{
                $data->status_platform = Album::STATUS_PLATFORM_OFF;
            }

            $data->save();

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