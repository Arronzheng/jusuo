<?php $side_menus = \App\Http\Services\common\PrivilegeService::get_backend_side_menu();?>
<?php $now_side_menu_id = session('admin.menu_info.now_side_menu_id');?>
  <?php $authService = new \App\Http\Services\v1\admin\AuthService();
$guard = $authService->getAuthUserGuardName();?>


<ul class="layui-nav layui-nav-tree c-side-menu" id="side-nav-ul">
    <div class="layui-logo">管理后台</div>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="@if($now_side_menu_id==0) layui-this @endif" href="{{url($url_prefix."admin/common/side_menu_redirect")}}">首页</a>
    </li>
    @foreach($side_menus as $level1)
        <li class="layui-nav-item layui-nav-itemed">
            <a class="" href="{{$level1->url?url($url_prefix."admin/common/side_menu_redirect?mid=".$level1->id):'javascript:;'}}">{{$level1->display_name}}</a>
            @if(!$level1->child)
            <img style="position:absolute;height:17px;width:15px;top:12px;right:18px;" src="{{asset('v1/images/admin/common/side_nav_point_right.png')}}"/>
            @endif
            @if($level1->child)
                <dl class="layui-nav-child">
                    @foreach($level1->child as $level2)
                        <dd >
                            <a href="{{$level2->url?url($url_prefix."admin/common/side_menu_redirect?mid=".$level2->id):'javascript:;'}}">{{$level2->display_name}}</a>
                            @if($level2->child)
                                <ul class="sub-nav-panel">
                                    @foreach($level2->child as $level3)
                                        <li>
                                            @if($level3->child)
                                                <div class="title">{{$level3->display_name}}</div>
                                                <div class="nav-list">
                                                    @foreach($level3->child as $level4)
                                                        <a href="{{$level4->url?url($url_prefix."admin/common/side_menu_redirect?mid=".$level4->id):'javascript:;'}}" class="nav-block">{{$level4->display_name}}</a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <a class="title" href="{{$level3->url?url($url_prefix."admin/common/side_menu_redirect?mid=".$level3->id):'javascript:;'}}">{{$level3->display_name}}</a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </dd>
                    @endforeach
                </dl>
            @endif


        </li>

    @endforeach

</ul>