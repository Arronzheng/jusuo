<?php
/**
 * Created by PhpStorm.
 * User: cwq53
 * Date: 2020/2/19
 * Time: 11:46
 */

namespace App\Http\Controllers\v1\site\center\album_list;

use App\Http\Services\v1\admin\AuthService;
use App\Models\Album;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\HouseType;
use App\Models\SpaceType;
use App\Models\Style;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlbumListController extends ApiController{

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



        $styles = Style::all();
        $house_types = HouseType::all();
        $space_type = SpaceType::all();
        $count_area = [
            ['id'=>'0_50','name'=>'50㎡及以下'],
            ['id'=>'50_80','name'=>'50-80㎡'],
            ['id'=>'80_100','name'=>'80-100㎡'],
            ['id'=>'100_130','name'=>'100-130㎡'],
            ['id'=>'130_m','name'=>'130㎡及以上'],
        ];
        $status = Album::statusGroup();
        $status_arr = [];
        foreach($status as $k => $v){
            $arr['id'] = $k;
            $arr['name'] = $v;
            array_push($status_arr,$arr);
        }
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



        //
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


        $time_info = array();
        $time_info['title'] = '上传时间';
        $time_info['value'] = 'time';
        $time_info['has_selected'] = false;

        if(isset($query_array[$time_info['value']])){
            $type_query = $query_array[$time_info['value']];
            for($i=0;$i<count($upload_time);$i++){
                if($upload_time[$i]->id == $type_query){
                    $time_info['has_selected'] = true;
                    $upload_time[$i]['selected'] = 1;
                }else{
                    $upload_time[$i]['selected'] = 0;
                }
            }
        }
        $time_info['data'] = $upload_time;


        $style_info = array();
        $style_info['title'] = '风格';
        $style_info['value'] = 'stl';
        $style_info['has_selected'] = false;
        if(isset($query_array[$style_info['value']])){
            $type_query = $query_array[$style_info['value']];
            for($i=0;$i<count($styles);$i++){
                if($styles[$i]->id == $type_query){
                    $styles[$i]->selected = 1;
                    $style_info['has_selected'] = true;
                }else{
                    $styles[$i]->selected = 0;
                }
            }
        }
        $style_info['data'] = $styles;


        //
        $house_type_info = array();
        $house_type_info['title'] = '户型';
        $house_type_info['value'] = 'ht';
        $house_type_info['has_selected'] = false;
        if(isset($query_array[$house_type_info['value']])){
            $type_query = $query_array[$house_type_info['value']];
            for($i=0;$i<count($house_types);$i++){
                if($house_types[$i]->id == $type_query){
                    $house_types[$i]->selected = 1;
                    $house_type_info['has_selected'] = true;
                }else{
                    $house_types[$i]->selected = 0;
                }
            }
        }
        $house_type_info['data'] = $house_types;



        $space_type_info = array();
        $space_type_info['title'] = '空间';
        $space_type_info['value'] = 'spt';
        $space_type_info['has_selected'] = false;
        if(isset($query_array[$space_type_info['value']])){
            $type_query = $query_array[$space_type_info['value']];
            for($i=0;$i<count($space_type);$i++){
                if($space_type[$i]->id == $type_query){
                    $space_type[$i]->selected = 1;
                    $space_type_info['has_selected'] = true;

                }else{
                    $space_type[$i]->selected = 0;
                }
            }
        }
        $space_type_info['data'] = $space_type;


        $count_area_info = array();
        $count_area_info['title'] = '面积';
        $count_area_info['value'] = 'ca';
        $count_area_info['has_selected'] = false;

        if(isset($query_array[$count_area_info['value']])){
            $type_query = $query_array[$count_area_info['value']];
            for($i=0;$i<count($count_area);$i++){
                if($count_area[$i]['id'] == $type_query){
                    $count_area[$i]['selected'] = 1;
                    $count_area_info['has_selected'] = true;

                }else{
                    $count_area[$i]['selected'] = 0;
                }
            }
        }
        $count_area_info['data'] = $count_area;



        $result[] = $style_info;
        $result[] = $house_type_info;
        $result[] = $space_type_info;
        $result[] = $count_area_info;
        $result[] = $status_info;
        $result[] = $time_info;

        return $this->respDataReturn($result);

    }

    public function index(Request $request){
        $designer = Auth()->user();

        $builder = Album::query()
            ->join('search_albums as sa','sa.album_id','=','albums.id')
            ->select(['albums.id','albums.type','albums.code','sa.web_id_code','albums.designer_id',
                'albums.photo_cover','albums.count_area','albums.title','albums.count_visit',
                'albums.count_praise','albums.count_fav','albums.status','albums.period_status','albums.period_status','albums.count_use','albums.created_at'])
            ->has('designer.detail')
            ->with(['designer'=>function($designer){
                $designer->select(['id']);
                $designer->with(['detail'=>function($designer_detail){
                    $designer_detail->select(['id','designer_id','approve_realname','top_status','nickname','url_avatar']);
                }]);
            }]);

        //是否提交名称
        if($title = $request->input('title','')){
            $builder->where('albums.title','like','%'.$title.'%');
        }

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

        //是否体提交产品编号
        if($product_code = $request->input('product_no',null)){
            $builder->whereHas('product_ceramics',function($query) use ($product_code){
                $query->where('product_ceramics.code',$product_code);
            });
        }

        //是否提交审核状态
        if($status = $request->input('status',null)){
            $builder->where('albums.status',$status);
        }

        //是否提交风格
        if($style = $request->input('stl',null)){
            $builder->whereHas('style',function($query) use ($style){
                $query->where('styles.id',$style);
            });
            $builder->where('sa.style','like','%|'.$style.'|%');

        }

        //是否提交户型
        if($house_type = $request->input('ht',null)){
            $builder->whereHas('house_types',function($query) use ($house_type){
                $query->where('house_types.id',$house_type);
            });
            //$builder->where('sa.house_type','like','%|'.$house_type.'|%');
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

        $builder->where('status','<>',Album::STATUS_DELETE);

        $albums = $builder->where('albums.designer_id',$designer->id)->get();
        //$albums = $builder->get();

        $albums->transform(function($v) use($designer){

            //"identity":true,"hot":true,"liked":false,"collect":false



            $v->hot = false;
            if($v->designer->detail->top_status == DesignerDetail::TOP_STATUS_YES){
                $v->hot = true;
            }

            if(!Str::startsWith($v->photo_cover,['http://','https://'])){
                $v->photo_cover = url($v->photo_cover);
            }

            $v->house_type_text = (count($v->house_types)>0) ? $v->house_types()->first()->value('name') : '';
            $v->style_text = (count( $v->style)>0) ? $v->style()->first()->value('name') : '';

            $v->statusText = Album::statusGroup($v->status);

            unset($v->id);

            return $v;
        });


        return $this->respDataReturn($albums);
    }
}