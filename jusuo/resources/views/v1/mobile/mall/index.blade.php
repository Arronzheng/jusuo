@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/static/iconfont/iconfont.css',
       '/v1/css/mobile/mall.css?v='.str_random(random_int(20,30)),
       '/v1/static/swiper/swiper.min.css?v='.str_random(random_int(20,30)),

   ],
   'js'=>[
       '/v1/static/swiper/swiper.min.js',
       '/v1/js/ajax.js',
       '/v1/js/mobile/mall/index.js?v='.str_random(random_int(20,30))
   ],
   'title'=>'积分商城'
])

@section('content')
    <div id='top-category-outer'>
        <div id="top-category-content"></div>
    </div>

    <div id='top-category-service'>
        <div id="sort-filter-outer">
            <div class="sort-filter-text">筛选</div><div class="sort-filter-icon"></div>
        </div>
    </div>

    <div class="bannerBox">
        <div id="swipers"></div>
        <div class="swiper-pagination"></div>
        <div id="banner-bottom">
            <div id="banner-bottom-circle"></div>
        </div>
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

    <div id="filter-content-outer" class="hidden">
        <div class="filter-group-outer">
            <div class="filter-title">品牌</div>
            <div class="filter-group">
                <div class="filter-content-block">不限</div>
                <div class="filter-content-block checked">新明珠</div>
                <div class="filter-content-block">蒙娜丽莎</div>
                <div class="filter-content-block">东鹏</div>
                <div class="filter-content-block">新中源</div>
                <div class="filter-content-block">欧神诺</div>
            </div>
        </div>
        <div class="filter-group-outer">
            <div class="filter-title">类别</div>
            <div class="filter-group">
                <div class="filter-content-block">不限</div>
                <div class="filter-content-block checked">家纺</div>
                <div class="filter-content-block">厨卫</div>
            </div>
        </div>
        <div class="filter-group-outer">
            <div class="filter-title">积分范围</div>
            <div class="filter-group">
                <div class="filter-content-block">不限</div>
                <div class="filter-content-block checked">0-50</div>
                <div class="filter-content-block">50-100</div>
                <div class="filter-content-block">100-200</div>
                <div class="filter-content-block">200-500</div>
                <div class="filter-content-block">>500</div>
            </div>
        </div>
        <div class="filter-submit-outer">
            <div class="filter-submit-cancel">取消</div>
            <div class="filter-submit-reset">重置</div>
            <div class="filter-submit-submit">确定</div>
        </div>
    </div>
    </div>
    <div id="filter-content-mask" class="hidden"></div>
    <div id="product-list-outer">

    </div>


    <input type="hidden" id="i_b" value="{{request()->input('b')}}"/> {{--积分品牌--}}
    <input type="hidden" id="i_r" value="{{request()->input('r')}}"/> {{--积分范围--}}
    <input type="hidden" id="i_c1" value="{{request()->input('c1')}}"/> {{--当前选择的一级分类--}}
    <input type="hidden" id="i_c2" value="{{request()->input('c2')}}"/> {{--当前选择的二级分类--}}
    <input type="hidden" id="i_sort_order" value="{{$sort_order}}"/> {{--排序--}}

    <script id="good-list-empty" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

    <script id="cat1-list-tpl" type="text/html">
        @verbatim
        {{each data cat i }}
        <div class="top-category" onclick="selectCat1('{{cat.id}}')" id="cat1-{{cat.id}}">{{cat.name}}</div>
        {{/each}}
        @endverbatim
    </script>

    <script id="filter-type-tpl" type="text/html">
        @verbatim
        {{each data filter_item i }}
        <div class="filter-group-outer">
            <div class="filter-title">{{filter_item.title}}</div>
            <div class="filter-group nav_text">
                <div class="filter-content-block nav_t all-option {{if !filter_item.has_selected}}checked{{/if}}" onclick="changeFilterType(this,'{{filter_item.value}}',0)">不限</div>
                {{each filter_item.data item_value j }}
                <div class="filter-content-block nav_t {{if item_value.selected }}checked{{/if}}" onclick="changeFilterType(this,'{{filter_item.value}}','{{item_value.id}}')">{{item_value.name}}</div>
                {{/each}}
            </div>
        </div>
        {{/each}}
        <div id="filter-outer-close">关闭</div>
        @endverbatim
    </script>

    <script id="good-list-tpl" type="text/html">

        @verbatim

        {{each data good i }}

        <div class="product-item-outer" onclick="goDetail('{{good.web_id_code}}')">
            <div class="product-image" style="background-image:url({{good.cover}})"></div>
            <div class="product-right">
                <div class="product-title">
                    {{good.name}}
                </div>
                <div class="product-description">
                    {{good.short_intro}}
                </div>
                <div class="product-price"><span class="font-lg">{{good.integral}}</span>积分<del class="orange">￥{{good.market_price}}</del></div>
                <div class="product-data"><span>{{if good.exchange_amount>0}}{{good.exchange_amount}}人兑换{{/if}}</span></div>
            </div>
        </div>

        {{/each}}
        @endverbatim

    </script>



@endsection

@section('script')
    <script>


    </script>


@endsection