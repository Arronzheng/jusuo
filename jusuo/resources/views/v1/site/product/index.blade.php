@extends('v1.site.layout',[
   'css'=>[
       '/v1/css/site/common/pager.css',
       '/v1/css/site/product/index.css',
       '/v1/static/iconfont/iconfont.css',
       '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
       '/v1/static/js/xlPaging1.js',
       '/v1/static/swiper/swiper.min.js',
   ]
])

@section('content')

    <div class="product-list-container" >
        <div class="nav_lujin">
            <span class="navtext1">首页 / 产品库 / </span>
            <span class="navtext2">共<span id="data-count"></span>个符合条件的结果<span id="keyword"></span><span id="clear-keyword">清除×</span></span>
        </div>
        <div class="bannerBox">
            <div class="bannerBox" id="swipers"></div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="product-nav" id="allnav"></div>

        <div class="paixucontainer">
            <?php
            $sort_order = request()->input('order','comples_desc');
            $sort = '';
            $order = '';
            if($sort_order){
                $sort_order_array = explode('_',$sort_order);
                $sort = isset($sort_order_array[0])?$sort_order_array[0]:'';
                $order = isset($sort_order_array[1])?$sort_order_array[1]:'';
            }
            ?>
            <div class="paixu" id="paixu">
                <div class="paixu-block" data-type="comples">
                    <span class="paixu_label @if($sort=='comples')active1 @endif" id="paixu_0" onclick="change_paixu(this)">综合</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='comples' && $order=='asc')active @endif" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='comples' && $order=='desc')active @endif" ></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="pop">
                    <span class="paixu_label @if($sort=='pop')active1 @endif" id="paixu_1" onclick="change_paixu(this)">人气</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='pop' && $order=='asc')active @endif" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='pop' && $order=='desc')active @endif" ></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="visit">
                    <span class="paixu_label @if($sort=='visit')active1 @endif" id="paixu_2" onclick="change_paixu(this)">浏览量</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='visit' && $order=='asc')active @endif" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='visit' && $order=='desc')active @endif" ></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="time">
                    <span class="paixu_label @if($sort=='time')active1 @endif" id="paixu_2" onclick="change_paixu(this)">最新</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc  @if($sort=='time' && $order=='asc')active @endif" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='time' && $order=='desc')active @endif" ></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="price">
                    <span class="paixu_label @if($sort=='price')active1 @endif" id="paixu_2" onclick="change_paixu(this)">价格</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='price' && $order=='asc')active @endif" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='price' && $order=='desc')active @endif" ></span>
                        </div>
                    </div>
                </div>
            </div>
            <span class="price">价格区间</span>
            <input type="text" id="i_min_price" value="{{request()->input('mip')}}" class="priceinput"/>
            <div class="pline"></div>
            <input type="text" id="i_max_price" value="{{request()->input('map')}}" class="priceinput1"/>
            <span class="price1">元</span>
            <input type="text"  id="i_search" value="{{request()->input('search')}}" class="priceinput2" placeholder="搜索名称、型号" />
            <div class="button" onclick="submit_search()">搜索</div>
        </div>

        <div class="product">
            <div id="produ">
                <div class="productcontainer" id="product">


                </div>
            </div>
        </div>

        {{--<input type="hidden" id="i_pc" value="{{request()->input('pc')}}"/>--}} {{--经营类别--}}
        <input type="hidden" id="i_kw" value="{{request()->input('k')}}"/> {{--关键字--}}
        <input type="hidden" id="i_bd" value="{{request()->input('bd')}}"/> {{--品牌--}}
        <input type="hidden" id="i_stl" value="{{request()->input('stl')}}"/> {{--风格--}}
        <input type="hidden" id="i_series" value="{{request()->input('series')}}"/> {{--系列--}}
        <input type="hidden" id="i_clr" value="{{request()->input('clr')}}"/> {{--色系--}}
        <input type="hidden" id="i_tc" value="{{request()->input('tc')}}"/> {{--工艺类别--}}
        <input type="hidden" id="i_sc" value="{{request()->input('sc')}}"/> {{--规格--}}

        <input type="hidden" id="i_sort_order" value="{{request()->input('order')}}"/> {{--排序--}}

        <input type="hidden" id="i_page" value="{{request()->input('page')}}"/> {{--页码--}}

        <div id="pager"></div>
    </div>

    <script id="filter-type-tpl" type="text/html">
        @verbatim
        {{each data filter_item i }}
        <div class="nav_container">
            <div class="nav_span1" >
                <span class="nav_span">{{filter_item.title}}</span>
            </div>
            <div class="nav_text" >
                <span class="nav_t {{if !filter_item.has_selected}}active{{/if}}" onclick="change_filter_type(this,'{{filter_item.value}}',0)">不限</span>

                {{each filter_item.data item_value j }}
                <span class="nav_t {{if item_value.selected }}active{{/if}}" onclick="change_filter_type(this,'{{filter_item.value}}','{{item_value.id}}')">{{item_value.name}}</span>
                {{/each}}
            </div>
        </div>
        {{/each}}
        @endverbatim
    </script>

    <script id="product-list-empty" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

    <script id="product-list-tpl" type="text/html">

        @verbatim
        {{each data product i }}
        <div class="productview">
            <div class="pimage" onclick="go_detail('{{product.web_id_code}}')" id="primage_0" style="background-image: url(&quot;{{product.cover}}&quot;);"></div>
            <div class="productname">{{product.name}}</div>
            <div class="productcode">{{product.code}}</div>
            <div class="priceview">
                <span class="pricetxt">{{product.price}}</span>
                <div class="lookview">
                    <span class="iconfont {{if product.collected}}icon-buoumaotubiao44{{else}}icon-shoucang2{{/if}}" id="collected_{{i}}" style="color:{{if product.collected}}#1582FF{{else}}#B7B7B7{{/if}};" onclick="collected({{i}})"></span>
                    <span class="looknumber" id="collectednumber_{{i}}" style="color:{{if product.collected}}#1582FF{{else}}#B7B7B7{{/if}};" onclick="collected({{i}})">{{product.count_fav}}</span>
                </div>
            </div>
            <div class="details">
                <span class="companytext">{{product.brand.short_name}}</span>
                <div class="lookview">
                    <div class="dingwei"></div>
                    <span class="areatxt"></span>
                </div>
            </div>
        </div>
        {{/each}}
        @endverbatim

    </script>

@endsection

@section('script')

    <script>

        var filter_types_api_url = "{{url('/product/api/list_filter_types')}}?__bs="+__cache_brand;
        var product_list_api = "{{url('/product/api/list_products')}}?__bs="+__cache_brand;
        var product_page_api = "{{url('/product')}}?__bs="+__cache_brand;
        var product_collect_api_url = "{{url('/product/api/collect')}}?__bs="+__cache_brand;
        var current_url = "{{url()}}";
        <?php
        $dlr = request()->input('dlr');
        ?>
        var dlr_param='{{isset($dlr)?$dlr:''}}';

    </script>

    <script src="{{asset('/v1/js/site/product/index.js')}}"></script>


@endsection