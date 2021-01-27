<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticAccountDealer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticAlbumService
{


    private $albums;
    private $designers;
    private $sellers;
    private $handled_brand_ids = array();

    public function __construct()
    {
        $this->albums = Album::where('status',Album::STATUS_PASS)->get();
        $this->designers = Designer::where('organization_type','<>',Designer::ORGANIZATION_TYPE_NONE)->get();
        $this->sellers = OrganizationDealer::where('status',OrganizationDealer::STATUS_ON)
            ->get();
    }

    /**
     * 处理
     */
    public function update()
    {
        set_time_limit(1800);

        try{

            \Log::info('------------计算方案统计------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $sellers = $this->sellers;
            $album_counts = count($sellers);
            $sellers = collect($sellers)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);

            $multipleData = [];

            foreach($sellers as $chunk){

                foreach($chunk as $v){

                    $count_seller_album = $this->count_seller_album($v->id);

                    //销售商统计
                    $multipleData[] = [
                        'belong_brand_id'=> 0,
                        'belong_dealer_id'=> $v->id,
                        'count_album' => $count_seller_album['count_album'],
                        'count_album_top' => $count_seller_album['count_album_top'],
                        'created_at' =>Carbon::now(),
                    ];

                    $brand_id = $v->p_brand_id;

                    if(!in_array($brand_id,$this->handled_brand_ids)){
                        $count_brand_album = $this->count_brand_album($v->p_brand_id);

                        //品牌统计
                        $multipleData[] = [
                            'belong_brand_id'=>$v->p_brand_id,
                            'belong_dealer_id'=> 0,
                            'count_album' => $count_brand_album['count_album'],
                            'count_album_top' => $count_brand_album['count_album_top'],
                            'created_at' =>Carbon::now(),
                        ];

                        array_push($this->handled_brand_ids,$brand_id);
                    }



                }

            }

            $foreach_end_time = microtime(true);


            //批量新增
            $update_start_time = microtime(true);
            $insert = DB::table('statistic_albums')->insert($multipleData);
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
            \Log::info('------------计算方案统计------------');

        }catch (\Exception $e){

            \Log::info('------------计算方案统计错误------------');
            \Log::info('------------计算方案统计错误------------');

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
     * 销售商方案数（仅包含销售商设计师方案）
     * @param $seller_id
     * @return mixed
     */
    public function count_seller_album($seller_id)
    {
        $result = array();
        $result['count_album'] = 0;
        $result['count_album_top'] = 0;

        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_id)
            ->pluck('id')->toArray();

        //方案数
        $count_album = $this->albums
            ->whereIn('designer_id',$seller_designer_ids)
            ->where('status',Album::STATUS_PASS);

        $result['count_album'] = $count_album->count();

        //方案置顶数
        $count_album_top = $count_album
            ->sum(function($album){
                return $album->top_status_brand + $album->top_status_platform + $album->top_status_dealer;
            });

        $result['count_album_top'] = $count_album_top;

        return $result;

    }

    /**
     * 品牌方案数（包含品牌设计师、销售商设计师方案）
     * @param $seller_id
     * @return mixed
     */
    public function count_brand_album($brand_id)
    {
        $result = array();
        $result['count_album'] = 0;
        $result['count_album_top'] = 0;

        $brand_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id)
            ->pluck('id')->toArray();

        $seller_ids = $this->sellers
            ->where('p_brand_id',$brand_id)->where('status',OrganizationDealer::STATUS_ON)
            ->pluck('id')->toArray();

        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_ids)
            ->pluck('id')->toArray();

        $designer_ids = collect(array_merge($brand_designer_ids,$seller_designer_ids))->unique()->values()->all();

        $count_album = $this->albums
            ->whereIn('designer_id',$designer_ids)
            ->where('status',Album::STATUS_PASS); //必须是已审核通过的;

        $result['count_album'] = $count_album->count();

        //方案置顶数
        $count_album_top = $count_album
            ->sum(function($album){
                return $album->top_status_brand + $album->top_status_platform + $album->top_status_dealer;
            });

        $result['count_album_top'] = $count_album_top;

        return $result;
    }



}