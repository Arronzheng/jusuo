<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\StatisticAccountBrand;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticAccountBrandService
{
    private $day_1_range = null;
    private $day_7_range = null;
    private $day_30_range = null;

    private $product_ceramics;
    private $albums;
    private $designers;
    private $sellers;

    public function __construct()
    {

        //区间时间
        $now_time = time();
        $this->day_1_range = [];
        $this->day_1_range[0] = date("Y-m-d H:i:s",strtotime("-1days",$now_time));
        $this->day_1_range[1] = date('Y-m-d H:i:s',$now_time);

        $this->day_7_range = [];
        $this->day_7_range[0] = date("Y-m-d H:i:s",strtotime("-7days",$now_time));
        $this->day_7_range[1] = date('Y-m-d H:i:s',$now_time);

        $this->day_30_range = [];
        $this->day_30_range[0] = date("Y-m-d H:i:s",strtotime("-30days",$now_time));
        $this->day_30_range[1] = date('Y-m-d H:i:s',$now_time);

        $this->product_ceramics = ProductCeramic::query()
            ->where('status','<>',ProductCeramic::STATUS_REJECT)
            ->get();
        $this->albums = Album::where('status',Album::STATUS_PASS)->get();
        $this->designers = Designer::where('organization_type','<>',Designer::ORGANIZATION_TYPE_NONE)->get();
        $this->sellers = OrganizationDealer::get();
    }

    /**
     * 对于statistic_account_brand表
     * 更新所有品牌的非区间统计数据
     */
    public function update()
    {
        set_time_limit(1800);

        try{

            \Log::info('------------计算品牌统计------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $brands = OrganizationBrand::where('status',OrganizationBrand::STATUS_ON)
                ->get();
            $album_counts = count($brands);
            $brands = collect($brands)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);
            $multipleData = [];


            foreach($brands as $chunk){

                foreach($chunk as $v){

                    $count_product = $this->count_product($v->id);
                    $count_product_day_1 = $this->count_product($v->id,$this->day_1_range);
                    $count_product_day_7 = $this->count_product($v->id,$this->day_7_range);
                    $count_product_day_30 = $this->count_product($v->id,$this->day_30_range);
                    $count_album = $this->count_album($v->id);
                    $count_album_day_1 = $this->count_album($v->id,$this->day_1_range);
                    $count_album_day_7 = $this->count_album($v->id,$this->day_7_range);
                    $count_album_day_30 = $this->count_album($v->id,$this->day_30_range);
                    $count_designer = $this->count_designer($v->id);
                    $count_designer_day_1 = $this->count_designer($v->id,$this->day_1_range);
                    $count_designer_day_7 = $this->count_designer($v->id,$this->day_7_range);
                    $count_designer_day_30 = $this->count_designer($v->id,$this->day_30_range);
                    $count_designer_lv1 = $this->count_designer_lv1($v->id);
                    $count_designer_lv1_day_1 = $this->count_designer_lv1($v->id,$this->day_1_range);
                    $count_designer_lv1_day_7 = $this->count_designer_lv1($v->id,$this->day_7_range);
                    $count_designer_lv1_day_30 = $this->count_designer_lv1($v->id,$this->day_30_range);
                    $count_designer_lv2 = $this->count_designer_lv2($v->id);
                    $count_designer_lv2_day_1 = $this->count_designer_lv2($v->id,$this->day_1_range);
                    $count_designer_lv2_day_7 = $this->count_designer_lv2($v->id,$this->day_7_range);
                    $count_designer_lv2_day_30 = $this->count_designer_lv2($v->id,$this->day_30_range);
                    $count_dealer_lv1 = $this->count_dealer_lv1($v->id);
                    $count_dealer_lv1_day_1 = $this->count_dealer_lv1($v->id,$this->day_1_range);
                    $count_dealer_lv1_day_7 = $this->count_dealer_lv1($v->id,$this->day_7_range);
                    $count_dealer_lv1_day_30 = $this->count_dealer_lv1($v->id,$this->day_30_range);
                    $count_dealer_lv2 = $this->count_dealer_lv2($v->id);
                    $count_dealer_lv2_day_1 = $this->count_dealer_lv2($v->id,$this->day_1_range);
                    $count_dealer_lv2_day_7 = $this->count_dealer_lv2($v->id,$this->day_7_range);
                    $count_dealer_lv2_day_30 = $this->count_dealer_lv2($v->id,$this->day_30_range);

                    $multipleData[] = [
                        'brand_id'=>$v->id,
                        'count_product' =>$count_product,
                        'count_product_increase_day_1' =>$count_product_day_1,
                        'count_product_increase_day_7' =>$count_product_day_7,
                        'count_product_increase_day_30' =>$count_product_day_30,
                        'count_album' =>$count_album,
                        'count_album_increase_day_1' =>$count_album_day_1,
                        'count_album_increase_day_7' =>$count_album_day_7,
                        'count_album_increase_day_30' =>$count_album_day_30,
                        'count_designer' =>$count_designer,
                        'count_designer_increase_day_1' =>$count_designer_day_1,
                        'count_designer_increase_day_7' =>$count_designer_day_7,
                        'count_designer_increase_day_30' =>$count_designer_day_30,
                        'count_designer_lv1' =>$count_designer_lv1,
                        'count_designer_lv1_increase_day_1' =>$count_designer_lv1_day_1,
                        'count_designer_lv1_increase_day_7' =>$count_designer_lv1_day_7,
                        'count_designer_lv1_increase_day_30' =>$count_designer_lv1_day_30,
                        'count_designer_lv2' =>$count_designer_lv2,
                        'count_designer_lv2_increase_day_1' =>$count_designer_lv2_day_1,
                        'count_designer_lv2_increase_day_7' =>$count_designer_lv2_day_7,
                        'count_designer_lv2_increase_day_30' =>$count_designer_lv2_day_30,
                        'count_dealer_lv1' =>$count_dealer_lv1,
                        'count_dealer_lv1_increase_day_1' =>$count_dealer_lv1_day_1,
                        'count_dealer_lv1_increase_day_7' =>$count_dealer_lv1_day_7,
                        'count_dealer_lv1_increase_day_30' =>$count_dealer_lv1_day_30,
                        'count_dealer_lv2' =>$count_dealer_lv2,
                        'count_dealer_lv2_increase_day_1' =>$count_dealer_lv2_day_1,
                        'count_dealer_lv2_increase_day_7' =>$count_dealer_lv2_day_7,
                        'count_dealer_lv2_increase_day_30' =>$count_dealer_lv2_day_30,
                        'created_at' =>Carbon::now(),
                    ];
                }

            }

            $foreach_end_time = microtime(true);

            //批量新增
            $update_start_time = microtime(true);
            $insert = DB::table('statistic_account_brands')->insert($multipleData);
            $update_end_time = microtime(true);
            $update_all_time+= $update_end_time-$update_start_time;


            $end_time = microtime(true);
            $all_use_time = ($end_time - $start_time) * 1000;
            $select_use_time = ($select_end_time - $select_start_time) * 1000;
            $foreach_use_time = ($foreach_end_time - $foreach_start_time) * 1000;
            $update_use_time = $update_all_time * 1000;
            $end_memory = memory_get_usage();
            $use_memory = $end_memory - $start_memory;

            \Log::info("数据量：{$album_counts}条");
            \Log::info("[总执行时间：{$all_use_time}]毫秒");
            //\Log::info("[查询执行时间：{$select_use_time}]毫秒");
            \Log::info("[foreach处理执行时间：{$foreach_use_time}]毫秒");
            \Log::info("[更新数据执行时间：{$update_use_time}]毫秒");
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算品牌统计------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算品牌统计错误',array(
                $e->getTraceAsString()
            ));

        }


    }


    public static function get_statistic_log($brand_id)
    {
        $stat = StatisticAccountBrand::where('brand_id',$brand_id)->first();
        if(!$stat){
            $stat = new StatisticAccountBrand();
            $stat->brand_id = $brand_id;
            $stat->save();
        }
        return $stat;
    }


    /**
     * 产品数
     * @param $brand_id
     * @return mixed
     */
    public function count_product($brand_id,$dateTimeRange=null)
    {
        $data = $this->product_ceramics
            ->where('brand_id',$brand_id);

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());





        return $count;
    }

    /**
     * 方案数（包含销售商设计师和品牌设计师的方案）
     * @param $brand_id
     * @return mixed
     */
    public function count_album($brand_id,$dateTimeRange=null)
    {
        //品牌设计师
        $brand_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id)
            ->pluck('id')->toArray();

        $seller_ids = $this->sellers
            ->where('p_brand_id',$brand_id)->where('status',OrganizationDealer::STATUS_ON)
            ->pluck('id')->toArray();

        //销售商设计师
        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_ids)
            ->pluck('id')->toArray();

        $designer_ids = collect(array_merge($brand_designer_ids,$seller_designer_ids))->unique()->values()->all();

        $data = $this->albums
            ->whereIn('designer_id',$designer_ids)
            ->where('status',Album::STATUS_PASS); //必须是已审核通过的;

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }

    /**
     * 直属设计师数
     * @param $brand_id
     * @return mixed
     */
    public function count_designer($brand_id,$dateTimeRange=null)
    {
        $data = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id);

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }


    /**
     * 一级销售商设计师数
     * @param $brand_id
     * @return mixed
     */
    public function count_designer_lv1($brand_id,$dateTimeRange=null)
    {
        $seller_lv1_ids = $this->sellers
            ->where('p_brand_id',$brand_id)
            //20200803->whereIn('status',[OrganizationDealer::STATUS_ON,OrganizationDealer::STATUS_WAIT_VERIFY])
            ->where('level',1)
            ->pluck('id')->toArray();

        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_lv1_ids)
            ->pluck('id')->toArray();

        $data = $this->designers
            ->whereIn('id',$seller_designer_ids);

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null) {
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }

    /**
     * 二级销售商设计师数
     * @param $brand_id
     * @return mixed
     */
    public function count_designer_lv2($brand_id,$dateTimeRange=null)
    {
        $seller_lv2_ids = $this->sellers
            ->where('p_brand_id',$brand_id)
            //20200803->whereIn('status',[OrganizationDealer::STATUS_ON,OrganizationDealer::STATUS_WAIT_VERIFY])
            ->where('level',2)
            ->pluck('id')->toArray();

        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_lv2_ids)
            ->pluck('id')->toArray();

        $data = $this->designers
            ->whereIn('id',$seller_designer_ids);

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }

    /**
     * 一级销售商数
     * @param $brand_id
     * @return mixed
     */
    public function count_dealer_lv1($brand_id,$dateTimeRange=null)
    {
        $data = $this->sellers
            ->where('p_brand_id',$brand_id)
            //20200803->whereIn('status',[OrganizationDealer::STATUS_ON,OrganizationDealer::STATUS_WAIT_VERIFY])
            ->where('level',1);

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null) {
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }

    /**
     * 二级销售商数
     * @param $brand_id
     * @return mixed
     */
    public function count_dealer_lv2($brand_id,$dateTimeRange=null)
    {
        $data = $this->sellers
            ->where('p_brand_id',$brand_id)
            //20200803->whereIn('status',[OrganizationDealer::STATUS_ON,OrganizationDealer::STATUS_WAIT_VERIFY])
            ->where('level',2);

        $start_time = null;
        $end_time = null;

        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $filtered = $data->filter(function ($item) use($start_time,$end_time) {
            $time = strtotime($item->created_at);
            if($start_time!==null){
                if($start_time < $time && $time < $end_time){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }

}