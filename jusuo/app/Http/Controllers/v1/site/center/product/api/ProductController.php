<?php

namespace App\Http\Controllers\v1\site\center\product\api;

use App\Http\Services\v1\admin\AuthService;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\FavProduct;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\ProductCeramicAuthorizeStructure;
use App\Models\ProductCeramicStructure;
use Doctrine\DBAL\Driver\IBMDB2\DB2Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends ApiController
{
    private $authService;

    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //获取筛选类型数据
    public function list_filter_types(Request $request)
    {
        $query = $request->input('query','');
        $query_string = urldecode($query);
        parse_str($query_string,$query_array);

        $result = array();


        //应用类别
        $apply_categories = CeramicApplyCategory::select(['id','name'])->get();
        $apply_categories_info = array();
        $apply_categories_info['title'] = '应用类别';
        $apply_categories_info['value'] = 'ac';
        $apply_categories_info['has_selected'] = false;
        if(isset($query_array[$apply_categories_info['value']])){
            $type_query = $query_array[$apply_categories_info['value']];
            for($i=0;$i<count($apply_categories);$i++){
                if($apply_categories[$i]->id == $type_query){
                    $apply_categories[$i]->selected = 1;
                    $apply_categories_info['has_selected'] = true;
                }else{
                    $apply_categories[$i]->selected = 0;
                }
            }
        }
        $apply_categories_info['data'] = $apply_categories;

        //工艺类别
        $technology_categories = CeramicTechnologyCategory::select(['id','name'])->get();
        $technology_categories_info = array();
        $technology_categories_info['title'] = '工艺类别';
        $technology_categories_info['value'] = 'tc';
        $technology_categories_info['has_selected'] = false;
        if(isset($query_array[$technology_categories_info['value']])){
            $type_query = $query_array[$technology_categories_info['value']];
            for($i=0;$i<count($technology_categories);$i++){
                if($technology_categories[$i]->id == $type_query){
                    $apply_categories[$i]->selected = 1;
                    $apply_categories_info['has_selected'] = true;
                }else{
                    $apply_categories[$i]->selected = 0;
                }
            }
        }
        $technology_categories_info['data'] = $technology_categories;


        //色系
        $colors = CeramicColor::select(['id','name'])->get();
        $color_info = array();
        $color_info['title'] = '色系';
        $color_info['value'] = 'clr';
        $color_info['has_selected'] = false;
        if(isset($query_array[$color_info['value']])){
            $type_query = $query_array[$color_info['value']];
            for($i=0;$i<count($colors);$i++){
                if($colors[$i]->id == $type_query){
                    $colors[$i]->selected = 1;
                    $color_info['has_selected'] = true;
                }else{
                    $colors[$i]->selected = 0;
                }
            }
        }
        $color_info['data'] = $colors;

        //产品规格
        $spec = CeramicSpec::select(['id','name'])->get();
        $spec_info = array();
        $spec_info['title'] = '产品规格';
        $spec_info['value'] = 'spec';
        $spec_info['has_selected'] = false;
        if(isset($query_array[$spec_info['value']])){
            $type_query = $query_array[$spec_info['value']];
            for($i=0;$i<count($spec);$i++){
                if($spec[$i]->id == $type_query){
                    $spec[$i]->selected = 1;
                    $spec_info['has_selected'] = true;
                }else{
                    $spec[$i]->selected = 0;
                }
            }
        }
        $spec_info['data'] = $spec;

        //产品结构
        $structures = ProductCeramicStructure::select(['id','name'])->get();
        $structures_arr = array();
        $structures_arr['title'] = '产品结构';
        $structures_arr['value'] = 'str';
        $structures_arr['has_selected'] = false;
        if(isset($query_array[$structures_arr['value']])){
            $type_query = $query_array[$structures_arr['value']];
            for($i=0;$i,count($structures);$i++){
                if($structures[$i]->id == $type_query){
                    $structures[$i]->selected = 1;
                    $structures_arr['has_selected'] = true;
                }else{
                    $structures_arr->selected = 0;
                }
            }
        }
        $structures_arr['data'] = $structures;

        //产品状态
        $status = ProductCeramic::statusGroup();
        $status_arr = [];
        foreach($status as $k => $v){
            $arr['id'] = $k;
            $arr['name'] = $v;
            array_push($status_arr,$arr);
        }
        $status_info = array();
        $status_info['title'] = '状态';
        $status_info['value'] = 'status';
        $status_info['has_selected'] = false;

        if(isset($query_array[$status_info['value']])){
            $type_query = $query_array[$status_info['value']];
            for($i=0;$i<count($status_arr);$i++){
                if($status_arr[$i]->id == $type_query){
                    $status_arr[$i]['selected'] = 1;
                    $status_info['has_selected'] = true;
                }else{
                    $status_info[$i]['selected'] = 0;
                }
            }
        }
        $status_info['data'] = $status_arr;



        $result[] = $color_info;
        $result[] = $apply_categories_info;
        $result[] = $technology_categories_info;
        $result[] = $spec_info;
        $result[] = $status_info;
        $result[] = $structures_arr;

        return $this->respDataReturn($result);

    }

    public function get_user_brand(Request $request){
        $designer = $request->user();

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            $orgId = $designer->organization_id;
        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            $seller_id = $designer->organization_id;
            $seller = OrganizationDealer::where('id',$seller_id)->first();
            if($seller){
                $orgId = $seller->p_brand_id;
            }
        }else{
            return $this->respDataReturn([],'用户无所属品牌');
        }

        $brand = OrganizationBrand::where('id',$orgId)->first();

        return $this->respDataReturn($brand);



    }

    //获取产品列表数据
    public function list_products(Request $request)
    {

        $designer = Auth()->user();

        $builder = ProductCeramic::query()
            ->with(['brand'=>function($query){
                $query->select(['id','short_name']);
            }])
            ->select(['id','visible','brand_id','code','series_id',
                'structure_id','web_id_code','photo_product',
                'guide_price','name as productTitle','count_fav','status','spec_id'])
            ->where('visible',ProductCeramic::VISIBLE_YES); //全显示，同时显示上下架状态
            //->where('status',ProductCeramic::STATUS_PASS);

        $seller_lv1_id = null;

        if($designer->organization_type == Designer::ORGANIZATION_TYPE_BRAND){
            //如果属于品牌
            $orgId = $designer->organization_id;
            $builder->where('brand_id',$orgId);

        }else if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
            //属于经销商
            $seller_id = $designer->organization_id;
            $seller = OrganizationDealer::where('id',$seller_id)->first();
            if($seller->p_dealer_id == 0){
                $seller_lv1_id = $seller->id;
                //是否一级
                $product_ids =DB::table('product_ceramic_authorizations')
                    ->where('dealer_id',$seller->id)
                    //->where('status','=',ProductCeramicAuthorization::STATUS_ON) 全显示，但显示上下架
                    ->pluck('product_id')->toArray();
            }else{
                //二级 找到上一级
                $p_seller = OrganizationDealer::where('id',$seller->p_dealer_id)->first();
                if($p_seller){
                    $seller_lv1_id = $p_seller->id;
                    $product_ids =DB::table('product_ceramic_authorizations')
                        ->where('dealer_id',$p_seller->id)
                        //->where('status','=',ProductCeramicAuthorization::STATUS_ON)  全显示，但显示上下架
                        ->pluck('product_id')->toArray();
                }
            }
            $builder->whereIn('product_ceramics.id',$product_ids);


        }else{
            return $this->respDataReturn([],'用户无所属组织');
        }


        //搜索名称/型号
        if($name = $request->input('name','')){
            $like = '%'.$name.'%';

            $builder->where(function($query) use ($like){
                $query->where('name','like',$like);
                $query->orWhere('code','like',$like);
            });
        }

        //应用类别
        if($ac = $request->input('ac',null)){
            $builder->whereHas('apply_categories',function($query) use ($ac){
                $query->where('ceramic_apply_categories.id',$ac);
            });
        }

        //工艺类别
        if($tc = $request->input('tc',null)){
            $builder->whereHas('technology_categories',function($query) use ($tc){
                $query->where('product_ceramic_technology_categories.id',$tc);
            });
        }

        //是否提交色系
        if($clr = $request->input('clr',null)){
            $builder->whereHas('colors',function($query) use ($clr){
                $query->where('ceramic_colors.id',$clr);
            });
        }

        //产品规格
        if($spec = $request->input('spec',null)){
            $builder->whereHas('spec',function($query) use ($spec){
                $query->where('ceramic_specs.id',$spec);
            });
        }

        //状态
        if($status = $request->input('status',null)){
            $builder->where('status',$status);
        }

        //结构
        if($product_str = $request->input('str',null)){
            $pcas = ProductCeramicAuthorizeStructure::where('structure_id',$product_str)->pluck('authorization_id')->toArray();
            $sq = DB::table('product_ceramic_authorizations')->whereIn('id',$pcas)->pluck('product_id')->where('status','!=',ProductCeramicAuthorization::STATUS_OFF)->toArray();
            $builder->whereIn('product_ceramics.id',$sq);
        }


        $datas = $builder->get();



        $datas->transform(function($v) use($designer,$seller_lv1_id){

            $v->collected = false;
            if($designer){
                $collected = FavProduct::where('designer_id',$designer->id)->where('product_id',$v->id)->first();
                if($collected){ $v->collected = true; }
            }

            $series_text = CeramicSeries::where('id',$v->series_id)->value('name');
            $v->series_text = $series_text;

            $ac_text = ' ';
            if(count($v->apply_categories) > 0){
                foreach ($v->apply_categories as $category){
                    $ac_text = $ac_text.$category->name;
                }
            }
            $v->ac_text = $ac_text;

            $tc_text = ' ';
            if(count($v->technology_categories) > 0){
                foreach ($v->technology_categories as $category){
                    $tc_text = $category->name.$tc_text;
                }
            }
            $v->tc_text = $tc_text;

            $colors_text = ' ';
            if(count($v->colors) > 0){
                foreach ($v->colors as $color){
                    $colors_text = $color->name.$colors_text;
                }
            }
            $v->colors_text = $colors_text;

            $spec_text = CeramicSpec::where('id',$v->spec_id)->value('name');
            $v->spec_text =  $spec_text ? $spec_text : '';


            $v->status_text = ProductCeramic::statusGroup($v->status);


            //获取第一张产品图为封面
            $v->cover =  '';
            $photo_product = \Opis\Closure\unserialize($v->photo_product);
            if(isset($photo_product[0])){
                $v->cover = $photo_product[0];
            }

            //展示下架状态（默认品牌设计师时）
            $v->visible_text = ProductCeramic::visibleGroup($v->visible);

            //产品结构
            $str_text = ' ';
            if($designer->organization_type == Designer::ORGANIZATION_TYPE_SELLER){
                //销售商设计师
                $structure_names = DB::table('product_ceramic_structures as pcs')
                    ->join('product_ceramic_authorize_structures as pcas','pcas.structure_id','=','pcs.id')
                    ->join('product_ceramic_authorizations as pca','pca.id','=','pcas.authorization_id')
                    ->where('pca.product_id',$v->id)
                    ->where('pca.dealer_id',$seller_lv1_id)
                    ->select(['pcs.name'])
                    ->groupBy('pca.product_id')
                    ->pluck('name')->toArray();
                $str_text = implode(',',$structure_names);

                //修改展示下架状态，数据源来自品牌授权产品给销售商的授权表
                $visible_data = DB::table('product_ceramic_authorizations as pca')
                    ->where('pca.product_id',$v->id)
                    ->where('pca.dealer_id',$seller_lv1_id)
                    ->first();
                if($visible_data){
                    $v->visible_text = ProductCeramicAuthorization::statusGroup($visible_data->status);
                }

            }
            $v->str_text = $str_text;

            $v->product_detail_href = url('/product/s/'.$v->web_id_code);

            unset($v->id);
            unset($v->brand_id);
            unset($v->guide_price);
            if(isset($v->brand) && $v->brand){
                unset($v->brand->id);
            }

            return $v;
        });


        return $this->respDataReturn($datas);


    }
}