@extends('v1.admin.layout',[
     'css' => isset($css)?$css:[],
     'js' => isset($js)?$js:[],
     'style' => isset($style)?$style:'',
     'script' => isset($script)?$script:'',
     'header_title' => '销售商管理后台',
     'guard'=>'seller',
])

@section('body_nav')
    {{--@include('v1.admin.components.layout.body_nav')--}}
@endsection
@section('content')
    @yield('content')
@endsection


@section('side_nav')
    @include('v1.admin.components.layout.side_nav')
@endsection