@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/index.css?v='.str_random(random_int(20,30)),
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/index.js?v='.str_random(random_int(20,30)),
        '/v1/js/ajax.js',
   ]
])

@section('content')

    <div class="bannerBox">
        <div class="bannerBox" id="swipers"></div>
        <div class="swiper-pagination"></div>
    </div>
    <div class="container">
        <div class="designer">
            <div class="designer_left">
                <img src="/v1/images/site/index/icon-album.png" class="designer_icon"/>
                <span class="designer_title">设计方案</span>
            </div>
            <div id="designer_swiper"></div>
            <a href="/album" class="designer_right">查看更多 ></a>
        </div>
        <div class="designer_view" id="designer"></div>
        <div class="product">
            <div class="designer_left">
                <img src="/v1/images/site/index/icon-product.png" class="designer_icon"/>
                <span class="designer_title">热门产品</span>
            </div>
            <div id="product_swiper"></div>
            <a href="/product" class="designer_right">查看更多 ></a>
        </div>
        <div class="shareview1" style="display:none;">▲</div>
        <div class="shareview" style="display:none;" id="label">
        </div>
        <div class="designer_view1" id="product"></div>
        <div class="hotdesigner">
            <div class="designer_left">
                <img src="/v1/images/site/index/icon-designer.png" class="designer_icon"/>
                <span class="designer_title">人气设计师</span>
            </div>
            <div id="hotdesigner_swiper"></div>
            <a href="/designer" class="designer_right">查看更多 ></a>
        </div>
        <div class="designer_view2" id="hotdesigner"></div>
        <div class="hotdesigner">
            <div class="designer_left">
                <img src="/v1/images/site/index/icon-dealer.png" class="designer_icon"/>
                <span class="designer_title">材料商家</span>
            </div>
            <div id="material_swiper"></div>
            <a href="/dealer" class="designer_right">查看更多 ></a>
        </div>
        <div class="designer_view2" id="material"></div>
        <div class="companylogoview" id="companylogo1"></div>
        <div class="bottomimage">
        </div>
    </div>

@endsection

@section('script')

@endsection