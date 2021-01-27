<?php

namespace App\Http\Controllers\v1\admin\platform\album\index\hd\brand\api;

use App\Http\Services\common\LayuiTableService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\MsgAlbumDesigner;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\MsgAlbumBrand;
use App\Services\v1\admin\MsgAlbumBrandService;
use App\Services\v1\admin\MsgAlbumDesignerService;
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
        $owner_name = $request->input('owner',null);
        $nickname = $request->input('nn',null);
        $product_name = $request->input('pn',null);

        $entry = Album::query()
            ->select([
                DB::raw('(select count(*) from album_space_types as abs where abs.album_id = albums.id) as space_count'),
                'albums.id','type','title','code','status','designer_id','updated_at','count_area','visible_status','status_platform'
            ])
            ->whereHas('designer',function($query)use($owner_name,$brand_id,$nickname){
                if($nickname!==null){
                    $query->whereHas('detail',function($detail)use($nickname){
                        $detail->where('nickname','like','%'.$nickname.'%');
                    });
                }
                $query->where(function($query1) use($owner_name,$brand_id){

                    //筛选品牌或销售商设计师
                    $query1->where(function($brand_designer)use($owner_name,$brand_id){
                        $brand_designer->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND);

                        if($brand_id>0 || $owner_name!==null){
                            $brand_designer->whereHas('brand',function($organization)use($brand_id,$owner_name){
                                //所属品牌
                                if($brand_id>0){
                                    $organization->where('id',$brand_id);
                                }
                                //所有方名称
                                if($owner_name!==null){
                                    $organization->where('name','like','%'.$owner_name  .'%');
                                }
                            });
                        }
                    });

                    $query1->orWhere(function($seller_designer)use($owner_name,$brand_id){
                        $seller_designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);

                        if($brand_id>0 || $owner_name!==null){
                            $seller_designer->whereHas('seller',function($organization)use($brand_id,$owner_name){
                                //所属品牌
                                if($brand_id>0){
                                    $organization->where('organization_dealers.p_brand_id',$brand_id);
                                }
                                //所有方名称
                                if($owner_name){
                                    $organization->where('name','like','%'.$owner_name  .'%');
                                }
                            });
                        }

                    });



                });



            })
            ->where('status',Album::STATUS_PASS); //必须是已审核通过的


        if($product_name!=null){
            $entry->whereHas('album_sections',function($section)use($product_name){
                $section->whereHas('product_ceramics',function($product_ceramic)use($product_name){
                    $product_ceramic->where('name','like','%'.$product_name.'%');
                });
            });
        }





        if($title!==null){
           $entry->where('title','like',"%".$title."%");
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
            $v->changeStatusApiUrl = url('admin/platform/album/index/hd/brand/api/'.$v->id.'/status');

            unset($v->designer);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //方案平台展示/下架
    public function change_status($id, Request $request)
    {

        $operation = $request->input('op','');
        if(!in_array($operation,['on','off'])){
            $this->respFail('操作错误');

        }

        $data = Album::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        $new_status = Album::STATUS_PLATFORM_OFF;

        if($data->status_platform == Album::STATUS_PLATFORM_OFF){
            $this->respFail('无法操作');
        }

        if($data->status_platform == Album::STATUS_PLATFORM_ON){
            if($operation=='on'){
                $this->respFail('不能重复操作');
            }
        }

        if($data->status_platform == Album::STATUS_PLATFORM_VERIFYING){
            if($operation=='on'){
                $new_status = Album::STATUS_PLATFORM_ON;
            }
        }

        $designer = Designer::find($data->designer_id);
        $brand_id = 0;
        if(!$designer){
            $this->respFail('设计师错误');
        }

        switch($designer->organization_type){
            case Designer::ORGANIZATION_TYPE_BRAND:
                $brand_id = $designer->organization_id;
                break;
            case Designer::ORGANIZATION_TYPE_SELLER:
                $seller = OrganizationDealer::find($designer->organization_id);
                if(!$seller){
                    $this->respFail('设计师归属错误');
                }
                $brand_id = $seller->brand->id;
                break;
            default:
                $this->respFail('设计师归属错误');
                break;
        }


        DB::beginTransaction();

        try{

            //更新状态
            $data->status_platform = $new_status;

            $msg_content = '您的方案已允许在平台展示。方案标题：'.$data->title;
            if($new_status==Album::STATUS_PLATFORM_OFF){
                $msg_content = '您的方案未被允许在平台展示。方案标题：'.$data->title;
            }

            //写入品牌方案通知
            $msg = new MsgAlbumBrandService();
            $msg->setBrandId($brand_id);
            $msg->setContent($msg_content);
            $msg->setType(MsgAlbumBrand::TYPE_SWITCH_BY_PLATFORM);
            $result1= $msg->add_msg();

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