@extends('v1.site.layout',[
   'css'=>[
       '/v1/static/iconfont/iconfont.css',
       '/v1/css/site/album/common.css',
       '/v1/css/site/album/detail.css'
   ],
   'js'=>[
       '/v1/js/site/center/common/common.js',
       '/v1/static/js/xlPaging.js'
   ]
])

@section('content')

    <div class="container">
        <div class="nav_lujin navigator-location" id="navigator-location-0">
            <span class="navtext1">首页 / 设计方案 / </span>
            <span class="navtext2"><span class="album-title"></span></span>
        </div>
        <div class="row">
            <div class="left">
                <div id="photo-cover"></div>


                <div id="view" style="background-color:#ffffff;padding:30px;margin-top:20px;position: relative;">

                    <div class="title2view navigator-location">
                        <span class="linea"></span>
                        <label class="title2">户型</label>
                    </div>
                    <div id="photo-layout"></div>

                    <div>
                        <div id="album-sections"></div>
                    </div>

                </div>

                <div class="product navigator-location" style="background-color:#ffffff;padding:30px 0;">
                    <div id="album-products"></div>

                </div>

                <div class="product navigator-location"
                     style="background-color:#ffffff;padding-top:30px;padding-bottom:40px;">
                    <label class="producttitle">相似方案</label>
                    <div id="produ1">
                        <div class="productcontainer" id="product1">
                            <div id="album-similiars"></div>
                        </div>
                    </div>
                    <div class="lookall" id="album_similiar_lookall" data-url="" onclick="click_more_similiar(this)">
                        更多相似方案 >
                    </div>
                </div>
                @if(!isset($is_preview) || !$is_preview)
                <div class="product navigator-location"
                     style="background-color:#ffffff;padding-top:30px;padding-bottom:30px;">
                    <div class="head">
                        <label class="producttitle">评论（<span class="comment-total">0</span>）</label>

                    </div>
                    <div id="fb_pinglun">
                        <div class="pinglunblock" placeholder="写下您的评论..." id="comment-input" contenteditable="true"
                             onkeyup="check_comment_content()"></div>
                        <div style="display:none;" id="comment-input-shadow"></div>

                        <div class="button2" type="submit" onclick="commit_comment()" id="btnButton"
                             style="background-color: rgb(217, 217, 217); cursor: not-allowed;">发表
                        </div>
                    </div>
                    <div id="pinglun">

                        <div id="comment-container">
                            <div id="album-comments"></div>

                        </div>
                    </div>
                    <div class="pinglunbottom" id="b-list-more-comment" onclick="list_more_comment()">
                        <label class="lfollowtext1">查看更多...</label>
                    </div>
                    <div class="pinglunbottom" id="lookall_close" style="display:none;" onclick="pinglun_lookall()">
                        <label class="lfollowtext1">收起 ></label>
                    </div>
                </div>
                    @endif
            </div>
            <div class="right">
                <div id="album-basic-info"></div>

                <div id="designer-profile"></div>

                <div class="readview" id="slideBar">
                    <div class="bar-container">
                        <span class="daohangtitle">导航栏</span>
                        <div class="sidebar">
                            <div class="branch"></div>
                            <ul id="sidenav">

                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <input type="hidden" id="i_product_page" value="1"/>
    <input type="hidden" id="i_comment_page" value="1"/>

    @verbatim

    <script id="navigator-slides-tpl" type="text/html">
        {{each datas item index}}
        {{if index != datas.length-1}}
        <li id="slideItem-{{index}}"><a href="#navigator-location-{{index}}"><span>{{item}}</span></a><i
                    class="dark"></i></li>
        {{else}}
        <li id="slideItem-{{index}}"><a href="#navigator-location-{{index}}"><span>{{item}}（<font class="comment-total">0</font>）</span></a><i
                    class="dark"></i></li>

        {{/if}}
        {{/each}}
    </script>

    <script id="album-comments-tpl" type="text/html">
        {{each datas.data comment comment_index}}
        <div class="pinglunview" data-id="{{comment.id}}">
            <div class="lpersonimage" id="lpersonimage_0"
                 style="background-image: url(&quot;{{comment.author_avatar}}&quot;);"></div>
            <div class="pinglunrview">
                <div class="pinglunrhead">
                    <label class="lpertext">{{comment.author}}</label>
                    <label class="ltimetext">{{comment.publish_time}}</label>
                </div>
                <div class="pinglunmhead">
                    <label class="lcontenttext">{{@ comment.content}}</label>
                </div>
                <div class="pinglunthead" onclick="click_follow(this)">
                    {{if comment.followable}}
                    <label class="lfollowtext1">@跟帖</label>
                    {{/if}}
                </div>
            </div>
        </div>
        <div class="linew"></div>
        {{/each}}
    </script>

    <script id="album-similiars-tpl" type="text/html">
        {{each datas album album_index}}
        <div class="productview">
            <div class="imageview">
                <div class="areaview1"></div>
                <div class="areaview1t">
                    <label class="positext1">{{album.count_area}}㎡</label>
                </div>
                <div class="pimage" onclick="window.open('/album/s/{{album.web_id_code}}')" id="pimage_0"
                     style="background-image: url(&quot;{{album.photo_cover}}&quot;);"></div>
                <div class="nametext">{{album.title}}</div>
                <div class="deview">
                    <div class="perimage" id="perimage_0"
                         style="background-image: url(&quot;{{album.designerAvatar}}&quot;);"></div>
                    <label class="pertext">{{album.designerNickname}}</label>
                    <div class="lookview">
                        <img src="/v1/images/site/sjfa_xq/album-similiar-visit.png" class="xiconimage">
                        <label class="viewtext">{{album.count_visit}}</label>
                    </div>
                </div>
            </div>
        </div>
        {{/each}}
    </script>

    <script id="album-products-tpl" type="text/html">
        <label class="producttitle">产品清单（{{datas.total}}）</label>
        <div id="product_swiper">
            <div class="m_swiper">
                <span id="p_swiper1" class="m_swiper_span">瓷砖</span>
                <!--<span id="p_swiper0" class="m_swiper_span1" onclick="change_product_type(0)">不限</span>
                <span id="p_swiper2" class="m_swiper_span" onclick="change_product_type(2)">卫浴</span>-->
            </div>
        </div>
        <div id="produ">
            <div class="productcontainer" id="product">
                {{each datas.data product product_index}}
                <div class="productview">
                    <div class="imageview">
                        <div class="positionview" style="width:78px;"></div>
                        <div class="positionviewt" style="width:78px;">
                            <img src="/v1/images/site/sjfa_xq/产品清单-定位icon.png" class="posiimage">
                            <label class="positext">{{product.sales_area}}</label>
                        </div>
                        <div class="positionview1"></div>
                        <div class="positionview1t">
                            <img src="/v1/images/site/sjfa_xq/产品清单-收藏icon.png" class="posiimage1">
                            <label class="positext1">{{product.count_fav}}</label>
                        </div>
                        <div class="pimage" onclick="click_product('{{product.web_id_code}}')" id="primage_0"
                             style="background-image: url(&quot;{{product.cover}}&quot;);"></div>
                        <div class="nametext">{{product.name}}</div>
                        <div class="companyview">
                            <label title="{{product.sales_name}}" class="companytext">{{product.sales_name}}</label>
                            <label title="{{product.sales_price}}" class="pricetext">{{product.sales_price}}</label>
                        </div>
                    </div>
                </div>
                {{/each}}
            </div>
        </div>

        <div id="pager" style="margin-left:-10px;text-align:center;"></div>

    </script>

    <script id="designer-profile-tpl" type="text/html">
        <div class="personview">
            <div class="personimage" onclick="go_designer_detail('{{designer.web_id_code}}')" style="background-image: url('{{designer.url_avatar}}');"></div>
            <span class="personname">{{designer.nickname}}</span>
            <span class="company">{{if designer.organization}} {{designer.organization}} {{/if}}</span>
            <div class="personblock">
                <div class="designerview">
                    <span class="desinernumber">{{designer.count_upload_album}}</span>
                    <span class="desiner">设计方案</span>
                </div>
                <div class="designerview1">
                    <span class="desinernumber fans-count">{{designer.fans}}</span>
                    <span class="desiner">粉丝</span>
                </div>
            </div>

            <div class="guanzhu" onclick="guanzhu()" {{if designer.focused==true}}style="display:none" {{/if}}>+
                <span class="guanzhuT">关注</span>
            </div>

            <div class="guanzhu1" onclick="guanzhu()" {{if designer.focused==false}}style="display:none" {{/if}}>
                <span class="guanzhuT">已关注</span>
            </div>

        </div>
    </script>

    <script id="photo-cover-tpl" type="text/html">
        <div class="image1" style="background-image:url('{{album.photo_cover}}')"></div>
    </script>

    <script id="photo-layout-tpl" type="text/html">

        <div class="image2view">
            {{each album.photo_layout_data image i}}
            <a href="{{image}}">
                <div class="image2" id="image_0" style="background-image: url('{{image}}');"></div>
            </a>
            {{/each}}
        </div>
        <label class="detailsd" id="detailsd0">{{album.description_layout}}</label>
    </script>

    <script id="album-basic-info-tpl" type="text/html">
        <div class="right1">
            <h1 class="title1">{{album.title}}</h1>
            <div class="detailsview" >
                {{if album.code}}<span class="details1">方案编号：{{album.code}}</span>{{/if}}
                {{if album.verify_time}}<span class="details1">上线时间：{{album.verify_time}}</span>{{/if}}
                {{if album.city_text}}<span class="details1">所在城市：{{album.city_text}}</span>{{/if}}
                {{if album.address_street}}<span class="details1">街道：{{album.address_street}}</span>{{/if}}
                {{if album.address_residential_quarter}}<span
                        class="details1">小区：{{album.address_residential_quarter}}</span>{{/if}}
                {{if album.address_building}}<span class="details1">所在楼栋：{{album.address_building}}</span>{{/if}}
                {{if album.address_layout_number}}<span
                        class="details1">所在户型号：{{album.address_layout_number}}</span>{{/if}}
                {{if album.house_type_text}}<span class="details1">户型：{{album.house_type_text}}</span>{{/if}}
                {{if album.count_area}}<span class="details1">面积：{{album.count_area}}㎡</span>{{/if}}
                {{if album.style_text}}<span class="details1">风格：{{album.style_text}}</span>{{/if}}
            </div>
            {{if album.description_layout}}
            <div class="details1view" >设计说明：{{album.description_design}}</div>{{/if}}
            <div class="likeview">
                <div class="likeview1">
                    <div class="block">
                        <span class="iconfont icon-dianzan"
                              style="color:{{if album.liked}}#1582FF {{else}}#777777 {{/if}}" id="dianzan"
                              onclick="like()"></span>
                        <span class="blocktext" id="dianzannumber">{{album.count_praise}}</span>
                    </div>
                    <div class="block">
                        <span class="iconfont icon-buoumaotubiao44" onclick="collect()"
                              style="color:{{if album.collected}}#1582FF {{else}}#777777 {{/if}}" id="shoucang"></span>
                        <span class="blocktext" id="shoucangnumber">{{album.count_fav}}</span>
                    </div>
                    <div class="block">
                        <span class="iconfont icon-fuzhi" id="fuzhi"></span>
                        <span class="blocktext" id="fuzhinumber">{{album.count_use}}</span>
                    </div>
                    <div class="block">
                        <span class="iconfont icon-liulan" id="liulan"></span>
                        <span class="blocktext" id="liulannumber">{{album.count_visit}}</span>
                    </div>
                    <div class="block share-hover-btn"  >
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

    <script id="album-sections-tpl" type="text/html">


        {{each datas section section_index}}
        <div class="album-section-block navigator-location">
            <div class="title2view">
                <span class="linea"></span>
                <label class="title2">{{section.space_type_text}}</label>
            </div>
            <div class="productnav">
                {{if section.content.design.photos.length>0}}
                <div id="d_swiper_{{section_index}}_0" class="section-tab-title d_swipwer_span1"
                     onclick="change_section_tab({{section_index}},0)"
                     style="width:104px;">空间设计
                </div>
                {{/if}}
                {{if section.content.product.photos.length>0}}
                <div id="d_swiper_{{section_index}}_1" class="section-tab-title d_swipwer_span"
                     onclick="change_section_tab({{section_index}},1)"
                     style="width:104px;">产品应用
                </div>
                {{/if}}
                {{if section.content.build.photos.length>0 }}
                <div id="d_swiper_{{section_index}}_2" class="section-tab-title d_swipwer_span"
                     onclick="change_section_tab({{section_index}},2)" style="width:72px;">
                    施工
                </div>
                {{/if}}
            </div>
            <div class="section-tab-content section-design" style="display:block">
                <div class="image2view">
                    <a target="_blank" href="{{section.content.design.photos[0]}}">
                        {{if section.content.design.photos.length>0}}
                        <div class="image2" id="image_{{section_index}}" src="{{section.content.design.photos[0]}}"
                             style="background-image: url('{{section.content.design.photos[0]}}');"></div>
                        {{else}}
                        <div class="no-pic-tips">暂无空间设计图~</div>
                        {{/if}}
                    </a>
                    <div class="detailsimageview" id="detailimage_1">
                        {{each section.content.design.photos image_url image_index}}
                        <div class="img-thumb {{if image_index==0}}image4{{else}}image3{{/if}}"
                             id="image_{{section_index}}_{{image_index}}"
                             onmouseover="changepicture({{section_index}},'design',{{image_index}})"
                             data-src="{{image_url}}" style="background-image: url('{{image_url}}');"></div>

                        {{/each}}
                    </div>
                    <div class="labelview">
                        <div class="labelblock" style="width:60px">{{section.count_area}}㎡</div>
                        <div class="labelblock">{{section.style_text}}</div>
                    </div>
                </div>
                <label class="detailsd" id="detailsd1">{{section.content.design.description}}</label>
            </div>
            <div class="section-tab-content section-product">
                <div class="image2view">
                    <a target="_blank" href="{{section.content.product.photos[0]}}">
                        {{if section.content.product.photos.length>0}}
                        <div class="image2" id="image_{{section_index}}" src="{{section.content.product.photos[0]}}"
                             style="background-image: url('{{section.content.product.photos[0]}}');"></div>
                        {{else}}
                        <div class="no-pic-tips">暂无产品应用图~</div>
                        {{/if}}
                    </a>
                    <div class="detailsimageview" id="detailimage_1">
                        {{each section.content.product.photos image_url image_index}}
                        <div class="img-thumb {{if image_index==0}}image4{{else}}image3{{/if}}"
                             id="image_{{section_index}}_{{image_index}}"
                             onmouseover="changepicture({{section_index}},'product',{{image_index}})"
                             data-src="{{image_url}}" style="background-image: url('{{image_url}}');"></div>

                        {{/each}}
                    </div>
                    <div class="labelview">
                        {{if section.count_area}}
                        <div class="labelblock" style="width:60px">{{section.count_area}}㎡</div>
                        {{/if}}
                        {{if section.style_text}}
                        <div class="labelblock">{{section.style_text}}</div>
                        {{/if}}
                    </div>
                </div>
                <label class="detailsd" id="detailsd1">{{section.content.product.description}}</label>
            </div>
            <div class="section-tab-content  section-build">
                <div class="image2view">
                    <a target="_blank" href="{{section.content.build.photos[0]}}">
                        {{if section.content.build.photos.length>0}}
                        <div class="image2" id="image_{{section_index}}" src="{{section.content.build.photos[0]}}"
                             style="background-image: url('{{section.content.build.photos[0]}}');"></div>
                        {{else}}
                        <div class="no-pic-tips">暂无施工图~</div>
                        {{/if}}
                    </a>
                    <div class="detailsimageview" id="detailimage_1">
                        {{each section.content.build.photos image_url image_index}}
                        <div class="img-thumb {{if image_index==0}}image4{{else}}image3{{/if}}"
                             id="image_{{section_index}}_{{image_index}}"
                             onmouseover="changepicture({{section_index}},'build',{{image_index}})"
                             data-src="{{image_url}}" style="background-image: url('{{image_url}}');"></div>

                        {{/each}}
                    </div>
                    <div class="labelview">
                        <div class="labelblock" style="width:60px">{{section.count_area}}㎡</div>
                        <div class="labelblock">{{section.style_text}}</div>
                    </div>
                </div>
                <label class="detailsd" id="detailsd1">{{section.content.build.description}}</label>
            </div>

        </div>

        {{/each}}


    </script>

    <script id="list-empty-tpl" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

    @endverbatim

@endsection

@section('script')
    <script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

    <script>

        var album_info_api_url = "{{url('/album/api/get_album_info/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_section_api_url = "{{url('/album/api/list_album_sections/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_product_api_url = "{{url('/album/api/list_album_products/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_similiar_api_url = "{{url('/album/api/list_album_similiars/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var commit_comment_api_url = "{{url('/album/api/commit_comment/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_comments_api_url = "{{url('/album/api/list_album_comments/'.request()->route('id'))}}?__bs=" + __cache_brand;
        var album_collect_api_url = "{{url('/album/api/collect')}}?__bs=" + __cache_brand;
        var album_like_api_url = "{{url('/album/api/like')}}?__bs=" + __cache_brand;
        var designer_focus_api_url = "{{url('/designer/api/focus')}}?__bs=" + __cache_brand;
        var current_url = "{{url()}}";


    </script>

    <script src="{{asset('/v1/js/site/album/detail.js')}}"></script>

@endsection