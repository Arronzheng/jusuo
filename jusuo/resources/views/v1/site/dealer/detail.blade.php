@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/dealer/detail.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        'https://api.map.baidu.com/api?v=2.0&ak=67jMQ5DmYTe1TLMBKFUTcZAR',
        '/v1/static/js/jquery.baiduMap.min.js',
        '/v1/js/site/dealer/detail.js?v='.str_random(random_int(20,30)),
        '/v1/js/ajax.js',
   ]
])

@section('content')
    <div class="container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 材料商家 / </span>
            <span class="navtext2" id="dealer_short_name"></span>
        </div>
        <div class="head" id="head"></div>
        <div class="middle">
            <div class="middle_left">

                <div class="design_team" id="b0">
                    <div class="team_title">设计团队</div>
                    <div class="team_container" id="design_team"></div>
                    {{--<div class='team_lookmore'>查看更多 ></div>--}}
                </div>
                <div class="fangan" id="b1">
                    <label class="producttitle">设计案例</label>
                    <div id='album_swiper'></div>
                    <div id="produ">
                        <div class="productcontainer" id="fangan"></div>
                    </div>
                    {{--<div id="page"></div>--}}
                    <a class="album_lookall" target="_blank" style="display: block;" href="{{url('/album?dlr='.$dealerId)}}">查看更多</a>

                </div>
                <div class="product" id="b2">
                    <label class="producttitle">热门产品</label>
                    <div id='product_swiper1'></div>
                    <div id="produ1">
                        <div class="productcontainer1"id="product"></div>
                    </div>
                    <a class="pro_lookall" target="_blank" style="display: block;" href="{{url('/product?dlr='.$dealerId)}}">查看更多</a>
                </div>
                <div class="product" style="background-color:#ffffff;padding-top:30px;padding-left:30px;margin-top:20px;padding-bottom:30px;" id="b3">
                    <div class="producttitle cuxiao-title">近期促销</div>
                    <div class="cuxiao" id="cuxiao"></div>
                </div>
            </div>
            <div class="middle_right">
                <div class="company_profile">
                    <div class="aboutme" id='aboutme'></div>
                </div>
                <div class="daohang" id="daohang-outer">
                    <span class="daohangtitle1">门店导航</span>
                    <div class="daohangcontainer" id="daohang"></div>
                    <div id="companydetail"></div>
                </div>
                <div class="readview" id="slideBar">
                    <span class="daohangtitle">导航栏</span>
                    <div class="sidebar">
                        <div class="branch"></div>
                        <ul id="sidenav">
                            <li class="active" id="slideItem-0"><a href="#b0"><span>设计团队</span></a><i class="dark"></i></li>
                            <li id="slideItem-1"><a href="#b1"><span>设计案例</span></a><i class="dark"></i></li>
                            <li id="slideItem-2"><a href="#b2"><span>热门产品</span></a><i class="dark"></i></li>
                            <li id="slideItem-3"><a href="#b3"><span>近期促销</span></a><i class="dark"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>

        var dealer_id='{{isset($dealerId)?$dealerId:0}}';

    </script>

@endsection