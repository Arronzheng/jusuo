<?php $body_navs = \App\Http\Services\common\PrivilegeService::get_backend_body_nav();?>
<?php $now_body_nav_id = session('admin.menu_info.now_body_nav_id');?>
<?php $now_body_nav_root_id = session('admin.menu_info.now_body_nav_root_id');?>
<?php $now_body_tab_id = session('admin.menu_info.now_body_tab_id');?>

<style>
    .body-nav{}
    .body-nav .layui-nav-child{top:50px;}
    .layui-tab-title li{font-size:12px;line-height:40px!important;}
    .layui-tab-title li.layui-this{background-color:#ffb800;}
    .layui-tab-title li.layui-this a{color:#ffffff;}
</style>
@if(count($body_navs)>0)
    <div class="body-nav">
        <!--<ul class="layui-nav layui-bg-cyan" lay-filter="">
            @foreach($body_navs as $body_nav)
                <li class="layui-nav-item">
                    <?php $nav_parent_redirect_url = url($url_prefix."admin/common/body_nav_redirect?mid=".$body_nav->id."&url=".urlencode($body_nav->url))?>
                    <a class="@if($now_body_nav_id==$body_nav->id) layui-this @endif" href="@if($body_nav->url) {{$nav_parent_redirect_url}} @else javascript:; @endif">{{$body_nav->display_name}}</a>
                    @if($body_nav->child)
                        <dl class="layui-nav-child"> {{--二级菜单--}}
                            @foreach($body_nav->child as $child)
                                <?php $nav_child_redirect_url = url($url_prefix."admin/common/body_nav_redirect?mid=".$child->id."&url=".urlencode($child->url))?>
                                <dd class="@if($now_body_nav_id==$child->id) layui-this @endif" ><a href="{{$nav_child_redirect_url}}">{{$child->display_name}}</a></dd>
                            @endforeach
                        </dl>
                    @endif
                </li>
            @endforeach
        </ul> -->
        <div>
            <div class="layui-tab root-nav">
                <ul class="layui-tab-title">
                    @foreach($body_navs as $body_nav)
                        <?php $nav_parent_redirect_url = url($url_prefix."admin/common/body_nav_root_redirect?mid=".$body_nav->id."&url=".urlencode($body_nav->url))?>
                        <li class="@if($now_body_nav_root_id==$body_nav->id) layui-this @endif">
                            <a href="{{$nav_parent_redirect_url}}">{{$body_nav->display_name}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="layui-tab sub-nav">
                <ul class="layui-tab-title">
                    @foreach($body_navs as $body_nav)
                        @if($body_nav->child)
                            @foreach($body_nav->child as $child)
                                <?php $nav_child_redirect_url = url($url_prefix."admin/common/body_nav_redirect?mid=".$child->id."&url=".urlencode($child->url))?>
                                @if($now_body_nav_root_id == $child->parent_id)
                                    <li class="@if($now_body_nav_id==$child->id) layui-this @endif">
                                        <a href="{{$nav_child_redirect_url}}">{{$child->display_name}}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif

                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){

            var sub_nav_length = $('.body-nav .sub-nav li').length;
            console.log(sub_nav_length);
            if(sub_nav_length<=0){$('.body-nav .sub-nav').hide();}

        });
    </script>
@endif

<?php $body_tabs = \App\Http\Services\common\PrivilegeService::get_backend_body_tab();?>

@if(count($body_tabs)>0)
    <div class="body-tab">
        <div class="layui-tab " >
            <ul class="layui-tab-title">
                @foreach($body_tabs as $body_tab)
                    <?php $tab_url = url($url_prefix."admin/common/body_tab_redirect?id=".$body_tab->id."&url=".urlencode($body_tab->url))?>
                    <li  class="@if($now_body_tab_id == $body_tab->id) layui-this @endif" onclick="location.href='{{$tab_url}}'" >
                        <a href="javascript:;">{{$body_tab->display_name}}</a>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
@endif