<?php

namespace App\Http\Controllers\v1\admin\brand\product\authorize\show\object\api;

use App\Http\Controllers\Controller;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\AdministratorDealer;
use App\Models\Album;
use App\Models\Area;
use App\Models\CertificationDealer;
use App\Models\Designer;
use App\Models\DetailDealer;
use App\Models\LogProductAuthorization;
use App\Models\MsgAccountDealer;
use App\Models\MsgProductCeramicBrand;
use App\Models\MsgProductCeramicDealer;
use App\Models\MsgSystemDealer;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\RoleDealer;
use App\Models\StatisticAccountDealer;
use App\Services\v1\admin\MsgAccountSellerService;
use App\Services\v1\admin\MsgProductCeramicBrandService;
use App\Services\v1\admin\MsgSystemSellerService;
use App\Services\v1\admin\OrganizationDealerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Http\Services\v1\admin\ParamCheckService;


class ObjectController extends ApiController
{
    private $globalService;
    private $roleOrganizationRepository;
    private $authService;
    private $infiniteTreeService;
    private $getNameServices;

    public function __construct(
        GlobalService $globalService,
        AuthService $authService,
        InfiniteTreeService $infiniteTreeService,
        GetNameServices $getNameServices
    ){
        $this->globalService = $globalService;
        $this->authService = $authService;
        $this->infiniteTreeService = $infiniteTreeService;
        $this->getNameServices = $getNameServices;
    }


    //账号列表数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $area_district_id = $request->input('a_d',null);
        $company_name = $request->input('cn',null);
        $level = $request->input('lv',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = DB::table('administrator_dealers as ab')
            ->where('b.p_brand_id',$brand->id)
            ->where('b.status',OrganizationDealer::STATUS_ON)
            ->where('b.level',1);

        //筛选服务地区
        if($area_district_id!==null){
            $entry->where('area_serving_id',$area_district_id);
        }

        if($company_name!==null){
            //筛选公司名称
            $entry->where('b.name','like',"%".$company_name."%");
        }

        if($level!==null && $level!=''){
            $entry->where('b.level',$level);
        }


        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('ab.created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }


        $loginAdmin = $this->authService->getAuthUser();

        $entry->join('organization_dealers as b','ab.dealer_id','=','b.id')
            ->join('detail_dealers as bd','bd.dealer_id','=','b.id')
            ->select([
                'b.id','ab.login_username','ab.login_account','b.name as dealer_name','b.id as dealer_id',
                'contact_name','contact_telephone','quota_designer','quota_designer_used',
                'b.created_at','b.expired_at','ab.status as account_status','b.level','b.p_dealer_id',
                'b.status as dealer_status','b.contact_address',
                'bd.area_serving_id','bd.area_belong_id','bd.point_focus','bd.star_level'
            ]);

        $entry->orderBy('id','desc');

        $datas = $entry->paginate($limit);

        $datas->transform(function($v)use($loginAdmin){
            $v->isOn = $v->account_status==AdministratorDealer::STATUS_ON;
            //已授权/可授权设计师账号数
            $v->designer_count = $v->quota_designer_used;
            //服务城市
            if ($v->area_serving_id){
                $district = Area::where('id',$v->area_serving_id)->first();
                if ($district){
                    $city =  Area::where('id',$district->pid)->first();
                    if ($city){
                        $province =  Area::where('id',$city->pid)->first();

                        if ($province){
                            $v->area_serving = $province->name.'/'.$city->name.'/'.$district->name;
                        }
                    }
                }
            }
            //所在城市
            if ($v->area_belong_id){
                $district = Area::where('id',$v->area_belong_id)->first();
                if ($district){
                    $city =  Area::where('id',$district->pid)->first();
                    if ($city){
                        $province =  Area::where('id',$city->pid)->first();

                        if ($province){
                            $v->area_belong = $province->name.'/'.$city->name.'/'.$district->name;
                        }
                    }
                }
            }
            $statistic_dealer = StatisticAccountDealer::where('dealer_id',$v->dealer_id)->first();
            //合作产品数
            $v->product_count = 0;
            if($statistic_dealer){
                $v->product_count = $statistic_dealer->count_product;
            }
            //方案数
            $v->album_count = 0;
            if($statistic_dealer){
                $v->album_count = $statistic_dealer->count_album;
            }
            //关注度
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //授权/取消授权对象
    public function authorize_object(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        //参数判断
        $rules = [
            'op' => 'required',
            'pids' => 'required',
            'oids' => 'required',
        ];

        $messages = [
            'op.required' => '请选择要操作的类型',
            'pids.required' => '请选择要操作的产品',
            'oids.required' => '请选择要操作的对象',
        ];

        $validator = Validator::make($input_data,$rules,$messages);

        if ($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $msg_result ='';
            foreach($messages as $k=>$v){
                $msg_result .= $v[0]."<br/>";
            }
            $this->respFail($msg_result);
        }

        //判断操作的正确性
        $operation = 'cancel';
        if(!in_array($input_data['op'],['authorize','cancel'])){
            $this->respFail('操作类型错误！');
        }
        $operation = $input_data['op'];

        //判断产品的数量
        $product_ids = $input_data['pids'];
        if(count($product_ids)<=0){
            $this->respFail('请选择产品！');
        }

        //判断对象的数量
        $object_ids = $input_data['oids'];
        if(count($object_ids)<=0){
            $this->respFail('请选择授权对象！');
        }

        DB::beginTransaction();

        try{

            //获取当前品牌的前端选择的一级销售商
            $sellers = OrganizationDealerService::getBrandLegalSeller1Entry($brand->id)
                ->whereIn('id',$object_ids)
                ->get();
            $insert_data = [];
            $product = ProductCeramic::find($product_ids[0]);
            $product_count = count($product_ids);
            $product_name = $product->name;
            $seller_ids = $sellers->pluck('id')->toArray();
            if($operation=='authorize'){

                for($i=0;$i<count($sellers);$i++){
                    $exist_product_ids = $sellers[$i]->product_ceramic_authorizations()->get()->pluck('id')->toArray();
                    $final_product_id = array_merge($exist_product_ids,$product_ids);
                    $sellers[$i]->product_ceramic_authorizations()->sync($final_product_id);

                    //写入品牌产品通知
                    $insert_data[] = [
                        'dealer_id' => $sellers[$i]->id,
                        'type' => MsgProductCeramicDealer::TYPE_AUTHORIZATION,
                        'content' => '['.$product_name.']等'.$product_count.'个产品被品牌授权。',
                    ];

                }

                //写进操作记录
                $log_product_auth = new LogProductAuthorization();
                $log_product_auth->brand_id = $brand->id;
                $log_product_auth->administrator_id = $loginAdmin->id;
                $log_product_auth->product_type = LogProductAuthorization::PRODUCT_TYPE_CERAMIC;
                $log_product_auth->log_type = LogProductAuthorization::LOG_TYPE_SHOW;
                $log_product_auth->log_type_operation = LogProductAuthorization::LOG_TYPE_OPERATION_AUTHORIZE;
                $log_product_auth->product_ids = implode(',',$product_ids);
                $log_product_auth->object_ids = implode(',',$seller_ids);
                $log_auth = $log_product_auth->save();
                if(!$log_auth){
                    DB::rollback();
                    $this->respFail('记录操作失败！');
                }

            }else{
                for($i=0;$i<count($sellers);$i++){

                    $sellers[$i]->product_ceramic_authorizations()->detach($product_ids);
                    //写入品牌产品通知
                    $insert_data[] = [
                        'dealer_id' => $sellers[$i]->id,
                        'type' => MsgProductCeramicDealer::TYPE_AUTHORIZATION,
                        'content' => '['.$product_name.']等'.$product_count.'个产品被品牌取消授权。',
                    ];

                }

                //写进操作记录
                $log_product_auth = new LogProductAuthorization();
                $log_product_auth->brand_id = $brand->id;
                $log_product_auth->administrator_id = $loginAdmin->id;
                $log_product_auth->product_type = LogProductAuthorization::PRODUCT_TYPE_CERAMIC;
                $log_product_auth->log_type = LogProductAuthorization::LOG_TYPE_SHOW;
                $log_product_auth->log_type_operation = LogProductAuthorization::LOG_TYPE_OPERATION_CANCEL;
                $log_product_auth->product_ids = implode(',',$product_ids);
                $log_product_auth->object_ids = implode(',',$seller_ids);
                $log_auth = $log_product_auth->save();
                if(!$log_auth){
                    DB::rollback();
                    $this->respFail('记录操作失败！');
                }
            }

            if(count($insert_data)>0){
                DB::table('msg_product_ceramic_dealers')->insert($insert_data);
            }


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }




}
