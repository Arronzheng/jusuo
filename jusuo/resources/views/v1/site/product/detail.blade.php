@extends('v1.site.layout',[
   'css'=>[
       '/v1/static/iconfont/iconfont.css',
       '/v1/css/site/common/pager.css',
       '/v1/css/site/product/detail.css'
   ],
   'js'=>[
       '/v1/static/js/xlPaging.js'
   ]
])

@section('content')

    <div class="product-detail-container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 产品库 / <span id="product-category"></span> /</span>
            <span class="navtext2"> <span class="product-title"></span></span>
        </div>
        <div class="wholecontainer">
            <div class="leftcontainer">
                <div class="p_detail" id="p_detail">

                </div>
                <div class="p_product" id="navigator-location-0">
                    <div id="product-kinds-container">

                    </div>

                    <div class="look_similar" onclick="get_more_kind()">所有产品 &gt;</div>

                </div>
                <div class="alldetail" id="navigator-location-1">

                    <div id="detail-table">

                    </div>

                    <div id="product-accessories-container">

                    </div>

                    <div id="product-collocations-container">

                    </div>

                    <div id="product-spaces-container">

                    </div>

                    <div id="product-videos-container">

                    </div>

                </div>

                <div class="fangan" id="navigator-location-2">
                    <label class="producttitle">相关方案</label>
                    {{--风格导航--}}
                    <div id="product_swiper"></div>
                    <div class="productcontainer2" id="product-albums-container">


                    </div>
                    <div class="lookall" id="product_lookall" onclick="get_more_album()">更多相关方案 &gt;</div>
                </div>
                @if(!isset($is_preview) || !$is_preview)
                <div class="product" style="background-color:#ffffff;padding-top:31px;padding-bottom:40px;" id="navigator-location-3">
                    <div class="headt">
                        <label class="producttitle" >问答（<span class="qa-total"></span>）</label>
                    </div>
                    <div id="fb_pinglun">
                        <div class="pinglunblock" id="qa-input" contenteditable="true" onkeyup="check_qa_content()"></div>
                        <div class="pl_placeholder">写下您的问题…</div>
                        <div class="button2" type="submit" onclick="commit_qa()" id="btnButton">提问</div>
                    </div>
                    <div id="pinglun" style="margin-top:10px;">
                        <div id="product-qas-container"></div>

                    </div>
                    <div class="pinglunbottom" id="b-list-more-qa" style="display:none;" onclick="list_more_qa()">
                        <label class="lfollowtext1">查看更多...</label>
                    </div>

                </div>
                    @endif
            </div>
            <div class="rightcontainer">
                <div class="company" id="company">

                </div>
                <div class="s_product" id="product-similiars-container">

                </div>
                <div class="readview" id="slideBar">
                    <div class="bar-container">
                        <span class="daohangtitle">导航栏</span>
                        <div class="sidebar">
                            <div class="branch" ></div>
                            <ul id="sidenav">
                                <li id="slideItem-0">
                                    <a href="#navigator-location-0" >
                                        <span>同类产品</span>
                                    </a>
                                    <i class="dark" ></i>
                                </li>
                                <li id="slideItem-1">
                                    <a href="#navigator-location-1" >
                                        <span>详情介绍</span>
                                    </a>
                                    <i class="dark" ></i>
                                </li>
                                <li id="slideItem-2">
                                    <a href="#navigator-location-2" >
                                        <span>相关方案</span>
                                    </a>
                                    <i class="dark" ></i>
                                </li>
                                <li id="slideItem-3">
                                    <a href="#navigator-location-3" >
                                        <span>问答（<font class="qa-total">0</font>）</span>
                                    </a>
                                    <i class="dark" ></i>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <input type="hidden" id="i_product_page" value="1"/>
    <input type="hidden" id="i_qa_page" value="1"/>

    @verbatim

    <script id="product-qas-tpl" type="text/html">
        {{each datas qa i}}
            {{if qa.question}}
            <div class="pinglunview1">
                <div class="askcontainer">
                    <div class="ask_left">问</div>
                    <div class="ask_right">
                        <div class="ask_person">
                            <div class="ask_image" id="ask_image0"
                                 style="background-image: url(&quot;{{qa.ask_avatar}}&quot;);"></div>
                            <div class="ask_name">{{qa.ask_name}}</div>
                            <div class="ask_time">于{{qa.ask_time}}&nbsp;提问</div>
                        </div>
                        <div class="question">{{qa.question}}</div>
                    </div>
                </div>
                {{if qa.answer}}
                <div class="askcontainer1">
                    <div class="ask_left1">答</div>
                    <div class="ask_right">
                        <div class="ask_person">
                            <div class="ask_image" id="answer_image0"
                                 style="display:none;background-image: url(&quot;/v1/images/site/index/c1.png&quot;);"></div>
                            <div class="ask_name">{{qa.answer_name}}</div>
                            <div class="ask_time">于{{qa.answer_time}}&nbsp;回答</div>
                        </div>
                        <div class="question">{{qa.answer}}</div>
                    </div>
                </div>
                {{/if}}
            </div>
            {{/if}}
        {{/each}}

    </script>


    <script id="product-albums-tpl" type="text/html">
        {{each datas album i }}
        <div class="g_productview" onclick="click_album('{{album.web_id_code}}')">
            <div class="g_imageview">
                <div class="g_areaview1"></div>
                <div class="g_areaview1t">
                    <label class="g_positext1">{{album.count_area}}㎡</label>
                </div>
                <div class="g_pimage" id="pimage_{{i}}" style="background-image: url(&quot;{{album.photo}}&quot;);"></div>
                <div class="g_nametext">{{album.title}}</div>
                <div class="g_deview">
                    <div class="g_perimage" id="perimage_{{i}}" style="background-image: url(&quot;{{album.author_avatar}}&quot;);"></div>
                    <label class="g_pertext">{{album.author_name}}</label>
                    <div class="g_lookview">
                        <img src="/v1/images/site/sjfa_xq/album-similiar-visit.png" class="g_xiconimage">
                        <label class="g_viewtext">{{album.count_visit}}</label>
                    </div>
                </div>
            </div>
        </div>
        {{/each}}

    </script>

    <script id="styles-tpl" type="text/html">

        <div class="m_swiper">
            <span id="p_swiper0" class="m_swiper_span1" onclick="change_pswiper(0,0)">不限</span>
            {{each datas style i}}
            <span id="p_swiper{{i+1}}" class="m_swiper_span" onclick="change_pswiper({{i+1}},'{{style.id}}')">{{style.name}}</span>
            {{/each}}
        </div>

    </script>

    <script id="product-kinds-tpl" type="text/html">
        <span class="protitle">同类产品</span><span style="color:#333;font-size:16px;margin-left:10px;">{{series.name}}</span>
        <div style="color:#333;font-size:14px;line-height:20px;margin:10px 20px 10px 0">{{series.description}}</div>
        <div class="productcontainer" id="product">
            {{each datas product i}}
            <div class="productview">
                <div class="pimage" id="primage_{{i}}" onclick="go_product_detail('{{product.web_id_code}}')" style="background-image: url(&quot;{{product.photo_cover}}&quot;);"></div>
                <div class="productname">{{product.name}}</div>
                <div class="productcode">{{product.code}}</div>
                <div class="priceview">
                    <span class="pricetxt">{{product.sales_price}}</span>
                    <div class="lookview">
                        <span class="iconfont {{if product.collected}}icon-buoumaotubiao44{{else}}icon-shoucang2{{/if}}" id="collected_{{i}}" style="color:{{if product.collected}}#1582FF{{else}}#B7B7B7{{/if}};" onclick="collected({{i}})"></span>
                        <span class="looknumber" id="collectednumber_{{i}}" style="color:{{if product.collected}}#1582FF{{else}}#B7B7B7{{/if}};" onclick="collected({{i}})">{{product.count_fav}}</span>
                    </div>
                </div>
                <div class="details">
                    <span class="companytext">{{product.sales_name}}</span>
                    <div class="lookview">
                        <div class="dingwei"></div>
                        <span class="areatxt">{{product.sales_area}}</span>
                    </div>
                </div>
            </div>
            {{/each}}
        </div>


    </script>

    <script id="product-similiars-tpl" type="text/html">
        {{if datas.length>0}}
        <div class="s_head">
            <span class="protitle">相似产品</span>
            <span class="s_lookall" onclick="get_more_similiar()">所有产品</span>
        </div>
        <div class="productcontainer1" id="sproduct">
            {{each datas product i}}
            <div class="productview1">
                <div class="pimage1" id="primage1_{{i}}" onclick="go_product_detail('{{product.web_id_code}}')" style="background-image: url(&quot;{{product.photo_cover}}&quot;);"></div>
                <div class="productname1">{{product.name}}</div>
                <div class="productcode1">{{product.code}}</div>
                <div class="priceview1"><span class="pricetxt1">{{product.sales_price}}</span>
                    <div class="lookview1">
                        <span class="iconfont {{if product.collected}}icon-buoumaotubiao44{{else}} icon-shoucang2{{/if}}" id="collected1_{{i}}" style="color:{{if product.collected}}#1582FF{{else}}#B7B7B7{{/if}};font-size:12px;" onclick="collected1({{i}})"></span>
                        <span class="looknumber1" id="collectednumber1_{{i}}" style="color:{{if product.collected}}#1582FF{{else}}#B7B7B7{{/if}}" onclick="collected1({{i}})">{{product.count_fav}}</span>
                    </div>
                </div>
            </div>
            {{/each}}
        </div>
        {{/if}}

    </script>


    <script id="product-videos-tpl" type="text/html">
        {{if product.photo_video.length>0}}
            <div class="title3view">
                <span class="linea"></span>
                <label class="title2">产品视频</label>
            </div>
            <!--<div class="postor" style="background-image: url(&quot;images/cpk_xq/c1.png&quot;);">
                <div class="play" onclick="playvideo()"></div>
            </div>-->
            {{each product.photo_video video i}}
            <video width="710" controls="controls"
                   src="{{video}}"
                   style="margin-top:20px;max-height: 532px;" id="video">您的浏览器不支持视频播放
            </video>
            {{/each}}

            <!--<video width="710" controls="controls"
                   src="https://cms.cnc.blzstatic.cn/cms/gallery/4G8KGRWWG7FS1557732437703.mp4"
                   style="margin-top:20px;max-height: 532px;" id="video">您的浏览器不支持视频播放
            </video>-->

        {{/if}}

    </script>

    <script id="product-spaces-tpl" type="text/html">
        {{if datas.length>0}}
            <div class="title3view">
                <span class="linea"></span>
                <label class="title2">空间应用</label>
            </div>
            {{each datas space i }}
            <a target="_blank" href="{{space.photo}}">
                <img class="placeimage" id="placeimage{{i}}" src="{{space.photo}}"/>
            </a>
            <div class="placetitle">{{space.title}}</div>
            <div class="placetext">空间说明：{{space.note}}</div>
            {{/each}}

        {{/if}}

    </script>

    <script id="product-collocations-tpl" type="text/html">
        {{if datas.length>0}}
            <div class="title3view">
                <span class="linea"></span>
                <label class="title2">产品搭配</label>
            </div>
            <div class="peijiancontainer1">
                {{each datas collocation i}}
                <div class="peijianview1">
                    <div class="matchimage" onclick="go_product_detail('{{collocation.web_id_code}}')" id="matchimage{{i}}" style="background-image: url(&quot;{{if collocation.photo}}{{collocation.photo[0]}}{{/if}}&quot;);"></div>
                    <div class="matchname">{{collocation.name}}</div>
                    {{if collocation.technology_categories_text}}
                    <div class="peijianid2">
                        <span class="pj_span">工艺类别</span>
                        <span class="dp_span1">{{collocation.technology_categories_text}}</span>
                    </div>
                    {{/if}}
                    {{if collocation.spec_text}}
                    <div class="peijianid2">
                        <span class="pj_span">产品规格</span>
                        <span class="dp_span1">{{collocation.spec_text}}</span>
                    </div>
                    {{/if}}
                    {{if collocation.note}}
                    <div class="peijianid2">
                        <div class="pj_span">应用说明</div>
                        <div class="pj_span2">{{collocation.note}}</div>
                    </div>
                    {{/if}}
                </div>
                {{/each}}

            </div>
        {{/if}}
    </script>

    <script id="product-accessories-tpl" type="text/html">
        {{if datas.length>0}}
            <div class="title3view">
                <span class="linea"></span>
                <label class="title2">配件</label>
            </div>
            <div class="peijiancontainer">
                {{each datas accessory i}}
                <div class="peijianview">
                    <div class="peijianimage" id="peijianimage{{i}}"
                         style="background-image: url(&quot;{{accessory.photo[0]}}&quot;);"></div>
                    <div class="peijianid">
                        <span class="pj_span">配件编号</span>
                        <span class="pj_span1">{{accessory.code}}</span>
                    </div>
                    <div class="peijianid1">
                        <span class="pj_span">加工工艺</span>
                        <span class="pj_span1">{{accessory.technology}}</span>
                    </div>
                    <div class="peijianid1">
                        <span class="pj_span">配件规格</span>
                        <span class="pj_span1">{{accessory.spec_text}}</span>
                    </div>
                </div>
                {{/each}}

            </div>
        {{/if}}
    </script>

    <script id="detail-table-tpl" type="text/html">

        <div class="title2view">
            <span class="linea"></span>
            <label class="title2">详细介绍</label>
        </div>
        <div class="detail-intro">
            <table border="0">
                {{ if product.key_technology!= ''}}
                <tr>
                    <td class="intro-title">核心工艺</td>
                    <td class="intro-content" colspan="3">
                        {{product.key_technology}}
                    </td>
                </tr>
                {{ /if }}
                {{ if product.physical_chemical_property.length>0 }}
                <tr>
                    <td class="intro-title">理化性能</td>
                    <td class="intro-content" colspan="3">
                        {{each product.physical_chemical_property property i}}
                        <span class="block3title">{{property}}</span>
                        {{/each}}
                    </td>
                </tr>
                {{ /if }}
                {{ if product.function_feature.length>0 }}
                <tr>
                    <td class="intro-title">功能特征</td>
                    <td class="intro-content" colspan="3">
                        {{each product.function_feature property i}}
                        <span class="block3title">{{property}}</span>
                        {{/each}}
                    </td>
                </tr>
                {{ /if }}
                {{ if product.customer_value!='' }}
                <tr>
                    <td class="intro-content" colspan="3">
                        {{product.customer_value}}
                    </td>
                </tr>
                {{ /if }}
            </table>
        </div>

        <!--{if product.photo_product.length>0}}
            <div class="title3view">
                <span class="linea"></span>
                <label class="title2">产品图</label>
            </div>
            {each product.photo_product photo i}}
            <a target="_blank" href="{photo}}">
                <img class="proimage" id="proimage{i}}" width="100%" src="{photo}}"/>

            </a>
            {/each}}
        {/if}}-->
        {{if product.photo_practicality.length>0}}
            <div class="title3view">
                <span class="linea"></span>
                <label class="title2">实物图</label>
            </div>
            {{each product.photo_practicality photo i}}
            <a target="_blank" href="{{photo}}">
                <img class="proimage" id="enimage{{i}}" width="100%" src="{{photo}}"/>
            </a>
            {{/each}}
        {{/if}}
    </script>


    <script id="top-basic-tpl" type="text/html">
        <div class="p_imagecontainer">
            <a target="_blank" href="{{product.photo_product[0]}}" id="p_imagehref">
                <div class="p_image" id="p_image"
                     style="background-image: url(&quot;{{product.photo_product[0]}}&quot;);"></div>
            </a>
            <div class="p_images">
                {{each product.photo_product photo i}}
                    {{if i<=5}}
                    <a href="javascript:;">
                        <div class="p_img" id="p_img{{i}}" onmouseover="changepicture({{i}})"
                             style="background-image: url(&quot;{{photo}}&quot;);"></div>
                    </a>
                    {{/if}}
                {{/each}}
            </div>
        </div>
        <div class="p_detailtext">
            <div class="p_name">{{product.name}}</div>
            <div class="priceviewq">
                {{if product.sales_price}}
                <span class="price">{{product.sales_price}}</span>
                {{/if}}
                <div class="lianxi" onclick="current_product_collect()">
                    <span class="iconfont icon-buoumaotubiao44" style="color:#FFFFFF;font-size:14px;"></span>
                    <span class="lianxi_txt">收藏产品</span>
                </div>
            </div>
            <div class="pro_details">
                <div class="prod_con">
                    <div class="pro_label">产品编号</div>
                    <div class="pro_txt">{{product.code}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">品类</div>
                    <div class="pro_txt">{{product.product_category}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">品牌</div>
                    <div class="pro_txt">{{product.brand_name}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">规格</div>
                    <div class="pro_txt">{{product.spec_text}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">色系</div>
                    <div class="pro_txt">{{product.colors_text}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">所属系列</div>
                    <div class="pro_txt">{{product.series_text}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">应用类别</div>
                    <div class="pro_txt">{{product.apply_categories_text}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">工艺类别</div>
                    <div class="pro_txt">{{product.technology_categories_text}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">表面特征</div>
                    <div class="pro_txt">{{product.surface_features_text}}</div>
                </div>
                <div class="prod_con">
                    <div class="pro_label">空间风格</div>
                    <div class="pro_txt">{{product.styles_text}}</div>
                </div>
            </div>
            <div class="likeview">
                <div class="likeview1">
                    <div class="block">
                        <span class="iconfont icon-liulan" id="liuan" style="color:#777777;"></span>
                        <span class="blocktext" id="liulannumber">{{product.count_visit}}</span>
                    </div>
                    <div class="block">
                        <div class="guanlian" id="guanlian"></div>
                        <span class="blocktext" id="guanlianshu">{{product.count_album}}</span>
                    </div>
                    <div class="block">
                        <span class="iconfont icon-buoumaotubiao44" id="shoucang" style="color:{{if product.collected}}#1582FF{{else}}#777777{{/if}};"
                              onclick="current_product_collect()"></span>
                        <span class="blocktext" id="shoucangnumber" onclick="current_product_collect()"
                              style="color:{{if product.collected}}#1582FF{{else}}#777777{{/if}};">{{product.count_fav}}</span>
                    </div>
                    <div class="block">
                        <div class="hot" id="hot"></div>
                        <span class="blocktext" id="hotnumber">{{product.point_focus}}</span>
                    </div>
                    <div class="block share-hover-btn" >
                        <span class="iconfont icon-share-fill" style="color:#777777;"></span>
                        <span class="blocktext">分享</span>
                        <div id="share-outer" class="">
                            <div class="angle"></div>
                            <div class="weixin-box">
                                <div id="qrcodeCanvas" style="display:inline-block"></div>
                                <p style="top:-30px">打开微信“扫一扫”，将本页分享到朋友圈</p>
                            </div>
                            <div class="angle-1"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </script>

    <script id="sales-detail-tpl" type="text/html">
        <div onclick="go_seller_detail('{{product.sales_url}}')">
            <div class="c_image" id="c_image" style="background-image: url(&quot;{{product.sales_avatar}}&quot;);"></div>
            <div class="c_name">{{product.sales_name}}</div>
            <div class="c_tele">
                <div class="tele_icon"></div>
                <span class="tele">{{product.sales_phone}}</span>
            </div>
            <div class="c_addr">
                <div class="addr_icon"></div>
                <span class="addr">{{product.sales_address}}</span>
            </div>
            <div class="c_button">商家详情</div>
        </div>

    </script>


    <script id="list-empty-tpl" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

    @endverbatim

@endsection

@section('script')
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>


    <script>



        var product_info_api_url = "{{url('/product/api/get_product_info/'.request()->route('id'))}}";
        var product_accessories_api_url = "{{url('/product/api/list_product_accessories/'.request()->route('id'))}}";
        var product_collocations_api_url = "{{url('/product/api/list_product_collocations/'.request()->route('id'))}}";
        var product_spaces_api_url = "{{url('/product/api/list_product_spaces/'.request()->route('id'))}}";
        var product_similiars_api_url = "{{url('/product/api/list_product_similiars/'.request()->route('id'))}}";
        var product_kinds_api_url = "{{url('/product/api/list_product_kinds/'.request()->route('id'))}}";
        var product_albums_api_url = "{{url('/product/api/list_product_albums/'.request()->route('id'))}}";
        var product_qas_api_url = "{{url('/product/api/list_product_qas/'.request()->route('id'))}}";
        var commit_qa_api_url = "{{url('/product/api/commit_qa/'.request()->route('id'))}}";
        var current_url = "{{url()}}";
        var list_styles_api_prefix = "{{url('/product/api/list_styles/'.request()->route('id'))}}";
        var product_collect_api_url = "{{url('/product/api/collect')}}";




    </script>

    <script src="{{asset('/v1/js/site/product/detail.js')}}"></script>

@endsection