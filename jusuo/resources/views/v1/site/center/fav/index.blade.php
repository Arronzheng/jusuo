@extends('v1.site.center.layout',[
    'css' => [],
    'js'=>[
        '/v1/js/site/center/common/common.js',
        '/v1/js/site/center/fav/index.js',
        '/v1/static/js/xlPaging.js'
    ]
])

@section('main-content')
        <!-- 收藏关注-->
<div class="detailview" id="b3" >
    <div class="desigplan" id="shoucangdaohang"></div>
    <div id="d0content">
        <div id="produ5" style="padding-top:17px;">
            <div class="c0container" id="d0container"></div>
        </div>
        <div class="page4" id="page5"></div>
    </div>
    <div id="d1content" style="display:none;">
        <div class="scprohead">
            {{--<div class="scprobotton1"  id="fav_prodduct_org_1">所属组织</div>--}}
            {{--<div class="scprobotton2"  id="fav_prodduct_org_0">其他产品</div>--}}
        </div>
        <div id="produ6">
            <div class="c0container" id="d1container"></div>
        </div>
        <div class="page4" id="page6"></div>
    </div>
    <div id="d2content" style="display:none;">
        <div id="produ7" style="padding-top:20px;">
            <div class="c0container" id="d2container"></div>
        </div>
        <div class="page4" id="page7" style="margin-top:40px;"></div>
    </div>
</div>


@endsection


@section('script')



@endsection


