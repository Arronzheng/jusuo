@extends('v1.site.layout',[
   'hide_nav_row' => true,
   'hide_top_right' => true,
   'hide_footer' => true,
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
   ],
   'js'=>[
        '/v1/static/iconfont/iconfont.js',
   ]
])

<style>
    .icon-outer{height:40vw;width:100%;display: flex;flex-direction: row;justify-content: center;align-content: center;align-items: center;}
    .icon-outer div{flex:none;color:#aaa;}
    .icon-outer .icon{width:300px;height:300px;fill: currentColor;overflow: hidden;}
    .icon-outer .hint{font-size:18px;margin-left:10px;}
    .icon-outer .back a{font-size:18px;margin-left:1em;color:rgb(17, 112, 243) !important;}
</style>

@section('content')

    <div class="icon-outer">
        @if($error==\App\Services\v1\site\PageService::ErrorNoResult)
            <div>
                <svg class="icon" aria-hidden="true">
                    <use xlink:href="#icon-kongbaiye1"></use>
                </svg>
            </div>
            <div><span class="hint">抱歉，找不到符合条件的记录</span></div>
        @elseif($error==\App\Services\v1\site\PageService::ErrorNoAuthority)
            <div>
                <svg class="icon" aria-hidden="true">
                    <use xlink:href="#icon-sousuokongbaiye"></use>
                </svg>
            </div>
            <div><span class="hint">抱歉，访问权限不足</span></div>
        @elseif($error==\App\Services\v1\site\PageService::ErrorNoService)
            <div>
                <div>
                    <svg class="icon" aria-hidden="true">
                        {{--<use xlink:href="#icon-dizhikongbaiye"></use>--}}
                        <use xlink:href="#icon-kongbaiye-wuwangluo"></use>
                    </svg>
                </div>
            </div>
            <div><span class="hint">抱歉，您不在该商家的服务范围</span></div>
        @elseif($error==\App\Services\v1\site\PageService::ErrorNoLogin)
            <div>
                <div>
                    <svg class="icon" aria-hidden="true">
                        {{--<use xlink:href="#icon-dizhikongbaiye"></use>--}}
                        <use xlink:href="#icon-kongbaiye-wuwangluo"></use>
                    </svg>
                </div>
            </div>
            <div><span class="hint">抱歉，请先登录</span></div>
            <script>
                showLoginReg(true);
            </script>
        @else
            <div>
                <div>
                    <svg class="icon" aria-hidden="true">
                        <use xlink:href="#icon-kongbaiye-wuwangluo"></use>
                    </svg>
                </div>
            </div>
            <div><span class="hint">抱歉，页面走丢了..</span></div>
        @endif
        <div class="back"><a href="javascript:history.back(-1);">点击返回</a></div>
    </div>

@endsection

@section('script')

@endsection