<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticAccountDealer;
use App\Services\v1\site\DesignerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticAccountDealerService
{

    private $day_1_range = null;
    private $day_7_range = null;
    private $day_30_range = null;

    private $product_ceramics_authorizations;
    private $albums;
    private $designers;

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

        $this->product_ceramics_authorizations = ProductCeramicAuthorization::query()
            ->where('status','<>',ProductCeramicAuthorization::STATUS_OFF)
            ->get();
        $this->albums = Album::where('status',Album::STATUS_PASS)->get();
        $this->designers = Designer::where('organization_type','<>',Designer::ORGANIZATION_TYPE_NONE)->get();
    }

    /**
     * 更新所有销售商的统计数据
     */
    public function update()
    {
        set_time_limit(1800);

        try{

            \Log::info('------------计算销售商统计------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $sellers = OrganizationDealer::where('status',OrganizationDealer::STATUS_ON)
                ->get();
            $album_counts = count($sellers);
            $sellers = collect($sellers)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);

            $multipleData = [];

            foreach($sellers as $chunk){

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

                    $multipleData[] = [
                        'dealer_id'=>$v->id,
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
                        'created_at' =>Carbon::now(),

                    ];
                }

            }

            $foreach_end_time = microtime(true);


            //批量新增
            $update_start_time = microtime(true);
            $insert = DB::table('statistic_account_dealers')->insert($multipleData);
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
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算销售商统计------------');

        }catch (\Exception $e){

            \Log::info('------------计算销售商统计错误------------');
            \Log::info('------------计算销售商统计错误------------');

        }


    }

    public static function get_statistic_log($seller_id)
    {
        $stat = StatisticAccountDealer::where('dealer_id',$seller_id)->first();
        if(!$stat){
            $stat = new StatisticAccountDealer();
            $stat->dealer_id = $seller_id;
            $stat->save();
        }
        return $stat;
    }


    /**
     * 上线产品数
     * @param $seller_id
     * @return mixed
     */
    public function count_product($seller_id,$dateTimeRange=null)
    {
        $data = $this->product_ceramics_authorizations
            ->where('dealer_id',$seller_id)
            ->where('status',ProductCeramicAuthorization::STATUS_ON);

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
     * 方案数（仅包含销售商设计师方案）
     * @param $seller_id
     * @return mixed
     */
    public function count_album($seller_id,$dateTimeRange=null)
    {
        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_id)
            ->pluck('id')->toArray();

        $data = $this->albums
            ->whereIn('designer_id',$seller_designer_ids)
            ->where('status',Album::STATUS_PASS);

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
     * @param $seller_id
     * @return mixed
     */
    public function count_designer($seller_id,$dateTimeRange=null)
    {
        $data = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->where('organization_id',$seller_id);
            //20200803->whereIn('status',[Designer::STATUS_ON,Designer::STATUS_VERIFYING]);

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