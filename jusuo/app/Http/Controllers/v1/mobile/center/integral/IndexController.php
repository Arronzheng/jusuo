<?php

namespace App\Http\Controllers\v1\mobile\center\integral;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\Album;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\FavProduct;
use App\Models\IntegralGood;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\LogBrandSiteConfig;
use App\Services\v1\common\IntegralService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IndexController extends VersionController
{

    private $apiSv;

    public function __construct(ApiService $apiService)
    {
        $this->apiSv = $apiService;
    }

    public function index()
    {
        $designer = Auth::user();
        if(!$designer){
            return $this->goTo404(PageService::ErrorNoAuthority,'','mobile');
        }

        //积分规则链接
        $integral_rule_link = '';

        $designer_session = session('designer_scope.brand_id');
        if(isset($designer_session)){
            $brand_id = session('designer_scope.brand_id');
            $brandConfig = LogBrandSiteConfig::where('target_brand_id',$brand_id)->first();
            if($brandConfig){
                $brandConfigContent = \Opis\Closure\unserialize($brandConfig->content);
                if(isset($brandConfigContent['integral_rule_link']) && $brandConfigContent['integral_rule_link']){
                    $integral_rule_link = $brandConfigContent['integral_rule_link'];
                }
            }
        }




        return $this->get_view('v1.mobile.center.integral.index',compact('integral_rule_link'));
    }


    /*---------------------api方法----------------------*/


    //变动明细表
    public function my_integral_log()
    {
        $loginDesigner = Auth::user();

        $datas = IntegralLogDesigner::query()
            ->orderBy('id','desc')
            ->where('designer_id',$loginDesigner->id)
            ->select(['id','type','integral','available_integral','remark','created_at'])
            ->paginate(10);

        $datas->transform(function($v){
            $v->type_text = IntegralLogDesigner::typeGroup($v->type);
            return $v;
        });

        return $this->apiSv->respDataReturn($datas);
    }

    //兑换记录列表api
    public function my_exchange_log()
    {
        $loginDesigner = Auth::user();

        $logs = IntegralLogBuy::query()
            ->orderBy('id','desc')
            ->where('designer_id',$loginDesigner->id)
            ->select([
                'id','goods_id','count','total','receiver_name','receiver_tel',
                'receiver_address','receiver_province_id','receiver_city_id',
                'receiver_area_id','status','created_at'
            ])
            ->paginate(10);

        $logs_collection = collect($logs->items());

        $goods_ids = $logs_collection->pluck('goods_id')->toArray();
        $province_ids = $logs_collection->pluck('receiver_province_id')->toArray();
        $city_ids = $logs_collection->pluck('receiver_city_id')->toArray();
        $district_ids = $logs_collection->pluck('receiver_area_id')->toArray();

        $area_ids = array_merge($province_ids,$city_ids,$district_ids);

        $area_list = Area::query()->whereIn('id',$area_ids)->get();

        $goods_list = IntegralGood::query()->whereIn('id',$goods_ids)->get();

        $logs->transform(function ($v) use($goods_list,$area_list){
            //商品名称
            $v->good_name = '';
            $good_info = $goods_list->where('id',$v->goods_id)->first();
            if($good_info){
                $v->good_name = $good_info->name;
            }
            //收货地址
            $v->full_address = '';
            $province = $area_list->where('id',$v->receiver_province_id)->first();
            $city = $area_list->where('id',$v->receiver_city_id)->first();
            $district = $area_list->where('id',$v->receiver_area_id)->first();
            if($province){ $v->full_address .= $province->name; }
            if($city){ $v->full_address .= $city->name; }
            if($district){ $v->full_address .= $district->name; }
            $v->full_address .= $v->receiver_address;
            //状态值
            $v->status_text = IntegralLogBuy::statusGroup($v->status);
            //是否显示取消兑换
            $v->show_cancel = 0;
            if($v->status == IntegralLogBuy::STATUS_TO_BE_SENT){
                $v->show_cancel = 1;
            }

            return $v;
        });


        return $this->apiSv->respDataReturn($logs);
    }

    public function cancel_exchange($id)
    {
        $loginDesigner = Auth::user();

        $integral_service = new IntegralService();
        $cancel_result = $integral_service->cancel_exchange($id,$loginDesigner->id);

        if($cancel_result['status'] == 0){
            return $this->apiSv->respFailReturn($cancel_result['msg']);
        }else{
            return $this->apiSv->respDataReturn([]);
        }

    }
}
