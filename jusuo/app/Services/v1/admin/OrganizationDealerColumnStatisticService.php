<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavDealer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticAccountBrand;
use App\Models\VisitDealer;
use App\Models\VisitDesigner;
use Illuminate\Support\Facades\DB;

class OrganizationDealerColumnStatisticService
{

    private $designers;
    private $albums;
    private $log_designer_visit;
    private $product_ceramics_authorizations;
    private $fav_dealers;
    private $visit_dealers;
    private $datetime_after;
    private $point_focus_config = null;

    public function __construct()
    {
        $this->designers = Designer::all();
        $this->albums = Album::all();
        $this->product_ceramics_authorizations = ProductCeramicAuthorization::query()
            ->where('status','<>',ProductCeramicAuthorization::STATUS_OFF)
            ->get();
        $this->fav_dealers = FavDealer::all();
        $this->visit_dealers = VisitDealer::all();

        $point_focus_config = ParamConfigUseService::find_root('platform.sys_info.seller.focus.cal_rule');
        if(isset($point_focus_config['days'])
            && isset($point_focus_config['designer_album_sum'])
            && isset($point_focus_config['index_read_count']))
        {
            $point_focus_config['days'] = intval($point_focus_config['days']);
            //访问数据应该获取哪个时间之后的
            $this->datetime_after = date("Y-m-d H:i:s",strtotime("-".$point_focus_config['days']."days",time()));
            $this->log_designer_visit = VisitDesigner::where('created_at','>',$this->datetime_after)->get();

            $point_focus_config['designer_album_sum'] = floatval($point_focus_config['days']);
            $point_focus_config['index_read_count'] = floatval($point_focus_config['index_read_count']);
            $this->point_focus_config = $point_focus_config;
        }
        return false;
    }

    /**
     * 对于organization_brands和detail_brands表
     * 更新所有销售商的统计字段数据
     */
    public function update()
    {
        set_time_limit(300);

        try{

            \Log::info('------------计算销售商主表、详情表统计字段------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $datas = OrganizationDealer::where('status',OrganizationDealer::STATUS_ON)
                ->get();
            $album_counts = count($datas);
            $datas = collect($datas)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);
            foreach($datas as $chunk){

                $organizationData = [];
                $organizationDetailData = [];
                foreach($chunk as $v){
                    $quota_designer_used = $this->count_seller_designer($v->id);
                    $point_focus = $this->cal_point_focus($v->id);
                    $count_fav = $this->count_fav($v->id);
                    $count_album = $this->count_album($v->id);
                    $count_product = $this->count_product($v->id);
                    $count_visit = $this->count_visit($v->id);

                    $organizationData[] = [
                        'id'=>$v->id,
                        'quota_designer_used' =>$quota_designer_used,
                    ];

                    $organizationDetailTemp = [];
                    $organizationDetailTemp['dealer_id'] = $v->id;
                    $organizationDetailTemp['point_focus'] = $point_focus;
                    $organizationDetailTemp['count_fav'] = $count_fav;
                    $organizationDetailTemp['count_album'] = $count_album;
                    $organizationDetailTemp['count_product'] = $count_product;
                    $organizationDetailTemp['count_view'] = $count_visit;
                    $organizationDetailTemp['count_designer'] = $quota_designer_used;
                    $organizationDetailData[] = $organizationDetailTemp;

                }
                $update_start_time = microtime(true);
                //批量更新
                if(count($organizationData)>0){
                    DBService::updateBatch('organization_dealers',$organizationData);
                }
                if(count($organizationDetailData)>0){
                    DBService::updateBatch('detail_dealers',$organizationDetailData);
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
            \Log::info('------------计算销售商主表、详情表统计字段------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算销售商主表、详情表统计字段错误',array(
                $e->getTraceAsString()
            ));

        }


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
     * 被关注数
     * @param $seller_id
     * @return mixed
     */
    public function count_fav($seller_id,$dateTimeRange=null)
    {

        $data = $this->fav_dealers
            ->whereIn('target_dealer_id',$seller_id);

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
     * 主页访问次数
     * @param $seller_id
     * @return mixed
     */
    public function count_visit($seller_id,$dateTimeRange=null)
    {

        $data = $this->visit_dealers
            ->whereIn('target_dealer_id',$seller_id);

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
     * 销售商设计师已开通数量
     * @param $seller_id
     * @return mixed
     */
    public function count_seller_designer($seller_id)
    {
        $count = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->where('organization_id',$seller_id)
            //20200803->whereIn('status',[Designer::STATUS_ON,Designer::STATUS_VERIFYING])
            ->count();
        return $count;
    }


    /**
     * 计算销售商关注度
     * @param $brand_id
     * @return mixed
     */
    public function cal_point_focus($seller_id)
    {
        //销售商关注度 = [X] 天内的（ 直属设计师上传方案量 * [Y] +  设计师主页浏览量 × [Z] ）
        if(!$this->point_focus_config){
            return false;
        }

        //直属设计师上传方案量
        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->where('organization_id',$seller_id)
            ->pluck('id');
        $upload_album_count =
            $this->albums->whereIn('designer_id',$seller_designer_ids)
                ->where('created_at','>',$this->datetime_after)
                ->count();

        //设计师主页浏览量
        $designer_visit_count =
            $this->log_designer_visit->whereIn('target_designer_id',$seller_designer_ids)
            ->count();

        $point_focus = $upload_album_count * $this->point_focus_config['designer_album_sum'] +
            $designer_visit_count * $this->point_focus_config['index_read_count'] ;

        return floor($point_focus);
    }

}