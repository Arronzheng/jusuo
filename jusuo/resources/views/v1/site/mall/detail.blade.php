@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/mall/detail.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/mall/detail.js',
        '/v1/js/ajax.js',
   ]
])

@section('content')

    <div class="designer-list-container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 积分商城 / </span>
            <span class="navtext2" id="top-title"></span>
        </div>

        <div class="sale-info" id="good-info">

        </div>

        <div class="sale-detail-outer">
            <div class="detail-text">商品详情</div>
            <div id="detail-desc"></div>
        </div>

    </div>

    <script id="good-info-tpl" type="text/html">

        @verbatim
        <div class="info-left">
            <div class="img-outer background-center" id="sale-img" style="background-image:url('{{data.photo[0]}}')"></div>
            <div class="select-box">
                {{each data.photo photo i}}
                <div class="img-outer ">
                    <img src="{{photo}}">
                </div>
                {{/each}}
            </div>
        </div>
        <div class="right-info">
            <div class="title">{{data.name}}</div>
            <div class="title-text">{{data.short_intro}}</div>
            <div class="price-box"><span class="price">{{data.integral}}积分</span><span class="original-price"><del>¥{{data.market_price}}</del></span><span class="original-price">已兑{{data.exchange_amount}}件</span></div>
            <div class="detail">
                <table>
                    {{each data.param param i}}
                    <tr><td>{{param.key}}</td><td>{{param.value}}</td></tr>
                    {{/each}}
                </table>
            </div>
            <div id="choose-btns" class="choose-btns clearfix">
                <div class="wrap-input choose-amount">
                    <input class="text buy-num" id="buy-num" value="1" type="number">
                    <a class="btn-reduce disabled" onclick="reduceAmount()">-</a>
                    <a class="btn-add" onclick="addAmount()">+</a>
                </div>
                <a href="javascript:;" onclick="addOrder()"><div id="InitCartUrl" class="btn-special1 btn-lg">立即兑换</div></a>
            </div>
        </div>
        @endverbatim

    </script>

@endsection

@section('script')

    <script>

        var web_id_code = "{{$web_id_code}}";

    </script>

@endsection