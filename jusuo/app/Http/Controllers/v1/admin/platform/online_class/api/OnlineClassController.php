<?php

namespace App\Http\Controllers\v1\admin\platform\online_class\api;

use App\Http\Services\common\HttpService;
use App\Models\Banner;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\OnlineClassBrand;
use App\Models\OrganizationBrand;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
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
        $limit = $request->input('limit',20);

        $entry = OnlineClassBrand::query();

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            $brand = OrganizationBrand::find($v->brand_id);
            $v->brand_company_name = $brand->name;
            $v->brand_name = $brand->brand_name;
            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    //修改密码
    public function update(Request $request)
    {
        $input_data = $request->all();
        $loginAdmin = $this->authService->getAuthUser();


        $validator = Validator::make($input_data, [
            'id' => 'required',
            'login_password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->respFail('请完整填写信息后再提交！');
        }

        $data = OnlineClassBrand::find($input_data['id']);

        if(!$data){
            $this->respFail('权限不足！');
        }


        if($input_data['login_password'] != $input_data['confirm_password']){
            $this->respFail('两次密码输入不一致！');
        }


        DB::beginTransaction();

        try{

            $request_params = [
                'brandUserNo'=>$data->class_user_no,
                'mobilePsw'=>trim($input_data['login_password']),
                'rePwd'=>trim($input_data['confirm_password']),
                'remark'=>""
            ];
            $result = HttpService::post_json("http://39.98.131.113:5840/user/pc/brand/update/password",$request_params);


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

            $this->respData(['api_resp'=>$result]);
        }catch(\Exception $e){
            DB::rollback();
            $this->respFail('系统错误！'.$e->getMessage());

        }

    }


}