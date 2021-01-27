<?php

namespace App\Http\Controllers\v1\admin\platform\sub_admin\brand\api;

use App\Http\Controllers\Controller;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\GetNameServices;
use App\Http\Services\common\GetVerifiCodeService;
use App\Http\Services\common\GlobalService;
use App\Http\Services\common\HttpService;
use App\Http\Services\common\InfiniteTreeService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\OrganizationService;
use App\Http\Services\common\StrService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\ParamCheckService;
use App\Http\Services\v1\admin\PrivilegeSellerService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorDealer;
use App\Models\AdministratorPrivilegeBrand;
use App\Models\AdministratorRoleBrand;
use App\Models\Area;
use App\Models\Designer;
use App\Models\DetailBrand;
use App\Models\MsgAccountBrand;
use App\Models\MsgSystemBrand;
use App\Models\OnlineClassBrand;
use App\Models\OnlineClassDesigner;
use App\Models\OrganizationBrand;
use App\Models\OrganizationDealer;
use App\Models\PrivilegeBrand;
use App\Models\PrivilegeDealer;
use App\Models\ProductCategory;
use App\Models\RoleBrand;
use App\Http\Services\v1\admin\ParamConfigUseService;
use App\Models\RolePrivilegeBrand;
use App\Models\StatisticAccountBrand;
use App\Services\v1\admin\MsgAccountBrandMultiService;
use App\Services\v1\admin\MsgAccountBrandService;
use App\Services\v1\admin\MsgSystemBrandMultiService;
use App\Services\v1\admin\MsgSystemBrandService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


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

        $login_name = $request->input('ln',null);
        $name = $request->input('name',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = DB::table('administrator_brands as ab');


        if($login_name!==null){
            $entry = $entry->where(function($query)use($login_name){
                $query->where('ab.login_username','like','%'.$login_name.'%');
                $query->orWhere('ab.login_account','like','%'.$login_name.'%');
            });
        }

        if($name!==null){
            $entry = $entry->where('b.name','like',"%".$name."%");
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('ab.created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');


        $loginAdmin = $this->authService->getAuthUser();

        $entry->join('organization_brands as b','ab.brand_id','=','b.id')
            ->join('detail_brands as bd','bd.brand_id','=','b.id')
            ->select(['ab.id','ab.login_username','ab.login_account','b.brand_name','b.name as company_name','b.product_category',
                'contact_name','contact_telephone','quota_dealer_lv1','quota_dealer_lv2',
                'quota_designer_brand','quota_designer_brand_used',
                'quota_designer_dealer','quota_dealer_lv1_used','quota_dealer_lv2_used',
                'quota_designer_dealer_used','b.created_at','b.expired_at','ab.status as account_status',
                'b.status as brand_status','bd.area_belong_id'])
            ->where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES);

        $datas = $entry->paginate($limit);

        $datas->transform(function($v)use($loginAdmin){
            $product_category_id = $v->product_category;
            $v->product_category_name = '';
            $product_category = ProductCategory::find($product_category_id);
            if($product_category){
                $v->product_category_name = $product_category->name;
            }
            $v->isOn = $v->account_status==AdministratorBrand::STATUS_ON;
            $v->canEditPrivilege = 0;
            if($v->brand_status == OrganizationBrand::STATUS_ON){
                $v->canEditPrivilege = 1;
            }
            //已授权/可授权设计师账号数
            $designerCount = ($v->quota_designer_brand_used+$v->quota_designer_dealer_used) ."/" .
                ($v->quota_designer_brand+$v->quota_designer_dealer);
            //已 授权/可授权销售商账号数
            $sellerCount = ($v->quota_dealer_lv1_used+$v->quota_dealer_lv2_used) ."/".($v->quota_dealer_lv1+$v->quota_dealer_lv2);
            $v->designer_count = $designerCount;
            $v->dealer_count = $sellerCount;
            $v->account_status_text = AdministratorBrand::statusGroup($v->account_status);
            $v->brand_status_text = OrganizationBrand::statusGroup($v->brand_status);
            $v->changeStatusApiUrl = url('admin/platform/sub_admin/brand/api/account/'.$v->id.'/status');
            //所在城市
            $v->area_belong = '';
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
        $handleAdmin = AdministratorBrand::query()
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

    //获取管理员权限编辑的权限树
    public function get_admin_privilege_options(Request $request)
    {
        $admin_id = $request->input('adm_id',0);

        $selected_privileges = [];
        $brandAdmin = AdministratorBrand::find($admin_id);
        if(!$brandAdmin){$this->respFail('品牌管理员不存在');}

        //管理员已关联的【角色】权限（这些权限不用加到权限选项里）
        $selected_role_permissions = $brandAdmin->getPermissionsViaRoles()->pluck('id');

        $selected_privileges = $brandAdmin->getDirectPermissions()->pluck('id');

        //只需获取品牌的一级权限给用户选择（20191120）
        $privilege_options = PrivilegeBrand::where('shown',PrivilegeBrand::SHOWN_YES)
            ->where('level',0)
            ->whereNotIn('id',$selected_role_permissions)
            ->get();

        /*$sorted = collect($privilege_options)->sortBy(function ($privilege, $key) {
            $path = $privilege['path'];
            $path_num = $this->findNum($path);
            return $path_num.$privilege['sort'];
        });

        $privilege_options = $sorted->values()->all();*/

        $data['list'] = $privilege_options;
        $data['checkedId'] = $selected_privileges;

        $this->respData($data);

    }

    //更新品牌管理员的权限
    public function edit_privilege(Request $request)
    {
        $inputData = $request->all();

        $brandAdmin = AdministratorBrand::find($inputData['id']);
        if(!$brandAdmin){
            $this->respFail('品牌管理员不存在！');
        }

        $brand = $brandAdmin->brand;

        $validator = Validator::make($inputData, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        if($brand->status != OrganizationBrand::STATUS_ON){
            $this->respFail('未审核的品牌不能修改权限！');
        }

        //处理数据
        if(!isset($inputData['privileges']) || !is_array($inputData['privileges'])){
            $inputData['privileges'] = [];
        }

        //判断该品牌管理员是否超级管理员
        if(!$brandAdmin->is_super_admin){
            $this->respFail('不允许编辑！');
        }

        DB::beginTransaction();

        try{

            //前端选择的一级权限
            $newPrivilegeL0s = $inputData['privileges'];
            $newPrivileges = [];
            if(count($newPrivilegeL0s)>0){
                $brandAllPrivileges = PrivilegeBrand::where('shown',PrivilegeBrand::SHOWN_YES)->get();
                //根据前端选择的一级权限，获取其及所有子孙权限
                $newPrivileges = $brandAllPrivileges
                    ->filter(function ($item) use($newPrivilegeL0s) {
                        //与上面得出的销售商一级菜单id相同或者path包含id的，则通过
                        $flag = false;
                        if(in_array($item->id,$newPrivilegeL0s)){
                            $flag = true;
                        }else{
                            $path_ids = explode(',',$item->path);
                            if(array_intersect($newPrivilegeL0s,$path_ids)){
                                $flag = true;
                            }
                        }
                        return $flag;
                    })->pluck('id')->toArray();
            }

            //是否打开在线课堂品牌账号新建的弹窗
            $open_online_class_account = false;


            //以下是新逻辑 20200706 11:04 斌
            //判断是否真正修改过管理员权限
            //array_diff：该数组包括了所有在被比较的数组（array1）中，但是不在任何其他参数数组（array2 或 array3 等等）中的值。
            $oldPrivileges = $brandAdmin->getDirectPermissions()->pluck('id')->toArray();
            if($oldPrivileges!=$newPrivileges){
                //如果取消了某个一级权限，则将品牌该管理员所涉及的所有管理员（包含子孙），其所创建的角色的该一级权限将全部删除
                $deletedPrivilegeIds = array_diff($oldPrivileges,$newPrivileges); //本次被取消的权限
                $addedPrivilegeIds = array_diff($newPrivileges,$oldPrivileges); //本次被增加的权限
                if($deletedPrivilegeIds || $addedPrivilegeIds) {
                    $relatedFinalIds = [$brandAdmin->id];
                    $childrenAdminsIds = AdministratorBrand::query()
                        ->whereRaw(" find_in_set('".$brandAdmin->id."',path) ")
                        ->get()->pluck('id')->toArray();
                    if(count($childrenAdminsIds)>0){
                        $relatedFinalIds = array_merge($relatedFinalIds,$childrenAdminsIds);
                    }
                    //最终的管理员ids
                    if(count($relatedFinalIds)>0){
                        $relatedRoleIds = RoleBrand::query()
                            ->whereIn('created_by_administrator_id',$relatedFinalIds)
                            ->get()->pluck('id')->toArray();
                        if($relatedRoleIds){
                            if($deletedPrivilegeIds>0){
                                //品牌管理员删掉被取消的相关权限
                                RolePrivilegeBrand::query()
                                    ->whereIn('privilege_id',$deletedPrivilegeIds)
                                    ->whereIn('role_id',$relatedRoleIds)
                                    ->delete();
                            }
                            if($addedPrivilegeIds>0){
                                //平台管理员新分配给品牌管理员的权限，这里不需要操作，等品牌超级管理员自己分配给角色
                            }
                        }

                        //品牌管理员被平台管理员修改其权限树时，要写入msg_account_brands表
                        $msgSystemDatas = [];
                        $msgAccountDatas = [];
                        $now_time = Carbon::now();
                        foreach($relatedFinalIds as $administrator_id){
                            $admin = AdministratorBrand::find($administrator_id);
                            if($admin->is_super_admin == AdministratorBrand::IS_SUPER_ADMIN_YES){
                                $msgSystemDatas[] = [
                                    'brand_id' =>$admin->brand->id,
                                    'content' =>'你的账号权限于'.$now_time.'被上级管理员修改了。',
                                    'type' => MsgSystemBrand::TYPE_UPDATE_PRIVILEGE
                                ];
                                $result = MsgSystemBrandMultiService::add($msgSystemDatas);
                                if(!$result){
                                    DB::rollback();
                                    $this->respFail('账号通知失败，请重新操作');
                                }
                            }else{
                                $msgAccountDatas[] = [
                                    'administrator_id'=>$administrator_id,
                                    'brand_id' =>$admin->brand->id,
                                    'content' =>'你的账号权限于'.$now_time.'被上级管理员修改了。',
                                    'type' => MsgAccountBrand::TYPE_UPDATE_PRIVILEGE
                                ];
                                $result = MsgAccountBrandMultiService::add($msgAccountDatas);
                                if(!$result){
                                    DB::rollback();
                                    $this->respFail('账号通知失败，请重新操作');
                                }
                            }

                        }

                    }

                    //同步品牌管理员权限
                    //$brandAdmin->syncPermissions($newPrivileges);  //用权限包的这个方法会执行很慢
                    $brandAdmin->permissions()->sync($newPrivileges);
                }

                $time1 = microtime(true);
                //----------根据品牌的新一级权限，更新品牌旗下销售商的一级权限及其子孙权限--------
                PrivilegeSellerService::sync_brand_seller_privilege($brand->id);
                $time2 = microtime(true);
                $time = (($time2-$time1));
                SystemLogService::simple('更新品牌旗下销售商执行时间：'.number_format($time, 10, '.', '')."秒",array());

                //以下是旧逻辑
                /*//----------本管理员所涉及的所有管理员（包含子孙），其所创建的角色的权限将全部清零，需通过这些管理员重新分配
                if(array_diff($oldPrivileges,$newPrivileges) || array_diff($newPrivileges,$oldPrivileges)){
                    $relatedFinalIds = [$brandAdmin->id];
                    $childrenAdminsIds = AdministratorBrand::query()
                        ->whereRaw(" find_in_set('".$brandAdmin->id."',path) ")
                        ->get()->pluck('id')->toArray();
                    if(count($childrenAdminsIds)>0){
                        $relatedFinalIds = array_merge($relatedFinalIds,$childrenAdminsIds);
                    }
                    //最终的管理员ids
                    if(count($relatedFinalIds)>0){
                        $relatedRoleIds = RoleBrand::query()
                            ->whereIn('created_by_administrator_id',$relatedFinalIds)
                            ->get()->pluck('id')->toArray();
                        if($relatedRoleIds){
                            RolePrivilegeBrand::whereIn('role_id',$relatedRoleIds)->delete();
                        }

                        //品牌管理员被平台管理员修改其权限树时，要写入msg_account_brands表
                        $multipleData = [];
                        $now_time = Carbon::now();
                        foreach($relatedFinalIds as $administrator_id){
                            $admin = AdministratorBrand::find($administrator_id);
                            $multipleData[] = [
                                'administrator_id'=>$administrator_id,
                                'brand_id' =>$admin->brand->id,
                                'content' =>'你的账号权限于'.$now_time.'被上级管理员修改了。',
                                'type' => MsgAccountBrand::TYPE_UPDATE_PRIVILEGE
                            ];
                        }

                        $result = MsgAccountBrandMultiService::add($multipleData);
                        if(!$result){
                            DB::rollback();
                            $this->respFail('账号通知失败，请重新操作');
                        }
                    }
                    //同步品牌管理员权限
                    $brandAdmin->syncPermissions($newPrivileges);
                }

                //----------根据品牌的新一级权限，更新品牌旗下销售商的一级权限及其子孙权限--------
                PrivilegeSellerService::sync_brand_seller_privilege($brand->id);*/

                //在线课堂逻辑
                $online_class_privilege_slug = "online_class";
                $privilege = PrivilegeBrand::where('name',$online_class_privilege_slug)->first();
                if(in_array($privilege->id,$newPrivilegeL0s)){
                    //判断该品牌是否已有课堂账号
                    $exist = OnlineClassBrand::where('brand_id',$brand->id)->count();
                    if($exist<=0){
                        $open_online_class_account = true;
                    }
                }
            }


            DB::commit();

            $this->respData([
                'show_online_class_account'=>$open_online_class_account
            ]);

        }catch (\Exception $e){

            DB::rollback();

            SystemLogService::simple('',array(
                $e->getMessage(),
                $e->getTraceAsString()
            ));

            $this->respFail('系统错误！');
        }
    }

    private function findNum($str=''){

        $str=trim($str);
        if(empty($str)){
            return '';
        }
        $temp=array('1','2','3','4','5','6','7','8','9','0');
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(in_array($str[$i],$temp)){
                $result.=$str[$i];
            }
        }
        return $result;
    }

    //账号新建
    public function account_store(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'brand_name' => 'required',
            'login_username' => 'required',
            'login_mobile' => 'required',
            'product_category_id' => 'required',
            'link_man' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        //添加品牌名称
        if ($input_data['brand_name']!=''){
            $length = mb_strlen($input_data['brand_name'],'UTF-8');
            $limit = ParamConfigUseService::find_root('platform.basic_info.brand.brand_name.character_limit');
            if ($length>$limit) {
                $this->respFail('品牌名称不可超过'.$limit.'个字符！');
            }
        }
        $checkArray = [
            'platform.basic_info.brand.brand_name.character_limit'=>$input_data['brand_name'],
            'platform.app_info.global.brand.contact_name.character_limit'=>$input_data['link_man'],
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

        //相同品牌名称
        $exist = OrganizationBrand::where('brand_name',$input_data['brand_name'])
            ->first();
        if($exist){$this->respFail('品牌名称已被使用，请换一个');}

        //其他校验
        //判断登录用户名是否存在
        $exist = AdministratorBrand::query()->where('login_username',$input_data['login_username'])->first();
        if($exist){$this->respFail('登录用户名已存在！');}

        $exist = AdministratorDealer::query()->where('login_username',$input_data['login_username'])->first();
        if($exist){$this->respFail('登录用户名已存在(s)！');}


        $exist = AdministratorBrand::query()->where('login_mobile',$input_data['login_mobile'])->first();
        if($exist){$this->respFail('手机号码已存在！');}

        $exist = AdministratorDealer::query()->where('login_mobile',$input_data['login_mobile'])->first();
        if($exist){$this->respFail('手机号码已存在(s)！');}

        //判断经营品类是否存在
        $productCatExist = ProductCategory::find($input_data['product_category_id']);
        if(!$productCatExist){$this->respFail('经营品类不存在！');}

        DB::beginTransaction();

        $loginAdmin = $this->authService->getAuthUser();

        try{
            //先新建组织
            //要先获取organization_id_code
            $organization_id_code = $this->getNameServices->getBrandIdCode();
            //建品牌
            $organization = new OrganizationBrand();
            if (!$organization){$this->respFail('系统错误');}
            $organization->brand_name = $input_data['brand_name'];
            $organization->short_name = $input_data['brand_name'];
            $organization->product_category = $input_data['product_category_id'];
            //由平台超级管理员新建，所以设为0
            $organization->create_administrator_id = 0;
            $organization->contact_name = $input_data['link_man'];
            $organization->organization_id_code = $organization_id_code;
            $organization->status = OrganizationBrand::STATUS_WAIT_VERIFY;
            //有效期为一周后
            $now = time();
            $expired_at = date("Y-m-d H:i:s",strtotime("+7days",$now));
            $organization->expired_at = $expired_at;
            $organization->save();

            //更新组织账号
            $brandAccountCode = $this->getNameServices->getBrandAccountName($organization->id,$input_data['product_category_id']);
            $organization->organization_account = $brandAccountCode;
            $organization->save();

            //建品牌详情信息
            $detail = new DetailBrand();
            $detail->brand_id = $organization->id;
            $detail->save();
            if (!$detail){$this->respFail('系统错误');}

            //建品牌统计信息
            $stat = new StatisticAccountBrand();
            $stat->brand_id = $organization->id;
            $stat->save();
            if (!$stat){$this->respFail('系统错误');}

            //建品牌超级管理员
            $path = [0];
            $level = 0;
            $path = implode(',',$path);

            //分配超级管理员账号
            $loginAccount = $this->getNameServices->getBrandAdminAccountName($organization->id,1,$input_data['product_category_id']);
            $admin = new AdministratorBrand();
            $admin->brand_id = $organization->id;
            $admin->login_account = $loginAccount;
            $admin->login_username = $input_data['login_username'];
            $admin->login_mobile = $input_data['login_mobile'];
            $admin->login_password = bcrypt($input_data['login_mobile']);
            $admin->created_by_administrator_id = $loginAdmin->id;
            $admin->path = $path;
            $admin->level= 1;
            $admin->is_super_admin = AdministratorBrand::IS_SUPER_ADMIN_YES;
            $admin->save();



            //分配未审核管理员的角色
            $role = RoleBrand::OfName('brand.pre_super_admin')->first();
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

    //在线课堂账号新建
    public function online_class_account_store(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'b' => 'required',
            'name' => 'required',
            'login_account' => 'required',
            'login_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }



        //其他校验
        $brand_id = $request->input('b');
        $exist1 = OnlineClassBrand::query()->where('brand_id',$brand_id)->first();
        if($exist1){$this->respFail('该品牌已有账号！');}


        $exist2 = OnlineClassBrand::query()->where('login_account',$input_data['login_account'])->first();
        if($exist2){$this->respFail('登录账号已存在！');}

        $exist3 = OnlineClassBrand::query()->where('name',$input_data['name'])->first();
        if($exist3){$this->respFail('账号名称已存在！');}

        $brand = OrganizationBrand::find($brand_id);
        if(!$brand){$this->respFail('品牌不存在！');}


        DB::beginTransaction();

        $loginAdmin = $this->authService->getAuthUser();

        $login_account = $request->input('login_account');
        $login_password = $request->input('login_password');
        $name = $request->input('name');

        try{

            //调用远程接口，新建课堂品牌账号
            $result = HttpService::post_json("http://39.98.131.113:5840/user/pc/platform/save",[
                'accountMobile'=>$login_account,
                'clientName'=>$name,
                'mobilePsw'=>$login_password,
                'remark'=>""
            ]);


            if($result['code'] == '200'){
                $account = new OnlineClassBrand();
                $account->brand_id = $brand_id;
                $account->login_account = $login_account;
                $account->login_password = $login_password;
                $account->name = $name;
                $account->class_id = (string)$result['data']['id'];
                $account->class_client_id = (string)$result['data']['clientId'];
                $account->class_user_no = $result['data']['userNo'];
                $account->save();
            }else{
                DB::rollback();

                $this->respFail('课堂接口调用出错！',self::API_CODE_FAIL,$result);
            }


            DB::commit();

            $this->respData(['api_resp'=>$result]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }

    }


    //修改密码
    public function modify_pwd(Request $request)
    {
        $input_data = $request->all();

        $validator = Validator::make($input_data, [
            'id' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $admin = AdministratorBrand::where('is_super_admin',AdministratorBrand::IS_SUPER_ADMIN_YES)
            ->find($input_data['id']);
        if(!$admin){
            $this->respFail('信息不存在！');
        }

        //其他校验
        if($input_data['new_password'] != $input_data['confirm_password']){
            $this->respFail('两次新密码输入不一致！');
        }

        DB::beginTransaction();

        try{
            $admin->login_password = bcrypt($input_data['new_password']);
            $result = $admin->save();

            //写入账号通知


            //写入账号通知
            $now_time = Carbon::now();
            $result1 = false;
            if($admin->is_super_admin == AdministratorBrand::IS_SUPER_ADMIN_YES){
                $msg = new MsgSystemBrandService();
                $msg->setBrandId($admin->brand->id);
                $msg->setContent('您的密码于'.$now_time.'被上级管理员修改');
                $msg->setType(MsgSystemBrand::TYPE_MODIFY_PWD);
                $result1= $msg->add_msg();
            }else{
                $msg = new MsgAccountBrandService();
                $msg->setAdministratorId($admin->id);
                $now_time = Carbon::now();
                $msg->setContent('您的密码于'.$now_time.'被上级管理员修改');
                $msg->setType(MsgAccountBrand::TYPE_MODIFY_PWD);
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
            SystemLogService::simple('',array(
                $e->getTraceAsString()
            ));

            $this->respFail('系统错误');
        }

    }

    //账号更新（配额管理）
    public function account_update($id,Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();

        $filter = [
            'id' => 'required',
            'quota_dealer_lv1' => 'required',
            'quota_dealer_lv2' => 'required',
            'quota_designer_brand' => 'required',
            'quota_designer_dealer' => 'required',
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
            !preg_match("/^[0-9][0-9]*$/",$input_data['quota_dealer_lv1']) ||
            !preg_match("/^[0-9][0-9]*$/",$input_data['quota_dealer_lv2']) ||
            !preg_match("/^[0-9][0-9]*$/",$input_data['quota_designer_brand']) ||
            !preg_match("/^[0-9][0-9]*$/",$input_data['quota_designer_dealer'])
        ){
            $this->respFail('请输入正整数数值！');
        }

        //判断登录用户名是否存在
        $handleAdmin = AdministratorBrand::query()
            ->find($input_data['id']);
        if(!$handleAdmin){$this->respFail('品牌超级管理员不存在！');}

        $brand_id = $handleAdmin->brand_id;
        $quota_dealer_lv1 = intval($input_data['quota_dealer_lv1']);
        $quota_dealer_lv2 = intval($input_data['quota_dealer_lv2']);
        $quota_designer_brand = intval($input_data['quota_designer_brand']);
        $quota_designer_dealer = intval($input_data['quota_designer_dealer']);

        //quota_dealer_lv1检查
        $dealer_lv1_count = OrganizationDealer::query()
            ->where('level',1)->where('p_brand_id',$brand_id)
            ->count();
        if($quota_dealer_lv1< $dealer_lv1_count){
            $this->respFail('该品牌的一级销售商已有'.$dealer_lv1_count.'个，请最小设置为这个数值！');
        }
        //quota_dealer_lv2检查
        $dealer_lv2_count = OrganizationDealer::query()
            ->where('level',2)->where('p_brand_id',$brand_id)
            ->count();
        if($quota_dealer_lv2< $dealer_lv2_count){
            $this->respFail('该品牌的二级销售商已有'.$dealer_lv2_count.'个，请最小设置为这个数值！');
        }
        //quota_designer_brand检查
        $designer_brand_count = Designer::where("organization_id",$brand_id)->count();
        if($quota_designer_brand <$designer_brand_count){
            $this->respFail('该品牌的直属设计师已有'.$designer_brand_count.'个，请最小设置为这个数值！');
        }
        //quota_designer_dealer检查
        $seller_ids = OrganizationDealer::where('p_brand_id',$brand_id)->get()->pluck('id');
        $designer_dealer_count = Designer::whereIn("organization_id",$seller_ids)->count();
        if($quota_designer_dealer <$designer_dealer_count){
            $this->respFail('该品牌的经销商设计师已有'.$designer_dealer_count.'个，请最小设置为这个数值！');
        }

        DB::beginTransaction();

        try{

            $brand = $handleAdmin->brand;

            if(!$brand){
                $this->respFail('品牌不存在！');
            }

            $brand->quota_dealer_lv1 = $input_data['quota_dealer_lv1'];
            $brand->quota_dealer_lv2 = $input_data['quota_dealer_lv2'];
            $brand->quota_designer_brand = $input_data['quota_designer_brand'];
            $brand->quota_designer_dealer = $input_data['quota_designer_dealer'];
            $brand->expired_at = $input_data['expired_at'];
            $saveResult = $brand->save();

            //该品牌下超过新有效期的销售商账号的有效期更新
            $brand_expired_at = $input_data['expired_at'];
            $seller_over_expired = OrganizationDealer::query()
                ->where('p_brand_id',$brand->id)
                ->where('expired_at','>',$brand_expired_at)
                ->update(['expired_at'=>$brand_expired_at]);


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

    public function change_status($id, Request $request)
    {

        //判断登录用户名是否存在
        $handleAdmin = AdministratorBrand::query()
            ->find($id);
        if(!$handleAdmin){$this->respFail('品牌超级管理员不存在！');}

        $brand = $handleAdmin->brand;
        if(!$brand){$this->respFail('品牌不存在！');}

        DB::beginTransaction();

        try{
            //同时设置品牌超级管理员和品牌

            //更新状态
            if($handleAdmin->status==AdministratorBrand::STATUS_OFF){
                $handleAdmin->status = AdministratorBrand::STATUS_ON;
            }else{
                $handleAdmin->status = AdministratorBrand::STATUS_OFF;
            }

            $adminResult = $handleAdmin->save();

            if(!$adminResult){
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



}
