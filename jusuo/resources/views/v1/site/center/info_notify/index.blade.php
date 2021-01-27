@extends('v1.site.center.layout',[
    'css' => [],
    'js'=>[
        '/v1/js/site/center/common/common.js',
        '/v1/js/site/center/info_notify/index.js',
        '/v1/static/js/xlPaging.js'
    ]
])

@section('main-content')
<!--消息通知-->
<div class="detailview" id="b4" >
    <div class="desigplan" id="noticedaohang"></div>
    <div id="c0content">
        <div id="produ2">
            <div class="c0container" id="c0container"></div>
        </div>
        <div id="page2"></div>
    </div>
    <div id="c1content" style="display:none;">
        <div id="produ3">
            <div class="c0container" id="c1container"></div>
        </div>
        <div id="page3"></div>
    </div>
    <div id="c2content" style="display:none;">
        <div id="produ4">
            <div class="c0container" id="c2container"></div>
        </div>
        <div class="page4" id="page4"></div>
    </div>
</div>


@endsection


@section('script')



@endsection


