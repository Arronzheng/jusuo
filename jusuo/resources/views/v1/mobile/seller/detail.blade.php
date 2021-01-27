@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/common.css?v='.str_random(random_int(20,30)),
       '/v1/css/mobile/seller.css?v='.str_random(random_int(20,30)),
       '/v1/css/mobile/products.css?v='.str_random(random_int(20,30)),
   ],
   'js'=>[
       '/v1/js/ajax.js',
       '/v1/js/mobile/util.js',
       '/v1/js/mobile/seller.js?v='.str_random(random_int(20,30))
   ]
])

@section('content')
    <style>
        .sub-container{background-color: inherit;padding:0;height:auto;overflow: inherit;}
        /*.sub-container .item-outer:last-child{margin-bottom:10px;}*/
    </style>



    <div class="container">

        <div class="top-bg brand-image">

        </div>

        <div class="avatar">
            <img class="brand-logo"
                 src=""/>
        </div>

        <div id="seller-profile"></div>
        <div class="splitter"></div>

        <div class="sub-container" id="designer-list">
            <div class="sub-container-title">设计团队(<span id="designer-count"></span>)</div>
            <div class="designer-container no-scroll-bar">
                <div class="list-container">

                </div>
            </div>
        </div>

        <div class="splitter"></div>

        <div class="sub-container" id="album-list">
            <div class="sub-container-title">设计方案{{--(<span id="album-count"></span>)--}}</div>
            <div class="album-container-outer">


                {{--<div class="album-container">
                    <div class="album-container-image"
                         style="background-image:url(http://pf.fsqmh.com/storage/images/design/21/20/02/GB/0BLnuRSg7UVfz1QNNa29SEMpvkgPuFnyUqPXR1Rl.jpeg)"></div>
                    <div class="album-container-avatar"
                         style="background-image:url(http://pf.fsqmh.com/storage/images/avatar/designer/20/02/F3/6jJg2qY31XAQUvsSwfUi1gJ2BERy0uuFsUDtASfg.jpeg)"></div>
                    <div class="album-container-title">国宾一号｜最初自由 的美式</div>
                    <div class="album-container-tag-outer">
                        <span class="tag mj">270㎡</span>
                        <span class="tag fg">美式</span>
                        <span class="tag fg">混搭风</span>
                    </div>
                    <div class="album-container-content">
                        客厅空间墙面以干练利落的形式，兼容柔和材质与中性色彩的软装配饰，软装与硬装两者间的立与破、融合与碰撞，造就了空间上的慵懒与理性之风，给人立体上的延伸感。U型的沙发包围区，两面靠窗，充足的采光，让空间尽显大气。
                    </div>
                </div>--}}
            </div>
            <a class="sub-container-more" style="display: block;" href="{{url('/mobile/album/list_albums?dlr='.request()->route('web_id_code'))}}">查看全部方案 ></a>

        </div>

        <div class="splitter"></div>

        <div class="sub-container" id="product-list">
            <div class="sub-container-title">热销产品{{--(<span id="product-count"></span>)--}}</div>
            <div class="item-list">

            </div>
            <a class="sub-container-more" style="display: block;" href="{{url('/mobile/product/list_products?dlr='.request()->route('web_id_code'))}}">查看全部产品 ></a>
        </div>

        <div class="splitter"></div>

        <div class="sub-container">
            <div class="sub-container-title margin-bottom-10">近期促销</div>
            <div class="cuxiao" id="cuxiao"></div>
        </div>

    </div>

    <a href="/mobile/center"><div class="go-to-center" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:21vw;background-image:url(../../../v1/images/mobile/go_to_center.png);background-size:contain;background-repeat:no-repeat;background-position:center;"></div></a>

    @verbatim
    <script id="self-promotion-tpl" type="text/html">
        {{@ seller.self_promotion}}
    </script>

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


    <script id="list-album-tpl" type="text/html">
        {{each datas album i}}
        <div class="album-container" onclick="go_album_detail('{{album.web_id_code}}')">
            <div class="album-container-image"
                 style="background-image:url('{{album.photo_cover}}')"></div>
            <div class="album-container-avatar"
                 style="background-image:url('{{album.designerPhoto}}')"></div>
            <div class="album-container-title">{{album.title}}</div>
            <div class="album-container-tag-outer">
                <span class="tag mj">{{album.count_area}}㎡</span>
                {{each album.style style j}}
                <span class="tag fg">{{style}}</span>
                {{/each}}
            </div>
            <div class="album-container-content">
                <div class="album-container-content-span active"><span class="iconfont icon-mima-xianshi"></span>{{album.count_visit}}
                </div>
                <div class="album-container-content-span"><span class="iconfont icon-dianzan2"></span>{{album.count_praise}}</div>
                <div class="album-container-content-span"><span class="iconfont icon-shoucang2"></span>{{album.count_fav}}</div>
                <div class="album-container-content-span"><span class="iconfont icon-ic_huifu"></span>{{album.count_comment}}</div>
                <div class="album-container-content-span">by {{album.designer}}</div>
            </div>
        </div>
        {{if i == datas.length-1}}
        <div class="album-container-last"></div>
        {{/if}}
        {{/each}}
    </script>

    <script id="list-designer-tpl" type="text/html">
        {{each datas designer i}}
        <div class="designer-outer" onclick="location.href='/mobile/designer/s/{{designer.web_id_code}}'">
            <div class="avatar"
                 style="background-image:url('{{designer.url_avatar}}')"></div>
            <div class="name">{{designer.nickname}}</div>
            <div class="hint">{{designer.count_album}}套方案</div>
        </div>
        {{/each}}
    </script>


    <script id="seller-profile-tpl" type="text/html">
        <div id="focus-btn" onclick="fav_dealer()">{{if seller.faved}}已关注{{else}}关注{{/if}}</div>
        <div class="praise-outer" style="display:none;"><span class="iconfont icon-dianzan2" ></span><span id="album-praise-count">{{seller.album_praise_count}}</span></div>
        <div class="seller-name">{{seller.name}}</div>
        <div class="tag-outer">
            <span class="tag hx">设计师 {{seller.designer}}</span>
            <span class="tag mj">设计方案 {{seller.album}}</span>
            <span class="tag fg">产品 {{seller.product}}</span>
        </div>
        <div class="location">
            <div id="address-outer" class="{{if seller.self_lat&&seller.self_lng}}available{{/if}}" lat="{{ seller.self_lat }}" lng="{{ seller.self_lng }}" name="{{ seller.name }}" address="{{ seller.self_address }}"><span class="iconfont icon-location"></span>{{seller.self_address}}</div>
            {{if seller.contact_telephone}}
            <span class="iconfont icon-dianhua"></span>
            <a href="tel:{{seller.contact_telephone}}" class="telephone">{{seller.contact_telephone}}</a>
            {{/if}}
        </div>

        <!--<div class="section-outer">
            <div class="section-title">品牌理念</div>
            &lt;!&ndash;<div class="section-sub-content">蒙娜丽莎集团素以技术创新闻名业内，以科技创新为核心推进企业发展。集团于1999年成立了企业研发中心，2004年被认定为“广东省建筑陶瓷工程技术研究开发中心”，2005年被认定为“广东省企业技术中心”，2008年被认定为国家高新技术企业，2010年被评为国家火炬计划重点高新技术企业。2013年12月，蒙娜丽莎集团技术中心经国家发改委等五部委认定为“国家认定企业技术中心”，并于2014年1月正式挂牌成立。</div>
            <div class="section-sub-content">蒙娜丽莎集团共获得专利授权658项，其中发明专利86项（含国外发明专利3项），实用新型专利67项，外观设计505项，涵盖建筑陶瓷设计、生产、应用和环保治理等多方面，在生产制造领域实现了较高的自动化与绿色化，处于行业领先水平。</div>
            &ndash;&gt;
            <div class="section-content">秉承“每个家
                都值得拥有蒙娜丽莎”的品牌发展理念，蒙娜丽莎将达芬奇对待产品的态度融入到品牌文化中并转化为对待产品的要求，与此同时，将蒙娜丽莎的微笑作为营销服务的核心精神，使顾客在感受艺术化产品的同时，享受高品质的服务所带来的精神回报，满足人们多样的生活方式需求。
            </div>
        </div>-->
        <div class="section-outer">
            <div class="section-title">商家介绍</div>
            <div class="section-content collapse-2-line">
                {{@ seller.introduction}}
            </div>
        </div>

        {{ if seller.promise && seller.promise!='' }}
        <div class="section-outer">
            <div class="section-title">服务承诺</div>
            <div class="section-content collapse-2-line">
                {{@ seller.promise}}
            </div>
        </div>
        {{ /if }}

    </script>

    @endverbatim

@endsection

@section('script')
    <script>

        var seller_info_api_url = "{{url('/mobile/dealer/api/get_seller_info/'.request()->route('web_id_code'))}}?__bs="+__cache_brand;
        var list_designer_api_url = "{{url('/mobile/dealer/api/list_designer/'.request()->route('web_id_code'))}}?__bs="+__cache_brand;
        var list_album_api_url = "{{url('/mobile/dealer/api/list_album/'.request()->route('web_id_code'))}}?__bs="+__cache_brand;
        var list_product_api_url = "{{url('/mobile/dealer/api/list_product/'.request()->route('web_id_code'))}}?__bs="+__cache_brand;

        //微信JSSDK初始化
        wx.config(<?php echo $jssdkConfig ?>);

    </script>
@endsection