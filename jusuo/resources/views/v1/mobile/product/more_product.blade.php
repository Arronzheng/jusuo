@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/album.css',
       '/v1/css/mobile/common.css',
       '/v1/css/mobile/products.css',
   ],
   'js'=>[
        '/v1/static/js/xlPaging.js',
        '/v1/js/ajax.js',
        '/v1/js/mobile/more_products.js?v='.str_random(random_int(20,30)),
   ]
])
@section('content')

    <div class="sub-container" id="product_list">
        <div class="sub-container-title">产品搭配(0)</div>
        <div class="item-list">

        </div>
    </div>

    @verbatim
        <script id="product_list_tpl" type="text/html">
            <div class="sub-container-title">产品搭配({{ data.length }})</div>
            <div class="item-list">
                {{ each data item i }}
                <div class="item-outer">
                    <div class="item-image"
                         style="background-image: url(&quot;{{item.photo[0]}}&quot;);"></div>
                    <div class="action-data-outer">
                        <div class="action {{ if item.collected }}active{{ /if }}" id="fav_product_collocations_button{{i}}" onclick="bind_fav_product_collocations(this)" data-index="{{i}}"><span class="iconfont icon-shoucang2"></span>{{ item.count_fav }}</div>
                        <div class="action"><span class="iconfont icon-yinyongziyuan"></span>{{ item.count_album }}</div>
                        <div class="action"><span class="iconfont icon-mima-xianshi"></span>{{ item.count_visit }}</div>
                    </div>
                    <div class="item-info">
                        <div class="title-outer">{{ item.name }}</div>
                        <div class="info-outer">{{ item.spec_text }}</div>
                    </div>
                </div>
                {{ /each }}
            </div>
        </script>

    @endverbatim

@endsection

@section('script')

    <script>
        var product_list_collocations = "{{ url('/mobile/product/get_list_product_collocations/'.request()->route('id')) }}"
        var product_fav = "{{ url('/mobile/product/api/fav_product') }}"
    </script>


@endsection