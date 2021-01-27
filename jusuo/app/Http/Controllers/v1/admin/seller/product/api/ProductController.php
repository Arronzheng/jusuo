<?php

namespace App\Http\Controllers\v1\admin\seller\product\api;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicPriceService;
use App\Http\Services\v1\admin\ProductCeramicService;
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
        $seller = $loginAdmin->dealer;
        $brand = $seller->brand;

        $keyword = $request->input('keyword',null);
        $apply_category_id = $request->input('ac',null);
        $technology_category_id = $request->input('tc',null);
        $color_id = $request->input('clr',null);
        $spec_id = $request->input('spec',null);
        $product_status = $request->input('status',null);
        $visible_status = $request->input('vstatus',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = ProductCeramic::query();

        $parent_seller_id = 0;
        $structure_seller_id = 0;
        if($seller->level==2){
            $parent_seller = $seller->parent_dealer;
            $parent_seller_id = $parent_seller->id;
            $structure_seller_id = $parent_seller_id;
        }else{
            $parent_seller = $seller;
            $structure_seller_id = $seller->id;
        }

        //选出本销售商被授权的产品
        $entry->whereHas('dealer',function($query)use($seller,$parent_seller_id){
            $query->where('organization_dealers.id',$seller->id);
            if($parent_seller_id){
                $query->orWhere('organization_dealers.id',$parent_seller_id);
            }
        });

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
                $query->orWhere('short_name','like','%'.$keyword.'%');
            });
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v)use($seller,$parent_seller,$structure_seller_id){
            $structure_names = DB::table('product_ceramic_structures as pcs')
                ->join('product_ceramic_authorize_structures as pcas','pcas.structure_id','=','pcs.id')
                ->join('product_ceramic_authorizations as pca','pca.id','=','pcas.authorization_id')
                ->where('pca.product_id',$v->id)
                ->where('pca.dealer_id',$structure_seller_id)
                ->select(['pcs.name'])
                ->groupBy('pca.product_id')
                ->pluck('name')->toArray();
            $v->structure_text = implode(',',$structure_names);
          
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
            $v->image = '';
            //获取第一个产品图作为缩略图
            $photo_product = unserialize($v->photo_product);
            if(is_array($photo_product) && isset($photo_product[0]) && $photo_product[0]){
                $v->image = $photo_product[0];
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
            //使用量
            //一个方案算一次使用量（即使在多个空间都使用到这个产品，也只算1次）
            $v->usage = 0;
            $usage = AlbumProductCeramic::query()
                ->where('product_ceramic_id',$v->id)
                ->groupBy('album_id')
                ->count();
            $v->usage = $usage;
            $v->changeStatusApiUrl = url('admin/seller/product/api/'.$v->id.'/status');
            $authorization = ProductCeramicAuthorization::query()
                ->where('product_id',$v->id)
                ->where('dealer_id',$parent_seller->id)
                ->first();
            $v->authorization_status = $authorization->status;
            $v->authorization_id = $authorization->id;
            //定价方式
            $price_way = $authorization->price_way;
            $v->price_top = 0;
            $v->price_bottom = 0;
            $v->price_way_text = ProductCeramic::priceWayGroup($price_way);
            if($price_way==ProductCeramic::PRICE_WAY_FLOAT){
                $price_range = ProductCeramicPriceService::get_float_price_range($authorization);
                $v->price_top = $price_range['top'];
                $v->price_bottom = $price_range['bottom'];
            }
            $unit_text = '';
            $unit = $authorization->unit;
            if($unit){
                $unit_text = "/".ProductCeramicAuthorization::unitGroup($unit);
            }
            $v->price_text = $authorization->price."元".$unit_text;
            $v->seller_level = $seller->level;
            $v->status_text = ProductCeramicAuthorization::statusGroup(isset($authorization->status)?$authorization->status:'');
            $v->can_set_price = $price_way==ProductCeramic::PRICE_WAY_FLOAT || $price_way==ProductCeramic::PRICE_WAY_CHANNEL;
            $v->album_counts = $v->albums()->count();
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        $brand = $seller->brand;

        if($seller->level==2){
            $this->respFail('暂无权限');
        }

        $data = ProductCeramic::query()
            ->whereHas('dealer',function($query)use($seller){
                $query->where('organization_dealers.id',$seller->id);
            })
            ->where('brand_id',$brand->id)
            ->find($id);
        if(!$data){
            die('数据不存在');
        }


        DB::beginTransaction();

        try{

            $authorization = ProductCeramicAuthorization::query()
                ->where('product_id',$data->id)
                ->where('dealer_id',$seller->id)
                ->first();

            if(!$authorization){
                die('数据不存在');
            }

            //更新状态
            if($authorization->status==ProductCeramicAuthorization::STATUS_ON){
                $authorization->status = ProductCeramicAuthorization::STATUS_OFF;
            }else{
                if(
                    (($authorization->price_way==ProductCeramicAuthorization::PRICE_WAY_QD)
                    ||($authorization->price_way==ProductCeramicAuthorization::PRICE_WAY_FD))
                &&((!isset($authorization->price)||($authorization->price==0)))
                ){
                    $this->respFail('请先设置价格');
                }
                $authorization->status = ProductCeramicAuthorization::STATUS_ON;
            }

            $authorization->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误'.$e->getMessage());
        }

    }

    public function set_price(Request $request)
    {
        $authorization_id = $request->input('auid',0);
        $price = $request->input('price',0);

        $loginAdmin = $this->authService->getAuthUser();
        $seller = $loginAdmin->dealer;
        $brand = $seller->brand;

        $data = ProductCeramicAuthorization::query()
            ->where('id',$authorization_id)
            ->where('dealer_id',$seller->id)
            ->first();
        if(!$data){
            $this->respFail('数据不存在');
        }

        //判断定价方式是否允许定价
        if($data->price_way != ProductCeramic::PRICE_WAY_FLOAT &&
            $data->price_way != ProductCeramic::PRICE_WAY_CHANNEL){
            $this->respFail('不允许定价');
        }

        if($seller->level==2){
            $this->respFail('暂无权限');
        }

        if(!$authorization_id){
            $this->respFail('暂无权限');
        }

        if(!is_numeric($price)){
            $this->respFail('价格请输入正确数值');
        }

        if($price==0){
            $this->respFail('请设置价格');
        }



        DB::beginTransaction();

        try{
            $price = floatval($price);
            $price = floor($price*100)/100;

            switch($data->price_way){
                case ProductCeramic::PRICE_WAY_UNIFIED:
                    //统一定价
                    $this->respFail('无法定价');
                    break;
                case ProductCeramic::PRICE_WAY_FLOAT:
                    //浮动定价
                    //判断是否在浮动价格之间
                    $price_range = ProductCeramicPriceService::get_float_price_range($data);
                    if($price>$price_range['top'] || $price<$price_range['bottom']){
                        $this->respFail('价格请设置在'.$price_range['bottom']."~".$price_range['top'].'元之间');
                    }
                    break;
                case ProductCeramic::PRICE_WAY_CHANNEL:
                    //渠道定价
                    break;
                case ProductCeramic::PRICE_WAY_NOT_ALLOW:
                    //不定价
                    $this->respFail('无法定价');
                    break;
            }

            //更新价格
            $data->price = $price;

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误'.$e->getMessage());
        }

    }


}