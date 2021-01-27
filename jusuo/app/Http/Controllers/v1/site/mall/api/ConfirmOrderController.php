<?php

namespace App\Http\Controllers\v1\site\mall\api;


use App\Http\Controllers\v1\VersionController;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\FavAlbum;
use App\Models\FavDesigner;
use App\Models\HouseType;
use App\Models\IntegralBrand;
use App\Models\IntegralGood;
use App\Models\IntegralGoodsCategory;
use App\Models\IntegralLogBuy;
use App\Models\IntegralLogDesigner;
use App\Models\LikeAlbum;
use App\Models\OrganizationBrand;
use App\Models\ProductCeramic;
use App\Models\SearchAlbum;
use App\Models\ShoppingAddress;
use App\Models\SiteConfigPlatform;
use App\Models\Space;
use App\Models\SpaceType;
use App\Models\StatisticDesigner;
use App\Models\Style;
use App\Services\v1\admin\OrganizationBrandService;
use App\Services\v1\admin\StatisticDesignerService;
use App\Services\v1\site\AlbumService;
use App\Services\v1\site\ApiService;
use App\Services\v1\site\BsAlbumDataService;
use App\Services\v1\site\BsProductDataService;
use App\Services\v1\site\DesignerService;
use App\Services\v1\site\IntegralGoodService;
use App\Services\v1\site\LocationService;
use App\Services\v1\site\OpService;
use App\Services\v1\site\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ConfirmOrderController extends ApiController
{
    private $globalService;

    public function __construct(
        GlobalService $globalService
    ){
        $this->globalService = $globalService;
    }


    //确认订单页-》获取商品信息（详情页提交兑换后，url传商品id和数量到确认订单页）
    public function confirm_good_info($web_id_code)
    {
        $good = IntegralGood::query()
            ->where('status',IntegralGood::STATUS_ON)
            ->select([
                'id','name','cover','integral'
            ])
            ->where('web_id_code',$web_id_code)
            ->first();

        if(!$good){
            $this->respFail('信息不存在');
        }

        $this->respData($good);

    }
    
    //确认订单页-》获取收货地址api
    public function address_list()
    {
        $loginDesigner = Auth::user();

        //获取当前登录用户的收货地址列表
        $addresses = ShoppingAddress::query()
            ->select([
                'id','province_id','city_id','area_id','receiver_name','receiver_tel',
                'receiver_address','is_default'
            ])
            ->where('designer_id',$loginDesigner->id)
            ->orderBy('is_default','desc')
            ->orderBy('id','desc')
            ->get();

        $province_ids = $addresses->pluck('province_id')->toArray();
        $city_ids = $addresses->pluck('city_id')->toArray();
        $district_ids = $addresses->pluck('area_id')->toArray();


        $area_ids = array_merge($province_ids,$city_ids,$district_ids);

        $area_list = Area::query()->whereIn('id',$area_ids)->get();

        $addresses->transform(function($v)use($area_list){

            $v->province_name = '';
            $province = $area_list->where('id',$v->province_id)->first();
            if($province){ $v->province_name = $province->name; }

            $v->city_name = '';
            $city = $area_list->where('id',$v->city_id)->first();
            if($city){ $v->city_name = $city->name; }

            $v->area_name = '';
            $district = $area_list->where('id',$v->area_id)->first();
            if($district){ $v->area_name = $district->name; }



            return $v;

        });

        $this->respData($addresses);
    }

    //确认订单页-》新增收货地址api
    public function store_address(Request $request)
    {
        $loginDesigner = Auth::user();

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'receiver_name' => 'required',
            'receiver_tel' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'receiver_address' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //其他校验
        //校验是否有相同的地址
        $exist = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->where('receiver_name',$input_data['receiver_name'])
            ->where('receiver_tel',$input_data['receiver_tel'])
            ->where('province_id',$input_data['province_id'])
            ->where('city_id',$input_data['city_id'])
            ->where('area_id',$input_data['area_id'])
            ->where('receiver_address',$input_data['receiver_address'])
            ->first();
        if($exist){
            $this->respFail('已有相同地址信息！');
        }

        //查询该用户有多少个收货地址
        $count = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->count();

        if($count>=10){
            $this->respFail('最多可保存10条收货地址！已达限额');
        }

        //获取省市区名称
        $area_info = Area::query()
            ->whereIn('id',[$input_data['province_id'],$input_data['city_id'],$input_data['area_id']])
            ->get()->pluck('name');
        if(!$area_info || count($area_info) !=3){
            $this->respFail('省市区信息错误！');
        }

        //新增数据
        $address = new ShoppingAddress();
        $address->designer_id = $loginDesigner->id;
        $address->province_id = $input_data['province_id'];
        $address->city_id = $input_data['city_id'];
        $address->area_id = $input_data['area_id'];
        $address->receiver_name = $input_data['receiver_name'];
        $address->receiver_tel = $input_data['receiver_tel'];
        $address->receiver_address = $input_data['receiver_address'];
        //$address->full_address = $area_info[0].$area_info[1].$area_info[2].$input_data['receiver_address'];
        if($count <= 0){
            //无收货地址则设为默认
            $address->is_default = ShoppingAddress::IS_DEFAULT_YES;
        }else{
            $address->is_default = ShoppingAddress::IS_DEFAULT_NO;
        }
        $address->save();

        $this->respData(['data'=>$address]);
    }

    //确认订单页-》更新收货地址api
    public function update_address($id,Request $request)
    {
        $loginDesigner = Auth::user();

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'receiver_name' => 'required',
            'receiver_tel' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'area_id' => 'required',
            'receiver_address' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $address = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->find($id);

        if(!$address){
            $this->respFail('地址不存在！');
        }

        //其他校验
        //校验是否有相同的地址
        $exist = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->where('receiver_name',$input_data['receiver_name'])
            ->where('receiver_tel',$input_data['receiver_tel'])
            ->where('province_id',$input_data['province_id'])
            ->where('city_id',$input_data['city_id'])
            ->where('area_id',$input_data['area_id'])
            ->where('receiver_address',$input_data['receiver_address'])
            ->where('id','<>',$id)
            ->first();
        if($exist){
            $this->respFail('已有相同地址信息！');
        }

        //更新数据
        $address->province_id = $input_data['province_id'];
        $address->city_id = $input_data['city_id'];
        $address->area_id = $input_data['area_id'];
        $address->receiver_name = $input_data['receiver_name'];
        $address->receiver_tel = $input_data['receiver_tel'];
        $address->receiver_address = $input_data['receiver_address'];

        $address->save();

        $this->respData(['data'=>$address]);
    }

    //确认订单页-》删除收货地址api
    public function destroy_address($id,Request $request)
    {
        $loginDesigner = Auth::user();

        $address = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->find($id);

        if(!$address){
            $this->respFail('地址不存在！');
        }

        //删除
        $address->delete();

        $this->respData([]);
    }

    //确认订单页-》收货地址设为默认api
    public function set_address_default($id,Request $request)
    {
        $loginDesigner = Auth::user();

        $address = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->find($id);

        if(!$address){
            $this->respFail('地址不存在！');
        }

        try{

            DB::beginTransaction();

            //取消其他地址的默认
            DB::table('shopping_addresses')
                ->where('designer_id',$loginDesigner->id)
                ->where('id','<>',$address->id)
                ->update(['is_default'=>ShoppingAddress::IS_DEFAULT_NO]);

            //设置当前地址为默认
            $address->is_default = ShoppingAddress::IS_DEFAULT_YES;
            $address->save();

            DB::commit();


            $this->respData(['data'=>$address]);

        }catch(\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！');
        }

    }
    
    //确认订单页-》提交订单
    public function submit_order(Request $request)
    {
        $loginDesigner = Auth::user();

        $good_web_id_code = $request->input('g',0);
        $address_id = $request->input('a',0);
        $count = $request->input('c',0);
        $remark = $request->input('r','');

        if($count <=0 ){
            $this->respFail('请选择数量！');
        }

        $good = IntegralGoodService::getForSaleEntry()
            ->where('web_id_code',$good_web_id_code)
            ->first();

        if(!$good){
            $this->respFail('商品不存在！');
        }

        $good_id = $good->id;

        //商品单价
        $integral = intval($good->integral);
        if($integral<=0){
            $this->respFail('商品信息异常！');
        }

        //商品数量
        $count = intval($count);

        //订单总价
        $total = intval($integral * $count);

        //判断用户积分
        $designer_detail = DesignerDetail::query()->where('designer_id',$loginDesigner->id)->first();
        if(!$designer_detail){
            $this->respFail('用户信息不存在');
        }
        $before_integral = intval($designer_detail->point_money);
        if($before_integral < $total){
            $this->respFail('您的积分余额不足，无法下单');
        }

        //收货地址信息
        $address = ShoppingAddress::query()
            ->where('designer_id',$loginDesigner->id)
            ->find($address_id);
        if(!$address){
            $this->respFail('收货地址信息不存在');
        }

        try{

            DB::beginTransaction();

            //扣除后用户剩余积分
            $after_integral = $before_integral - $total;

            //扣除用户积分
            $designer_detail->decrement('point_money',$total);

            //记录用户积分改变
            $log = new IntegralLogDesigner();
            $log->designer_id = $loginDesigner->id;
            $log->type = IntegralLogDesigner::TYPE_ADMIN_MINUS;
            $log->integral = $total;
            $log->available_integral = $after_integral;
            $log->remark = IntegralLogDesigner::REASONS[IntegralLogDesigner::TYPE_EXCHANGE];
            $log->save();

            //生成订单
            $order = new IntegralLogBuy();
            $order->designer_id = $loginDesigner->id;
            $order->goods_id = $good_id;
            $order->integral = $integral;
            $order->count = $count;
            $order->total = $total;
            $order->receiver_province_id = $address->province_id;
            $order->receiver_city_id = $address->city_id;
            $order->receiver_area_id = $address->area_id;
            $order->receiver_name = $address->receiver_name;
            $order->receiver_tel = $address->receiver_tel;
            $order->receiver_address = $address->receiver_address;
            $order->remark = $remark;
            $order->status = IntegralLogBuy::STATUS_TO_BE_SENT;
            $order->save();


            DB::commit();

            $this->respData(['order_id'=>$order->id]);

        }catch(\Exception $e){

            DB::rollback();
            $this->respFail('系统错误');

        }




    }
}
