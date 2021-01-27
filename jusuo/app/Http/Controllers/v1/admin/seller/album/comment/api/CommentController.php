<?php

namespace App\Http\Controllers\v1\admin\seller\album\comment\api;

use App\Http\Services\common\OrganizationService;
use App\Models\Album;
use App\Models\AlbumComments;
use App\Models\CeramicSeries;
use App\Http\Services\common\file_upload\FormUploadService;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\LayuiTableService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\AdministratorBrand;
use App\Models\Designer;
use App\Models\DesignerDetail;
use App\Models\PrivilegeBrand;
use App\Models\ProductCeramic;
use App\Models\RoleBrand;
use App\Models\RolePrivilegeBrand;
use App\Models\TestData;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends ApiController
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
        $seller = $loginAdmin->dealer;

        $keyword = $request->input('keyword',null);
        $dateStart = $request->input('date_start',null);
        $dateEnd = $request->input('date_end',null);
        $sort = $request->input('sort','');
        $order = $request->input('order','');
        $limit = $request->input('limit',10);

        $entry = AlbumComments::query();

        $entry->whereHas('album',function($album)use($seller){
            $album->whereHas('designer',function($designer)use($seller){
                $designer->where('organization_type',Designer::ORGANIZATION_TYPE_SELLER);
                $designer->where('organization_id',$seller->id);
            });
        });

        if($keyword!==null){
            $entry->where(function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%');
                $query->orWhere('short_name','like','%'.$keyword.'%');
            });
        }

        if($dateStart!==null && $dateEnd!==null){
            $entry->whereBetween('created_at', array($dateStart.' 00:00:00', $dateEnd.' 23:59:59'));
        }

        if($sort && $order){
            $entry->orderByRaw("CONVERT(".$sort." USING gbk) ".$order);
        }

        $entry->orderBy('id','desc');

        $datas =$entry->paginate(intval($limit));

        $datas->transform(function($v){

            $v->album_title = '';
            $v->album_web_id_code = '';
            $album = Album::find($v->album_id);
            if($album){
                $v->album_title = $album->title;
                $v->album_web_id_code = $album->web_id_code;
            }

            $v->changeStatusApiUrl = url('admin/seller/album/comment/api/'.$v->id.'/status');
            $v->status_text = AlbumComments::statusGroup($v->status);

            return $v;
        });

        //转为layui可用的数据格式
        $datas = LayuiTableService::getTableResponse($datas);

        return json_encode($datas);
    }

    public function change_status($id, Request $request)
    {
        $loginAdmin = $this->authService->getAuthUser();


        $data = AlbumComments::find($id);
        if(!$data){
            $this->respFail('数据不存在');
        }

        DB::beginTransaction();

        try{

            //更新状态
            if($data->status==AlbumComments::STATUS_OFF){
                $data->status = AlbumComments::STATUS_ON;
            }else{
                $data->status = AlbumComments::STATUS_OFF;
            }

            $data->save();

            DB::commit();

            $this->respData([]);

        }catch (\Exception $e){

            DB::rollback();

            $this->respFail($e);
        }

    }
}