<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\StatisticAccountBrand;
use App\Models\VisitDesigner;
use Illuminate\Support\Facades\DB;

class AlbumColumnStatisticService
{

    private $albums;
    private $point_focus_config = null;

    public function __construct()
    {
        $this->albums = Album::all();

        $point_focus_config = ParamConfigUseService::find_root('platform.album.sys_info.focus.cal_rule');
        if(isset($point_focus_config['read_ratio'])
            && isset($point_focus_config['like_ratio'])
            && isset($point_focus_config['share_ratio'])
            && isset($point_focus_config['collection_ratio']))
        {
            $point_focus_config['read_ratio'] = floatval($point_focus_config['read_ratio']);
            $point_focus_config['like_ratio'] = floatval($point_focus_config['like_ratio']);
            $point_focus_config['share_ratio'] = floatval($point_focus_config['share_ratio']);
            $point_focus_config['collection_ratio'] = floatval($point_focus_config['collection_ratio']);
            $this->point_focus_config = $point_focus_config;
        }
        return false;
    }

    /**
     * 更新所有方案的统计字段数据
     */
    public function update()
    {
        set_time_limit(300);

        try{

            \Log::info('------------计算方案主表、详情表统计字段------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $datas = Album::where('status',Album::STATUS_PASS)
                ->get();
            $album_counts = count($datas);
            $datas = collect($datas)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);
            foreach($datas as $chunk){

                $albumData = [];
                foreach($chunk as $v){
                    $point_focus = $this->cal_point_focus($v->id);

                    if($point_focus!==false){
                        $albumTemp = [];
                        $albumTemp['id'] = $v->id;
                        $albumTemp['point_focus'] = $point_focus;
                        $albumData[] = $albumTemp;
                    }

                }
                $update_start_time = microtime(true);
                //批量更新

                if(count($albumData)>0){
                    DBService::updateBatch('albums',$albumData);
                }
                $update_end_time = microtime(true);
                $update_all_time+= $update_end_time-$update_start_time;

            }
            $foreach_end_time = microtime(true);

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
            \Log::info('------------计算方案主表、详情表统计字段------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算方案主表、详情表统计字段错误',array(
                $e->getTraceAsString()
            ));

        }


    }


    /**
     * 计算方案关注度
     * @param $brand_id
     * @return mixed
     */
    public function cal_point_focus($album_id)
    {
        //方案关注度 = 浏览量 × [X] + 点赞量 × [Y] + 收藏量 × [Z] + 分享次数 × [A]
        if(!$this->point_focus_config){
            return false;
        }

        $album = $this->albums
            ->where('id',$album_id)
            ->first();

        $point_focus =
            $album->count_visit * $this->point_focus_config['read_ratio'] +
            $album->count_praise * $this->point_focus_config['like_ratio'] +
            $album->count_fav * $this->point_focus_config['collection_ratio'] +
            $album->count_share * $this->point_focus_config['share_ratio'] ;

        /*SystemLogService::simple('album->'.$album->id,array(
            $album->count_visit."/".$this->point_focus_config['read_ratio']."/".$album->count_visit * $this->point_focus_config['read_ratio'],
            $album->count_praise."/".$this->point_focus_config['like_ratio']."/".$album->count_praise * $this->point_focus_config['like_ratio'],
            $album->count_fav."/".$this->point_focus_config['collection_ratio']."/".$album->count_fav * $this->point_focus_config['collection_ratio'],
            $album->count_share."/".$this->point_focus_config['share_ratio']."/".$album->count_share * $this->point_focus_config['share_ratio'],
            floor($point_focus)
        ));*/

        return floor($point_focus);
    }

}