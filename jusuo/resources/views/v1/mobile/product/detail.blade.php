@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/static/iconfont/iconfont.css',
       '/v1/css/mobile/product.css?v='.str_random(random_int(20,30)),
       '/v1/css/mobile/products.css?v='.str_random(random_int(20,30)),

   ],
   'js'=>[
       '/v1/static/js/xlPaging.js',
       '/v1/js/ajax.js',
       '/v1/js/mobile/product.js?v='/*.str_random(random_int(20,30))*/
       //20200721如果用random的v后缀，可能使wx.config无效，所以注释掉
   ]
])

@section('content')
    <style>
        .sub-container{background-color: inherit;padding:0;height:auto;overflow: inherit;}
        .sub-container .item-outer:last-child{margin-bottom:10px;}
    </style>

    <div id="cover-swiper-outer" class="cover-swiper-outer">
        <div class="swiper-wrapper" id="product_photo">
            {{--<div class="swiper-slide"--}}
                 {{--style="background-image:url(http://pf.fsqmh.com/storage/images/product/21/20/02/6Q/Rvuo4gy81TH1buqSOXUMML0BebD7tSQGePGAj34t.jpeg)"></div>--}}
            {{--<div class="swiper-slide"--}}
                 {{--style="background-image:url(http://pf.fsqmh.com/storage/images/product/21/20/02/FR/qTMzv9sMvriMWaHDlEbAMH2GOAFu3hbQM3hV5VSm.jpeg)"></div>--}}
            {{--<div class="swiper-slide"--}}
                 {{--style="background-image:url(http://pf.fsqmh.com/storage/images/product/21/20/02/UK/Sy66I3t6w586n7E8p0O9mGGeYg4QhQ9Z4kqkKir1.jpeg)"></div>--}}
        </div>
        <div class="swiper-pagination"></div>
        <div class="action-outer" id="collect_info">
            <div class="action"><span class="iconfont icon-shoucang"></span>0</div>
            <div class="action"><span class="iconfont icon-yinyongziyuan"></span>0</div>
        </div>
    </div>



    <div class="container">
        <div class="product-name" id="product_name"></div>
        <div class="brand" id="brand_info">

        </div>
        <div class="tag-outer" id="tag_info">

        </div>
        <div class="section-outer" id="section_outer">
            {{--<div class="section-title">详细介绍</div>
            <div class="section-sub-title">核心工艺</div>
            <div class="section-sub-content"></div>
            <div class="section-sub-title">理化性能</div>
            <div class="section-sub-content"></div>
            <div class="section-sub-title">功能特征</div>
            <div class="section-sub-content"></div>
            <div class="section-sub-title">顾客价值</div>
            <div class="section-sub-content"></div>--}}
        </div>
        <div class="section-outer" id="section_content_div">
            <div class="section-title">实物图</div>
            <div class="section-content" id="section_content">

            </div>
        </div>
        <div class="section-outer" id="product_video">
            <div class="section-title">产品视频</div>
                {{--<video controls="controls" src="https://charm100.com/storage/videos/product/21/20/02/9v/b0cynl4Q09QxK1Wez2aYYrIm8gDMbpmyJXlGJQ5B.mp4" id="video">您的浏览器不支持视频播放</video>--}}
        </div>
        <div class="section-outer">
            <div class="section-title">空间应用</div>
            <div id="space_images">
                {{--<div class="swiper-wrapper" id="space_images">--}}
                    {{--<div class="swiper-slide"--}}
                         {{--style="background-image:url(http://pf.fsqmh.com/storage/images/product/21/20/02/h7/QWp5doBoF1LXjUW5jU7jsQfOe2EIiLzuo9koLprN.jpeg)"></div>--}}
                    {{--<div class="swiper-slide"--}}
                         {{--style="background-image:url(http://pf.fsqmh.com/storage/images/product/21/20/02/mI/Hh3TQKmTqNpqlqWSwH5D6jpEuDsqtQJvztA8AXly.jpeg)"></div>--}}
                    {{--<div class="swiper-slide"--}}
                         {{--style="background-image:url(http://pf.fsqmh.com/storage/images/product/21/20/02/7i/GU7P4gKelGA9e3qetUSlPM9P3aWb0NkUkm3PSZQJ.jpeg)"></div>--}}
                {{--</div>--}}
                {{--<div class="swiper-pagination"></div>--}}
            </div>
            {{--<div class="section-content" id="space_note">

            </div>--}}
        </div>

        <div class="splitter"></div>

        <!--配件-->
        <div class="sub-container" id="product_accessories">
            <div class="sub-container-title">配件(0)</div>
            <div class="sub-container-more">查看全部配件 ></div>
        </div>

        <div class="splitter"></div>

        <!--产品搭配-->
        <div class="sub-container" id="product_collocations">
            <div class="sub-container-title">产品搭配(0)</div>
            <div class="sub-container-more">查看全部搭配产品 ></div>
        </div>

        <div class="splitter"></div>

        <!--问答-->
        <div class="sub-container" id="product_qas">
            <div class="sub-container-title">全部问答(0)</div>

            <a href="comment.html"><div class="sub-container-more">查看全部评论 ></div></a>
        </div>

        <div class="splitter"></div>

        <!--相关方案-->
        <div class="sub-container" id="product_albums">

            <!--<div class="sub-container-more">查看更多方案 ></div>-->
        </div>
    </div>

    <div class="comment-input-outer">
        <div id="comment-input"><input placeholder="说点什么吧" id="product_queston" /> </div>
        <div id="comment-send" class="active" onclick="bind_send_product_qa()">发送</div>
    </div>

    <div class="back-to-top" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:11vw;background-image:url(../../../v1/images/mobile/back_to_top.png);background-size:contain;background-repeat:no-repeat;background-position:center;"></div>
    <a href="/mobile/center"><div class="go-to-center" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:21vw;background-image:url(../../../v1/images/mobile/go_to_center.png);background-size:contain;background-repeat:no-repeat;background-position:center;"></div></a>

    @verbatim

        <!--轮播图-->
        <script id="product_images_tpl" type="text/html">
            {{each product.photo_product photo i}}
            {{if i<=5}}
            <div class="swiper-slide" id="swiper-slide{{i}}"
                 style="background-image: url(&quot;{{photo}}&quot;);"></div>
            {{/if}}
            {{/each}}
        </script>

        <!--关注-->
        <script id="collect_info_tpl" type="text/html">
            <div class="action {{ if product.collected }} active {{/if}}" data-product_fav_id="{{ product.web_id_code }}" onclick="bind_fav_product()" id="product_fav_button"><span class="iconfont icon-shoucang"></span>{{ product.count_fav }}</div>
            <div class="action"><span class="iconfont icon-yinyongziyuan"></span>{{ product.count_album }}</div>
        </script>

        <!--品牌信息-->
        <script id="brand_info_tpl" type="text/html">
            <img class="brand-image" src="{{product.sales_avatar}}" />
            <div class="brand-text">{{product.brand_name}} {{product.series_text}} {{ product.spec_text }} {{product.colors_text}}</div>
        </script>

        <!--标签-->
        <script id="tag_info_tpl" type="text/html">

            <span class="tag hx">{{product_category}}</span>

            {{ each apply_categories_arr item index }}
                <span class="tag hx">{{ item }}</span>
            {{ /each }}

            {{ each technology_categories_arr item index }}
                <span class="tag hx">{{ item }}</span>
            {{ /each }}


            {{ each surface_features_arr item index }}
                <span class="tag mj">{{ item }}</span>
            {{ /each }}

            {{ each styles_arr item index }}
                <span class="tag fg">{{ item }}</span>
            {{ /each }}
        </script>

        <!--详细介绍-->
        <script id="section_tpl" type="text/html">
            {{ if product.customer_value!='' }}
            <div class="section-title">详细介绍</div>
            <div class="section-sub-content">{{ product.customer_value }}</div>
            {{ /if }}
            {{ if product.key_technology!='' }}
            <div class="section-sub-title">核心工艺</div>
            <div class="section-sub-content">{{ product.key_technology }}</div>
            {{ /if }}
            {{ if product.physical_chemical_property.length>0 }}
            <div class="section-sub-title">理化性能</div>
            <div class="section-sub-content">
                {{each product.physical_chemical_property property i}}
                    {{ property }}
                {{/each}}
            </div>
            {{ /if }}
            {{ if product.function_feature.length>0 }}
            <div class="section-sub-title">功能特征</div>
            <div class="section-sub-content">
                {{each product.function_feature property i}}
                    {{property}}
                {{/each}}
            </div>
            {{ /if }}

        </script>

       <!--实物图-->
        <script id="section_content_tpl" type="text/html">
            {{each product.photo_practicality item i}}
                <img id="product_photo_item{{i}}" src="{{ item }}"/>
            {{/each}}
        </script>

        <!--视频-->
        <script id="product_video_tpl" type="text/html">
            {{ if product.photo_video.length>0 }}
            <div class="section-title">产品视频</div>
            {{ each product.photo_video item i}}
                <video controls="controls" src="{{ item }}" id="video">您的浏览器不支持视频播放</video>
            {{ /each }}
            {{ /if }}
        </script>

        <!--空间应用轮播-->
        <script id="space_images_tpl" type="text/html">
            {{ each data item i }}
            <div class="section-sub-title">{{ item.title }}</div>
            <img src="{{ item.photo }}"/>
            <div class="section-sub-content">{{ item.note }}</div>
            {{ /each }}
        </script>

        <!--配件-->
        <script id="product_accessories_tpl" type="text/html">
            {{ if data.length>0 }}
            <div class="sub-container-title">配件({{ data.length }})</div>
            <div class="item-list">
                {{ each data item i }}
                    <div class="item-outer">
                        <div class="item-image" style="background-image: url(&quot;{{item.photo[0]}}&quot;);"></div>
                        <div class="item-info">
                            <div class="title-outer single-line">{{item.code}}</div>
                            <div class="info-outer">{{ item.spec_text }}</div>
                        </div>
                </div>
                {{ /each }}
            </div>
            <div class="sub-container-more">查看全部配件 ></div>
            {{ /if }}
        </script>

        <!--产品搭配-->
        <script id="product_collocations_tpl" type="text/html">
            {{ if data.length>0 }}
            <div class="sub-container-title">产品搭配({{ data.length }})</div>
            <div class="item-list">
                {{each data item i}}
                <div class="item-outer">
                    <div class="item-image"
                         style="background-image: url(&quot;{{item.photo[0]}}&quot;);" onclick="bind_to_product(this)" data-index="{{ i }}"></div>
                    <div class="item-info">
                        <div class="title-outer single-line">{{ item.name+' '+item.code }}</div>
                        <div class="info-outer">{{ item.spec_text }}</div>
                        <div class="action-data-outer">
                            <div class="action {{ if item.collected }}active{{ /if }}" id="fav_product_collocations_button{{i}}" onclick="bind_fav_product_collocations(this)" data-index="{{i}}"><span class="iconfont icon-shoucang2"></span>{{ item.count_fav }}</div>
                            <div class="action"><span class="iconfont icon-yinyongziyuan"></span>{{ item.count_album }}</div>
                            <div class="action"><span class="iconfont icon-mima-xianshi"></span>{{ item.count_visit }}</div>
                        </div>
                    </div>
                </div>
                {{ /each }}
            </div>
            <div class="sub-container-more" onclick="bind_more_product()">查看全部搭配产品 ></div>
            {{ /if }}
        </script>

        <!--问答-->
        <script id="product_qas_tpl" type="text/html">
            <div class="sub-container-title">全部问答({{ data.length }})</div>
            {{each data item i}}
            <div class="comment-outer">
                <div class="sub-container-avatar"
                     style="background-image: url(&quot;{{item.ask_avatar}}&quot;);"></div>
                <div class="comment-text-outer">
                    <div class="sub-container-nickname">{{ item.ask_name }}<span class="sub-container-time">{{ item.ask_time }}</span></div>
                    <div class="comment-praise"><span class="iconfont icon-dianzan2"></span>0</div>
                    <div class="sub-container-content">{{ item.question }}</div>
                    <div class="sub-container-content answer">{{ item.answer }}</div>
                </div>
            </div>
            {{ /each }}

            <div class="sub-container-more" onclick="more_qa()">查看全部评论 ></div>
        </script>

        <!--相关方案-->
        <script id="product_albums_tpl" type="text/html">
            {{ if data.length>0 }}
            <div class="sub-container-title">相关方案</div>
            <div class="album-container-outer">
                {{ each data item i }}
                    <div class="album-container">
                        <div class="album-container-image"
                             style="background-image: url(&quot;{{item.photo}}&quot;);" onclick="bind_to_album(this)" data-index="{{ i }}"></div>
                        <div class="album-container-avatar"
                             style="background-image: url(&quot;{{item.author_avatar}}&quot;);" onclick="bind_to_designer(this)" data-index="{{ i }}"></div>
                        <div class="album-container-title">{{ item.title }}</div>
                        <div class="tag-outer">
                            <span class="tag mj">{{ item.count_area }}㎡</span>
                            {{each item.style_arr arr arr_index}}
                                <span class="tag fg">{{ arr }}</span>
                                <span class="tag fg">{{ arr }}</span>
                            {{ /each }}

                        </div>
                        <div class="album-container-content">
                            <div class="album-container-content-span active"><span class="iconfont icon-mima-xianshi"></span>{{ item.count_visit }}</div>
                            <div class="album-container-content-span {{ if item.liked }}active{{/if}}" onclick="like_album(this)" data-index="{{i}}" ><span class="iconfont icon-dianzan2"></span>{{ item.count_praise }}</div>
                            <div class="album-container-content-span {{ if item.collected }}active{{/if}}" data-index="{{i}}" onclick="collect_album(this)"><span class="iconfont icon-shoucang2"></span>{{ item.count_fav }}</div>
                            <div class="album-container-content-span"><span class="iconfont icon-ic_huifu"></span>{{ item.count_comment }}</div>
                            <div class="album-container-content-span">by {{ item.author_name }}</div>
                        </div>
                    </div>
                {{ /each }}

                <div class="album-container-last"></div>
            </div>
            {{ /if }}
        </script>

    @endverbatim



@endsection

@section('script')
    <script>
        var product_info_api_url = "{{url('/mobile/product/get_product_info/'.request()->route('web_id_code'))}}?__bs=";
        var product_list_space_url = "{{ url('/mobile/product/get_list_product_spaces/'.request()->route('web_id_code')) }}"
        var product_list_accessories = "{{ url('/mobile/product/get_list_product_accessories/'.request()->route('web_id_code')) }}"
        var product_list_collocations = "{{ url('/mobile/product/get_list_product_collocations/'.request()->route('web_id_code')) }}"
        var product_list_albums = "{{ url('/mobile/product/get_list_product_albums/'.request()->route('web_id_code')) }}"
        var product_list_qas = "{{ url('/mobile/product/get_list_product_qas/'.request()->route('web_id_code')) }}"
        var product_send_qa = "{{ url('/mobile/product/api/send_product_qa/'.request()->route('web_id_code')) }}"
        var product_fav = "{{ url('/mobile/product/api/fav_product') }}"
        var disigner_fav = "{{ url('/mobile/designer/api/focus') }}"
        var like_album_url = "{{ url('/mobile/album/api/like')}}"
        var album_collect_api_url = "{{url('/mobile/album/api/collect')}}"

        //微信JSSDK初始化
        wx.config(<?php echo $jssdkConfig ?>);

    </script>


@endsection