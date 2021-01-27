<?php

namespace App\Http\Controllers\v1\admin\common;

use App\Http\Controllers\v1\VersionController;
use App\Http\Repositories\common\OrganizationRepository;
use App\Http\Services\common\file_upload\UploadOssService;
use App\Http\Services\common\PrivilegeService;
use App\Http\Services\common\SystemLogService;
use App\Http\Services\v1\admin\AuthService;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use App\Models\Area;
use App\Models\PrivilegeOrganization;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackendMenuController extends VersionController
{

    public function __construct(){
        
    }

    public function side_menu_redirect(Request $request)
    {
        $auth_service = new AuthService();

        $menu_id = $request->input('mid',0);
        session()->put('admin.menu_info.now_side_menu_id',$menu_id);

        $guardName = $auth_service->getAuthUserGuardName();

        $defaultUrl = 'admin/' . $auth_service->getAuthUserGuardName();

        if($menu_id==0){
            session()->forget('admin.menu_info');
            return redirect(url('admin/'.$guardName));
        }else{
            $privilegeModel = GuardRBACService::getPrivilegeModelByGuard($guardName);
            $privilege = $privilegeModel->find($menu_id);
            if($privilege && $privilege->url){
                //如果当前菜单有跳转url，则跳转
                $url = PrivilegeService::get_backend_format_url($privilege->url);
                return redirect($url);
            }else{
                //如果当前菜单无跳转url，则返回
                return back();
            }
        }

    }

    public function side_menu_redirect_bak(Request $request)
    {
        $auth_service = new AuthService();

        $menu_id = $request->input('mid',0);
        session()->put('admin.menu_info.now_side_menu_id',$menu_id);

        $guardName = $auth_service->getAuthUserGuardName();

        $defaultUrl = 'admin/' . $auth_service->getAuthUserGuardName();

        if($menu_id==0){
            session()->forget('admin.menu_info');
            return redirect(url('admin/'.$guardName));
        }else{
            $privilegeModel = GuardRBACService::getPrivilegeModelByGuard($guardName);
            $privilege = $privilegeModel->find($menu_id);
            if($privilege && $privilege->url){
                //如果当前菜单有跳转url，则跳转
                $url = PrivilegeService::get_backend_format_url($privilege->url);
                return redirect($url);
            }else{
                //如果当前菜单无跳转url，则查找下级body菜单有无可跳转的url
                $privileges = PrivilegeService::get_backend_privilegs();

                $sorted = collect($privileges)->sortBy('path')->sortBy('sort');

                $sortedPrivileges = $sorted->all();

                if($privilege->level==4){
                    return redirect($defaultUrl);
                }

                //levels
                $levelDiff = 4-$privilege->level;
                $levels = [];
                for($i=1;$i<=$levelDiff;$i++){
                    $levels[] = $privilege->level+$i;
                }

                //筛选符合条件的nav权限
                $sortedPrivileges = collect($sortedPrivileges)->filter(function($v) use($privilege,$levels){
                    if(!in_array($v->level,$levels)){return false;}
                    if(strpos($v->path,(string)$privilege->id) === false){return false;}
                    if(!$v->is_show_menu){return false;}
                    if(!$v->url){return false;}
                    return true;
                });

                $result = array();
                $result['data'] = null;
                if(count($sortedPrivileges)>0){
                    //获取首个数据
                    $sortedPrivileges = $sortedPrivileges->toArray();
                    sort($sortedPrivileges);
                    $first = array_shift($sortedPrivileges);
                    return redirect($first['url']);
                }else {
                    //该菜单子孙节点都没有可跳转url
                    return redirect($defaultUrl);
                }
            }
        }

    }

    public function body_nav_root_redirect(Request $request)
    {

        $menu_id = $request->input('mid',0);
        $body_nav_root_url = $request->input('url','');
        session()->put('admin.menu_info.now_body_nav_root_id',$menu_id);

        $body_nav_root_url = urldecode($body_nav_root_url);

        if($body_nav_root_url){
            session()->forget('admin.menu_info.now_body_nav_id');
            session()->forget('admin.menu_info.now_body_tab_id');
            $url = PrivilegeService::get_backend_format_url($body_nav_root_url);
            return redirect($url);
        }else{
            $result = PrivilegeService::get_backend_body_nav_sub_first($menu_id);
            if(isset($result['data']) && $result['data']){
                //如果body_nav有可跳转的，则跳转
                session()->put('admin.menu_info.now_body_nav_id',$result['data']['id']);
                return redirect($result['url']);
            }else{
                //如果body_nav有可跳转的，则再找body的tab菜单有没有可跳转url
                $body_tab_first = PrivilegeService::get_backend_body_tab_first_by_level1();

                $url = $body_tab_first['url'];
                if($body_tab_first['data']){
                    session()->put('admin.menu_info.now_body_nav_id',$body_tab_first['data']['parent_id']);
                    session()->put('admin.menu_info.now_body_tab_id',$body_tab_first['data']['id']);
                }
                return redirect($url);
            }
        }



    }

    public function body_nav_redirect(Request $request)
    {

        $menu_id = $request->input('mid',0);
        $body_nav_url = $request->input('url','');
        session()->put('admin.menu_info.now_body_nav_id',$menu_id);

        $body_nav_url = urldecode($body_nav_url);
        $url = PrivilegeService::get_backend_body_tab_first_url($body_nav_url);

        return redirect($url);
    }

    public function body_tab_redirect(Request $request)
    {

        $menu_id = $request->input('id',0);
        $body_tab_url = $request->input('url','');
        session()->put('admin.menu_info.now_body_tab_id',$menu_id);

        $body_tab_url = urldecode($body_tab_url);
        $url = PrivilegeService::get_backend_format_url($body_tab_url);

        return redirect($url);
    }


}
