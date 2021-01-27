<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticAccountDealer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticProductCeramicService
{

    private $album_products;

    public function __construct()
    {

        $this->album_products = DB::table('albums as a')
            ->select(['apc.album_id','apc.product_ceramic_id',
                'a.top_status_platform','a.top_status_brand','a.top_status_dealer',
                'a.top_status_designer'])
            ->join('album_product_ceramics as apc','apc.album_id','=','a.id')
            ->join('product_ceramics as p','apc.product_ceramic_id','=','p.id')
            ->where('a.status',Album::STATUS_PASS)
            ->where('p.status',ProductCeramic::STATUS_PASS)
            ->get();
    }

    /**
     * 更新所有产品的统计数据
     */
    public function update()
    {
        set_time_limit(1800);

        try{

            \Log::info('------------计算产品统计------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $datas = ProductCeramic::where('status',ProductCeramic::STATUS_PASS)->get();
            $data_counts = count($datas);
            $datas = collect($datas)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);

            $multipleData = [];

            foreach($datas as $chunk){

                foreach($chunk as $v){

                    $count_seller_album = $this->count_seller_album($v->id);

                    $multipleData[] = [
                        'product_id'=> $v->id,
                        'count_album' => $count_seller_album['count_album'],
                        'count_album_top' => $count_seller_album['count_album_top'],
                        'created_at' =>Carbon::now(),
                    ];

                }

            }

            $foreach_end_time = microtime(true);


            //批量新增
            $update_start_time = microtime(true);
            $insert = DB::table('statistic_product_ceramics')->insert($multipleData);
            $update_end_time = microtime(true);
            $update_all_time+= $update_end_time-$update_start_time;


            $end_time = microtime(true);
            $all_use_time = ($end_time - $start_time) * 1000;
            $select_use_time = ($select_end_time - $select_start_time) * 1000;
            $foreach_use_time = ($foreach_end_time - $foreach_start_time) * 1000;
            $update_use_time = $update_all_time * 1000;
            $end_memory = memory_get_usage();
            $use_memory = $end_memory - $start_memory;

            \Log::info("数据量：{$data_counts}条");
            \Log::info("[总执行时间：{$all_use_time}]毫秒");
            //\Log::info("[查询执行时间：{$select_use_time}]毫秒");
            \Log::info("[foreach处理执行时间：{$foreach_use_time}]毫秒");
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算产品统计------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算产品统计错误',array(
                $e->getTraceAsString()
            ));

        }


    }


    /**
     * 处理
     * @param $seller_id
     * @return mixed
     */
    public function count_seller_album($product_id)
    {
        $result = array();
        $result['count_album'] = 0;
        $result['count_album_top'] = 0;

        //方案数
        $count_album = $this->album_products
            ->where('product_ceramic_id',$product_id);

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