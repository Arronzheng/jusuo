<?php $side_menus = \App\Http\Services\common\PrivilegeService::get_backend_side_menu();?>
<?php $now_side_menu_id = session('admin.menu_info.now_side_menu_id');?>
  <?php $authService = new \App\Http\Services\v1\admin\AuthService();
$guard = $authService->getAuthUserGuardName();?>
<ul class="layui-nav layui-nav-tree c-side-menu" id="side-nav-ul">
    <div class="layui-logo">管理后台</div>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">账号管理</a>
        <dl class="layui-nav-child">
            {{--<dd class="@if(\Illuminate\Support\Facades\Request::getPathInfo()=='/admin/product') layui-this @endif"><a  href="{{url('/admin/product')}}">商品列表</a></dd>--}}
            <dd>
                <a href="">角色列表</a>
                <ul class="sub-nav-panel">
                    <li><a class="title" href="/admin/{{$guard}}/role">平台角色</a></li>
                    <li><a class="title" href="/admin/{{$guard}}/privilege">平台权限</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">账号列表</a>
                <ul class="sub-nav-panel">
                    <li><a class="title" href="admin_platform">平台管理员</a></li>
                    <li><a class="title" href="admin_brand">品牌超级管理员</a></li>
                    <li><a class="title" href="designer">自由设计师</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">资料审核</a>
                <ul class="sub-nav-panel">
                    <li>
                        <div class="title">组织资料</div>
                        <div class="nav-list">
                            <a href="" class="nav-block">品牌</a>
                            <a href="" class="nav-block">装饰公司</a>
                        </div>
                    </li>
                    <li>
                        <div class="title">设计师基础资料</div>
                        <div class="nav-list">
                            <a href="" class="nav-block">自由设计师</a>
                        </div>
                    </li>
                    <li>
                        <div class="title">设计师实名资料</div>
                        <div class="nav-list">
                            <a href="" class="nav-block">自由设计师</a>
                        </div>
                    </li>
                </ul>
            </dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">平台设置</a>
        <dl class="layui-nav-child">
            <dd>
                <a href="">系统信息</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">设计师</a></li>
                    <li><a class="title">销售商</a></li>
                    <li><a class="title">品牌</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">基本信息</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">全局</a></li>
                    <li><a class="title">设计师</a></li>
                    <li><a class="title">销售商</a></li>
                    <li><a class="title">品牌</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">应用信息</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">全局</a></li>
                    <li><a class="title">设计师</a></li>
                    <li><a class="title">销售商</a></li>
                    <li><a class="title">品牌</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">方案相关</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">系统信息</a></li>
                    <li><a class="title">基础信息</a></li>
                    <li><a class="title">应用信息</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">产品相关</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">经营品类选项</a></li>
                    <li><a class="title">产品结构选项</a></li>
                    <li>
                        <div class="title">瓷砖</div>
                        <div class="nav-list">
                            <a href="" class="nav-block">系统信息</a>
                            <a href="" class="nav-block">基础信息</a>
                            <a href="" class="nav-block">应用信息</a>
                        </div>
                    </li>
                </ul>
            </dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">方案管理</a>
        <dl class="layui-nav-child">
            <dd>
                <a href="">方案列表</a>
                <ul class="sub-nav-panel">
                    <li>
                        <div class="title">高清图</div>
                        <div class="nav-list">
                            <a href="" class="nav-block">自由设计师方案</a>
                            <a href="" class="nav-block">品牌方案</a>
                        </div>
                    </li>
                </ul>
            </dd>
            <dd>
                <a href="">方案审核</a>
                <ul class="sub-nav-panel">
                    <li>
                        <div class="title">高清图</div>
                        <div class="nav-list">
                            <a href="" class="nav-block">自由设计师方案</a>
                        </div>
                    </li>
                </ul>
            </dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">产品管理</a>
        <dl class="layui-nav-child">
            <dd><a href="">产品审核</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">信息统计</a>
        <dl class="layui-nav-child">
            <dd>
                <a href="">账号统计</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">品牌</a></li>
                    <li><a class="title">销售商</a></li>
                    <li><a class="title">设计师</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">方案统计</a>
            </dd>
            <dd>
                <a href="">产品统计</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">瓷砖</a></li>
                </ul>
            </dd>
            <dd>
                <a href="">活跃度统计</a>
                <ul class="sub-nav-panel">
                    <li><a class="title">设计师</a></li>
                    <li><a class="title">销售商</a></li>
                    <li><a class="title">品牌</a></li>
                </ul>
            </dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">信息通知</a>
        <dl class="layui-nav-child">
            <dd><a href="">账号通知</a></dd>
            <dd><a href="">系统通知</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-nav-itemed">
        <a class="" href="javascript:;">安全中心</a>
        <dl class="layui-nav-child">
            <dd><a href="">修改密码</a></dd>
            <dd><a href="">绑定微信</a></dd>
        </dl>
    </li>
</ul>