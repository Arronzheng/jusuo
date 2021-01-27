<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\AlbumProductCeramic;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\StatisticAccountBrand;
use App\Models\VisitDesigner;
use Illuminate\Support\Facades\DB;

class ProductColumnStatisticService
{

    private $products;
    private $album_products;
    private $point_focus_config = null;

    public function __construct()
    {
        $this->products = ProductCeramic::all();
        $this->album_products = AlbumProductCeramic::all();

        $point_focus_config = ParamConfigUseService::find_root('platform.product.ceramic.sys_info.focus.cal_rule');
        if(isset($point_focus_config['system_read_ratio'])
            && isset($point_focus_config['usage_ratio'])
            && isset($point_focus_config['collection_ratio']))
        {
            $point_focus_config['system_read_ratio'] = floatval($point_focus_config['system_read_ratio']);
            $point_focus_config['usage_ratio'] = floatval($point_focus_config['usage_ratio']);
            $point_focus_config['collection_ratio'] = floatval($point_focus_config['collection_ratio']);
            $this->point_focus_config = $point_focus_config;
        }
        return false;
    }

    /**
     * 更新所有产品的统计字段数据
     */
    public function update()
    {
        set_time_limit(300);

        try{

            \Log::info('------------计算产品主表、详情表统计字段------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);
            $datas = ProductCeramic::where('status',ProductCeramic::STATUS_PASS)
                ->get();
            $product_counts = count($datas);
            $datas = collect($datas)->chunk(500);
            $select_end_time = microtime(true);

            $foreach_start_time = microtime(true);
            foreach($datas as $chunk){

                $productData = [];
                foreach($chunk as $v){
                    $point_focus = $this->cal_point_focus($v->id);

                    if($point_focus!==false){
                        $productTemp = [];
                        $productTemp['id'] = $v->id;
                        $productTemp['point_focus'] = $point_focus;
                        $productData[] = $productTemp;
                    }

                }
                $update_start_time = microtime(true);
                //批量更新

                if(count($productData)>0){
                    DBService::updateBatch('product_ceramics',$productData);
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

            \Log::info("数据量：{$product_counts}条");
            \Log::info("[总执行时间：{$all_use_time}]毫秒");
            //\Log::info("[查询执行时间：{$select_use_time}]毫秒");
            \Log::info("[foreach处理执行时间：{$foreach_use_time}]毫秒");
            \Log::info("[更新数据执行时间：{$update_use_time}]毫秒");
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算产品主表、详情表统计字段------------');

        }catch (\Exception $e){

            SystemLogService::simple('计算产品主表、详情表统计字段错误',array(
                $e->getTraceAsString()
            ));

        }


    }


    /**
     * 计算产品关注度
     * @param $brand_id
     * @return mixed
     */
    public function cal_point_focus($product_id)
    {
        //产品关注度 = 系统自动浏览量 × A + 使用量 × B + 收藏量 × C

        if(!$this->point_focus_config){
            return false;
        }

        $product = $this->products
            ->where('id',$product_id)
            ->first();

        //使用量
        $usage = $this->album_products->where('product_ceramic_id',$product_id)->count();

        $point_focus =
            intval($product->count_visit) * $this->point_focus_config['system_read_ratio'] +
            intval($usage) * $this->point_focus_config['usage_ratio'] +
            intval($product->count_fav) * $this->point_focus_config['collection_ratio'];

        return floor($point_focus);
    }

}