@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/album.css?v='.str_random(random_int(20,30)),
   ],
   'js'=>[
       '/v1/js/mobile/album.js?v=1'/*.str_random(random_int(20,30))*/,
       //20200721如果用random的v后缀，可能使wx.config无效，所以注释掉
       '/v1/js/ajax.js',
   ]
])

@section('content')

    <div class="cover" style="" id="album_baseinfo">

    </div>

    <div class="container">

        <div class="tag-outer" id="album_tag">

            <!--<span class="location xq">青藤花苑</span>-->
        </div>
        <div class="section-title" id="album_description_title">简述</div>
        <div class="section-content" id="album_description">

        </div>
        <div class="section-title" id="album_huxing_photo_title">户型</div>
        <div class="section-content" id="album_huxing_photo">

        </div>
        <div class="section-content" id="album_description_layout">

        </div>
        <div id="album_section">

        </div>


        <div class="splitter"></div>

        <div class="sub-container" id="album_comments">
            <div class="sub-container-title">全部评论(0)</div>

            <div class="sub-container-more" id="more_comment_view">查看全部评论 ></div>
        </div>

        <div class="splitter"></div>

        <div class="sub-container" id="album_similiars">
            <div class="sub-container-title">推荐方案</div>

            <!--<div class="sub-container-more">查看更多方案 ></div>-->
        </div>

        <div class="splitter"></div>


        <div class="sub-container" id="product-list">
            <div class="sub-container-title">产品清单{{--(<span id="product-count"></span>)--}}</div>
            <div class="item-list">

            </div>
        </div>

    </div>

    <div class="comment-input-outer">
        <div id="comment-input"><input placeholder="说点什么吧"  id="album_comment_input"/> </div>
        <div id="comment-send" class="active" onclick="bind_send_album_comment()">发送</div>
    </div>

    <div class="back-to-top" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:11vw;background-image:url(../../../v1/images/mobile/back_to_top.png);background-size:contain;background-repeat:no-repeat;background-position:center;"></div>
    <a href="/mobile/center"><div class="go-to-center" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:21vw;background-image:url(../../../v1/images/mobile/go_to_center.png);background-size:contain;background-repeat:no-repeat;background-position:center;"></div></a>

    @verbatim
        <!--产品清单-->
        <script id="list-product-tpl" type="text/html">
            {{each datas product i}}
            <div class="item-outer" onclick="go_product_detail('{{product.web_id_code}}')">
                <div class="item-image"
                     style="background-image:url('{{product.photo_product}}')"></div>

                <div class="item-info">
                    <div class="title-outer">{{product.name}}</div>
                    <div class="info-outer">{{product.spec}}</div>
                    <div class="action-data-outer">
                        <div class="action"><span class="iconfont icon-shoucang2"></span>{{product.count_fav}}</div>
                        <div class="action"><span class="iconfont icon-yinyongziyuan"></span>{{product.count_album}}</div>
                        <div class="action"><span class="iconfont icon-mima-xianshi"></span>{{product.count_visit}}</div>
                    </div>
                </div>
            </div>
            {{/each}}
        </script>

        <!--基本信息-->
        <script id="album_baseinfo_tpl" type="text/html">
            <div class="title">{{ album.title }}</div>
            <div class="designer-info">
                <span class="nickname">by {{ album.designer_info.nickname }}，{{ album.designer_info.organization }}</span>
                <span class="level level-jinpai"></span>
            </div>
            <div class="action-outer">
                <div class="action {{ if(album.collected)}}active{{ /if }}" onclick="bind_fav_album(this)"><span class="iconfont icon-shoucang2"></span>{{ album.count_fav }}</div>
                <div class="action {{ if(album.liked) }}active {{ /if }}" onclick="bind_like_album(this)"><span class="iconfont icon-dianzan2"></span>{{ album.count_praise }}</div>
                <div class="action"><span class="iconfont icon-mima-xianshi"></span>{{ album.count_visit }}</div>
            </div>
            <div class="avatar"
                 style="background-image: url(&quot;{{ album.designer_info.url_avatar }}&quot;);" onclick="bind_to_album_designer()">
            </div>
            <div class="nickname">{{ album.designer_info.nickname }}</div>
        </script>

        <!--tag-->
        <script id="album_tag_tpl" type="text/html">
            <span class="tag hx">{{ album.house_type_text }}</span>
            <span class="tag mj">{{ album.count_area }}㎡</span>
            {{ each style_arr item i}}
                <span class="tag fg">{{ item }}</span>
            {{ /each }}
        </script>

        <!--户型图-->
        <script id="album_huxing_photo_tpl" type="text/html">
            <div class="section-outer">
            {{ each album.photo_layout_data item i}}
            <img class="section-img" src="{{ item }}" />
            {{ /each }}
            </div>
        </script>

        <!--空间-->
        <script id="album_section_tpl" type="text/html">
            {{ each sections section i}}
            <div class="section-outer">
                <div class="section-title">{{ section.space_type_text }}</div>
                <div class="section-tag-outer">
                    {{ if section.count_area>0 }}
                    <span class="tag mj">{{ section.count_area }}㎡</span>
                    {{ /if }}
                    {{ each section.style_arr style_item style_index}}
                        <span class="tag fg">{{ style_item }}</span>
                    {{ /each }}

                </div>

                {{ if section.content.design.photos.length>0 }}
                <div class="section-sub-title">
                    <span class="section-tab active" data-attr="0">空间设计</span>
                </div>
                <div class="section-content">
                    {{section.content.design.description}}
                </div>
                {{ each section.content.design.photos section_design_photo_item section_design_photo_index}}
                <img class="section-img" src="{{ section_design_photo_item }}" />
                {{ /each }}
                {{ /if }}

                {{ if section.content.product.photos.length>0 }}
                <div class="section-sub-title">
                    <span class="section-tab active" data-attr="1">产品应用图</span>
                </div>
                <div class="section-content">
                    {{section.content.product.description}}
                </div>
                {{ each section.content.product.photos  section_product_photo_item section_product_photo_index}}
                <img class="section-img" src="{{ section_product_photo_item }}" />
                {{ /each }}
                {{ /if }}

                {{ if section.content.build.photos.length>0 }}
                <div class="section-sub-title">
                    <span class="section-tab active" data-attr="2">施工图</span>
                </div>
                <div class="section-content">
                    {{section.content.build.description}}
                </div>
                {{ each section.content.build.photos  section_product_build_item section_product_build_index}}
                <img class="section-img" src="{{ section_product_build_item }}" />
                {{ /each }}
                {{ /if }}

            </div>
            {{ /each }}
        </script>

        <!--评论-->
        <script id="album_comments_tpl" type="text/html">
            <div class="sub-container-title">全部评论({{ data.length }})</div>
            {{ each data item i}}
            <div class="comment-outer">
                <div class="sub-container-avatar"
                     style="background-image: url(&quot;{{ item.author_avatar }}&quot;);"></div>
                <div class="comment-text-outer">
                    <div class="sub-container-nickname">{{ item.author }}<span class="sub-container-time">{{ item.publish_time }}</span></div>
                    <div class="comment-praise"><span class="iconfont icon-dianzan2"></span>0</div>
                    <div class="sub-container-content">{{# item.content }}</div>
                </div>
            </div>
            {{ /each }}

            <div class="sub-container-more" onclick="more_comment()">查看全部评论 ></div>
        </script>

        <!--相似方案-->
        <script id="album_similiars_tpl" type="text/html">
            <div class="sub-container-title">推荐方案</div>
            <div class="album-container-outer">
                {{ each data item i }}
                <div class="album-container">
                    <div class="album-container-image"
                         style="background-image: url(&quot;{{ item.photo_cover }}&quot;);" onclick="bind_to_album(this)" data-index="{{ i }}"></div>
                    <div class="album-container-avatar"
                         style="background-image: url(&quot;{{ item.url_avatar }}&quot;);" onclick="bind_to_designer(this)" data-index="{{ i }}"></div>
                    <div class="album-container-title">{{ item.title }}</div>
                    <div class="tag-outer">
                        <span class="tag mj">{{ item.count_area }}㎡</span>
                        {{ each item.style_arr style_item style_index }}
                            <span class="tag fg">{{ style_item }}</span>
                        {{ /each }}
                    </div>
                    <div class="album-container-content">
                        <div class="album-container-content-span active"><span class="iconfont icon-mima-xianshi"></span>{{ item.count_visit }}</div>
                        <div class="album-container-content-span {{ if item.liked }}active{{ /if }}" onclick="like_album(this)" data-index="{{i}}" ><span class="iconfont icon-dianzan2"></span>{{ item.count_praise }}</div>
                        <div class="album-container-content-span {{ if item.collected }}active{{/if}}" data-index="{{i}}" onclick="collect_album(this)"><span class="iconfont icon-shoucang2"></span>{{ item.count_fav }}</div>
                        <div class="album-container-content-span"><span class="iconfont icon-ic_huifu"></span>{{ item.count_comment }}</div>
                        <div class="album-container-content-span">by {{item.nickname}}</div>
                    </div>
                </div>
                {{ /each }}
                <div class="album-container-last"></div>
            </div>
        </script>
    @endverbatim

@endsection

@section('script')
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

    <script>

        var album_info_api_url = "{{url('/mobile/album/api/get_album_info/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_section_api_url = "{{url('/mobile/album/api/list_album_sections/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_product_api_url = "{{url('/mobile/album/api/list_album_products/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_similiar_api_url = "{{url('/mobile/album/api/list_album_similiars/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var commit_comment_api_url = "{{url('mobile/album/api/commit_comment/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_comments_api_url = "{{url('/mobile/album/api/list_album_comments/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_collect_api_url = "{{url('/mobile/album/api/collect')}}?__bs=" + __cache_brand;
        var album_like_api_url = "{{url('/mobile/album/api/like')}}?__bs=" + __cache_brand;
        var designer_focus_api_url = "{{url('/mobile/designer/api/focus')}}?__bs=" + __cache_brand;
        var current_url = "{{url()}}";

        //微信JSSDK初始化
        wx.config(<?php echo $jssdkConfig ?>);

    </script>


@endsection