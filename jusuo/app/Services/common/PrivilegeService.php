<?php

namespace App\Http\Services\common;

use App\Http\Services\common\StrService;
use App\Http\Services\v1\admin\AuthService;
use App\Http\Services\v1\admin\OrganizationService;
use App\Models\AdministratorBrand;
use App\Models\AdministratorOrganization;
use App\Models\Organization;
use App\Models\PrivilegeBrand;
use App\Models\PrivilegeDealer;
use App\Models\PrivilegeOrganization;
use App\Models\RoleOrganization;
use App\Services\common\GuardRBACService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivilegeService{
    //处理权限专用 全局service
    public function __construct(AuthService $authService)
    {
    }

    public static function get_auth_service()
    {
        $authService = new AuthService();
        return $authService;
    }

    //获取当前登录组织已分配的后台权限
    public static function get_backend_privilegs()
    {
        $privileges = self::get_auth_service()->getAuthUser()->getAllPermissions();
        return $privileges;
    }

    //获取后台左侧菜单权限
    public static function get_backend_side_menu()
    {
        $globalService = new GlobalService();
        $authService = new AuthService();
        $guardName = $authService->getAuthUserGuardName();

        /*if(session()->get('backend_side_menus')){
            $side_menus = session()->get('backend_side_menus');
        }else{
            session()->put('backend_side_menus',$side_menus);
        }*/

        $side_menus = self::get_backend_privilegs();

        //筛选将要显示到左侧菜单的权限
        //$privilegeModel = GuardRBACService::getPrivilegeModelByGuard($guardName);

        $side_menus = collect($side_menus)
            ->where('is_menu',1)
            ->whereIn('level',[0,1,2,3])
            ->sortBy('level')
            ->sortBy('path')
            ->sortBy('sort');


        $side_menus = $globalService->array_to_tree($side_menus,0,0,'parent_id');

        return $side_menus;
    }


    //获取后台主要内容nav菜单权限
    public static function get_backend_body_nav()
    {
        $globalService = new GlobalService();

        $privileges = self::get_backend_privilegs();

        $level1_id = session('admin.menu_info.now_side_menu_id');

        if($level1_id){
            //筛选将要显示到主要内容nav的权限
            $body_navs = collect($privileges)
                ->where('is_menu',PrivilegeOrganization::IS_SHOW_MENU_YES)
                ->sortBy('path')->sortBy('sort')->filter(function($v) use($level1_id){
                    if(!in_array($v->level,[2,3])){return false;}
                    if(strpos($v->path,(string)$level1_id) === false){return false;}
                    if(!$v->is_menu){return false;}
                    return true;
                });

            $body_navs = $globalService->array_to_tree($body_navs,$level1_id,0,'parent_id');
        }else{
            $body_navs = array();
        }


        return $body_navs;
    }

    //获取后台主要内容tab菜单权限
    public static function get_backend_body_tab()
    {
        $privileges = self::get_backend_privilegs();

        $level3_id = session('admin.menu_info.now_body_nav_id');

        if($level3_id){
            //筛选将要显示到主要内容tab的权限
            $body_tabs = collect($privileges)
                ->sortBy('path')->sortBy('sort')->filter(function($v) use($level3_id){
                    if(!in_array($v->level,[4])){return false;}
                    if(strpos($v->path,(string)$level3_id) === false){return false;}
                    if(!$v->is_menu){return false;}
                    return true;
                });
        }else{
            $body_tabs = array();
        }


        return $body_tabs;
    }

    //获取后台主要内容nav菜单的首个可跳转url（通过点击侧边栏获取）
    public static function get_backend_body_nav_first_by_sidemenu()
    {
        $privileges = self::get_backend_privilegs();

        $level1_id = session('admin.menu_info.now_side_menu_id');

        $sorted = collect($privileges)->sortBy('path')->sortBy('sort');

        $body_navs = $sorted->all();

        //筛选符合条件的nav权限
        $body_navs = collect($body_navs)->filter(function($v) use($level1_id){
            if(!in_array($v->level,[2,3])){return false;}
            if(strpos($v->path,(string)$level1_id) === false){return false;}
            if(!$v->is_menu){return false;}
            if(!$v->url){return false;}
            return true;
        });


        $result = array();
        $result['data'] = null;
        if(count($body_navs)>0){
            //获取首个数据
            $body_navs = $body_navs->toArray();
            sort($body_navs);
            $first = array_shift($body_navs);
            $result['data'] = $first;
            $result['url'] = self::get_backend_format_url($first['url']);
        }else {
            $result['url'] = 'admin/' . self::get_auth_service()->getAuthUserGuardName();
        }

        return $result;
    }

    //获取后台主要内容nav菜单中的子菜单的首个数据
    public static function get_backend_body_nav_sub_first($root_id)
    {
        $privileges = self::get_backend_privilegs();

        $sorted = collect($privileges)->sortBy('path')->sortBy('sort');

        $sub_navs = $sorted->all();

        //筛选符合条件的nav权限
        $sub_navs = collect($sub_navs)->filter(function($v) use($root_id){
            if(!in_array($v->level,[3])){return false;}
            if(strpos($v->path,(string)$root_id) === false){return false;}
            if(!$v->is_menu){return false;}
            if(!$v->url){return false;}
            return true;
        });

        $result = array();
        if(count($sub_navs)>0){
            //获取首个数据
            $sub_navs = $sub_navs->toArray();
            sort($sub_navs);
            $first = array_shift($sub_navs);
            $result['data'] = $first;
            $result['url'] = self::get_backend_format_url($first['url']);
        }else {
            $result['url'] = 'admin/' . self::get_auth_service()->getAuthUserGuardName();
        }

        return $result;
    }

    //获取后台主要内容nav菜单的首个可跳转url
    public static function get_backend_body_tab_first_url($body_nav_url='')
    {
        $privileges = self::get_backend_privilegs();

        $level3_id = session('admin.menu_info.now_body_nav_id');
        $sorted = collect($privileges)
            ->sortBy('path')
            ->sortBy('sort');
        $privileges = $sorted->values()->all();

        //筛选符合条件的tab权限
        $body_tabs = collect($privileges)->filter(function($v) use($level3_id){
            if(!in_array($v->level,[4])){return false;}
            if(strpos($v->path,(string)$level3_id) === false){return false;}
            if(!$v->is_menu){return false;}
            if(!$v->url){return false;}
            return true;
        });


        if(count($body_tabs)>0){
            $body_tabs = $body_tabs->toArray();
            //获取首个数据
            $first = array_shift($body_tabs);
            session()->put('admin.menu_info.now_body_tab_id',$first['id']);
            return self::get_backend_format_url($first['url']);
        }else{
            //若无，则返回原来的url
            if($body_nav_url){
                return self::get_backend_format_url($body_nav_url);
            }else{
                session()->forget('admin.menu_info');
                return 'admin/'.self::get_auth_service()->getAuthUserGuardName();
            }
        }
    }

    //获取后台主要内容nav菜单的首个可跳转url
    public static function get_backend_body_tab_first_by_level1()
    {
        $privileges = self::get_backend_privilegs();

        $level1_id = session('admin.menu_info.now_side_menu_id');
        $sorted = collect($privileges)
            ->sortBy('path')
            ->sortBy('sort');
        $privileges = $sorted->values()->all();

        //筛选符合条件的tab权限
        $body_tabs = collect($privileges)->filter(function($v) use($level1_id){
            if(!in_array($v->level,[4])){return false;}
            if(strpos($v->path,(string)$level1_id) === false){return false;}
            if(!$v->is_menu){return false;}
            if(!$v->url){return false;}
            return true;
        });


        $result = array();
        $result['data'] = null;

        if(count($body_tabs)>0){
            $body_tabs = $body_tabs->toArray();
            //获取首个数据
            $first = array_shift($body_tabs);
            $result['data'] = $first;
            $result['url'] = self::get_backend_format_url($first['url']);
        }else{
            session()->forget('admin.menu_info');
            $result['url'] = 'admin/' . self::get_auth_service()->getAuthUserGuardName();
        }

        return $result;
    }

    //获取后台主要内容nav菜单的首个可跳转url
    public static function get_backend_bodsy_tab_first_by_level2()
    {
        $privileges = self::get_backend_privilegs();

        $level2_id = session('admin.menu_info.now_body_nav_root_id');
        $sorted = collect($privileges)
            ->sortBy('path')
            ->sortBy('sort');
        $privileges = $sorted->values()->all();

        //筛选符合条件的tab权限
        $body_tabs = collect($privileges)->filter(function($v) use($level2_id){
            if(!in_array($v->level,[4])){return false;}
            if(strpos($v->path,(string)$level2_id) === false){return false;}
            if(!$v->is_menu){return false;}
            if(!$v->url){return false;}
            return true;
        });


        $result = array();
        $result['data'] = null;

        if(count($body_tabs)>0){
            $body_tabs = $body_tabs->toArray();
            //获取首个数据
            $first = array_shift($body_tabs);
            $result['data'] = $first;
            $result['url'] = self::get_backend_format_url($first['url']);
        }else{
            session()->forget('admin.menu_info');
            $result['url'] = 'admin/' . self::get_auth_service()->getAuthUserGuardName();
        }

        return $result;
    }

    //获取后台左侧菜单的path
    public static function get_backend_format_url($url)
    {
        //当前登录组织
        $guard = self::get_auth_service()->getAuthUserGuardName();

        //替换{guard}为当前登录组织guard
        $result = preg_replace('/(?:\{)(.*)(?:\})/i',$guard, $url);

        return $result;
    }
    
    
    
    /*----------------定时、刷新任务专用-----------------*/

    public static function attach_super_admin_role($admin_id)
    {
        $admin = AdministratorOrganization::find($admin_id);
        if($admin && $admin->organization_type!=Organization::ORGANIZATION_TYPE_PLATFORM){
            //根据组织类型不同，将超级管理员其与不同超级管理员角色绑定
            $role = self::get_organization_super_admin_role($admin->organization_type);
            if($role){
                $admin->attachRole($role);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //同步各超级管理员角色的权限
    public static function sync_super_admin_role_privileges($guardName)
    {

        $role = self::get_organization_super_admin_role($guardName);

        $privilegeModel = GuardRBACService::getPrivilegeModelByGuard($guardName);

        if($role){
            $privileges = $privilegeModel->get()->pluck('id');

            $role->permissions()->sync($privileges);

            return true;
        }else{
            return false;
        }



    }

    private static function get_organization_super_admin_role($guardName)
    {
        $roleModel = GuardRBACService::getRoleModelByGuard($guardName);

        if($roleModel){
            $role = $roleModel->where('name',$guardName.'.super_admin')
                ->first();

            return $role;
        }else{
            return null;
        }


    }
    /*----------------定时刷新任务-----------------*/
}