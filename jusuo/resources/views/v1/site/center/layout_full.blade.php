<?php
$final_css = [
        '/v1/css/site/center/layout.css',
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

@section('content')


    <div class="center-container">
        @yield('main-content')
        <div style="clear:both;"></div>

    </div>


@endsection

@section('script')
    <script>

    </script>
@endsection

@section('body')
    @yield('body')
@endsection

