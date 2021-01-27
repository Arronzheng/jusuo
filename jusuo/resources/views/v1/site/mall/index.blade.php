@extends('v1.site.layout',[
   'css'=>[
        '/v1/css/site/common/pager.css',
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/mall/index.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/mall/index.js',
        '/v1/js/ajax.js',
        '/v1/static/js/xlPaging1.js',

   ]
])

@section('content')
    <style>
        .list-empty{font-size:12px;text-align:center;color:#888888;padding:20px 0 40px 0;}

    </style>
    <div class="designer-list-container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 积分商城 / </span>
            <span class="navtext2"><span id="data-count">-</span>件商品正在热销{{--<span id="keyword"></span><span id="clear-keyword">清除×</span>--}}</span>
        </div>
        <div class="bannerBox">
            <div class="bannerBox" id="swipers"></div>
            <div class="swiper-pagination"></div>
        </div>

        <div class="category-outer-1" id="cat-1-list"></div>

        <div class="sale-outer-1" id="recommend-goods-list">

        </div>

        <?php
        $sort_order = request()->input('order','comples_desc');
        $isrw = request()->input('isrw','');
        $sort = '';
        $order = '';
        if($sort_order){
            $sort_order_array = explode('_',$sort_order);
            $sort = isset($sort_order_array[0])?$sort_order_array[0]:'';
            $order = isset($sort_order_array[1])?$sort_order_array[1]:'';
        }
        ?>

        <div class="product-nav" id="allnav"></div>

        <div class="paixucontainer">
            <div class="paixu" id="paixu">
                <div class="paixu-block" data-type="exchange">
                    <span class="paixu_label active1 " id="paixu_0" onclick="changePaixu(this)">兑换量</span>
                    <div class="up_down" onclick="changePaixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc "></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc active "></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="new">
                    <span class="paixu_label " id="paixu_1" onclick="changePaixu(this)">新上架</span>
                    <div class="up_down" onclick="changePaixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc "></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc "></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="cost">
                    <span class="paixu_label " id="paixu_2" onclick="changePaixu(this)">所需积分</span>
                    <div class="up_down" onclick="changePaixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc "></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc "></span>
                        </div>
                    </div>
                </div>

            </div>
            {{--<span class="price">积分区间</span>
            <input type="text" id="i_min_price" value="" class="priceinput">
            <div class="pline"></div>
            <input type="text" id="i_max_price" value="" class="priceinput1">--}}
        </div>

        <div class="product" >
            <div id="produ">
                <div class="productcontainer" id="good-list">

                </div>
            </div>
        </div>

        <input type="hidden" id="i_b" value="{{request()->input('b')}}"/> {{--积分品牌--}}
        <input type="hidden" id="i_r" value="{{request()->input('r')}}"/> {{--积分范围--}}
        <input type="hidden" id="i_c1" value="{{request()->input('c1')}}"/> {{--当前选择的一级分类--}}
        <input type="hidden" id="i_c2" value="{{request()->input('c2')}}"/> {{--当前选择的二级分类--}}
        <input type="hidden" id="i_sort_order" value="{{$sort_order}}"/> {{--排序--}}

        <input type="hidden" id="i_page" value="{{request()->input('page')}}"/> {{--页码--}}

        <div id="pager"></div>
    </div>

    <script id="recommend-goods-list-tpl" type="text/html">
        @verbatim
        {{each data good i }}
        <div class="sale-image background-cover" onclick="goDetail('{{good.web_id_code}}')" style="background-image: url('{{good.cover}}')">
            <div class="sale-promotion" style="background-image: linear-gradient( 135deg, #FDEB71 10%, #F8D800 100%);">
                <div class="sale-promotion-title single-line">{{good.name}}</div>
                <div class="sale-promotion-info single-line">
                    <!--<span class="brand">蒙娜丽莎</span>-->
                    <span class="del">￥{{good.market_price}}</span>
                    <span class="integral">{{good.integral}}</span>
                    <span class="integral-text">积分</span>
                    <span class="sell-count">已有{{good.exchange_amount}}人兑换</span>
                </div>
            </div>
        </div>
        {{/each}}
        @endverbatim
    </script>

    <script id="cat1-list-tpl" type="text/html">
        @verbatim
        {{each data cat i }}
        <div class="category-image" onclick="selectCat1('{{cat.id}}')" style="background-image: url('{{cat.photo}}')">{{cat.name}}</div>
        {{/each}}
        @endverbatim
    </script>


    <script id="good-list-tpl" type="text/html">

        @verbatim
        {{each data good i }}
        <div class="productview">
            <div class="pimage" onclick="goDetail('{{good.web_id_code}}')" id="primage_0" style="background-image: url('{{good.cover}}')"></div>
            <div class="priceview">
                <div class="pricetxt">{{good.integral}}积分</div>
                <div class="price">￥{{good.market_price}}</div>
                <div class="buycount">已兑{{good.exchange_amount}}件</div>
            </div>
            <div class="productname">{{good.name}}</div>
        </div>
        {{/each}}
        @endverbatim

    </script>


    <script id="filter-type-tpl" type="text/html">
        @verbatim
        {{each data filter_item i }}
        <div class="nav_container">
            <div class="nav_span1" style="width:98px;">
                <span class="nav_span">{{filter_item.title}}</span>
            </div>
            <div class="nav_text" style="width:972px;">
                <span class="nav_t all-option {{if !filter_item.has_selected}}active{{/if}}" onclick="changeFilterType(this,'{{filter_item.value}}',0)">不限</span>

                {{each filter_item.data item_value j }}
                <span class="nav_t {{if item_value.selected }}active{{/if}}" onclick="changeFilterType(this,'{{filter_item.value}}','{{item_value.id}}')">{{item_value.name}}</span>
                {{/each}}
            </div>
        </div>
        {{/each}}
        @endverbatim
    </script>

    <script id="good-list-empty" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

@endsection

@section('script')

    <script>

    </script>

@endsection