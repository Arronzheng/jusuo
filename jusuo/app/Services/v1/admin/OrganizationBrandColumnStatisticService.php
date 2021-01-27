<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Designer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\StatisticAccountBrand;
use Illuminate\Support\Facades\DB;

class OrganizationBrandColumnStatisticService
{

    private $designers;
    private $sellers;
    private $albums;
    private $point_focus_ratio = null;

    public function __construct()
    {
        $this->designers = Designer::all();
        $this->sellers = OrganizationDealer::all();
        $this->albums = Album::all();
        $point_focus_config = ParamConfigUseService::find_root('platform.sys_info.brand.focus.cal_rule');
        if(isset($point_focus_config['ratio'])){
            $this->point_focus_ratio = $point_focus_config['ratio'];
        }
        return false;
    }

    /**
     * 对于organization_brands和detail_brands表
     * 更新所有品牌的统计字段数据
     */
    public function update()
    {
        set_time_limit(300);

        try{

            \Log::info('------------计算品牌主表、详情表统计字段------------');
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
            foreach($brands as $chunk){

                $organizationData = [];
                $organizationDetailData = [];
                foreach($chunk as $v){
                    $quota_dealer_lv1_used = $this->count_seller_on_by_level($v->id,1);
                    $quota_dealer_lv2_used = $this->count_seller_on_by_level($v->id,2);
                    $quota_designer_brand_used = $this->count_brand_designer($v->id);
                    $quota_designer_dealer_used = $this->count_seller_designer($v->id);
                    $point_focus = $this->cal_point_focus($v->id);

                    $organizationData[] = [
                        'id'=>$v->id,
                        'quota_dealer_lv1_used' =>$quota_dealer_lv1_used,
                        'quota_dealer_lv2_used' =>$quota_dealer_lv2_used,
                        'quota_designer_brand_used' =>$quota_designer_brand_used,
                        'quota_designer_dealer_used' =>$quota_designer_dealer_used
                    ];

                    if($point_focus!==false){
                        $organizationDetailTemp = [];
                        $organizationDetailTemp['brand_id'] = $v->id;
                        $organizationDetailTemp['point_focus'] = $point_focus;
                        $organizationDetailData[] = $organizationDetailTemp;
                    }

                }
                $update_start_time = microtime(true);
                //批量更新
                if(count($organizationData)>0){
                    DBService::updateBatch('organization_brands',$organizationData);
                }
                if(count($organizationDetailData)>0){
                    DBService::updateBatch('detail_brands',$organizationDetailData);
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
            \Log::info('------------计算品牌主表、详情表统计字段------------');

        }catch (\Exception $e){

            \Log::info('------------计算品牌主表、详情表统计字段错误------------');
            \Log::info('------------计算品牌主表、详情表统计字段错误------------');

        }


    }


    /**
     * 一、二级经销商已开通数量
     * @param $brand_id
     * @return mixed
     */
    public function count_seller_on_by_level($brand_id,$level)
    {
        $count = $this->sellers
            //20200803->whereIn('status',[OrganizationDealer::STATUS_ON,OrganizationDealer::STATUS_WAIT_VERIFY])
            ->where('p_brand_id',$brand_id)
            ->where('level',$level)
            ->count();
        return $count;
    }

    /**
     * 品牌设计师已开通数量
     * @param $brand_id
     * @return mixed
     */
    public function count_brand_designer($brand_id)
    {
        $count = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id)
            //20200803->whereIn('status',[Designer::STATUS_ON,Designer::STATUS_VERIFYING])
            ->count();
        return $count;
    }

    /**
     * 经销商设计师已开通数量
     * @param $brand_id
     * @return mixed
     */
    public function count_seller_designer($brand_id)
    {
        $seller_ids = $this->sellers
            ->where('p_brand_id',$brand_id)
            //20200803->whereIn('status',[OrganizationDealer::STATUS_ON,OrganizationDealer::STATUS_WAIT_VERIFY])
            ->pluck('id')->toArray();

        $count = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_ids)
            //20200803->whereIn('status',[Designer::STATUS_ON,Designer::STATUS_VERIFYING])
            ->count();

        return $count;
    }

    /**
     * 计算品牌关注度
     * @param $brand_id
     * @return mixed
     */
    public function cal_point_focus($brand_id)
    {
        //品牌关注度 = （直属+旗下销售商）的设计师的方案关注度 × ratio 20200113
        if(!$this->point_focus_ratio){
            return false;
        }

        //品牌直属设计师的总方案关注度
        $brand_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_BRAND)
            ->where('organization_id',$brand_id)
            ->pluck('id');
        $brand_designer_album_point_focus =
           $this->albums->whereIn('designer_id',$brand_designer_ids)
                ->sum('point_focus');

        $seller_ids = $this->sellers
            ->where('p_brand_id',$brand_id)->where('status',OrganizationDealer::STATUS_ON)
            ->pluck('id')->toArray();


        $seller_designer_ids = $this->designers
            ->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER)
            ->whereIn('organization_id',$seller_ids)
            ->pluck('id');
        $seller_designer_album_point_focus =
            $this->albums->whereIn('designer_id',$seller_designer_ids)
            ->sum('point_focus');


        $album_point_focus = (intval($brand_designer_album_point_focus)+intval($seller_designer_album_point_focus));

        $brand_point_focus = floor($album_point_focus * $this->point_focus_ratio);

        return $brand_point_focus;
    }

}