@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/designer/detail.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/designer/detail.js?v='.str_random(random_int(20,30)),
        '/v1/js/ajax.js',
   ]
])

@section('content')

    <div class="container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 设计师 / </span>
            <span class="navtext2" id="designer_nickname"></span>
        </div>
        <div class="head" id="head"></div>
        <div class="middle">
            <div class="middle_left">
                <div class="designer" id="b0">
                    <div class="designer_left">
                        <img src="/v1/images/site/index/icon-album.png" class="designer_icon"/>
                        <span class="designer_title">代表作品</span>
                    </div>
                    <div class="sscontanier" id="designer_fa"></div>
                </div>
                <div class="fangan" id="b1">
                    <label class="producttitle">设计案例</label>
                    <div id='album_swiper'></div>
                    <div id="produ">
                        <div class="productcontainer" id="fangan"></div>
                    </div>
                    <a class="album_lookall" style="display: block;" href="{{url('/album?dsn='.$designerId)}}" target="_blank" >查看更多</a>

                </div>
            </div>
            <div class="middle_right">
                <div class="aboutme" id='aboutme'></div>
                <div class="readview" id="slideBar">
                    <span class="daohangtitle">导航栏</span>
                    <div class="sidebar">
                        <div class="branch"></div>
                        <ul id="sidenav">
                            <li class="active" id="slideItem-0"><a href="#b0"><span>代表作品</span></a><i class="dark"></i></li>
                            <li id="slideItem-1"><a href="#b1"><span>设计案例</span></a><i class="dark"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>

        var designer_id='{{isset($designerId)?$designerId:0}}';

    </script>

@endsection