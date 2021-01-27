<?php

namespace App\Http\Controllers\v1\admin\platform\integral\authorize\show\object\api;

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
use App\Models\IntegralGood;
use App\Models\LogIntegralGoodAuthorization;
use App\Models\MsgAccountBrand;
use App\Models\MsgAccountDealer;
use App\Models\MsgProductCeramicBrand;
use App\Models\MsgProductCeramicDealer;
use App\Models\MsgSystemBrand;
use App\Models\MsgSystemDealer;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\ProductCeramic;
use App\Models\ProductCeramicAuthorization;
use App\Models\RoleDealer;
use App\Models\StatisticAccountDealer;
use App\Services\v1\admin\MsgAccountBrandMultiService;
use App\Services\v1\admin\MsgAccountSellerService;
use App\Services\v1\admin\MsgProductCeramicBrandService;
use App\Services\v1\admin\MsgSystemBrandMultiService;
use App\Services\v1\admin\MsgSystemBrandService;
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

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = OrganizationBrand::select(['id','name','brand_name','organization_account','contact_name','contact_telephone'])
            ->orderBy('created_at','desc');

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->where('name','like',"%".$keyword."%");
                $query->orWhere('brand_name','like',"%".$keyword."%");
                $query->orWhere('short_name','like',"%".$keyword."%");
                $query->orWhere('organization_account','like',"%".$keyword."%");
                $query->orWhere('contact_name','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //授权/取消授权对象
    public function authorize_object(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();

        //参数判断
        $rules = [
            'op' => 'required',
            'pids' => 'required',
            'oids' => 'required',
        ];

        $messages = [
            'op.required' => '请选择要操作的类型',
            'pids.required' => '请选择要操作的商品',
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
        $goods_ids = $input_data['pids'];
        if(count($goods_ids)<=0){
            $this->respFail('请选择商品！');
        }

        //判断对象的数量
        $object_ids = $input_data['oids'];
        if(count($object_ids)<=0){
            $this->respFail('请选择开放对象！');
        }

        DB::beginTransaction();

        try{

            //获取当前平台在前端选择的品牌
            $objects = OrganizationBrand::whereIn('id',$object_ids)->get();
            $insert_data = [];
            $good = IntegralGood::find($goods_ids[0]);
            $good_count = count($goods_ids);
            $good_name = $good->name;
            $object_ids = $objects->pluck('id')->toArray();
            if($operation=='authorize'){

                for($i=0;$i<count($objects);$i++){
                    $exist_goods_ids = $objects[$i]->integral_good_authorizations()->get()->pluck('id')->toArray();
                    $final_product_id = array_merge($exist_goods_ids,$goods_ids);
                    $objects[$i]->integral_good_authorizations()->sync($final_product_id);

                    //写入品牌账号通知
                    $insert_data[] = [
                        'brand_id' => $objects[$i]->id,
                        'type' => MsgSystemBrand::TYPE_INTEGRAL_GOOD_AUTHORIZE,
                        'content' => '['.$good_name.']等'.$good_count.'个积分商品被平台开放授权。',
                    ];

                }

                //写进操作记录
                $log_auth = new LogIntegralGoodAuthorization();
                $log_auth->administrator_id = $loginAdmin->id;
                $log_auth->log_type = LogIntegralGoodAuthorization::LOG_TYPE_SHOW;
                $log_auth->log_type_operation = LogIntegralGoodAuthorization::LOG_TYPE_OPERATION_AUTHORIZE;
                $log_auth->good_ids = implode(',',$goods_ids);
                $log_auth->object_ids = implode(',',$object_ids);
                $log_auth = $log_auth->save();
                if(!$log_auth){
                    DB::rollback();
                    $this->respFail('记录操作失败！');
                }

            }else{

                DB::rollback();

                for($i=0;$i<count($objects);$i++){

                    $objects[$i]->integral_good_authorizations()->detach($goods_ids);

                    //写入品牌通知
                    $insert_data[] = [
                        'brand_id' => $objects[$i]->id,
                        'type' => MsgSystemBrand::TYPE_INTEGRAL_GOOD_AUTHORIZE,
                        'content' => '['.$good_name.']等'.$good_count.'个积分商品被平台取消开放授权。',
                    ];

                }

                //写进操作记录
                $log_auth = new LogIntegralGoodAuthorization();
                $log_auth->administrator_id = $loginAdmin->id;
                $log_auth->log_type = LogIntegralGoodAuthorization::LOG_TYPE_SHOW;
                $log_auth->log_type_operation = LogIntegralGoodAuthorization::LOG_TYPE_OPERATION_CANCEL;
                $log_auth->good_ids = implode(',',$goods_ids);
                $log_auth->object_ids = implode(',',$object_ids);
                $log_auth = $log_auth->save();
                if(!$log_auth){
                    DB::rollback();
                    $this->respFail('记录操作失败！');
                }
            }

            if(count($insert_data)>0){
                MsgSystemBrandMultiService::add($insert_data);
            }


            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());
        }

    }




}
