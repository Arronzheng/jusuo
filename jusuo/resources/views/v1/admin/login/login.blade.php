<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="description" content="超弦网络"/>
    <meta name="keywords" content=""/>
    <meta content="超弦网络" name="超弦网络"/>
    <title>登录</title>
    <link rel="stylesheet" href="{{asset('plugins/layui-v2.5.4/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('v1/css/admin/login/login.css')}}">
    {{--全局基本都要用的js放这里 start--}}
    <script src="{{asset('v1/js/jquery.min.js')}}"></script>
    <script src="{{asset('plugins/layui-v2.5.4/layui/layui.all.js')}}"></script>
    <script type="text/javascript" src="https://cdn.bootcss.com/layer/2.3/layer.js"></script>
    {{--兼容各浏览器的placeholder插件--}}
    <script src="https://cdn.bootcss.com/jquery-placeholder/2.3.1/jquery.placeholder.min.js"></script>
    <script src="{{asset('v1/js/ajax.js')}}"></script>

    <style>
        html {
            background-color: #f2f2f2;
            color: #666;
        }
        .layui-form-checkbox[lay-skin=primary]{
            height:30px !important;;
        }
        .layui-btn.layui-btn-custom-blue {
            background-color:#3D82F7;
        }
        #LAY_app{
            background:no-repeat center center;
            background-size:cover;
        }
    </style>
</head>

<body  >
<div id="LAY_app" class="layadmin-tabspage-none" @if(isset($params['login_background']) && $params['login_background'])style="background-image: url('{{$params['login_background']}}')"@endif>

    <div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">

        <div class="layadmin-user-login-main">
            <div class="layadmin-user-login-box layadmin-user-login-header">
                <h2>{{$params['login_title']}}</h2>

            </div>
            <form id="psw-login" class="layui-form"  method="post"  action="/admin/login">
                <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
                    <div class="layui-form-item">
                        <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                        <input type="text" name="login_username" id="LAY-user-login-username" lay-verify="required" placeholder="用户名" class="layui-input">
                    </div>
                    <div class="layui-form-item">
                        <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                        <input type="password" name="password" id="LAY-user-login-password" lay-verify="required" placeholder="密码" class="layui-input">
                    </div>

                    {{ csrf_field() }}
                    <div class="layui-form-item">
                        <button class="layui-btn layui-btn-fluid layui-btn-custom-blue" onclick="login()">登 入</button>
                    </div>

                </div>

            </form>
        </div>




    </div>
</div>

<div class="mui-backdrop"></div>




</body>
<script>
    @if ($errors->any())
    layer.msg('{{ $errors->first() }}');
            @endif

    var phone_test =/^1\d{10}$/;//验证规则：11位数字，以1开头。
    var username_test =/^[a-zA-Z0-9_]{4,16}$/;//验证规则：字母、数字、下划线组成，4-16位。
    var verificationCode_test =/^\d{6}$/;//验证规则：6位数字。


    function login(){
        var username = $(".psw-login form input[name='login_username']").val();
        var password = $(".psw-login form input[name='password']").val();
        if(!username_test.test(username)){
            alert('用户名格式错误');
            return;
        }
        $('#psw-login').submit();
    }

</script>

</html>