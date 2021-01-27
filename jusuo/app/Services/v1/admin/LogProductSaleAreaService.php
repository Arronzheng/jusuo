<?php


namespace App\Services\v1\admin;


use App\Http\Services\common\DBService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\SystemLogService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\LogAlbumTop;
use App\Models\LogProductSaleArea;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\StatisticDesigner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\DirectoryExists;

class LogProductSaleAreaService
{

    public static function update()
    {
        set_time_limit(300);

        try{

            \Log::info('------------计算产品在售城市------------');
            //开始内存
            $start_memory = memory_get_usage();
            $start_time = microtime(true);
            $update_all_time = 0;

            $select_start_time = microtime(true);

            //省市区数据
            $areas = Area::all();

            //已有产品在售城市信息
            $old_log_data = LogProductSaleArea::select(['id','product_id','province_id','city_id','district_id'])
                ->get();
            $data_counts = $old_log_data->count();

            //已授权给销售商的产品
            $datas = DB::table('product_ceramic_authorizations as pca')
                ->join('organization_dealers as seller','seller.id','=','pca.dealer_id')
                ->join('detail_dealers as sdetail','sdetail.dealer_id','=','seller.id')
                ->select(['pca.product_id','sdetail.area_serving_id'])
                ->get();

            $select_end_time = microtime(true);

            $handle_start_time = microtime(true);

            $filter = $datas->unique(function ($item) {
                return $item->product_id.$item->area_serving_id;
            })->groupBy('product_id');

            $datas = $filter->all();

            //要删除的数据
            $delete_ids = [];

            //已存在的数据
            $exist_ids = [];

            //要新增的数据
            $insert_data = [];

            //处理新计算出来的产品在售城市信息
            $new_log_data = [];
            foreach($datas as $key=>$item){
                $temp = [];
                $product_id = $key;
                $temp['product_id'] = $product_id;
                $area_serving_ids = collect($item)->pluck('area_serving_id');
                foreach($area_serving_ids as $area_serving_id){
                    $district = $areas->where('id', $area_serving_id)->first();
                    if($district){
                        $city = $areas->where('id',$district->pid)->first();
                        if($city){
                            $province = $areas->where('id',$city->pid)->first();
                            $temp['province_id'] = $province->id;
                            $temp['city_id'] = $city->id;
                            $temp['district_id'] = $district->id;
                            //判断在原集合中是否存在
                            $exist = $old_log_data->first(function ($value, $key)use(&$insert_data,&$exist_ids,$temp,$product_id) {
                                return $value->province_id==$temp['province_id'] &&
                                    $value->city_id==$temp['city_id'] &&
                                    $value->district_id==$temp['district_id'] &&
                                    $value->product_id == $product_id;
                            });

                            if($exist){
                                //如果存在
                                $exist_ids[] = $exist->id;
                            }else{
                                //如果不存在
                                $insert_data[] = [
                                    'product_id'=>$product_id,
                                    'province_id'=>$temp['province_id'],
                                    'city_id'=>$temp['city_id'],
                                    'district_id'=>$temp['district_id'],
                                ];
                            }

                        }
                    }
                }
            }

            //筛选出旧数据需要删除的id
            $delete_ids = $old_log_data->filter(function ($value, $key)use($exist_ids) {
                return !in_array($value->id,$exist_ids);
            })->pluck('id');
            $delete_ids = $delete_ids->all();

            //处理需要插入的数据
            if(count($insert_data)>0){
                DB::table('log_product_sale_areas')->insert($insert_data);
            }

            //处理需要删除的旧数据
            DB::table('log_product_sale_areas')->whereIn('id',$delete_ids)->delete();
            

            $handle_end_time = microtime(true);

            $end_time = microtime(true);
            $all_use_time = ($end_time - $start_time) * 1000;
            $select_use_time = ($select_end_time - $select_start_time) * 1000;
            $handle_use_time = ($handle_end_time - $handle_start_time) * 1000;
            $end_memory = memory_get_usage();
            $use_memory = $end_memory - $start_memory;

            \Log::info("数据量：{$data_counts}条");
            \Log::info("[总执行时间：{$all_use_time}]毫秒");
            \Log::info("[查询执行时间：{$select_use_time}]毫秒");
            \Log::info("[处理执行时间：{$handle_use_time}]毫秒");
            \Log::info('运行前内存：'. $start_memory.'bytes');
            \Log::info('运行后内存：'. $end_memory.'bytes');
            \Log::info('使用的内存:' . $use_memory.'bytes');
            \Log::info('------------计算产品在售城市------------');

        }catch (\Exception $e){

            \Log::info('------------计算产品在售城市错误------------');
            SystemLogService::simple('计算产品在售城市错误',array(
                $e->getTraceAsString()
            ));
            \Log::info('------------计算产品在售城市错误------------');

        }


    }

}