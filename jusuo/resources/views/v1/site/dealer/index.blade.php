@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/dealer/index.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/dealer/index.js',
        '/v1/js/ajax.js',
   ]
])

@section('content')
<div class="container">
    <div class="nav_lujin">
        <span class="navtext1">首页 / 材料商家 / </span>
        <span class="navtext2">共 <span id="result-count">0</span> 个符合条件的结果</span>
    </div>
    <div class="bannerBox">
        <div class="bannerBox" id="swipers"></div>
        <div class="swiper-pagination"></div>
    </div>

    <div class="designercontainer">
        <div class="designer_left">
            <img src="/v1/images/site/index/icon-company.png" class="designer_icon"/>
            <span class="designer_title">热门推荐</span>
        </div>
        <div class="companylogoview" id="companylogo1"></div>
    </div>
    {{--<div class="nav" id="allnav"></div>--}}
    <div class="paixucontainer">
        <div class="paixu" id="paixu"></div>
    </div>
    <div class="de_fangan">
        <div id="produ">
            <div id="de_fangan"></div>
        </div>
        <div id="page"></div>
    </div>
</div>

@endsection

@section('script')

@endsection