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
    <title></title>

    {{--全局基本都要用的js放这里 start--}}
    @include('v1.site.components.layout.global_script_tag')
    {{--全局基本都要用的js放这里 end--}}

    {{--全局基本都要用的css放这里 start--}}
    @include('v1.site.components.layout.global_link_tag')
    {{--全局基本都要用的css放这里 end--}}

    @if(isset($css))
        @foreach($css as $c)
            <link href="{{env('HTTP_SERVER')}}{{ $c }}" rel="stylesheet" type="text/css">
        @endforeach
    @endif

    @yield('style')

</head>
<body style="background-color:#fbfbfb;">

    @include('v1.site.components.layout.nav',[
    'hide_nav_row'=>isset($hide_nav_row)?$hide_nav_row:null,
    'hide_top_right'=>isset($hide_top_right)?$hide_top_right:null
    ]
    )

    @yield('content')

    @yield('body')

    @if(!isset($hide_footer) || !$hide_footer)
    @include('v1.site.components.layout.footer')
    @endif

<script>
    //超全局变量
    //var __cache_brand='{{isset($__BRAND_SCOPE)?$__BRAND_SCOPE:''}}';
    var __cache_brand='';

</script>
<script>
    //超全局方法

</script>
<script>

    //超全局操作
    $(document).ready(function(){
        /*var href;
        $('a').each(function(){
            if(typeof($(this).attr('href'))!="undefined" && $(this).attr('href').indexOf("back") == -1){
                href = $(this).attr('href');
                //有参数且非锚点
                if(href.indexOf('__bs')==-1&&href.indexOf('#')==-1&&href!='http://www.beian.miit.gov.cn'){
                    if(href.indexOf('?')==-1){
                        href += '?__bs='+__cache_brand;
                    }
                    else{
                        href += '&__bs='+__cache_brand;
                    }
                }
                $(this).attr('href',href);
            }
        });*/
        $('input, textarea').placeholder();
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