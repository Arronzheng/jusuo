<div class="nav-row">
    <div class="fixed-width">
        <div class="nav-left">
            <a class="logo-link" href="/index" target="_self">
                <div class="logo background-center"></div>
            </a>
            <div class="main-nav">
                <div class="nav-list">
                    <div class="drop-down nav-item">
                        <a class="nav-item-link" href="/index" target="_self">
                            <div class="text-cont">首页</div>
                        </a>
                    </div>
                    <div class="drop-down nav-item">
                        <a class="nav-item-link" href="/album" target="_self">
                            <div class="text-cont">设计方案</div>
                        </a>
                    </div>
                    <div class="drop-down nav-item">
                        <a class="nav-item-link" href="/product" target="_self">
                            <div class="text-cont">产品库</div>
                        </a>
                    </div>
                    <div class="drop-down nav-item">
                        <a class="nav-item-link" href="/designer" target="_self">
                            <div class="text-cont">设计师</div>
                        </a>
                    </div>
                    {{--<div class="drop-down nav-item">
                        <a class="nav-item-link" href="https://www.baidu.com/" target="_self">
                            <div class="text-cont">装饰公司</div>
                        </a>
                    </div>--}}
                    <div class="drop-down nav-item">
                        <a class="nav-item-link" href="/dealer" target="_self">
                            <div class="text-cont">材料商家</div>
                        </a>
                    </div>
                    {{--<div class="drop-down nav-item">
                        <a class="nav-item-link">
                            <div class="text-cont display-inline-block">设计课堂</div>
                            <div class="text-cont rotate-90 display-inline-block">
                                <span><span class="iconfont">&#xe60c;</span></span>
                            </div>
                        </a>
                        <div class="panel-subs">
                            <div class="sub-row">
                                <a class="select-link" href="/college" target="_self">
                                    <span class="text-cont">
                                        <span class="text">设计课堂</span>
                                    </span>
                                </a>
                            </div>
                            <div class="sub-row">
                                <a class="select-link" href="/intro" target="_self">
                                    <span class="text-cont">
                                        <span class="text">新手入门</span>
                                        <span class="nav-tag" style="color: #FFFFFF;background-color: #FF2B00">HOT</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>--}}
                </div>
                <div class="nav-search-btn"><span class="iconfont">&#xe618;</span></div>
                <div class="nav-search-cont">
                    <div class="drop-down nav-item">
                        <div class="text-cont display-inline-block" id="nav-search-type-text" data-attr="0">方案</div>
                        <div class="text-cont rotate-90 display-inline-block">
                            <span class="iconfont">&#xe60c;</span>
                        </div>
                        <div class="panel-subs">
                            <div class="sub-row" data-value="0" data-text="方案"><span class="text">方案</span></div>
                            {{--<div class="sub-row" data-value="1" data-text="户型"><span class="text">户型</span></div>--}}
                            <div class="sub-row" data-value="2" data-text="设计师"><span class="text">设计师</span></div>
                            <div class="sub-row" data-value="3" data-text="产品"><span class="text">产品</span></div>
                        </div>
                    </div>
                    <div class="input-cont"><input id="nav-search-input"></div>
                    <div class="close-btn"><span class="iconfont">&#xe62c;</span></div>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <div class="nav-btns">
                {{--@if(Auth::guard('web')->user())
                    <a class="btn-line" href="/center/album" target="_self">我的设计</a>
                    <a class="btn-fill" href="/center/album/create" target="_self">开始设计</a>
                @else
                    <a class="btn-line" id="login-to-center">我的设计</a>
                    <a class="btn-fill" id="login-to-create">开始设计</a>
                @endif--}}
            </div>
        </div>
    </div>
</div>