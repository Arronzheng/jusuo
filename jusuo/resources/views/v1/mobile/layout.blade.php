<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!--使用layui的话，这个一定要带上，否则样式会不兼容-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="email=no"/>
    <meta name="apple-mobile-web-app-title" content="千名营销">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <link rel="apple-touch-icon" href="apple-touch-icon-iphone.png"/>
    <link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-ipad.png"/>
    <link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-iphone4.png"/>
    <meta name="feBu" content="febu-msite">
    <meta name="serviceWithStage" content="prod">
    <meta content="true" name="webglStat">
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <title>{{isset($title)?$title:'手机版'}}</title>

    {{--全局基本都要用的js放这里 start--}}
    @include('v1.mobile.components.global_script_tag')
    {{--全局基本都要用的js放这里 end--}}

    {{--全局基本都要用的css放这里 start--}}
    @include('v1.mobile.components.global_link_tag')
    {{--全局基本都要用的css放这里 end--}}

    @if(isset($css))
        @foreach($css as $c)
            <link href="{{env('HTTP_SERVER')}}{{ $c }}" rel="stylesheet" type="text/css">
        @endforeach
    @endif

    @yield('style')

</head>
<body style="background-color:#fbfbfb;">

    @yield('content')

    @yield('body')

<script>
    //超全局变量
    var __cache_brand='';

</script>
<script>
    //超全局方法
    function m_go_login(){
        location.href="{{url('/mobile/login/redirect?r='.request()->fullUrl())}}";
    }

</script>
<script>

    var currUrl = "{{ request()->fullUrl() }}";

    function wxShare(title,desc,imgUrl){
        wx.ready(function () {
            wx.updateTimelineShareData({
                title: title,
                link: currUrl,
                imgUrl: imgUrl,
            });
            wx.updateAppMessageShareData({
                title: title,
                link: currUrl,
                imgUrl: imgUrl,
                desc: desc,
            });
        });
    }

    //超全局操作
    $(document).ready(function(){


    });

    $('.back-to-top').click(function(){
        if((document.body.scrollTop || document.documentElement.scrollTop) != 0){
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        }
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
    });

    //art-template模板引擎


</script>

@if(isset($js))
    @foreach($js as $j)
        <script src="{{env('HTTP_SERVER')}}{{ $j }}"></script>
    @endforeach
@endif

@yield('script')

</body>
</html>