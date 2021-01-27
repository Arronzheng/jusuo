<?php

namespace App\Http\Controllers\v1\admin\brand\online_class\api;

use App\Http\Services\common\HttpService;
use App\Http\Services\common\StrService;
use App\Models\Banner;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\DesignerDetail;
use App\Models\NewsBrand;
use App\Models\OnlineClassBrand;
use App\Models\OnlineClassDesigner;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use EasyWeChat\Work\GroupRobot\Messages\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OnlineClassController extends ApiController
{
    private $authService;
    public function __construct(
        AuthService $authService
    )
    {
        $this->authService = $authService;
    }

    //表格异步获取数据
    public function index(Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $limit = $request->input('limit',20);

        $entry = OnlineClassDesigner::query();

        $entry->orderBy('id','desc');

        $entry->where('brand_id',$brand->id);

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v)use($brand){
            $v->type_text = DesignerDetail::designerTypeGroup($v->type);
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //保存
    public function store(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;

        $messages = [
            'login_account.regex'     => '手机号不合法'];
        $validator = Validator::make($input_data, [
            'confirm_password' => 'required',
            'login_password' => 'required',
            'type' => 'required',
            'login_account' => 'regex:/^1[345789][0-9]{9}$/',
        ],$messages);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            if ($error) {
                $this->respFail($error);
            }else{
                $this->respFail('请完整填写信息后再提交！');
            }
        }


        //其他校验
        $exist2 = OnlineClassDesigner::query()->where('login_account',$input_data['login_account'])->first();
        if($exist2){$this->respFail('登录账号已存在！');}

        $class_brand = OnlineClassBrand::query()->where('brand_id',$brand->id)->first();
        if(!$class_brand){
            $this->respFail('品牌课程账号未开通！');
        }

        if($input_data['login_password'] != $input_data['confirm_password']){
            $this->respFail('两次密码输入不一致！');
        }

        DB::beginTransaction();

        $loginAdmin = $this->authService->getAuthUser();

        $login_account = $request->input('login_account');
        $login_password = $request->input('login_password');
        $confirm_password = $request->input('confirm_password');
        $designer_type = $request->input('type');

        try{

            //调用远程接口，新建课堂设计师账号
            $request_params = [
                'brandNo'=>$class_brand->class_client_id,
                'mobile'=>$login_account,
                'password'=>$login_password,
                'repassword'=>$confirm_password,
                'brandUserType'=>"0",
                'ip'=>$request->getClientIp()
            ];
            $result = HttpService::post_json("http://39.98.131.113:5840/user/api/user/register",$request_params);


            if($result['code'] == '200'){
                $account = new OnlineClassDesigner();
                $account->brand_id = $brand->id;
                $account->class_brand_id = $class_brand->id;
                $account->login_account = $login_account;
                $account->login_password = $login_password;
                $account->class_user_no = $result['data']['userNo'];
                $account->class_token = $result['data']['token'];
                $account->type = $designer_type;
                $account->save();
            }else{
                DB::rollback();

                $this->respFail('课堂接口调用出错！',self::API_CODE_FAIL,[
                    'request'=>$request_params,
                    'resp'=>$result
                ]);
            }


            DB::commit();

            $this->respData(['api_resp'=>$result]);

        }catch(\Exception $e){
            DB::rollback();

            $this->respFail('系统错误'.json_encode($e->getMessage()));
        }


    }

    //修改密码
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();
        $brand = $loginAdmin->brand;


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'login_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = OnlineClassDesigner::where('brand_id',$brand->id)->find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }


        if($input_data['login_password'] != $input_data['confirm_password']){
            $this->respFail('两次密码输入不一致！');
        }

        $class_brand = OnlineClassBrand::query()->where('brand_id',$brand->id)->first();
        if(!$class_brand){
            $this->respFail('品牌课程账号未开通！');
        }

        DB::beginTransaction();

        try{

            $request_params = [
                'brandNo'=>$class_brand->class_client_id,
                'mobile'=>trim($data->login_account),
                'newPassword'=>trim($input_data['login_password']),
                'confirmPassword'=>trim($input_data['confirm_password']),
            ];
            $result = HttpService::post_json("http://39.98.131.113:5840/user/api/user/update/password",$request_params);


            if($result['code'] == '200'){
                //更新
                $data->login_password = $input_data['login_password'] ;

                $data->save();
            }else{
                DB::rollback();

                $this->respFail('课堂接口调用出错！',self::API_CODE_FAIL,[
                    'response'=>$result,
                    'request'=>$request_params
                ]);
            }


            DB::commit();

            $this->respData([
                'request'=>$request_params,
                'result'=>$result
            ]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！');

        }

    }


}