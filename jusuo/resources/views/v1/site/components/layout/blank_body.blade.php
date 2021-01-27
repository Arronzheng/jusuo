
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
    <title>编辑</title>

    {{--全局基本都要用的js放这里 start--}}
    @include('v1.admin.components.layout.global_script_tag')
    {{--全局基本都要用的js放这里 end--}}

    {{--全局基本都要用的css放这里 start--}}
    @include('v1.admin.components.layout.global_link_tag')
    {{--全局基本都要用的css放这里 end--}}

    <link rel="stylesheet" href="{{asset('v1/css/admin/module/form.css')}}">

    @yield('style')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
        });
    </script>

</head>
<body >

@yield('content')
@yield('script')

</body>
</html>