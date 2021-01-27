<?php

namespace App\Http\Controllers\v1\admin\brand\product\verify\wait\api;

use App\Http\Services\common\OrganizationService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Http\Services\v1\admin\ProductCeramicService;
use App\Http\Services\v1\admin\SubAdminService;
use App\Models\AlbumProductCeramic;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\LogProductCeramic;
use App\Models\MsgProductCeramicBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\Style;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use App\Services\v1\admin\MsgProductCeramicBrandService;
use App\Services\v1\site\ProductService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WaitVerifyController extends ApiController
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

        /*$keyword = $request->input('keyword',null);
        $apply_category_id = $request->input('ac',null);
        $technology_category_id = $request->input('tc',null);
        $color_id = $request->input('clr',null);
        $spec_id = $request->input('spec',null);
        $product_status = $request->input('status',null);
        $visible_status = $request->input('vstatus',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');*/
        $limit = $request->input('limit',10);

        $entry = LogProductCeramic::query()
            ->where('is_approved',LogProductCeramic::IS_APROVE_VERIFYING); //显示待审核的产品

        /*//应用类别
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
        }*/

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){
            $product = $v->target_product;
            if($product){
                $v->status = $product->status;
                $v->visible = $product->visible;
            }

            $content = unserialize($v->content);
            $v->name = isset($content['name'])?$content['name']:'';
            $v->guide_price = isset($content['guide_price'])?$content['guide_price']:'';
            $v->code = isset($content['code'])?$content['code']:'';
            $v->product_type_text = ProductCeramic::typeGroup($content['type']);

            $v->spec = '';
            if(isset($content['spec_id'])){
                $spec = CeramicSpec::find($content['spec_id']);
                if($spec){
                    $v->spec = $spec->name;
                }
            }

            $v->series = '';
            if(isset($content['series_id'])){
                $series = CeramicSeries::find($content['series_id']);
                if($series){
                    $v->series = $series->name;
                }
            }

            $v->image = '';
            //获取第一个产品图作为缩略图
            if(isset($content['photo_product'])){
                $photo_product = unserialize($content['photo_product']);
                if(is_array($photo_product) && isset($photo_product[0]) && $photo_product[0]){
                    $v->image = $photo_product[0];
                }
            }

            //应用类别
            $v->apply_categories_text = '';
            if(isset($content['apply_categories'])){
                $apply_category_ids = $content['apply_categories'];
                $apply_categories = CeramicApplyCategory::whereIn('id',$apply_category_ids)->get()->pluck('name')->toArray();
                if(is_array($apply_categories) && count($apply_categories)>0){
                    $v->apply_categories_text = implode(',',$apply_categories);
                }
            }

            //工艺类别
            $v->technology_categories_text = '';
            if(isset($content['technology_categories'])){
                $technology_category_ids = $content['technology_categories'];
                $technology_categories = CeramicTechnologyCategory::whereIn('id',$technology_category_ids)->get()->pluck('name')->toArray();
                if(is_array($technology_categories) && count($technology_categories)>0){
                    $v->technology_categories_text = implode(',',$technology_categories);
                }
            }

            //表面特征
            $v->surface_features_text = '';
            if(isset($content['surface_features'])){
                $surface_features_ids = $content['surface_features'];
                $surface_features = CeramicSurfaceFeature::whereIn('id',$surface_features_ids)->get()->pluck('name')->toArray();
                if(is_array($surface_features) && count($surface_features)>0){
                    $v->surface_features_text = implode(',',$surface_features);
                }
            }

            //色系
            $v->colors_text = '';
            if(isset($content['colors'])){
                $colors_ids = $content['colors'];
                $colors = CeramicColor::whereIn('id',$colors_ids)->get()->pluck('name')->toArray();
                if(is_array($colors) && count($colors)>0){
                    $v->colors_text = implode(',',$colors);
                }
            }

            //可应用空间风格
            $v->styles_text = '';
            if(isset($content['styles'])){
                $styles_ids = $content['styles'];
                $styles = Style::whereIn('id',$styles_ids)->get()->pluck('name')->toArray();
                if(is_array($styles) && count($styles)>0){
                    $v->styles_text = implode(',',$styles);
                }
            }

            //使用量
            //一个方案算一次使用量（即使在多个空间都使用到这个产品，也只算1次）
            $v->usage = 0;

            $v->is_approve_text = LogProductCeramic::getIsApproved($v->is_approved);
            $v->price_way_text = ProductCeramic::priceWayGroup(isset($v->price_way)?$v->price_way:'');
            $v->type_text = LogProductCeramic::typeGroup($v->type);

            $admin = SubAdminService::getBrandAdminName($v->created_administrator_id);
            if($admin['name']<>'')
                $v->created_by = $admin['name'].'('.$admin['department'].','.$admin['position'].')';
            else
                $v->created_by = '';

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //异步搜索产品
    public function search_product(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $keyword = $request->input('keyword',null);

        $entry = ProductCeramic::query();

        $entry->select(['id','name','code']);
        $entry->limit(10);

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('code','like','%'.$keyword.'%');
            });
        }else{
            $entry->where('name','-1');
        }

        $entry->orderBy('id','desc');
        $entry->where('brand_id',$brand->id);

        $datas =$entry->get();

        $this->respData($datas);
    }

    //异步获取父产品列表
    public function get_parent_product(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = ProductCeramic::select(['id','name','code'])
            ->where('status',ProductCeramic::STATUS_PASS)
            ->where('type',ProductCeramic::TYPE_PRODUCT)
            ->where('brand_id',$brand->id)
            ->orderBy('created_at','desc');
        //->where('is_menu',PrivilegeBrand::IS_SHOW_MENU_YES) //默认只显示菜单权限

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->orWhere('name','like',"%".$keyword."%");
                $query->orWhere('code','like',"%".$keyword."%");
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

    //审核通过
    public function verify_approval($id)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;

        $log = LogProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->find($id);

        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogProductCeramic::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        $log_content = unserialize($log->content);

        try{

            DB::beginTransaction();
            $product = $log->target_product;

            //将log数据真正同步到product相关字段及其他表
            $product->brand_id = $brand->id;
            $product->create_by_administrator_id = $loginAdmin->id;
            $product->name = isset($log_content['name'])?$log_content['name']:'';
            $product->code = $log_content['code']?$log_content['code']:'';
            //$product->structure_id = $log_content['structure_id']?$log_content['structure_id']:'';
            $product->type = $log_content['type'];
            $product->guide_price = $log_content['guide_price'];
            $product->parent_id = $log_content['parent_id'];
            $product->sys_code = ProductCeramicService::get_sys_code($log_content['type'],$brand->id,$log_content['parent_id'],$log_content['series_id']);
            $product->key_technology = isset($log_content['key_technology'])?$log_content['key_technology']:'';
            $product->physical_chemical_property = isset($log_content['physical_chemical_property'])?$log_content['physical_chemical_property']:'';
            $product->function_feature = isset($log_content['function_feature'])?$log_content['function_feature']:'';
            $product->customer_value = isset($log_content['customer_value'])?$log_content['customer_value']:'';
            $product->series_id = isset($log_content['series_id'])?$log_content['series_id']:0;
            $product->spec_id = isset($log_content['spec_id'])?$log_content['spec_id']:0;
            $product->photo_product = isset($log_content['photo_product'])?$log_content['photo_product']:'';
            $product->photo_practicality = isset($log_content['photo_practicality'])?$log_content['photo_practicality']:'';
            $product->photo_video = isset($log_content['photo_video'])?$log_content['photo_video']:'';
            $product->status = ProductCeramic::STATUS_PASS;
            $product->visible = ProductCeramic::VISIBLE_YES;

            if($product->has_first_approved == ProductCeramic::HAS_FIRST_APPROVED_NO){
                $product->has_first_approved = ProductCeramic::HAS_FIRST_APPROVED_YES;
            }

            $product->save();

            //应用类别
            if(isset($log_content['apply_categories']) && count($log_content['apply_categories'])>0){
                $product->apply_categories()->sync($log_content['apply_categories']);
            }

            //工艺类别
            if(isset($log_content['technology_categories']) && count($log_content['technology_categories'])>0){
                $product->technology_categories()->sync($log_content['technology_categories']);
            }

            //色系
            if(isset($log_content['colors']) && count($log_content['colors'])>0){
                $product->colors()->sync($log_content['colors']);
            }

            //表面特征
            if(isset($log_content['surface_features']) && count($log_content['surface_features'])>0){
                $product->surface_features()->sync($log_content['surface_features']);
            }

            //可应用空间风格
            if(isset($log_content['styles']) && count($log_content['styles'])>0){
                $product->styles()->sync($log_content['styles']);
            }

            //产品配件
            $clear_accessories = DB::table('product_ceramic_accessories')->where('product_id',$product->id)->delete();
            $insert_accessories = DB::table('product_ceramic_accessories')->insert($log_content['accessories']);

            //产品搭配
            $clear_collocations = DB::table('product_ceramic_collocations')->where('product_id',$product->id)->delete();
            $insert_collocations = DB::table('product_ceramic_collocations')->insert($log_content['collocations']);

            //空间应用
            $clear_space_applications = DB::table('product_ceramic_spaces')->where('product_id',$product->id)->delete();
            $insert_space_applications = DB::table('product_ceramic_spaces')->insert($log_content['spaces']);

            //更新log状态
            $log->is_approved = LogProductCeramic::IS_APROVE_APPROVAL;
            $log->approve_administrator_id = $loginAdmin->id;
            $log->save();

            //更新产品
            $product->verify_time = Carbon::now();
            $product->save();



            //写入品牌产品通知
            $type_text = $log->type == LogProductCeramic::TYPE_FIRST_VERIFY?'首次审核':'修改审核';
            if($product->create_by_administrator_id){
                $msg = new MsgProductCeramicBrandService();
                $msg->setBrandId($brand->id);
                $msg->setAdministratorId($product->create_by_administrator_id);
                $msg->setContent('您的产品'.$type_text.'已通过。产品名称：'.$product->name.'，产品编码：'.$product->code);
                $msg->setType(MsgProductCeramicBrand::TYPE_VERIFICATION);
                $result1= $msg->add_msg();

                if(!$result1){
                    DB::rollback();
                    $this->respFail('品牌产品通知失败');
                }
            }

            //addToSearch
            ProductService::addToSearch();



            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());

        }

    }

    //审核驳回
    public function verify_reject($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $brand = $loginAdmin->brand;

        $reason = $request->input('reason','');

        if(!$reason){$this->respFail('请填写驳回理由！');}


        $log = LogProductCeramic::query()
            ->where('brand_id',$brand->id)
            ->find($id);

        if(!$log){$this->respFail('权限不足！');}

        //判断状态
        if($log->is_approved != LogProductCeramic::IS_APROVE_VERIFYING){
            $this->respFail('无法审核！');
        }

        DB::beginTransaction();

        try{

            $product = $log->target_product;

            //更新审核记录信息
            $log->is_approved = LogProductCeramic::IS_APROVE_REJECT;
            $log->approve_administrator_id = $loginAdmin->id;
            $log->remark = $reason;
            $log->save();

            //更新产品
            if($log->type == LogProductCeramic::TYPE_FIRST_VERIFY){
                //如果是首次审核
                $product->status = ProductCeramic::STATUS_REJECT;
                $product->visible = ProductCeramic::VISIBLE_NO;
                $product->save();
            }else{
                //如果是修改审核，则不需要改变产品状态
            }


            //写入品牌产品通知
            $type_text = $log->type == LogProductCeramic::TYPE_FIRST_VERIFY?'首次审核':'修改审核';
            if($product->create_by_administrator_id){
                $msg = new MsgProductCeramicBrandService();
                $msg->setBrandId($brand->id);
                $msg->setAdministratorId($product->create_by_administrator_id);
                $msg->setContent('您的产品'.$type_text.'已被驳回。产品名称：'.$product->name.'，产品编码：'.$product->code.'。驳回原因：'.$reason);
                $msg->setType(MsgProductCeramicBrand::TYPE_VERIFICATION);
                $result1= $msg->add_msg();
            }

            if(!$result1){
                DB::rollback();
                $this->respFail('品牌产品通知失败');
            }

            DB::commit();

//            //发送手机短信
//            if ($log->approve_type==LogOrganizationDetail::APPROVE_TYPE_ORGANIZATION_REGISTER){
//                $msgService = new GetVerifiCodeService();
//                $msg_content = '您的注册申请被驳回，请重新提交申请，谢谢。';
//                $msgService->sendMobile($verify_content['login_telephone'],$msg_content);
//            }

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail('系统错误！'.$e->getMessage());
        }

    }

}