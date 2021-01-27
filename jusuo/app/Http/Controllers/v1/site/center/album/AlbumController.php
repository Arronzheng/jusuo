<?php

namespace App\Http\Controllers\v1\site\center\album;

use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Area;
use App\Models\CeramicApplyCategory;
use App\Models\CeramicColor;
use App\Models\CeramicSeries;
use App\Models\CeramicSpec;
use App\Models\CeramicSurfaceFeature;
use App\Models\CeramicTechnologyCategory;
use App\Models\Designer;
use App\Models\HouseType;
use App\Models\LogProductCeramic;
use App\Models\PrivilegeBrand;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicSpace;
use App\Models\ProductCeramicStructure;
use App\Models\ProductStructure;
use App\Models\SpaceType;
use App\Models\Style;
use App\Models\TestData;
use App\Services\v1\site\DesignerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Test;

class AlbumController extends VersionController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $vdata = [];

        $designer = Auth::user();

        $keyword = $request->input('kw',null);
        $status = $request->input('sts',null);
        $style = $request->input('stl',null);
        $house_type_id = $request->input('htype',null);
        $product_code = $request->input('pc',null);

        $builder = Album::query()
            ->select(['albums.id','albums.period_status','albums.period_status','albums.visible_status',
                'albums.web_id_code','albums.is_representative_work',
            'albums.photo_cover','albums.title','albums.count_area','albums.count_visit',
            'albums.count_praise','albums.count_fav','albums.count_use','albums.created_at',
            'albums.status'])
            ->where('designer_id',$designer->id);/*
            ->where('status','<>',Album::STATUS_DELETE)*/


        if($time = $request->input('time',null)){
            $time_arr = explode('_',$time);
            if($time_arr[0] == 'min'){
                $start_min = $time_arr[1];
                $end_min = $time_arr[2];
                $start_time = Carbon::now()->subMinutes($start_min);
                $end_time = Carbon::now()->subMinutes($end_min);
                $builder->whereBetween('albums.created_at',[$end_time,$start_time]);
            }else if($time_arr[0] == 'hour'){
                $start_min = $time_arr[1];
                $end_min = $time_arr[2];
                $start_time = Carbon::now()->subHours($start_min);
                $end_time = Carbon::now()->subHours($end_min);
                $builder->whereBetween('albums.created_at',[$end_time,$start_time]);
            }else if($time_arr[0] == 'day'){
                $start_min = $time_arr[1];
                $end_min = $time_arr[2];
                $start_time = Carbon::now()->subDays($start_min);
                $end_time = Carbon::now()->subDays($end_min);
                $builder->whereBetween('albums.created_at',[$end_time,$start_time]);
            }else if($time_arr[0] == 'mon'){
                $start_min = $time_arr[1];
                $end_min = $time_arr[2];
                $start_time = Carbon::now()->subMonth($start_min);
                $end_time = Carbon::now()->subMonth($end_min);
                $builder->whereBetween('albums.created_at',[$end_time,$start_time]);
            }else if($time_arr[0] == 'year'){
                $start_min = $time_arr[1];
                $end_min = $time_arr[2];
                $start_time = Carbon::now()->subYear($start_min);
                $end_time = Carbon::now()->subYear($end_min);
                $builder->whereBetween('albums.created_at',[$end_time,$start_time]);
            }else{

            }
        }

        //是否提交面积
        if($area = $request->input('ca',null)){
            $area_info = explode('_',$area);
            if(isset($area_info[0]) && isset($area_info[1])){
                if($area_info[1]=='m'){
                    $builder->where('count_area','>',$area_info[0]);
                }else{
                    $min = floatval($area_info[0]);
                    $max = floatval($area_info[1]);
                    $builder->where('count_area','>',$min);
                    $builder->where('count_area','<=',$max);
                }

            }

        }

        if($house_type_id!==null){
            $builder->whereHas('house_types',function($query)use($house_type_id){
                $query->where('house_types.id',$house_type_id);
            });
        }

        if($product_code!==null){
            $builder->whereHas('product_ceramics',function($query)use($product_code){
                $query->where('product_ceramics.code','like','%'.$product_code.'%');
            });
        }

        if($style!==null){
            $builder->whereHas('style',function($query)use($style){
                $query->where('id',$style);
            });
        }
        
        if($keyword!==null){
            $builder->where('title','like','%'.$keyword.'%');
        }

        if($status!==null){
            $builder->where('status',$status);
        }else{
            $builder->where('status','<>',Album::STATUS_DELETE);

        }

        $builder->orderBy('albums.id','desc');

        $datas =$builder->paginate(12);

        $datas->transform(function($v){
            $house_types = $v->house_types()->get()->pluck('name')->toArray();
            $v->house_types = $house_types;
            $styles = $v->style()->get()->pluck('name')->toArray();
            $v->styles = $styles;
            $v->status_text = Album::statusGroup($v->status);
            if($v->visible_status == Album::VISIBLE_STATUS_OFF){
                $v->status_text = Album::visibleStatusGroup($v->visible_status);
            }
            $v->verify_note = '驳回信息：'.$v->verify_note;
            $v->changeVisibleStatusApiUrl = url('/center/album/api/'.$v->web_id_code.'/change_visible');

            unset($v->id);
            return $v;
        });

        $count_area = [
            ['id'=>'0_50','name'=>'50㎡及以下'],
            ['id'=>'50_80','name'=>'50-80㎡'],
            ['id'=>'80_100','name'=>'80-100㎡'],
            ['id'=>'100_130','name'=>'100-130㎡'],
            ['id'=>'130_m','name'=>'130㎡及以上'],
        ];

        $upload_time = [
            ['id' => 'min_0_5','name' => '刚刚'],
            ['id' => 'hour_0_1','name' => '近一小时'],
            ['id' => 'hour_0_8','name' => '近八小时'],
            ['id' => 'day_0_1','name' => '近一天'],
            ['id' => 'day_0_3','name' => '近三天'],
            ['id' => 'day_0_15','name' => '近半个月'],
            ['id' => 'mon_0_1','name' => '近一个月'],
            ['id' => 'mon_0_3','name' => '近三个月'],
            ['id' => 'mon_0_6','name' => '近半年'],
            ['id' => 'year_0_1','name' => '近一年'],
        ];

        $status = Album::statusGroup();
        $status_arr = [];
        foreach($status as $k => $v){
            $arr['id'] = $k;
            $arr['name'] = $v;
            array_push($status_arr,$arr);
        }

        $vdata['house_types'] = HouseType::all();
        $vdata['styles'] = Style::all();
        $vdata['count_area'] = $count_area;
        $vdata['upload_time'] = $upload_time;
        $vdata['status_arr'] = $status_arr;
        $vdata['datas'] = $datas;

        $brandId = DesignerService::getDesignerBrandScope($designer->id);
        $__BRAND_SCOPE = $this->compressBrandScope($brandId);

        return $this->get_view('v1.site.center.album.index',compact('vdata','__BRAND_SCOPE'));
    }


    //跳转方案预览
    public function preview_album_detail($web_id_code)
    {
        $loginDesigner = Auth::user();
        $brand_id = DesignerService::getDesignerBrandScope($loginDesigner->id);
        session()->put('designer_session.preview_designer_id',$loginDesigner->id);

        $preview_brand_id = $brand_id;
        session()->put('preview_brand_id',$preview_brand_id);

        return redirect('/album/sm/'.$web_id_code);
    }

    //查询产品
    public function choose_product(Request $request)
    {
        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.album.choose_product',compact('__BRAND_SCOPE'));
    }

    //新增页
    public function create(Request $request)
    {

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $cities = [];
        $districts = [];

        $vdata = [];
        $vdata['house_types'] = HouseType::all();
        $vdata['styles'] = Style::all();
        $vdata['space_types'] = SpaceType::all();

        $config = $this->get_edit_config_data();

        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.album.edit',compact(
            'provinces','cities','districts','config','vdata','__BRAND_SCOPE'
        ));
    }

    //编辑页
    public function edit($id,Request $request)
    {
        $loginUser = Auth::user();

        $album = Album::query()
            ->select(['albums.*'])
            ->where('designer_id',$loginUser->id)
            ->where('albums.web_id_code',$id)
            ->first();


        if(!$album){
            return back()->withErrors(['您没有相关权限']);
        }

        if($album->period_status != Album::PERIOD_STATUS_EDIT){
            return back()->withErrors(['方案不可编辑']);
        }

        //户型
        $house_type_ids = $album->house_types()->get()->pluck('id')->toArray();
        $album->house_type_ids = $house_type_ids;
        //风格
        $style_ids = $album->style()->get()->pluck('id')->toArray();
        $album->style_ids = $style_ids;

        $sections = $album->album_sections;
        $sections->transform(function($v){
            $v->content = unserialize($v->content);
            $style_ids = $v->styles()->get()->pluck('id')->toArray();
            $v->style_ids = $style_ids;
            return $v;
        });
        $album->sections =$sections;
        //产品
        $products = $album->product_ceramics;
        $products->transform(function($v){
            $v->type_text = ProductCeramic::typeGroup($v->type);
            $v->spec_text = '';
            $spec = $v->spec;
            if($spec){
                $v->spec_text = $spec->name;
            }
            return $v;
        });
        $album->products = $products;

        //省份数据
        $provinces = Area::where('level',1)->orderBy('id','asc')->select(['id','name'])->get();
        $cities = [];
        $districts = [];
        if($album->address_province_id){
            $cities = Area::where('level',2)->where('pid',$album->address_province_id)->orderBy('id','asc')->select(['id','name'])->get();
        }
        if($album->address_city_id){
            $districts = Area::where('level',3)->where('pid',$album->address_city_id)->orderBy('id','asc')->select(['id','name'])->get();
        }

        $vdata = [];
        $vdata['house_types'] = HouseType::all();
        $vdata['styles'] = Style::all();
        $vdata['space_types'] = SpaceType::all();

        $config = $this->get_edit_config_data();

        $this->extractBrandScope($request);
        $__BRAND_SCOPE = $this->compressBrandScope($this->brand_scope);

        return $this->get_view('v1.site.center.album.edit',compact(
            'provinces','cities','districts','config','vdata','album','__BRAND_SCOPE'
        ));
    }

    //获取创建/编辑所需的参数设置数据
    private function get_edit_config_data()
    {
        $loginUser = Auth::user();
        $designer = Designer::find($loginUser->id);
        $config = [];
        //参数设置
        $pcu = new ParamConfigUseService($loginUser->id);
        $app_info = $pcu->get_by_keyword('platform.album.app_info');
        $basic_info = $pcu->get_by_keyword('platform.album.basic_info');
        $config['title_required'] = $app_info['platform.album.app_info.title.required'];
        $config['title_char_limit'] = $app_info['platform.album.app_info.title.character_limit'];
        $config['street_required'] = $app_info['platform.album.app_info.address_street.required'];
        $config['street_char_limit'] = $app_info['platform.album.app_info.address_street.character_limit'];
        $config['residential_quarter_required'] = $app_info['platform.album.app_info.address_residential_quarter.required'];
        $config['residential_quarter_char_limit'] = $app_info['platform.album.app_info.address_residential_quarter.character_limit'];
        $config['building_required'] = $app_info['platform.album.app_info.address_building.required'];
        $config['building_char_limit'] = $app_info['platform.album.app_info.address_building.character_limit'];
        $config['layout_number_required'] = $app_info['platform.album.app_info.address_layout_number.required'];
        $config['layout_number_char_limit'] = $app_info['platform.album.app_info.address_layout_number.character_limit'];
        $config['description_design_required'] = $app_info['platform.album.app_info.description_design.required'];
        $config['description_design_char_limit'] = $app_info['platform.album.app_info.description_design.character_limit'];
        $config['photo_layout_min_limit'] = $app_info['platform.album.app_info.layout_photo.min_limit'];
        $config['description_layout_required'] = $app_info['platform.album.app_info.description_layout.required'];
        $config['description_layout_char_limit'] = $app_info['platform.album.app_info.description_layout.character_limit'];
        $config['each_space_description_required'] = $app_info['platform.album.app_info.each_space_description.required'];
        $config['each_space_description_char_limit'] = $app_info['platform.album.app_info.each_space_description.character_limit'];
        $config['each_space_product_app_description_required'] = $app_info['platform.album.app_info.each_space_product_app_description.required'];
        $config['each_space_product_app_description_char_limit'] = $app_info['platform.album.app_info.each_space_product_app_description.character_limit'];
        $config['each_space_build_description_required'] = $app_info['platform.album.app_info.each_space_build_description.required'];
        $config['each_space_build_description_char_limit'] = $app_info['platform.album.app_info.each_space_build_description.character_limit'];
        //空间最低数量
        $config['space_min_limit'] = $basic_info['platform.album.basic_info.space.min_limit'];
        //关联产品最低数量
        $config['product_min_limit'] = $basic_info['platform.album.basic_info.related_product.min_limit'];
        //总面积填写范围
        $config['total_area_range'] = $basic_info['platform.album.basic_info.total_area.number_range'];
        //各空间面积填写范围
        $config['section_area_range'] = $basic_info['platform.album.basic_info.space_area.number_range'];
        //总面积是否必填
        $config['total_area_required'] = $basic_info['platform.album.basic_info.total_area.required'];
        //各空间面积是否必填
        $config['section_area_required'] = $basic_info['platform.album.basic_info.each_space_area.required'];
        //各空间高清图最低数量
        $config['section_design_photo_min_limit'] = $app_info['platform.album.app_info.each_space_photo.min_limit'];
        //各空间产品应用图最低数量
        $config['section_product_photo_min_limit'] = $app_info['platform.album.app_info.each_space_product_app_photo.min_limit'];
        //各空间施工图最低数量
        $config['section_build_photo_min_limit'] = $app_info['platform.album.app_info.each_space_build_photo.min_limit'];

        return $config;
    }

}
