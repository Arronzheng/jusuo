@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/designer.css?v='.str_random(random_int(20,30))
   ],
   'js'=>[
       '/v1/js/ajax.js',
       '/v1/js/mobile/designer.js?v='/*.str_random(random_int(20,30))*/
       //20200721如果用random的v后缀，可能使wx.config无效，所以注释掉
   ]
])

@section('content')
    {{--<style>--}}
        {{--.sub-container{background-color: inherit;padding:0;height:auto;overflow: inherit;}--}}
        {{--.sub-container .item-outer:last-child{margin-bottom:10px;}--}}
    {{--</style>--}}

    <div id="cover" class="cover">
        <div id="cover-bg"></div>

    </div>
    <div class="avatar" id="cover-avatar"
         style="background-image:url('')">
    </div>


    <div class="container">

        <div id="designer-profile">

        </div>

        <div class="splitter"></div>

        <div id="nice-album" class="sub-container">

        </div>

    </div>

    {{--<div class="back-to-top" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:10vw;background-image:url(../../../v1/images/mobile/back_to_top.png);background-size:contain;"></div>--}}
    <a href="/mobile/center"><div class="go-to-center" style="position:fixed;width:8vw;height:8vw;right:10px;bottom:21vw;background-image:url(../../../v1/images/mobile/go_to_center.png);background-size:contain;background-repeat:no-repeat;background-position:center;"></div></a>

    @verbatim


    <script id="nice-album-tpl" type="text/html">
        <div class="sub-container-title">优秀方案({{ datas.length }})</div>
        <div class="album-container-outer">
            {{each datas album i}}
            <div class="album-container" onclick="go_album_detail('{{album.web_id_code}}')">
                <div class="album-container-image"
                     style="background-image:url('{{album.photo_cover}}')"></div>
                <div class="album-container-title">{{album.title}}</div>
                <div class="album-container-tag-outer">
                    <span class="tag mj">{{album.count_area}}㎡</span>
                    {{each album.styles style j}}
                    <span class="tag fg">{{style}}</span>
                    {{/each}}
                </div>
                <div class="album-container-content">
                    <div class="album-container-content-span active"><span class="iconfont icon-mima-xianshi"></span>{{album.count_visit}}</div>
                    <div class="album-container-content-span"><span class="iconfont icon-dianzan2"></span>{{album.count_praise}}</div>
                    <div class="album-container-content-span"><span class="iconfont icon-shoucang2"></span>{{album.count_fav}}</div>
                    <div class="album-container-content-span"><span class="iconfont icon-ic_huifu"></span>{{album.count_comment}}</div>
                    <div class="album-container-content-span">{{album.created_time}}</div>
                </div>
            </div>
            {{/each}}

            <div class="album-container-last"></div>
        </div>
    </script>


    <script id="designer-profile-tpl" type="text/html">

        {{ if designer.level_name}}
        <div class="reward-outer">
            <div class="iconfont icon-xingji"></div>
            <div class="reward">{{designer.level_name}}设计师</div>
        </div>
        {{/if}}

        <div class="designer-info">
            <div class="nickname">{{designer.detail.nickname}}</div>
            <div class="tag-outer">
                {{each designer.styles item i }}
                <span class="tag fg">{{item}}</span>
                {{/each}}
            </div>
            <div id="focus-block" class="action-outer">
                {{if designer.focused}}
                <div class="action" onclick="designer_focus()">取消关注</div>
                {{else}}
                <div class="action" onclick="designer_focus()">+关注</div>
                {{/if}}
            </div>
        </div>
        <div class="company"><a href="{{ if designer.company_link!='' }}/mobile/dealer/s/{{designer.company_link}} {{ /if }}"><span>来自 </span>{{designer.company}} </a>{{ if false }}<span class="icon-location iconfont"></span><span>{{designer.area_text}}</span>{{ /if }}<span class="exp">{{designer.detail.self_working_year}}年设计经验</span></div>
        <div class="data-outer">
            <div class="data-div">
                <div class="data-num">{{designer.detail.count_album}}</div>
                <div class="data-name">作品</div>
            </div>
            <div class="data-div">
                <div class="data-num">{{designer.detail.count_visit}}</div>
                <div class="data-name">浏览</div>
            </div>
            <div class="data-div">
                <div class="data-num" id="fan-count">{{designer.detail.count_fan}}</div>
                <div class="data-name">粉丝</div>
            </div>
            <div class="data-div">
                <div class="data-num">{{designer.detail.count_praise}}</div>
                <div class="data-name">获赞</div>
            </div>
        </div>

        {{ if designer.detail.self_introduction!=null && designer.detail.self_introduction!='' }}
        <div class="section-outer">
            <div class="section-title margin-top-0">设计师简介</div>
            <div class="section-content collapse-2-line">
                {{designer.detail.self_introduction}}
            </div>
        </div>
        {{ /if }}

        {{ if designer.space!=null && designer.space!='' }}
        <div class="section-outer">
            <div class="section-title">擅长空间</div>
            <div class="section-content collapse-2-line">
                {{designer.space}}
            </div>
        </div>
        {{ /if }}

    </script>

    @endverbatim

@endsection

@section('script')
    <script>
        var designer_info_api_url = "{{url('/mobile/designer/api/get_designer_info/'.request()->route('web_id_code'))}}?__bs="+__cache_brand;
        var nice_album_api_url = "{{url('/mobile/designer/api/list_nice_album/'.request()->route('web_id_code'))}}?__bs="+__cache_brand;
        var designer_focus_api_url = "{{url('/mobile/designer/api/focus')}}?__bs="+__cache_brand;

        //微信JSSDK初始化
        wx.config(<?php echo $jssdkConfig ?>);

    </script>
@endsection