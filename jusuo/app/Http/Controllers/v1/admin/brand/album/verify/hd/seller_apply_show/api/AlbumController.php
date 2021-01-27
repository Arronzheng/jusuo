<?php

namespace App\Http\Controllers\v1\admin\brand\album\verify\hd\seller_apply_show\api;

use App\Http\Services\common\LayuiTableService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\MsgAlbumBrand;
use App\Models\MsgAlbumDealer;
use App\Models\MsgAlbumDesigner;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Services\v1\admin\MsgAlbumBrandService;
use App\Services\v1\admin\MsgAlbumDesignerService;
use App\Services\v1\admin\MsgAlbumSellerService;
use App\Services\v1\admin\OrganizationDealerService;
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
                'albums.id','type','title','code','status','designer_id','updated_at','count_area','status_brand'
            ])
            ->whereHas('designer',function($query)use($brand){
                $query->whereHas('seller',function($organization)use($brand){
                    //所属品牌
                    $organization->where('organization_dealers.p_brand_id',$brand->id);
                });
            })//销售商方案
            ->where('status',Album::STATUS_PASS) //必须是已通过审核的
            ->where('status_brand',Album::STATUS_BRAND_VERIFYING); //必须是正在申请品牌展示的


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

            $v->status_text = Album::statusBrandGroup($v->status_brand?$v->status_brand:'');
            unset($v->designer);
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

        $data = Album::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        $seller_ids = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->get()
            ->pluck('id')->toArray();

        $designer = Designer::query()
            ->whereIn('organization_id',$seller_ids)
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->find($data->designer_id);
        if(!$designer){
            $this->respFail('数据不存在');
        }

        if($data->status_brand != Album::STATUS_BRAND_VERIFYING){
            $this->respFail('无法操作');
        }

        DB::beginTransaction();

        try{

            //更新状态
            $data->status_brand = $brand->id;
            $data->save();

            //写入方案通知
            $msg_content = '您的方案已允许在品牌展示。方案标题：'.$data->title;
            $msg = new MsgAlbumSellerService();
            $msg->setDealerId($designer->organization_id);
            $msg->setContent($msg_content);
            $msg->setType(MsgAlbumDealer::TYPE_SWITCH_BY_BRAND);
            $result1= $msg->add_msg();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //审核驳回
    public function verify_reject($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $reason = $request->input('reason','');

        if(!$reason){$this->respFail('请填写驳回理由！');}

        $seller_ids = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->get()
            ->pluck('id')->toArray();

        $data = Album::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        $designer = Designer::query()
            ->whereIn('organization_id',$seller_ids)
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->find($data->designer_id);
        if(!$designer){
            $this->respFail('数据不存在');
        }

        if($data->status_brand < Album::STATUS_BRAND_VERIFYING){
            $this->respFail('无法操作');
        }

        DB::beginTransaction();

        try{

            //更新状态
            $data->status_brand = Album::STATUS_BRAND_OFF;

            $data->save();

            //写入方案通知
            $msg_content = '您的方案被拒绝在品牌展示。方案标题：'.$data->title."，拒绝原因：".$reason;
            $msg = new MsgAlbumSellerService();
            $msg->setDealerId($designer->organization_id);
            $msg->setContent($msg_content);
            $msg->setType(MsgAlbumDealer::TYPE_SWITCH_BY_BRAND);
            $result1= $msg->add_msg();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }


    }

}