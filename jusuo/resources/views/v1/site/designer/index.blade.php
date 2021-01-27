@extends('v1.site.layout',[
   'css'=>[
       '/v1/css/site/common/pager.css',
       '/v1/css/site/designer/index.css',
       '/v1/static/iconfont/iconfont.css',
       '/v1/static/swiper/swiper.min.css',

   ],
   'js'=>[
       '/v1/static/js/xlPaging1.js',
       '/v1/static/swiper/swiper.min.js',

   ]
])

@section('content')

    <div class="designer-list-container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 设计师 / </span>
            <span class="navtext2">共<span id="data-count"></span>个符合条件的结果<span id="keyword"></span><span id="clear-keyword">清除×</span></span>
        </div>
        <div class="bannerBox">
            <div class="bannerBox" id="swipers"></div>
            <div class="swiper-pagination"></div>

        </div>
        <div class="designercontainer">
            <div class="designer_left">
                <img src="/v1/images/site/designer/pop-designer-icon@2x.png" class="designer_icon">
                <span class="designer_title">优秀设计师</span>
            </div>
            <div class="designer" id="designer">

            </div>
        </div>
        <div class="filter-nav" id="allnav">

        </div>
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
        <div class="paixucontainer">
            <div class="paixu" id="paixu">
                <div class="paixu-block" data-type="comples">

                    <span class="paixu_label @if($sort=='comples')active1 @endif" id="paixu_0"  onclick="change_paixu(this)">综合</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='comples' && $order=='asc')active @endif" id="paixu1_0" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='comples' && $order=='desc')active @endif" id="paixu2_0"></span>
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
                            <span class="iconfont icon-paixu-1 desc @if($sort=='pop' && $order=='desc')active @endif"  ></span>
                        </div>
                    </div>
                </div>

                <div class="paixu-block" data-type="album">
                    <span class="paixu_label @if($sort=='album')active1 @endif" id="paixu_2" onclick="change_paixu(this)">方案数</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='album' && $order=='asc')active @endif" ></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='album' && $order=='desc')active @endif"  ></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="de_fangan">
            <div id="produ" style="margin-bottom:30px;">
                <div id="designer-list-container">


                </div>
            </div>

            <input type="hidden" id="i_kw" value="{{request()->input('k')}}"/> {{--关键字--}}
            <input type="hidden" id="i_stl" value="{{request()->input('stl')}}"/> {{--擅长风格--}}
            <input type="hidden" id="i_sp" value="{{request()->input('sp')}}"/> {{--擅长空间--}}
            <input type="hidden" id="i_lv" value="{{request()->input('lv')}}"/> {{--等级--}}

            <input type="hidden" id="i_sort_order" value="{{request()->input('order')}}"/> {{--排序--}}

            <input type="hidden" id="i_page" value="{{request()->input('page')}}"/> {{--页码--}}

            <div id="pager"></div>

        </div>
    </div>

    <script id="nice-designer-tpl" type="text/html">
        @verbatim
        {{each datas designer i}}
        <div class="designeritem" onmouseenter="look_detail({{i}})" onmouseleave="look_detail({{i}})">
            <div id="d_normal{{i}}">
                <div class="d_image" id="d_image{{i}}" style="background-image: url(&quot;{{designer.avatar}}&quot;);">
                    <div class="d_back">
                        <div class="d_name">{{designer.nickname}}</div>
                        {{if designer.level}}
                        <div class="d_{{designer.level}}"></div>
                        {{/if}}
                    </div>
                </div>
            </div>
            <div class="d_zhezhao" id="d_zhezhao{{i}}" style="display: none; background-image: url(&quot;{{designer.avatar}}&quot;);">
                <div class="zhezhao"></div>
                <div class="dz-zhezhao">
                    <div class="dz_name">{{designer.nickname}}</div>
                    {{if designer.level}}
                    <div class="dz_{{designer.level}}"></div>
                    {{/if}}
                    <div class="dz_company">{{designer.company_name}}</div>
                    <div class="dz_fan">
                        <div class="d_fans">{{designer.fans}}</div>
                        <div class="d_fangan">粉丝数</div>
                    </div>
                    <div class="dz_fan1">
                        <div class="d_fans">{{designer.count_upload_album}}</div>
                        <div class="d_fangan">设计方案数</div>
                    </div>
                    <div class="d_botton" onclick="go_designer_detail('{{designer.web_id_code}}')">查看个人主页</div>
                </div>
            </div>
        </div>
        {{/each}}
        @endverbatim
    </script>

    <script id="designer-list-tpl" type="text/html">
        @verbatim
        {{each data designer i }}
        <div class="fangan_item" onclick="go_designer_detail('{{designer.web_id_code}}')">
            <div class="d_perimage" id="d_perimage{{i}}" style="background-image: url(&quot;{{designer.url_avatar}}&quot;);"></div>
            <div class="d_middle">
                <div class="dm_head">
                    <div class="df_name">{{designer.nickname}}</div>
                    <div class="dm_{{designer.level}}"></div>
                    {{if designer.focused==true}}
                    <div class="design_guanzhu1" onclick="de_guanzhu({{i}});event.cancelBubble=true"  id="desi_guanzhu{{i}}">已关注</div>
                    {{else}}
                    <div class="design_guanzhu" onclick="de_guanzhu({{i}});event.cancelBubble=true" id="desi_guanzhu{{i}}">关注</div>
                    {{/if}}
                </div>
                <div class="de_posi">
                    <div class="position_icon"></div>
                    <div class="de_position">{{designer.area_text}}</div>
                </div>
                <div class="de_style">擅长风格：{{designer.styles_text}}</div>
                <div class="de_detail">
                    <div class="dm_fan">
                        <div class="dm_fanspan">{{designer.fans}}</div>
                        <div class="dm_fanspan1">粉丝数</div>
                    </div>
                    <div class="dm_line"></div>
                    <div class="dm_fan1">
                        <div class="dm_fanspan">{{designer.count_upload_album}}</div>
                        <div class="dm_fanspan1">设计方案数</div>
                    </div>
                </div>
            </div>
            <div class="de_right">
                {{each designer.albums album k}}
                <div class="dr_container">
                    <div class="dr_image" id="dr_image{{i}}_{{k}}" style="background-image: url(&quot;{{album.photo_cover}}&quot;);"></div>
                    <div class="dr_text">{{album.title}}</div>
                </div>
                {{/each}}

            </div>
        </div>
        {{/each}}
        @endverbatim
    </script>

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

    <script id="designer-list-empty" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>


@endsection

@section('script')

    <script>


        var filter_types_api_url = "{{url('/designer/api/list_filter_types')}}?__bs="+__cache_brand;
        var designer_list_api = "{{url('/designer/api/list_designers')}}?__bs="+__cache_brand;
        var designer_page_api = "{{url('/designer')}}?__bs="+__cache_brand;
        var designer_focus_api_url = "{{url('/designer/api/focus')}}?__bs="+__cache_brand;
        var nice_designer_api_url = "{{url('/designer/api/list_nice_designers')}}?__bs="+__cache_brand;

    </script>


    <script src="{{asset('/v1/js/site/designer/index.js')}}"></script>

@endsection