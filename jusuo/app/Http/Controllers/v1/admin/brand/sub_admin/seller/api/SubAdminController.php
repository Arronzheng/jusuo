<?php

namespace App\Http\Controllers\v1\admin\brand\sub_admin\seller\api;

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
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\Area;
use App\Models\CertificationDealer;
use App\Models\DesignerDetail;
use App\Models\DetailDealer;
use App\Models\IntegralLogDealer;
use App\Models\MsgAccountDealer;
use App\Models\MsgSystemDealer;
use App\Models\OrganizationDealer;
use App\Models\ProductCategory;
use App\Models\RoleDealer;
use App\Models\SearchAlbum;
use App\Models\StatisticAccountDealer;
use App\Services\v1\admin\MsgAccountSellerService;
use App\Services\v1\admin\MsgSystemSellerService;
use App\Services\v1\admin\OrganizationBrandColumnStatisticService;
use App\Services\v1\admin\OrganizationDealerService;
use App\Services\v1\site\DealerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Http\Services\v1\admin\ParamCheckService;


class SubAdminController extends ApiController
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
    public function account_index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $login_name = $request->input('ln',null);
        $level = $request->input('lv',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);
        //$area_visible_province = $request->input('vp',null);
        $area_visible_city = $request->input('avc',null);
        //$area_serving_province = $request->input('asp',null);
        $area_serving_city = $request->input('asc',null);

        $entry = DB::table('administrator_dealers as ab')
            ->where('b.p_brand_id',$brand->id);

        //筛选可见城市
        if($area_visible_city!==null){
            $entry = $entry->whereRaw('(bd.area_visible_city like "%' . DealerService::JOINER . $area_visible_city . DealerService::JOINER . '%" )');
        }


        //筛选服务城市
        if($area_serving_city!==null){
            $entry = $entry->whereRaw('(bd.area_serving_city like "%' . DealerService::JOINER . $area_serving_city . DealerService::JOINER . '%" )');
        }

        if($login_name!==null){
            //筛选登录用户名/账号
            $entry = $entry->where(function ($query) use($login_name){
                $query->where('login_username','like',"%".$login_name."%");
                $query->orWhere('login_account','like',"%".$login_name."%");
            });
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
                'ab.id','ab.login_username','ab.login_account','ab.login_mobile','b.name as dealer_name','b.id as dealer_id',
                'bd.area_serving_city','bd.area_visible_city','contact_name','contact_telephone','quota_designer','quota_designer_used',
                'b.created_at','b.expired_at','ab.status as account_status','b.level','b.p_dealer_id',
                'b.status as dealer_status','bd.privilege_area_serving','b.web_id_code'
            ]);

        $entry->orderBy('id','desc');

        $datas = $entry->paginate($limit);

        $datas->transform(function($v)use($loginAdmin){
            //服务地区范围
            /*$privilege_area_serving_text = DetailDealer::privilegeAreaServingGroup($v->privilege_area_serving);
            $v->privilege_area_serving_text = $privilege_area_serving_text;*/
            //上级销售商
            $v->parent_seller_name = '无';
            if($v->p_dealer_id){
                $parent_seller = OrganizationDealer::find($v->p_dealer_id);
                if($parent_seller){
                    $v->parent_seller_name = $parent_seller->name;
                }
            }
            //法人代表
            $legal_person_name = '暂无信息';
            $certificationDealer = CertificationDealer::where('dealer_id',$v->dealer_id)->first();
            if($certificationDealer){
                $legal_person_name = $certificationDealer->legal_person_name;
            }
            $v->legal_person_name = $legal_person_name;
            $v->isOn = $v->account_status==AdministratorDealer::STATUS_ON;
            //已授权/可授权设计师账号数
            $designerCount = $v->quota_designer_used ."/" .$v->quota_designer;
            $v->designer_count = $designerCount;
            $v->account_status_text = AdministratorDealer::statusGroup($v->account_status);
            $v->dealer_status_text = OrganizationDealer::statusGroup($v->dealer_status);
            $v->changeStatusApiUrl = url('admin/brand/sub_admin/seller/api/account/'.$v->id.'/status');

            //服务城市集合
            $v->area_serving = '';
            if ($v->area_serving_city){
                $area_serving_city_ids = explode('|',trim($v->area_serving_city,'|'));
                $area_serving_citys = Area::where('level',2)->whereIn('id',$area_serving_city_ids)->get()->pluck('shortname')->toArray();
                if(count($area_serving_citys)>0){
                    $v->area_serving = implode(',',$area_serving_citys);
                }
            }

            //可见城市集合
            $v->area_visible = '';
            if ($v->area_visible_city){
                $area_visible_city_ids = explode('|',trim($v->area_visible_city,'|'));
                $area_visible_citys = Area::where('level',2)->whereIn('id',$area_visible_city_ids)->get()->pluck('shortname')->toArray();
                if(count($area_visible_citys)>0){
                    $v->area_visible = implode(',',$area_visible_citys);
                }
            }

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //获取管理员权限树
    public function get_admin_privilege($id)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $handleAdmin = AdministratorDealer::query()
            ->where(function($query) use($loginAdmin){
                $query->whereRaw(" find_in_set('".$loginAdmin->id."',path) ");
                $query->orWhere('id',$loginAdmin->id);
            })
            ->where('level','>=',$loginAdmin->level)
            ->find($id);

        $selected_privileges = [5];

        $privilege_options = $handleAdmin->getAllPermissions()->toArray();

        $data['list'] = $privilege_options;
        $data['checkedId'] = $selected_privileges;

        $this->respData($data);
    }

    //账号新建
    public function account_store(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'seller_name' => 'required',
            'contact_name' => 'required',
            'login_username' => 'required',
            'login_mobile' => 'required',
            'level' => 'required',
            //'area_serving_id' => 'required',
            'parent_seller_id' => 'present',
            //'privilege_area_serving' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //判断等级是否正确
        /*if(!key_exists($input_data['privilege_area_serving'],DetailDealer::privilegeAreaServingGroup())){
            $this->respFail('服务地区范围错误！');
        }*/

        //判断等级是否正确
        if(!in_array($input_data['level'],array('1','2'))){
            $this->respFail('销售商等级错误！');
        }

        //判断是否已达限额
        $level = intval($input_data['level']);
        $quota_dealer_lv1 = $brand->quota_dealer_lv1;
        $seller_count = OrganizationDealerService::countBrandOnAndVerifySellerByLevel($brand->id,$level);
        if( $seller_count >= $quota_dealer_lv1 ){
            $this->respFail('该类型销售商数量已达限额！');
        }

        $checkArray = [
            'platform.app_info.global.seller.name.character_limit'=>$input_data['seller_name'],
            'platform.app_info.global.seller.contact_name.character_limit'=>$input_data['contact_name'],
        ];
        $rejectReason = ParamCheckService::check_length_param_config($checkArray);
        if($rejectReason<>''){
            DB::rollback();
            $this->respFail($rejectReason);
        }

        //添加账号接收手机
        if ($input_data['login_mobile']!=''){
            $validator = Validator::make($input_data, [
                'login_mobile' => 'phone'
            ]);

            if ($validator->fails()) {
                $this->respFail('请填写正确的手机号！');
            }
        }

        //其他校验

        //判断登录用户名是否存在
        $exist = AdministratorDealer::query()->where('login_username',$input_data['login_username'])->first();
        if($exist){$this->respFail('登录用户名已存在！');}

        $exist = AdministratorBrand::query()->where('login_username',$input_data['login_username'])->first();
        if($exist){$this->respFail('登录用户名已存在(b)！');}

        $exist = AdministratorDealer::query()->where('login_mobile',$input_data['login_mobile'])->first();
        if($exist){$this->respFail('手机号码已存在！');}

        $exist = AdministratorBrand::query()->where('login_mobile',$input_data['login_mobile'])->first();
        if($exist){$this->respFail('手机号码已存在(b)！');}

        //判断城市合法性
       /* $district = Area::where('level',3)->select(['id','name'])->where('id',$input_data['area_serving_id'])->first();
        if(!$district){
            $this->respFail('城市不存在');
        }*/

        //如果是一级销售商，则判断本品牌在该城市是否已存在一级销售商（已改为与城市无关20191107）
        /*if($input_data['level']==1){
            $exist_seller1 = OrganizationDealerService::getBrandAllSeller1InCityEntry($brand->id,$district->id)
                ->orderBy('id','desc')
                ->select(['id','name'])
                ->first();
            if($exist_seller1){$this->respFail('该地区已存在一级销售商');}
        }*/

        //如果是二级销售商，则判断是否有选择上级销售商，以及上级销售商在服务地区的合法性
        if($input_data['level']==2){
            if(!$input_data['parent_seller_id']){
                $this->respFail('请选择所属一级销售商');
            }
            $parent_seller_id = $input_data['parent_seller_id'];
            $parent_seller1 = OrganizationDealerService::getBrandLegalSeller1Entry($brand->id)
                ->orderBy('id','desc')
                ->select(['id','name'])
                ->where('id',$parent_seller_id)
                ->first();
            if(!$parent_seller1){$this->respFail('该一级销售商不存在');}
        }


        //判断服务城市、可见城市合法性
        if(!isset($input_data['area_serving_city'])){
            $this->respFail('请至少添加一个服务城市');
        }else{
            if(count($input_data['area_serving_city'])<=0){
                $this->respFail('请至少添加一个服务城市');
            }else{
                if (count($input_data['area_serving_city']) != count(array_unique($input_data['area_serving_city']))) {
                    $this->respFail('服务城市有重复，请修改');
                }
            }
        }
        if(isset($input_data['area_visible_city'])){
            if (count($input_data['area_visible_city']) != count(array_unique($input_data['area_visible_city']))) {
                $this->respFail('可见城市有重复，请修改');
            }
        }



        DB::beginTransaction();

        $loginAdmin = $this->authService->getAuthUser();

        try{
            //先新建组织
            //要先获取organization_id_code
            $organization_id_code = $this->getNameServices->getSellerIdCode($brand->id);
            //建销售商
            $organization = new OrganizationDealer();
            if (!$organization){$this->respFail('系统错误');}
            $organization->p_brand_id = $brand->id;
            if($input_data['level']==2){
                $organization->p_dealer_id = $input_data['parent_seller_id'];

            }
            $organization->level = $input_data['level'];
            $organization->name = $input_data['seller_name'];
            $organization->short_name = $input_data['seller_name'];
            $organization->create_administrator_id = $loginAdmin->id;
            $organization->contact_name = $input_data['contact_name'];
            $organization->organization_id_code = $organization_id_code;
            $organization->expired_at = $brand->expired_at;
            $organization->product_category = $brand->product_category;
            $organization->save();

            //建销售商详情信息
            $area_serving_province = '';
            $area_serving_city = '';
            $area_visible_province = '';
            $area_visible_city = '';
            if(
                isset($input_data['area_serving_province']) &&
                is_array($input_data['area_serving_province']) &&
                count($input_data['area_serving_province'])>0 ){
                $area_serving_province = "|".implode('|',$input_data['area_serving_province'])."|";
            }
            if(
                isset($input_data['area_serving_city']) &&
                is_array($input_data['area_serving_city']) &&
                count($input_data['area_serving_city'])>0 ){
                $area_serving_city = "|".implode('|',$input_data['area_serving_city'])."|";
            }
            if(
                isset($input_data['area_visible_province']) &&
                is_array($input_data['area_visible_province']) &&
                count($input_data['area_visible_province'])>0 ){
                $area_visible_province = "|".implode('|',$input_data['area_visible_province'])."|";
            }
            if(
                isset($input_data['area_visible_city']) &&
                is_array($input_data['area_visible_city']) &&
                count($input_data['area_visible_city'])>0 ){
                $area_visible_city = "|".implode('|',$input_data['area_visible_city'])."|";
            }
            $detail = new DetailDealer();
            $detail->area_serving_province = $area_serving_province;
            $detail->area_serving_city = $area_serving_city;
            $detail->area_visible_province = $area_visible_province;
            $detail->area_visible_city = $area_visible_city;
            $detail->dealer_id = $organization->id;
            //$detail->area_serving_id = $district->id;
            $detail->product_category = $brand->product_category;
            //$detail->privilege_area_serving = $input_data['privilege_area_serving'];
            $detail->save();
            if (!$detail){$this->respFail('系统错误1');}


            //建销售商统计信息
            $statistic = new StatisticAccountDealer();
            $statistic->dealer_id = $organization->id;
            $statistic->save();
            if (!$statistic){$this->respFail('系统错误2');}

            //更新组织账号
            $brandAccountCode = $this->getNameServices->getSellerAccountName($organization->id);
            $organization->organization_account = $brandAccountCode;
            $organization->save();

            //建销售商管理员
            //分配超级管理员账号
            $loginAccount = $this->getNameServices->getSellerAdminAccountName($organization->id,1);
            $admin = new AdministratorDealer();
            $admin->dealer_id = $organization->id;
            $admin->login_account = $loginAccount;
            $admin->login_username = $input_data['login_username'];
            $admin->login_mobile = $input_data['login_mobile'];
            $admin->login_password = bcrypt($input_data['login_mobile']);
            $admin->created_by_administrator_id = $loginAdmin->id;
            $admin->is_super_admin = AdministratorDealer::IS_SUPER_ADMIN_YES;
            $admin->save();

            //分配未审核管理员的角色
            $role = RoleDealer::OfName('seller.pre_super_admin')->first();
            if(!$role){
                DB::rollback();
                $this->respFail('暂无可分配的管理员角色');
            }
            $admin->assignRole($role);

            DB::commit();

            //发送手机短信
            /*if ($input_data['telephone']){
                $msgService = new GetVerifiCodeService();
                $msg_content = '您的管理账号用户名为：'.$admin->login_username.'，初始密码为手机号码，请尽快登录修改，谢谢。';
                $msgService->sendMobile($input_data['telephone'],$msg_content);
            }*/

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }

    //编辑
    public function account_update($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $input_data = $request->all();

        //其他校验

        //判断登录用户名是否存在
        $admin_id = $id;
        $handleAdmin = AdministratorDealer::query()
            ->find($admin_id);
        if(!$handleAdmin){$this->respFail('销售商管理员不存在！');}
        $seller = $handleAdmin->dealer;
        $seller_detail = $seller->detail;

        //判断服务城市、可见城市合法性
        if(!isset($input_data['area_serving_city'])){
            $this->respFail('请至少添加一个服务城市');
        }else{
            if(count($input_data['area_serving_city'])<=0){
                $this->respFail('请至少添加一个服务城市');
            }else{
                if (count($input_data['area_serving_city']) != count(array_unique($input_data['area_serving_city']))) {
                    $this->respFail('服务城市有重复，请修改');
                }
            }
        }
        if(isset($input_data['area_visible_city'])){
            if (count($input_data['area_visible_city']) != count(array_unique($input_data['area_visible_city']))) {
                $this->respFail('可见城市有重复，请修改');
            }
        }

        DB::beginTransaction();

        try{

            //开始------------地域可见性设置

            $area_serving_province = '';
            $area_serving_city = '';
            $area_visible_province = '';
            $area_visible_city = '';
            if(
                isset($input_data['area_serving_province']) &&
                is_array($input_data['area_serving_province']) &&
                count($input_data['area_serving_province'])>0 ){
                $area_serving_province = "|".implode('|',$input_data['area_serving_province'])."|";
            }
            if(
                isset($input_data['area_serving_city']) &&
                is_array($input_data['area_serving_city']) &&
                count($input_data['area_serving_city'])>0 ){
                $area_serving_city = "|".implode('|',$input_data['area_serving_city'])."|";
            }
            if(
                isset($input_data['area_visible_province']) &&
                is_array($input_data['area_visible_province']) &&
                count($input_data['area_visible_province'])>0 ){
                $area_visible_province = "|".implode('|',$input_data['area_visible_province'])."|";
            }
            if(
                isset($input_data['area_visible_city']) &&
                is_array($input_data['area_visible_city']) &&
                count($input_data['area_visible_city'])>0 ){
                $area_visible_city = "|".implode('|',$input_data['area_visible_city'])."|";
            }
            $seller_detail->area_visible_province = $area_visible_province;
            $seller_detail->area_visible_city = $area_visible_city;
            $seller_detail->area_serving_province = $area_serving_province;
            $seller_detail->area_serving_city = $area_serving_city;
            $saveResult = $seller_detail->save();

            //应执行其下设计师、方案的可见性

            SearchAlbum::where('dealer_id',$seller_detail->dealer_id)->update([
                'area_serving_cities'=>$area_serving_city,
                'area_visible_cities'=>$area_visible_city,
            ]);

            DesignerDetail::where('dealer_id',$seller_detail->dealer_id)->update([
                'area_serving_cities'=>$area_serving_city,
                'area_visible_cities'=>$area_visible_city,
            ]);

            //结束------------地域可见性设置

            if(!$saveResult){
                DB::rollback();
                $this->respFail('数据保存失败！');
            }

            DB::commit();

            $this->respData();

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误');
        }

        $this->respFail();

    }

    //修改密码
    public function modify_pwd(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $admin_id = $input_data['id'];
        $admin = AdministratorDealer::find($admin_id);
        if(!$admin){
            $this->respFail('销售商管理员不存在！');
        }

        $dealer = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->find($admin->dealer_id);
        if(!$dealer){
            $this->respFail('信息不存在！');
        }

        //其他校验
        //品牌修改下属销售商管理员时，不需要提供旧密码，直接修改

        if($input_data['new_password'] != $input_data['confirm_password']){
            $this->respFail('两次新密码输入不一致！');
        }

        DB::beginTransaction();

        try{
            $admin->login_password = bcrypt($input_data['new_password']);
            $result = $admin->save();

            //写入账号通知
            $now_time = Carbon::now();
            $result1 = false;
            if($admin->is_super_admin == AdministratorDealer::IS_SUPER_ADMIN_YES){
                //写入销售商账号通知
                $msg = new MsgSystemSellerService();
                $msg->setDealerId($dealer->id);
                $msg->setContent('您的密码于'.$now_time.'被上级管理员修改');
                $msg->setType(MsgSystemDealer::TYPE_CERTIFICATION);
                $result1= $msg->add_msg();
            }else{
                $msg = new MsgAccountSellerService();
                $msg->setAdministratorId($admin->id);
                $msg->setDealerId($dealer->id);
                $msg->setContent('您的密码于'.$now_time.'被上级管理员修改');
                $msg->setType(MsgAccountDealer::TYPE_MODIFY_PWD);
                $result1= $msg->add_msg();
            }

            if(!$result || !$result1){
                DB::rollback();
                $this->respFail('操作失败');
            }

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }

    //修改密码
    public function integral_distribute(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'integral' => 'required',
            'remark' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $admin_id = $input_data['id'];
        $admin = AdministratorDealer::find($admin_id);
        if(!$admin){
            $this->respFail('销售商管理员不存在！');
        }

        $dealer = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->find($admin->dealer_id);
        if(!$dealer){
            $this->respFail('信息不存在！');
        }

        //其他校验
        //品牌修改下属销售商管理员时，不需要提供旧密码，直接修改

        $add_integral = intval($input_data['integral']);

        if($add_integral<=0){
            $this->respFail('请填写正整数！');
        }

        DB::beginTransaction();

        try{

            $before_integral = intval($dealer->point_money);
            $add_integral = intval($add_integral);
            $after_integral = $before_integral+$add_integral;

            //记录积分变化
            $log = new IntegralLogDealer();
            $log->dealer_id = $dealer->id;
            $log->type = IntegralLogDealer::TYPE_GIVEN_BY_BRAND;
            $log->integral = $add_integral;
            $log->available_integral = $after_integral;
            $log->remark = $input_data['remark'];
            $result = $log->save();

            if(!$result){
                DB::rollback();
                $this->respFail('操作失败');
            }

            //真正增加销售商积分
            DB::table('organization_dealers')
                ->where('id',$dealer->id)
                ->increment('point_money',$add_integral);

            DB::commit();

            $this->respData([]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }

    //配额管理
    public function account_config($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $filter = [
            'id' => 'required',
            'quota_designer' => 'required',
            'expired_at' => 'required'
        ];

        $input_data = $request->all();

        $validator = Validator::make($input_data, $filter);
        if ($validator->fails()) {
            $this->respFail('请正确填写信息后再提交！');
        }

        //其他校验

        //判断配额值是否正整数
        if(
            !preg_match("/^[0-9][0-9]*$/",$input_data['quota_designer'])
        ){
            $this->respFail('请输入正整数数值！');
        }

        //判断登录用户名是否存在
        $handleAdmin = AdministratorDealer::query()
            ->find($input_data['id']);
        if(!$handleAdmin){$this->respFail('销售商管理员不存在！');}
        $seller = $handleAdmin->dealer;

        //判断是否有超过品牌自身可授权的销售商设计师限额
        $brand_quota = intval($brand->quota_designer_dealer);
        //品牌旗下其他销售商的设计师限额总和
        $other_seller_quota = OrganizationDealerService::getBrandAllSellerEntry($brand->id)
            ->where('id','<>',$seller->id)
            ->sum('quota_designer');

        $seller_origin_quota = intval($seller->quota_designer);
        $new_seller_quota = intval($input_data['quota_designer']);
        /*if($new_seller_quota>$seller_origin_quota){
            if(($new_seller_quota+$other_seller_quota)>$brand_quota){
                $available = $brand_quota - $other_seller_quota;
                $this->respFail('数量超过可授权销售商设计师限额！请填写<='.$available.'的数量');
            }
        }*/

        $available = $brand_quota - $other_seller_quota;
        if($available<=0){
            if($available==0){
                $this->respFail('可授权销售商设计师额度为0！不足以分配');

            }else{
                $this->respFail('可授权销售商设计师额度不足！已超'.abs($available).'个');

            }

        }else{
            if($new_seller_quota > $available){
                $this->respFail('数量超过可授权销售商设计师限额！请填写<='.$available.'的数量');

            }
        }


        //判断有效期是否超过了所属品牌的有效期
        $seller_expired_at = $input_data['expired_at'];
        $seller_expired_at_time = strtotime($seller_expired_at);
        $brand_expired_at = $brand->expired_at;
        $brand_expired_at_time = strtotime($brand_expired_at);
        if($seller_expired_at_time>$brand_expired_at_time){
            $this->respFail('超过品牌的有效期：'.$brand_expired_at.'，无法设置');

        }


        DB::beginTransaction();

        try{

            if(!$seller){
                $this->respFail('销售商不存在！');
            }

            $seller->quota_designer = $input_data['quota_designer'];
            $seller->expired_at = $input_data['expired_at'];
            $saveResult = $seller->save();

            if(!$saveResult){
                DB::rollback();
                $this->respFail('数据保存失败！');
            }

            DB::commit();

            $this->respData();

        }catch(\Exception $e){

            DB::rollback();

            $this->respFail('系统错误');
        }

        $this->respFail();

    }

    //修改状态
    public function change_status($id, Request $request)
    {

        //判断登录用户名是否存在
        $handleAdmin = AdministratorDealer::query()
            ->find($id);
        if(!$handleAdmin){$this->respFail('销售商管理员不存在！');}

        $seller = $handleAdmin->dealer;
        if(!$seller){$this->respFail('销售商不存在！');}

        DB::beginTransaction();

        try{
            //同时设置销售商管理员和销售商

            //更新状态
            if($handleAdmin->status==AdministratorDealer::STATUS_OFF){
                $handleAdmin->status = AdministratorDealer::STATUS_ON;
            }else{
                $handleAdmin->status = AdministratorDealer::STATUS_OFF;
            }

            $adminResult = $handleAdmin->save();

            if(!$adminResult ){
                DB::rollback();
                $this->respFail('数据更新错误');
            }

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }

    //异步获取一级销售商
    public function ajax_parent_seller(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;
        if(!$brand){
            $this->respFail('品牌不存在');
        }

        $limit= $request->input('limit',30);
        $keyword= $request->input('keyword','');

        $entry = OrganizationDealer::query()
            ->where('status',OrganizationDealer::STATUS_ON)
            ->where('p_brand_id',$brand->id)
            ->where('level',1);

        if($keyword){
            $entry->where(function($query) use($keyword){
                $query->where('name','like',"%".$keyword."%");
                $query->orWhere('short_name','like',"%".$keyword."%");
                $query->orWhere('brand_name','like',"%".$keyword."%");
            });

        }

        $datas=$entry->paginate($limit);

        return response([
            'code'=>0,
            'msg' =>'',
            'count' =>$datas->total(),
            'data'  =>$datas->items()
        ]);
    }



}
