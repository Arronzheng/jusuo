<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\LogAlbumDownload;
use App\Models\LogAlbumTop;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticDesigner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\DirectoryExists;

class StatisticDesignerService
{
    private $designer_id = 0;
    static $upload_album_data = null;
    static $top_album_data = null;
    static $fav_album_data = null;
    static $praise_album_data = null;
    static $download_album_data = null;
    static $copy_album_data = null;
    static $fav_designer_data = null;

    private $day_1_range = null;
    private $day_7_range = null;
    private $day_30_range = null;

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
    }

    /**
     * 更新设计师统计表中的所有数据
     */
    public function update()
    {
        set_time_limit(1800);

        try{

            \Log::info('------------计算设计师统计表中的所有统计数据------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $designers = Designer::where('status',Designer::STATUS_ON)
                ->get();
            $designer_counts = count($designers);
            $designers = collect($designers)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);
            $multipleData = [];

            $service = new StatisticDesignerService();

            foreach($designers as $chunk){

                foreach($chunk as $v){


                    $count_upload_album = $service->count_upload_album($v->id);
                    $count_top_album = $service->count_top_album($v->id);
                    $count_fav_album = $service->count_fav_album($v->id);
                    $count_praise_album = $service->count_praise_album($v->id);
                    $count_download_album = $service->count_download_album($v->id);
                    $count_copy_album = $service->count_copy_album($v->id);
                    $count_fav_designer = $service->count_fav_designer($v->id);
                    $count_faved_designer = $service->count_faved_designer($v->id);

                    $count_upload_album_day_1 = $service->count_upload_album($v->id,$this->day_1_range);
                    $count_upload_album_day_7 = $service->count_upload_album($v->id,$this->day_7_range);
                    $count_upload_album_day_30 = $service->count_upload_album($v->id,$this->day_30_range);
                    $count_top_album_day_1 = $service->count_top_album($v->id,$this->day_1_range);
                    $count_top_album_day_7 = $service->count_top_album($v->id,$this->day_7_range);
                    $count_top_album_day_30 = $service->count_top_album($v->id,$this->day_30_range);
                    $count_fav_album_day_1 = $service->count_fav_album($v->id,$this->day_1_range);
                    $count_fav_album_day_7 = $service->count_fav_album($v->id,$this->day_7_range);
                    $count_fav_album_day_30 = $service->count_fav_album($v->id,$this->day_30_range);
                    $count_praise_album_day_1 = $service->count_praise_album($v->id,$this->day_1_range);
                    $count_praise_album_day_7 = $service->count_praise_album($v->id,$this->day_7_range);
                    $count_praise_album_day_30 = $service->count_praise_album($v->id,$this->day_30_range);
                    $count_download_album_day_1 = $service->count_download_album($v->id,$this->day_1_range);
                    $count_download_album_day_7 = $service->count_download_album($v->id,$this->day_7_range);
                    $count_download_album_day_30 = $service->count_download_album($v->id,$this->day_30_range);
                    $count_copy_album_day_1 = $service->count_copy_album($v->id,$this->day_1_range);
                    $count_copy_album_day_7 = $service->count_copy_album($v->id,$this->day_7_range);
                    $count_copy_album_day_30 = $service->count_copy_album($v->id,$this->day_30_range);
                    $count_fav_designer_day_1 = $service->count_fav_designer($v->id,$this->day_1_range);
                    $count_fav_designer_day_7 = $service->count_fav_designer($v->id,$this->day_7_range);
                    $count_fav_designer_day_30 = $service->count_fav_designer($v->id,$this->day_30_range);
                    $count_faved_designer_day_1 = $service->count_faved_designer($v->id,$this->day_1_range);
                    $count_faved_designer_day_7 = $service->count_faved_designer($v->id,$this->day_7_range);
                    $count_faved_designer_day_30 = $service->count_faved_designer($v->id,$this->day_30_range);


                    $tempData = [
                        'designer_id'=>$v->id,
                        'count_upload_album' =>$count_upload_album,
                        'count_top_album' =>$count_top_album,
                        'count_fav_album' =>$count_fav_album,
                        'count_praise_album' =>$count_praise_album,
                        'count_download_album' =>$count_download_album,
                        'count_copy_album' =>$count_copy_album,
                        'count_fav_designer' =>$count_fav_designer,
                        'count_faved_designer' =>$count_faved_designer,

                        'count_upload_album_day_1' =>$count_upload_album_day_1,
                        'count_upload_album_day_7' =>$count_upload_album_day_7,
                        'count_upload_album_day_30' =>$count_upload_album_day_30,
                        'count_top_album_day_1' =>$count_top_album_day_1,
                        'count_top_album_day_7' =>$count_top_album_day_7,
                        'count_top_album_day_30' =>$count_top_album_day_30,
                        'count_fav_album_day_1' =>$count_fav_album_day_1,
                        'count_fav_album_day_7' =>$count_fav_album_day_7,
                        'count_fav_album_day_30' =>$count_fav_album_day_30,
                        'count_praise_album_day_1' =>$count_praise_album_day_1,
                        'count_praise_album_day_7' =>$count_praise_album_day_7,
                        'count_praise_album_day_30' =>$count_praise_album_day_30,
                        'count_download_album_day_1' =>$count_download_album_day_1,
                        'count_download_album_day_7' =>$count_download_album_day_7,
                        'count_download_album_day_30' =>$count_download_album_day_30,
                        'count_copy_album_day_1' =>$count_copy_album_day_1,
                        'count_copy_album_day_7' =>$count_copy_album_day_7,
                        'count_copy_album_day_30' =>$count_copy_album_day_30,
                        'count_fav_designer_day_1' =>$count_fav_designer_day_1,
                        'count_fav_designer_day_7' =>$count_fav_designer_day_7,
                        'count_fav_designer_day_30' =>$count_fav_designer_day_30,
                        'count_faved_designer_day_1' =>$count_faved_designer_day_1,
                        'count_faved_designer_day_7' =>$count_faved_designer_day_7,
                        'count_faved_designer_day_30' =>$count_faved_designer_day_30,

                        'created_at' =>Carbon::now(),
                    ];

                    $multipleData[] = $tempData;
                }
            }

            $foreach_end_time = microtime(true);

            $update_start_time = microtime(true);
            //批量更新
            $insert = DB::table('statistic_designers')->insert($multipleData);
            $update_end_time = microtime(true);
            $update_all_time+= $update_end_time-$update_start_time;


            $end_time = microtime(true);
            $all_use_time = ($end_time - $start_time) * 1000;
            $select_use_time = ($select_end_time - $select_start_time) * 1000;
            $foreach_use_time = ($foreach_end_time - $foreach_start_time) * 1000;
            $update_use_time = $update_all_time * 1000;
            $end_memory = memory_get_usage();
            $use_memory = $end_memory - $start_memory;

            \Log::info("数据量：{$designer_counts}条");
            \Log::info("[总执行时间：{$all_use_time}]毫秒");
            //\Log::info("[查询执行时间：{$select_use_time}]毫秒");
            \Log::info("[foreach处理执行时间：{$foreach_use_time}]毫秒");
            \Log::info("[更新数据执行时间：{$update_use_time}]毫秒");
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算设计师统计表中的所有统计数据------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算设计师统计表中的所有统计数据错误',array(
                $e->getMessage(),
                $e->getTraceAsString()
            ));

        }


    }


    public static function get_statistic_log($designer_id)
    {
        $stat = StatisticDesigner::where('designer_id',$designer_id)->first();
        if(!$stat){
            $designer = Designer::find($designer_id);
            $belong_brand_id = 0;
            $belong_dealer_id = 0;
            switch ($designer->organization_type){
                case Designer::ORGANIZATION_TYPE_BRAND:
                    $belong_brand_id = $designer->organization_id;
                    break;
                case Designer::ORGANIZATION_TYPE_SELLER:
                    $belong_dealer_id = $designer->organization_id;
                    break;
                default:break;
            }
            $stat = new StatisticDesigner();
            $stat->designer_id = $designer_id;
            $stat->belong_brand_id = $belong_brand_id;
            $stat->belong_dealer_id = $belong_dealer_id;
            $stat->save();
        }
        return $stat;
    }

    //筛选符合条件的数据数量
    private function filter_data($data,$designer_id,$start_time=null,$end_time=null)
    {

        $filtered = $data->filter(function ($item) use($start_time,$end_time,$designer_id) {
            $time = strtotime($item->compare_time);
            if($designer_id == $item->designer_id){
                if($start_time!==null){
                    if($start_time < $time && $time < $end_time){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return true;
                }
            }
            return false;
        });

        $count = count($filtered->all());

        return $count;
    }


    /**
     * 上传的方案数
     * @param $designer_id
     * @return mixed
     */
    public function count_upload_album($designer_id,$dateTimeRange=null)
    {
        if(!self::$upload_album_data){
            $entry = Album::query()
                ->select(['designer_id','created_at as compare_time'])
                ->where('period_status',Album::PERIOD_STATUS_FINISH); //必须是已完成的
                //->whereBetween('created_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$upload_album_data = $entry->get();
        }

        $data = self::$upload_album_data;


        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

    /**
     * 方案被置顶次数
     * @param $designer_id
     * @return mixed
     */
    public function count_top_album($designer_id,$dateTimeRange=null)
    {
        if(!self::$top_album_data){
            $entry = DB::table('log_album_tops as log')
                ->select(['log.operator_id as designer_id','log.updated_at as compare_time'])
                ->join('albums as a','a.id','=','log.album_id')
                ->where('a.period_status',Album::PERIOD_STATUS_FINISH) //必须是已审核通过的
                ->where('log.op_type',LogAlbumTop::OP_TYPE_TOP)  //置顶动作
                ->where('log.organization_type','<>',OrganizationService::ORGANIZATION_TYPE_DESIGNER);  //设计师自己的置顶不算
                //->whereBetween('log.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$top_album_data = $entry->get();
        }

        $data = self::$top_album_data;

        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

    /**
     * 关注的方案数
     * @param $designer_id
     * @return mixed
     */
    public function count_fav_album($designer_id,$dateTimeRange=null)
    {

        if(!self::$fav_album_data) {

            $entry = DB::table('fav_albums as fa')
                ->select(['fa.designer_id','fa.updated_at as compare_time'])
                ->join('albums as a','a.id','=','fa.album_id')
                ->where('a.period_status',Album::PERIOD_STATUS_FINISH); //必须是已审核通过的
                //->whereBetween('fa.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$fav_album_data = $entry->get();
        }


        $data = self::$fav_album_data;


        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

    /**
     * 点赞的方案数
     * @param $designer_id
     * @return mixed
     */
    public function count_praise_album($designer_id,$dateTimeRange=null)
    {
        if(!self::$praise_album_data) {
            $entry = DB::table('like_albums as log')
                ->select(['log.designer_id','log.updated_at as compare_time'])
                ->join('albums as a','a.id','=','log.album_id')
                ->where('a.period_status',Album::PERIOD_STATUS_FINISH); //必须是已审核通过的
                //->whereBetween('log.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$praise_album_data = $entry->get();
        }

        $data = self::$praise_album_data;

        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);


        return $count;
    }

    /**
     * 下载的方案数（高清图）
     * @param $designer_id
     * @return mixed
     */
    public function count_download_album($designer_id,$dateTimeRange=null)
    {

        if(!self::$download_album_data) {
            $entry = DB::table('log_album_downloads as log')
                ->select(['log.designer_id','log.updated_at as compare_time'])
                ->join('albums as a','a.id','=','log.album_id')
                ->where('a.type',Album::TYPE_HD_PHOTO)  //高清图
                ->where('log.op_type',LogAlbumDownload::OP_TYPE_DOWNLOAD)  //下载
                ->where('a.period_status',Album::PERIOD_STATUS_FINISH); //必须是已审核通过的
                //->whereBetween('log.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$download_album_data = $entry->get();
        }

        $data = self::$download_album_data;

        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

    /**
     * 复制的方案数（酷家乐方案源）
     * @param $designer_id
     * @return mixed
     */
    public function count_copy_album($designer_id,$dateTimeRange=null)
    {

        if(!self::$copy_album_data) {
            $entry = DB::table('log_album_downloads as log')
                ->select(['log.designer_id','log.updated_at as compare_time'])
                ->join('albums as a','a.id','=','log.album_id')
                ->where('a.type',Album::TYPE_KUJIALE_SOURCE)  //高清图
                ->where('log.op_type',LogAlbumDownload::OP_TYPE_COPY)  //复制
                ->where('a.period_status',Album::PERIOD_STATUS_FINISH); //必须是已审核通过的
                //->whereBetween('log.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$copy_album_data = $entry->get();
        }

        $data = self::$copy_album_data;

        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

    /**
     * 关注的设计师数（此设计师关注了多少个人）
     * @param $designer_id
     * @return mixed
     */
    public function count_fav_designer($designer_id,$dateTimeRange=null)
    {

        if(!self::$fav_designer_data) {
            $entry = DB::table('fav_designers as fav')
                ->select(['fav.designer_id','fav.updated_at as compare_time']);
                //->whereBetween('fav.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$fav_designer_data = $entry->get();
        }

        $data = self::$fav_designer_data;

        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

    /**
     * 关注的设计师数（此设计师被多少个人关注了）
     * @param $designer_id
     * @return mixed
     */
    public function count_faved_designer($designer_id,$dateTimeRange=null)
    {

        if(!self::$fav_designer_data) {
            $entry = DB::table('fav_designers as fav')
                ->select(['fav.target_designer_id as designer_id','fav.updated_at as compare_time']);
                //->whereBetween('fav.updated_at', array($this->day_30_range[0],$this->day_30_range[1]));
            self::$fav_designer_data = $entry->get();
        }

        $data = self::$fav_designer_data;

        $start_time = null;
        $end_time = null;
        if($dateTimeRange!==null){
            $start_time = strtotime($dateTimeRange[0]);
            $end_time = strtotime($dateTimeRange[1]);
        }

        $count = $this->filter_data($data,$designer_id,$start_time,$end_time);

        return $count;
    }

}