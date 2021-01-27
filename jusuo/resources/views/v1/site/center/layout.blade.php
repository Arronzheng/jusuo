<?php
$final_css = [
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/myplan.css',
];
$final_js = [
        '/v1/js/ajax.js',
];
if(isset($css)){$final_css = array_merge($final_css,$css);}
if(isset($js)){$final_js = array_merge($final_js,$js);}
?>
@extends('v1.site.layout',[
    'css'=>$final_css,
    'js'=>$final_js
])

@yield('style')

@section('content')

    <div class="container">
        <div class="content">
            <div class="daohangview">
                <div id="daohangblock">
                    <?php
                    $current_url = \Illuminate\Support\Facades\URL::full();
                    $module = '';
                    if(strpos($current_url,url('/center/album')) !==false){
                        $module = 'album';
                    }else if(strpos($current_url,url('/center/product')) !==false){
                        $module = 'product';
                    }else if(strpos($current_url,url('/center/statistic')) !==false){
                        $module = 'statistic';
                    }else if(strpos($current_url,url('/center/fav')) !==false){
                        $module = 'fav';
                    }else if(strpos($current_url,url('/center/integral')) !==false){
                        $module = 'integral';
                    }else if(strpos($current_url,url('/center/info_notify')) !==false){
                        $module = 'info_notify';
                    }else if(
                            strpos($current_url,url('/center/basic_info')) !==false||
                            strpos($current_url,url('/center/app_info')) !==false||
                            strpos($current_url,url('/center/realname_info')) !==false
                    ){
                        $module = 'profile';
                    }else if(strpos($current_url,url('/center/security_center')) !==false){
                        $module = 'security_center';
                    }

                        $designer = Auth::user();
                        $is_realname = $designer->detail->approve_realname == \App\Models\DesignerDetail::APPROVE_REALNAME_YES;

                        $online_class_on = false;
                        $brand_id = \App\Services\v1\site\DesignerService::getDesignerBrandScope($designer->id);
                        $brandSuperAdmin = \App\Models\AdministratorBrand::where('brand_id',$brand_id)->where('is_super_admin',\App\Models\AdministratorBrand::IS_SUPER_ADMIN_YES)->first();
                        if($brandSuperAdmin && $brandSuperAdmin->can("online_class")){
                            $online_class_on = true;
                        }
                    ?>
                    @if($is_realname)<a class="@if($module=='album') nav_title1 @else nav_title @endif " href="{{url('/center/album')}}" id="navtitle0" ><span class="@if($module=='album') nav_text1 @else nav_text @endif" id="navtext0">我的方案</span></a>@endif
                    @if($is_realname)<a class="@if($module=='product') nav_title1 @else nav_title @endif" href="{{url('/center/product')}}" id="navtitle1" ><span class="@if($module=='product') nav_text1 @else nav_text @endif" id="navtext1">产品列表</span></a>@endif
                    @if($is_realname && $online_class_on)<a target="_blank" class="nav_title" href="{{url('http://www.ijusuo.cn')}}" id="navtitle6" ><span class="nav_text" id="navtext6">在线课堂</span></a>@endif
                    @if($is_realname)<a class="@if($module=='integral') nav_title1 @else nav_title @endif" href="{{url('/center/integral')}}" id="navtitle1" ><span class="@if($module=='integral') nav_text1 @else nav_text @endif" id="navtext1">我的积分</span></a>@endif
                    @if($is_realname)<a class="@if($module=='statistic') nav_title1 @else nav_title @endif" href="{{url('/center/statistic')}}" id="navtitle2" ><span class="@if($module=='statistic') nav_text1 @else nav_text @endif" id="navtext2">我的统计</span></a>@endif
                    @if($is_realname)<a class="@if($module=='fav') nav_title1 @else nav_title @endif" href="{{url('/center/fav')}}" id="navtitle3" ><span class="@if($module=='fav') nav_text1 @else nav_text @endif" id="navtext3">收藏关注</span></a>@endif
                    @if($is_realname)<a class="@if($module=='security_center') nav_title1 @else nav_title @endif" href="{{url('/center/security_center')}}" id="navtitle6" ><span class="@if($module=='security_center') nav_text1 @else nav_text @endif" id="navtext6">安全中心</span></a>@endif


                    <a class="@if($module=='profile') nav_title1 @else nav_title @endif" href="{{url('/center/basic_info')}}" id="navtitle5" ><span class="@if($module=='profile') nav_text1 @else nav_text @endif" id="navtext5">账号信息</span></a>
                    <a class="@if($module=='info_notify') nav_title1 @else nav_title @endif" href="{{url('/center/info_notify')}}" id="navtitle4" ><span class="@if($module=='info_notify') nav_text1 @else nav_text @endif" id="navtext4">消息通知</span></a>

                </div>
            </div>
            @yield('main-content')
        </div>

    </div>
@endsection

@section('script')
    <script>

    </script>
@endsection

@section('body')
    @yield('body')
@endsection

