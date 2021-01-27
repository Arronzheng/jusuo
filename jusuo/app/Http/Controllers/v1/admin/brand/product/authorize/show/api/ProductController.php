<?php

namespace App\Http\Controllers\v1\admin\brand\product\authorize\show\api;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
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
use App\Models\LogProductAuthorization;
use App\Models\MsgProductCeramicDealer;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\OrganizationDealerService;
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
        $brand = $loginAdmin->brand;

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

        $entry = ProductCeramic::query()
            ->where('status',ProductCeramic::STATUS_PASS);//必须是已通过审核的产品

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

        $datas->transform(function($v){
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
            $photo_product = @unserialize($v->photo_product);

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

            $v->changeStatusApiUrl = url('admin/brand/product/api/'.$v->id.'/status');
            $v->status_text = ProductCeramic::statusGroup(isset($v->status)?$v->status:'');
            $v->visible_text = ProductCeramic::visibleGroup(isset($v->visible)?$v->visible:'');
            $v->price_way_text = ProductCeramic::priceWayGroup(isset($v->price_way)?$v->price_way:'');

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //设置全局可见/不可见
    public function authorize_global(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $rules = [
            'op' => 'required',
            'ids' => 'required',
        ];

        $messages = [
            'op.required' => '请选择要操作的类型',
            'ids.required' => '请选择要操作的产品',
        ];

        $validator = Validator::make($input_data,$rules,$messages);

        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->respFail($msg_result);
        }

        //判断操作的正确性
        $operation = 'hide';
        if(!in_array($input_data['op'],['show','hide'])){
            $this->respFail('操作类型错误！');
        }
        $operation = $input_data['op'];

        //判断ids的数量
        $product_ids = $input_data['ids'];
        if(count($product_ids)<=0){
            $this->respFail('请选择产品！');
        }

        DB::beginTransaction();

        try{

            $insert_data = [];
            $product = ProductCeramic::find($product_ids[0]);
            $product_count = count($product_ids);
            $product_name = $product->name;

            //点击“全局可见/全局不可见”，可显示/取消显示给所有一级销售商
            //获取当前品牌的所有一级销售商
            $sellers = OrganizationDealerService::getBrandLegalSeller1Entry($brand->id)->get();
            if($operation=='show'){
                for($i=0;$i<count($sellers);$i++){
                    $exist_product_ids = $sellers[$i]->product_ceramic_authorizations()->get()->pluck('id')->toArray();
                    $final_product_id = array_merge($exist_product_ids,$product_ids);
                    $sellers[$i]->product_ceramic_authorizations()->sync($final_product_id);
                    //写入品牌产品通知
                    $insert_data[] = [
                        'dealer_id' => $sellers[$i]->id,
                        'type' => MsgProductCeramicDealer::TYPE_AUTHORIZATION,
                        'content' => '['.$product_name.']等'.$product_count.'个产品被品牌授权。',
                    ];
                }

                //写进操作记录
                $log_product_auth = new LogProductAuthorization();
                $log_product_auth->brand_id = $brand->id;
                $log_product_auth->administrator_id = $loginAdmin->id;
                $log_product_auth->product_type = LogProductAuthorization::PRODUCT_TYPE_CERAMIC;
                $log_product_auth->log_type = LogProductAuthorization::LOG_TYPE_SHOW;
                $log_product_auth->log_type_operation = LogProductAuthorization::LOG_TYPE_OPERATION_AUTHORIZE;
                $log_product_auth->product_ids = implode(',',$product_ids);
                $log_product_auth->object_ids = 'all';
                $log_auth = $log_product_auth->save();
                if(!$log_auth){
                    DB::rollback();
                    $this->respFail('记录操作失败！');
                }
            }else{
                for($i=0;$i<count($sellers);$i++){
                    $sellers[$i]->product_ceramic_authorizations()->detach($product_ids);
                    //写入品牌产品通知
                    $insert_data[] = [
                        'dealer_id' => $sellers[$i]->id,
                        'type' => MsgProductCeramicDealer::TYPE_AUTHORIZATION,
                        'content' => '['.$product_name.']等'.$product_count.'个产品被品牌取消授权。',
                    ];
                }

                //写进操作记录
                $log_product_auth = new LogProductAuthorization();
                $log_product_auth->brand_id = $brand->id;
                $log_product_auth->administrator_id = $loginAdmin->id;
                $log_product_auth->product_type = LogProductAuthorization::PRODUCT_TYPE_CERAMIC;
                $log_product_auth->log_type = LogProductAuthorization::LOG_TYPE_SHOW;
                $log_product_auth->log_type_operation = LogProductAuthorization::LOG_TYPE_OPERATION_CANCEL;
                $log_product_auth->product_ids = implode(',',$product_ids);
                $log_product_auth->object_ids = 'all';
                $log_auth = $log_product_auth->save();
                if(!$log_auth){
                    DB::rollback();
                    $this->respFail('记录操作失败！');
                }
            }

            if(count($insert_data)>0){
                DB::table('msg_product_ceramic_dealers')->insert($insert_data);
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }


}