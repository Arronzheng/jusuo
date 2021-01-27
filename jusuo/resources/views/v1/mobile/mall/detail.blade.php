@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/static/iconfont/iconfont.css',
       '/v1/css/mobile/mall/detail.css?v='.str_random(random_int(20,30)),
   ],
   'js'=>[
       '/v1/static/js/xlPaging.js',
       '/v1/js/ajax.js',
       '/v1/js/mobile/mall/detail.js?v='.str_random(random_int(20,30))
   ],
   'title'=>'商品详情'
])

@section('content')
    <style>
        body{padding-bottom:50px;}
        .market-price{text-decoration: line-through;font-size:12px;}
        #main-info .price-info{position:relative;}
        #main-info .params {
            margin-top: 10px;
            border-top: 1px solid #eee;
            color: #999;
            line-height: 20px;
        }

        #main-info .params table{}
        #main-info .params table,
        #main-info .params table tr th,
        #main-info .params table tr td {
            border: 1px solid rgba(0, 0, 0, 0);
        }

        #main-info .params table tr td {
            min-width: 90px;
        }

        #main-info .price-info {
            color: white;
            line-height: 16px;
            font-size: 16px;
            z-index: 1;
            margin-bottom:15px;
        }
        #main-info .price-info .action{display: inline-block;color:rgba(0,0,0,.5)}
        #main-info .price-info .action.active{color:rgb(255,136,0);}
        #main-info .price-info .action:not(:first-child){margin-left:5px;}
        #main-info .price-info span{margin-right:2px;}
        #detail .sub-container-title{color:#888888;}
        #detail .detail-content{padding:10px 0;}
        #short-intro{margin-top:10px;color:#aaaaaa;font-size:12px;}
        #good-name{font-size:16px;}

        .layui-layer-btn .layui-layer-btn0{background-color:rgb(255,136,0);border-color:rgb(255,136,0)}

        #operation-outer{display:flex;justify-content:flex-end;width:100%;border-top:1px solid #f2f2f2;align-items:center;position:fixed;height:50px;left:0;bottom:0;background-color:#ffffff;z-index:99;}
        #choose-amount{}
        #choose-amount button{border:1px solid #dedede;width:25px;height:25px;}
        #choose-amount input{border:1px solid #dedede;height:25px;width:40px;padding:0;text-align:center;}
        #submit-btn{border:0;margin:0 15px;background-color:rgb(255,136,0);height:25px;line-height:25px;text-align:center;color:#ffffff;padding:0 10px;}
    </style>

    <div id="cover-swiper-outer" class="cover-swiper-outer">
        <div class="swiper-wrapper" id="good-images"></div>
        <div class="swiper-pagination"></div>


    </div>




    <div class="container" id="main-info">


    </div>

    <div id="operation-outer">
        <div id="choose-amount">
            <button class="btn-reduce" onclick="reduceAmount()">-</button>
            <input class="buy-num" value="1" id="buy-num" readonly />
            <button class="btn-add" onclick="addAmount()">+</button>
        </div>
        <div id="submit-btn" onclick="addOrder()">
            立即兑换
        </div>
    </div>


    @verbatim

        <!--轮播图-->
        <script id="good-images-tpl" type="text/html">
            {{each data.photo photo i}}
            {{if i<=5}}
            <div class="swiper-slide" id="swiper-slide{{i}}"
                 style="background-image: url(&quot;{{photo}}&quot;);"></div>
            {{/if}}
            {{/each}}
        </script>

        <!--主要信息-->
        <script id="main-info-tpl" type="text/html">
            <div class="price-info">
                <div class="action active">{{data.integral}}积分</div>
                <div class="action market-price">￥{{data.market_price}}</div>
                <div class="action" style="font-size:12px;">&nbsp;{{data.exchange_amount}}人已兑换</div>
            </div>

            <div class="good-name" id="good-name">{{data.name}}</div>
            <div class="short-intro" id="short-intro">{{#data.short_intro}}</div>

            <div class="params">
                <table>
                    {{each data.param param i}}
                    <tr><td>{{param.key}}</td><td>{{param.value}}</td></tr>
                    {{/each}}
                </table>
            </div>

            <div class="splitter"></div>

            <div class="sub-container" id="detail">
                <div class="sub-container-title">详细信息</div>
                <div class="detail-content">

                    {{#data.detail}}

                </div>
            </div>
        </script>

    @endverbatim



@endsection

@section('script')
    <script>
        var web_id_code = "{{$web_id_code}}";


    </script>


@endsection