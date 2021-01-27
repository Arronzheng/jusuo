@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/center.css'
   ],
   'js'=>[
   ]
])

@section('content')

    <div id="cover">

    </div>

    <div class="container" id="container">

    </div>

    <script id="container-tpl" type="text/html">
        @verbatim
        <div class="designer-info">
            <div class="nickname">{{data.nickname}}</div>
            <div class="tag-outer">
                {{each data.style name i }}
                <span class="tag fg">{{name}}</span>
                {{/each}}
            </div>
            <div class="action-outer" onclick="location.href='/mobile/center/edit'">
                <div class="action"><span class="iconfont icon-ic_bianji"></span>修改</div>
            </div>
        </div>
        <div class="company"><a href="{{ if data.company_link!='' }}/mobile/dealer/s/{{data.company_link}} {{/if}}"><span>来自 </span>{{data.company}} </a>{{ if false }}<span class="icon-location iconfont"></span><span>{{data.city}}</span>{{ /if }}<span class="exp">{{data.exp_year}}</span></div>
        <div class="data-outer">
            <div class="data-div">
                <div class="data-num">{{data.count_album}}</div>
                <div class="data-name">作品</div>
            </div>
            <div class="data-div">
                <div class="data-num">{{data.count_visit}}</div>
                <div class="data-name">浏览</div>
            </div>
            <div class="data-div">
                <div class="data-num">{{data.count_fan}}</div>
                <div class="data-name">粉丝</div>
            </div>
            <div class="data-div">
                <div class="data-num">{{data.count_praise}}</div>
                <div class="data-name">获赞</div>
            </div>
        </div>

        <div class="splitter"></div>

        <div class="sub-container">
            <div class="menu"><a href="center/integral">我的积分<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{data.point_money}}</span></a></div>
            <div class="menu"><a href="center/my/albums">我的方案<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{data.count_album_all}}</span></a></div>
            <div class="menu"><a href="center/fav/products">收藏产品<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{data.count_product_fav}}</span></a></div>
            <div class="menu"><a href="center/fav/albums">收藏方案<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{data.count_album_fav}}</span></a></div>
            <div class="menu"><a href="center/fav/designers">关注设计师<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{data.count_designer_fav}}</span></a></div>
            <div class="menu"><a href="mall">积分商城<span class="right iconfont icon-arrowdropdown"></span></a></div>
            <div class="menu"><a href="/mobile/designer/s/{{ data.web_id_code }}">前往主页<span class="right iconfont icon-arrowdropdown"></span></a></div>
        </div>
        @endverbatim
    </script>

    <script id="cover-tpl" type="text/html">
        @verbatim
        <div class="cover">
            <div id="cover-bg"
                 style="background-image:url({{data.bg}})"></div>
        </div>

        <div class="avatar"
             style="background-image:url({{data.avatar}})"></div>

        <div class="reward-outer">
            {{if ''!=data.level_title}}
            <div class="iconfont icon-xingji"></div>
            <div class="reward">{{data.level_title}}</div>
            {{/if}}
        </div>
        @endverbatim
    </script>

@endsection

@section('script')

    <script>

        var center_index_api_url = "{{url('/mobile/center/api/index')}}";
        var current_url = "{{url()}}";

    </script>

    <script src="{{asset('/v1/js/mobile/center.js')}}"></script>

@endsection