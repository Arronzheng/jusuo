<div class="nav">
    <div class="nav-top" id="nav-top">
        <div class="fixed-width">
            <div class="pull-left">
                <a class="nav-text" href="{{url('index')}}" target="_self">首页</a>
                <span class="divider"></span>
                <a class="nav-text active"><span class="iconfont location">&#xe602; </span><span id="city">城市</span></a>
            </div>
            <div class="right-box">
                <a class="hidden nav-text" href="" target="_self">消息<span class="num">10</span></a>
                <span class="hidden divider"></span>
                <div class="hidden user-name display-inline-block gs-select top-select nav-text gs-user-menu-cont">
                    <div class="name-cont">
                        <a href="" target="_self">
                            <div class="display-inline-block">
                                <img class="avatar" src="https://qhyxpicoss.kujiale.com/avatars/LHUGHDIKOTHZGYHSAAAAACA8.jpg?x-oss-process=image/resize,m_fill,h_25/format,webp">
                                <span class="name-text">加仑子</span>
                                <div class="ripple"></div>
                            </div>
                            <div class="rotate-90 display-inline-block">
                                <span><span class="iconfont">&#xe60c;</span></span>
                            </div>
                        </a>
                    </div>
                    <div class="menu-arrow">
                        <div class="arrow-inner"></div>
                    </div>
                    <div class="user-menu">
                        <div class="info-outer block overflow-hidden">
                            <img class="menu-bg blur" src="https://qhyxpicoss.kujiale.com/avatars/LHUGHDIKOTHZGYHSAAAAACA8.jpg?x-oss-process=image/resize,m_fill,w_60/format,webp">
                            <a class="avatar display-inline-block" href="user" target="_self">
                                <img class="avatar-image" src="https://qhyxpicoss.kujiale.com/avatars/LHUGHDIKOTHZGYHSAAAAACA8.jpg?x-oss-process=image/resize,m_fill,w_60/format,webp">
                            </a>
                            <div class="info display-inline-block">
                                <div class="name-icon-outer">
                                    <div class="name display-inline-block single-line">加仑子加仑子加仑子加仑子加仑子加仑子</div>
                                    <div class="gradient display-inline-block tag-certificate">实名</div>
                                </div>
                                <div class="level-outer">
                                    <div class="exp">经验值</div>
                                    <div class="exp"><span class="num">100</span>/1000</div>
                                </div>
                                <div class="exp-bar-outer">
                                    <div class="exp-bar" style="width:10%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="menu-outer block">
                            <a class="menu-item" href="user" target="_self">
                                <span class="iconfont menu-text">&#xe60f;</span>
                                <span class="menu-text">我的方案</span>
                            </a>
                            <a class="menu-item" href="user" target="_self">
                                <span class="iconfont menu-text">&#xe630;</span>
                                <span class="menu-text">账号设置</span>
                            </a>
                        </div>
                        <div class="user-menu-footer block">退出</div>
                    </div>
                </div>
                @if(!$hide_top_right&&(!isset($is_preview)||!$is_preview))
                    @if(Auth::guard('web')->user())
                        <a class="nav-text" href="/center/album">欢迎您 {{Auth::guard('web')->user()->login_username}}</a> / <a class="nav-text" href="{{url('/account/logout')}}">退出</a>
                    @else
                        <a class="nav-text" id="login">登录 </a> <a class="nav-text active" id="register">免费注册</a>
                    @endif
                @endif
                {{--<span class="divider"></span>
                <a class="nav-text" href="/hc" target="_blank">帮助中心</a>--}}
                <span class="divider"></span>
                @if(!$hide_top_right&&(!isset($is_preview)||!$is_preview))
                    <?php
                        $pageBrandWebIdCode = '';
                        $pageBrandId = session()->get('pageBelongBrandId');
                        $pageBrand = \App\Models\OrganizationBrand::find($pageBrandId);
                        if($pageBrand){$pageBrandWebIdCode = $pageBrand->web_id_code;}
                    ?>
                <a class="nav-text" href="{{url('admin/login?b='.$pageBrandWebIdCode)}}">商家后台</a>
                @endif
            </div>
        </div>
    </div>
</div>