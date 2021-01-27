<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!--使用layui的话，这个一定要带上，否则样式会不兼容-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="description" content="超弦网络"/>
    <meta name="keywords" content=""/>
    <meta content="超弦网络" name="超弦网络"/>
    <title>管理员端</title>

    {{--全局基本都要用的js放这里 start--}}
    @include('v1.admin.components.layout.global_script_tag')
    {{--全局基本都要用的js放这里 end--}}

    {{--全局基本都要用的css放这里 start--}}
    @include('v1.admin.components.layout.global_link_tag')
    {{--全局基本都要用的css放这里 end--}}

    @if(isset($css))
        @foreach($css as $c)
            <link href="{{env('HTTP_SERVER')}}{{ $c }}" rel="stylesheet" type="text/css">
        @endforeach
    @endif



    @yield('style')



</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <ul class="layui-nav c-header-nav" lay-filter="">
            {{--<li class="layui-nav-item"><a href="">最新活动</a></li>
            <li class="layui-nav-item layui-this"><a href="">产品</a></li>
            <li class="layui-nav-item"><a href="">大数据</a></li>--}}
            <li class="layui-nav-item">
                <?php $authService = new \App\Http\Services\v1\admin\AuthService();$guard = $authService->getAuthUserGuardName();?>

                <a href="javascript:;">{{$authService->getAuthUser()->login_username}}</a>
                <dl class="layui-nav-child"> <!-- 二级菜单 -->
                    {{--<dd><a href="{{url('/admin/modify_pwd')}}">修改密码</a></dd>--}}
                    @if($guard == 'brand' || $guard == 'seller')
                        <dd><a href="{{ url($url_prefix.'admin/logout') }}">退出登录</a></dd>
                    @else
                        <dd><a href="{{ url($url_prefix.'admin/'.$guard.'/logout') }}">退出登录</a></dd>
                    @endif
                </dl>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            @yield('side_nav')
        </div>
    </div>

    <div class="layui-body">

        @yield('body_nav')
        @yield('content')

    </div>

</div>
@yield('body')
<script>
    //超全局变量
</script>
<script>
    //超全局方法


</script>
<script>
    var form = layui.form;

    //初始化layui组件
    layui.element.init();
    form.render();

    //表格表头超出显示提示
    var table_th_overtips_index = 0;

    //超全局操作
    $(document).ready(function(){
        $('input, textarea').placeholder();

        $('.config-submit-btn').show();
        $('.pc-hide-submit-btn').parent().siblings('.config-submit-btn').hide();

        $(document).on('mouseenter', '.layui-table-box th', function(){
            var $obj = $(this);
            var th_title = $obj.find('span').html()
            if(th_title){
                table_th_overtips_index = layer.tips(th_title, $obj, {tips: [1, '#3e82f7']});

            }
        }).on('mouseleave', '.layui-table-box th', function(){
            layer.close(table_th_overtips_index);
        });

        ajax_get('{{url($url_prefix.'admin/brand/api/get_title')}}',function(res){
            if(res.status){
                $("title").html(res.msg);
            }
            else if(res.status==0&&res.msg=='请重新登录'){
                ajax_get('{{url($url_prefix.'admin/seller/api/get_title')}}',function(res){
                    if(res.status){
                        $("title").html(res.msg);
                    }
                });
            }
        });

    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
    });

    <?php $global_message = session('admin.global.message');?>
    @if(isset($global_message) && $global_message)
    layer.msg('{{$global_message}}', {
        icon: 2,
        offset:'rt',
        time: 2000 //2秒关闭（如果不配置，默认是3秒）
    }, function(){
        //do something
    });
    @endif

    @if(Session::has('errors'))
    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    if(index){
        parent.layer.msg('{{Session::get('errors')->first()}}');
        parent.layer.close(index); //再执行关闭
    }else{
        layer.msg('{{Session::get('errors')->first()}}');

    }
    @endif


</script>

@if(isset($js))
    @foreach($js as $j)
        <script src="{{env('HTTP_SERVER')}}{{ $j }}"></script>
    @endforeach
@endif

@yield('script')


</body>
</html>