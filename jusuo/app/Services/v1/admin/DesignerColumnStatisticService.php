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

class DesignerColumnStatisticService
{

    private $designers;
    private $designer_details;
    private $albums;
    private $log_designer_visit;
    private $datetime_after;
    private $point_focus_config = null;

    public function __construct()
    {
        $this->designers = Designer::all();
        $this->albums = Album::all();
        $this->designer_details = DesignerDetail::select('id','designer_id','count_visit')->get();

        $point_focus_config = ParamConfigUseService::find_root('platform.sys_info.designer.focus.cal_rule');

        if(isset($point_focus_config['days'])
            && isset($point_focus_config['all_album_focus'])
            && isset($point_focus_config['index_read_count']))
        {
            $point_focus_config['days'] = intval($point_focus_config['days']);
            //访问数据应该获取哪个时间之后的
            $this->datetime_after = date("Y-m-d H:i:s",strtotime("-".$point_focus_config['days']."days",time()));

            $this->log_designer_visit = VisitDesigner::where('created_at','>',$this->datetime_after)->get();

            $point_focus_config['all_album_focus'] = floatval($point_focus_config['all_album_focus']);
            $point_focus_config['index_read_count'] = floatval($point_focus_config['index_read_count']);
            $this->point_focus_config = $point_focus_config;
        }

        /*return false;
        $point_focus_config['days'] = 30;
        //访问数据应该获取哪个时间之后的
        $this->datetime_after = date("Y-m-d H:i:s",strtotime("-".$point_focus_config['days']."days",time()));
        $this->log_designer_visit = VisitDesigner::where('created_at','>',$this->datetime_after)->get();*/

    }

    /**
     * 对于organization_brands和detail_brands表
     * 更新所有设计师的统计字段数据
     */
    public function update()
    {
        set_time_limit(300);

        try{

            \Log::info('------------计算设计师主表、详情表统计字段------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $datas = Designer::where('status',Designer::STATUS_ON)
                ->get();
            $data_counts = count($datas);
            $datas = collect($datas)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);
            foreach($datas as $chunk){

                $organizationData = [];
                $organizationDetailData = [];
                foreach($chunk as $v){
                    $point_focus = $this->cal_point_focus($v->id);
                    $stat = new StatisticDesignerService();
                    $albumCount = $stat->count_upload_album($v->id);
                    /*$organizationData[] = [
                        'id'=>$v->id,
                        'quota_designer_used' =>$quota_designer_used,
                    ];*/

                    if($point_focus!==false){
                        $organizationDetailTemp = [];
                        $organizationDetailTemp['designer_id'] = $v->id;
                        $organizationDetailTemp['point_focus'] = $point_focus;
                        $organizationDetailTemp['count_album'] = $albumCount;
                        $organizationDetailData[] = $organizationDetailTemp;
                    }

                }
                $update_start_time = microtime(true);
                //批量更新
                if(count($organizationData)>0){
                    DBService::updateBatch('designers',$organizationData);
                }
                if(count($organizationDetailData)>0){
                    DBService::updateBatch('designer_details',$organizationDetailData);
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

            \Log::info("数据量：{$data_counts}条");
            \Log::info("[总执行时间：{$all_use_time}]毫秒");
            //\Log::info("[查询执行时间：{$select_use_time}]毫秒");
            \Log::info("[foreach处理执行时间：{$foreach_use_time}]毫秒");
            \Log::info("[更新数据执行时间：{$update_use_time}]毫秒");
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算设计师主表、详情表统计字段------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算设计师主表、详情表统计字段错误',array(
                $e->getTraceAsString()
            ));

        }


    }



    /**
     * 计算设计师关注度
     * @param $brand_id
     * @return mixed
     */
    public function cal_point_focus($designer_id)
    {
        //设计师关注度 = 30天内的（ 该设计师所有方案关注度 × 1 + 预约设计数 × 1 + 设计师主页浏览量 × 1 ）

        if(!$this->point_focus_config){
            return false;
        }

        //该设计师所有方案关注度
        $all_album_point_focus =
            $this->albums->where('designer_id',$designer_id)
                ->where('created_at','>',$this->datetime_after)
                ->sum('point_focus');

        //设计师主页浏览量
        $designer_visit_count =
            $this->log_designer_visit->where('target_designer_id',$designer_id)
            ->count();

        //预约设计数暂无
        $point_focus = $all_album_point_focus + $designer_visit_count ;

        return floor($point_focus);
    }

}