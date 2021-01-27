@extends('v1.site.layout',[
   'css'=>[
       '/v1/css/site/common/pager.css',
       '/v1/css/site/album/index.css',
       '/v1/static/iconfont/iconfont.css',
   ],
   'js'=>[
       '/v1/static/js/xlPaging1.js',
   ]
])

@section('content')

    <div class="album-list-container" >
        <div class="nav_lujin">
            <span class="navtext1">首页 / 设计方案 / </span>
            <span class="navtext2">共<span id="data-count"></span>个符合条件的结果<span id="keyword"></span><span id="clear-keyword">清除×</span></span>
        </div>
        <div class="album-nav" id="allnav"></div>

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
        <div class="chooselabel">
            <div class="paixu" id="paixu">
                <div class="paixu-block" data-type="comples">
                    <span class="paixu_label @if($sort=='comples')active1 @endif" id="paixu_0" onclick="change_paixu(this)">综合</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc  @if($sort=='comples' && $order=='asc')active @endif" ></span>
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
                            <span class="iconfont icon-paixu-1 desc @if($sort=='pop' && $order=='desc')active @endif"  ></span>
                        </div>
                    </div>
                </div>
                <div class="paixu-block" data-type="time">
                    <span class="paixu_label @if($sort=='time')active1 @endif" id="paixu_2" onclick="change_paixu(this)">时间排序</span>
                    <div class="up_down" onclick="change_paixu(this)">
                        <div class="up">
                            <span class="iconfont icon-paixu asc @if($sort=='time' && $order=='asc')active @endif"></span>
                        </div>
                        <div class="down">
                            <span class="iconfont icon-paixu-1 desc @if($sort=='time' && $order=='desc')active @endif" ></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chooselabel1">
                <span class="iconfont @if($isrw==1)icon-xuankuang @else icon-xuankuang1 @endif" style="@if($isrw==1) color:#15 @endif"  id="chooselabel" onclick="changechooselabel(this)"></span>
                <span class="chooselabeltext">精选方案</span>
            </div>
            <div class="leadlabel" id="lead">
                {{--<span class="leadspan">引导关键词1</span><span
                        class="leadspan">引导关键词2</span><span class="leadspan">引导关键词3</span>--}}
            </div>
        </div>

        <div class="projectcontainer" id="project">
            
        </div>

        <input type="hidden" id="i_kw" value="{{request()->input('k')}}"/> {{--关键字--}}
        <input type="hidden" id="i_stl" value="{{request()->input('stl')}}"/> {{--风格--}}
        <input type="hidden" id="i_ht" value="{{request()->input('ht')}}"/> {{--户型--}}
        <input type="hidden" id="i_spt" value="{{request()->input('spt')}}"/> {{--空间--}}
        <input type="hidden" id="i_ca" value="{{request()->input('ca')}}"/> {{--面积--}}

        <input type="hidden" id="i_sort_order" value="{{$sort_order}}"/> {{--排序--}}

        <input type="hidden" id="i_isrw" value="{{request()->input('isrw',0)}}"/> {{--是否精选--}}

        <input type="hidden" id="i_page" value="{{request()->input('page')}}"/> {{--页码--}}

        <div id="pager"></div>
    </div>



    <script id="filter-type-tpl" type="text/html">
        @verbatim
        {{each data filter_item i }}
        <div class="nav_container">
            <div class="nav_span1" style="width:48px;">
                <span class="nav_span">{{filter_item.title}}</span>
            </div>
            <div class="nav_text" style="width:1022px;">
                <span class="nav_t {{if !filter_item.has_selected}}active{{/if}}" onclick="change_filter_type(this,'{{filter_item.value}}',0)">不限</span>

                {{each filter_item.data item_value j }}
                <span class="nav_t {{if item_value.selected }}active{{/if}}" onclick="change_filter_type(this,'{{filter_item.value}}','{{item_value.id}}')">{{item_value.name}}</span>
                {{/each}}
            </div>
        </div>
        {{/each}}
        @endverbatim
    </script>

    <script id="album-list-empty" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

    <script id="album-list-tpl" type="text/html">

        @verbatim
        {{each data album i }}
        <div class="projectitem" >
            <div class="designimage" onclick="click_album('{{album.web_id_code}}')"  id="image_0" style="background-image: url('{{album.photo_cover}}');"></div>
            {{if album.panorama}}
            <div class="wholeview">全景图</div>
            {{/if}}
            <div class="designer_text">
                <span class="d_area">{{album.count_area}}㎡&nbsp;|</span>
                <span class="d_title">&nbsp;{{album.title}}</span>
            </div>
            <div class="d_detail1">
                <span class="iconfont icon-chakan" id="look" style="color:#B7B7B7;margin-left:21px;"></span>
                <span class="looknumber">{{album.count_visit}}</span>
                <span class="iconfont {{ if album.liked }} icon-dianzan {{ else }} icon-dianzan2 {{ /if }}" id="like_{{i}}" style="color:{{ if album.liked }}#1582FF {{else}}#B7B7B7 {{/if}};margin-left:42px;" onclick="like({{i}})"></span>
                <span class="looknumber" id="likenumber_{{i}}" style="color:{{ if album.liked }}#1582FF {{else}}#B7B7B7 {{/if}}" onclick="like({{i}})">{{album.count_praise}}</span>
                <span class="iconfont {{ if album.collected }}icon-buoumaotubiao44 {{else}} icon-shoucang2 {{/if}}" id="collected_{{i}}" style="color:{{ if album.collected }}#1582FF {{else}} #B7B7B7 {{/if}};margin-left:42px;" onclick="collected({{i}})"></span>
                <span class="looknumber" id="collectednumber_{{i}}" style="color:{{ if album.collected }}#1582FF {{else}} #B7B7B7 {{/if}}" onclick="collected({{i}})">{{album.count_fav}}</span>
            </div>
            <div class="d_line"></div>
            <div class="d_person1">
                <div class="d_personimage1" id="d_personimage0" style="background-image: url('{{album.designerAvatar}}');"></div>
                <span class="d_personname">{{album.designerNickname}}</span>
                {{if album.designerIdentity==true}}
                <span class="iconfont icon-shimingrenzheng" style="color:#1582FF;font-size:16px;margin-left:10px;margin-top:4px;"></span>
                {{/if}}
                {{if album.designerHot==true}}
                <span class="iconfont icon-renqiwang" id="hot" style="color:#FFE115;margin-left:10px;margin-top:4px;"></span>
                {{/if}}
            </div>
        </div>
        {{/each}}
        @endverbatim

    </script>

@endsection

@section('script')

    <script>

        var album_list = [];
        var filter_types = [];

        var filter_types_api_url = "{{url('/album/api/list_filter_types')}}?__bs="+__cache_brand;
        var album_list_api = "{{url('/album/api/list_albums')}}?__bs="+__cache_brand
        var album_page_api = "{{url('/album')}}?__bs="+__cache_brand
        var album_collect_api_url = "{{url('/album/api/collect')}}?__bs="+__cache_brand
        var album_like_api_url = "{{url('/album/api/like')}}?__bs="+__cache_brand
        <?php
                $dsn = request()->input('dsn');
                $dlr = request()->input('dlr');
                ?>
        var dsn_param='{{isset($dsn)?$dsn:''}}';
        var dlr_param='{{isset($dlr)?$dlr:''}}';

    </script>

    <script src="{{asset('/v1/js/site/album/index.js')}}"></script>

@endsection