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
    <title>平台登陆</title>
    <link rel="stylesheet" href="{{ asset('/v1/css/admin/global.css') }}">
    <link rel="stylesheet" href="{{ asset('/v1/css/admin/login.css') }}">
    <script src="{{ asset('/v1/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/v1/js/jquery.qrcode.min.js') }}"></script>
    <script src="{{ asset('/v1/static/layer/layer.js') }}"></script>
    <script src="{{ asset('/v1/js/ajax.js') }}"></script>

    <script type="text/javascript">
        //引入该flexible.min.js
        !function(e,t){function n(){var n=l.getBoundingClientRect().width;t=t||540,n>t&&(n=t);var i=100*n/e;r.innerHTML="html{font-size:"+i+"px;}"}var i,d=document,o=window,l=d.documentElement,r=document.createElement("style");if(l.firstElementChild)l.firstElementChild.appendChild(r);else{var a=d.createElement("div");a.appendChild(r),d.write(a.innerHTML),a=null}n(),o.addEventListener("resize",function(){clearTimeout(i),i=setTimeout(n,300)},!1),o.addEventListener("pageshow",function(e){e.persisted&&(clearTimeout(i),i=setTimeout(n,300))},!1),"complete"===d.readyState?d.body.style.fontSize="16px":d.addEventListener("DOMContentLoaded",function(e){d.body.style.fontSize="16px"},!1)}(1280,1920);
    </script>
    <!--&lt;!&ndash;[if lt IE 9]>-->
    <!--<script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>-->
    <!--<script src="js/respond.min.js"></script>-->
    <!--<![endif]&ndash;&gt;-->
</head>

<body  style="background-image: url({{ asset('v1/images/brand/brand_login_banner.png') }});background-size: cover;min-height: auto;">
<div class="login-main" style="background-image: none;">
    <div class="login-main-title">
        <div class="brand-login-main font-weight-initial">
        <div class="brand-login-logo inline-block color-text-blank font-size-28">LOGO</div><div class="grey-line-box"></div>
        <div class="brand-login-name inline-block font-size-28 color-text-blank">系统管理平台</div>
        </div>
    </div>
    <div class="brand-wechat-login color-background-blank border-radius-4" id="phoneLogin">
        <div class="login-header">
                <span class="color-text-yellow" id="user_login_btn" style="margin-left: 26px;">账号登陆<div class="line-yellow"></div></span>
                <span style="margin-left: 74px;" id="wechat_login_btn">微信登陆<div class="line-yellow" style="display: none;"></div></span>
                <div class="line-grey"></div>
        </div>
        <div class="psw-login margin-0-auto">
            <form id="psw-login" method="post" action="/admin/platform/login">
                {{ csrf_field() }}
                <div class="psw-login-box"><span class="psw-login-logo login-username inline-block"></span><input name="login_username" placeholder="请输入账号"></div>
                <div class="psw-login-box"><span class="psw-login-logo login-psw inline-block"></span><input name="password" type="password"  placeholder="请输入密码"></div>
                <div class="border-radius-4 font-size-18 color-font-blank brand_login_btn" onclick="login()">登陆</div>
            </form>
        </div>

        <div class="wechat-login margin-0-auto" hidden>
            <div class="wechat-code" id="qrcode" onclick="refresh()">
                {{--<img src="./images/test-code.png">--}}
            </div>
            <div class="wechat-scan text-center font-size-16 cursor-pointer">
                <span>请使用微信扫一扫登录</span>
            </div>
        </div>

        <div class="link-box">
            {{--<a href="{{route('brand.login')}}">品牌商登录</a>
            <a href="{{route('seller.login')}}">销售商登录</a>
            <a href="{{route('designCompany.login')}}">装饰公司登录</a>--}}
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
        var username = $(".psw-login form input[name='username']").val();
        var password = $(".psw-login form input[name='password']").val();
        if(!username_test.test(username)){
            alert('用户名格式错误');
            return;
        }
        $('#psw-login').submit();
    }
    var count = 37;
    var over = true;
    var  t = '{{ $token }}';

</script>

</html>