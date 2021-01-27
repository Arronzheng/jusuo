@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/designer/detail.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/designer/detail.js',
        '/v1/js/ajax.js',
   ]
])

@section('content')

    <div class="container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 设计师 /</span>
            <span class="navtext2" id="designer_nickname">陈俊杰的主页</span>
        </div>
        <div class="head" id="head"></div>
        <div class="middle">
            <div class="middle_left">
                <div class="designer" id="b0">
                    <div class="designer_left">
                        <img src="images/index/设计方案icon.png" class="designer_icon"/>
                        <span class="designer_title">代表作品</span>
                    </div>
                    <div class="sscontanier" id="designer_fa"></div>
                </div>
                <div class="fangan" id="b1">
                    <label class="producttitle">设计案例</label>
                    <div id='product_swiper'></div>
                    <div id="produ">
                        <div class="productcontainer"id="fangan"></div>
                    </div>
                    <div id="page"></div>
                </div>
            </div>
            <div class="middle_right">
                <div class="aboutme" id='aboutme'></div>
                <div class="readview" id="slideBar">
                    <span class="daohangtitle">导航栏</span>
                    <div class="sidebar">
                        <div class="branch"></div>
                        <ul id="sidenav">
                            <li class="point_a"><a href="#b0"><span>代表作品</span></a><i class="point"></i></li>
                            <li><a href="#b1"><span>设计案例</span></a><i></i></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>

        var designer_info = null;

        var all_navigator_location_loaded = false;

        var designer_info_api_url = "{{url('/designer/api/get_designer_info/'.request()->route('id'))}}";
        var designer_section_api_url = "{{url('/designer/api/list_designer_sections/'.request()->route('id'))}}";

        var album_list = [];

        //跟帖信息
        var navigator_init_time = 5;
        var navigator_init_count = 0;

        $(function () {

            get_designer_info();

        });

        //获取方案基本信息
        function get_designer_info() {
            layer.load(1)

            ajax_get(designer_info_api_url, function (res) {

                layer.closeAll("loading");

                if (res.status) {

                    var designer = res.data;

                    designer_info = designer;

                    //设置顶部标题
                    $('.designer-title').html(designer_info.title)

                    //设置更多相似方案地址
                    $('#designer_similiar_lookall').attr('data-url',designer.more_similiar_url)

                    var photo_cover_html = template('photo-cover-tpl', {designer: designer});
                    var photo_layout_html = template('photo-layout-tpl', {designer: designer});
                    var designer_basic_info_html = template('designer-basic-info-tpl', {designer: designer});
                    var designer_profile_html = template('designer-profile-tpl', {designer: designer.designer_info});

                    $('#photo-cover').html(photo_cover_html)
                    $('#photo-layout').html(photo_layout_html)
                    $('#designer-basic-info').html(designer_basic_info_html)
                    $('#designer-profile').html(designer_profile_html)

                    init_navigator_view();

                }else{
                    layer.msg(res.msg)
                }


            }, function () {
            })
        }

        //获取方案空间列表
        function list_designer_sections() {

            ajax_get(designer_section_api_url, function (res) {

                if (res.status) {

                    var datas = res.data;

                    var designer_sections_html = template('designer-sections-tpl', {datas: datas});

                    $('#designer-sections').html(designer_sections_html)

                }else{
                    layer.msg(res.msg)
                }


            }, function () {
            })
        }


        //导航栏自动识别变化
        function init_navigator_change(){
            // //为了页面滚动对应导航
            var mainTopArr = new Array();
            for(var i=0;i<6;i++){
                var navigator_row_offset = $("#navigator-location-"+i).offset();
                //如果对应模块还没有加载完，则推迟一段时间后再执行
                if(!navigator_row_offset){
                    setTimeout(function(){
                        init_navigator_change();
                    },500);
                    return false;
                }
                var top =navigator_row_offset.top-50;
                console.log("#navigator-location-"+i+":"+top)
                navigator_init_count+=1;
                if(navigator_init_count<=navigator_init_time){
                    //如果导航滚动的初始化未达定义的最大次数，则继续初始化
                    setTimeout(function(){
                        init_navigator_change();
                    },2000);
                }
                mainTopArr.push(top);
            }
            $(window).scroll(function(){
                var scrollTop = $(this).scrollTop();
                var k;
                for(var i=0;i<mainTopArr.length;i++){
                    if(scrollTop>=mainTopArr[i]){
                        k=i;
                    }
                }
                $('#sidenav li:first').siblings().find('i').addClass('dark'); //当前选中样式
                var links = $('#sidenav li').find('a'); //赋予变量links 为元素a
                links.eq(k).next().addClass('point').removeClass('dark'); //当前圆点从暗变亮，移除类dark，添加point。
                links.eq(k).css('color', '#1582FF'); //当前a元素变亮
                links.eq(k).css('font-weight', 'bold'); //当前a元素变亮
                var links_par = links.eq(k).parent() //赋予变量links_par 为当前a元素的父元素li
                links_par.siblings().find('a').next().addClass('dark').removeClass('point'); //当前li元素的其他兄弟元素下的i元素变暗
                links_par.siblings().find('a').css('color', '');
                links_par.siblings().find('a').css('font-weight', 'normal');
                links_par.siblings().removeClass('point_a'); //移除初始选中默认的样式
            });
        }

        //导航栏吸顶效果
        function init_navigator_view(){
            //获取要定位元素距离浏览器顶部的距离
            var navH = $(".readview").offset().top;

            //滚动条事件
            $(window).scroll(function(){
                // //获取滚动条的滑动距离
                var scroH = $(this).scrollTop();
                //     var a=document.body.clientHeight-scroH
                //滚动条的滑动距离大于等于定位元素距离浏览器顶部的距离，就固定，反之就不固定
                if(scroH>=navH){
                    $("#sidenav i.point").css({"left":"30px"});
                    $(".branch").css({"left":"32px"});
                    $(".dark").css({"left":"30px"});
                    $(".readview").css({"position":"fixed","top":0});
                }else if(scroH<navH){
                    $("#sidenav i.point").css({"left":"820px"});
                    $(".branch").css({"left":"822px"});
                    $(".dark").css({"left":"820px"});
                    $(".readview").css({"position":"static"});
                }
            })
        }

        function init_pager(nowPage,total){
            // xlPaging.js 使用方法
            var nowpage = $("#pager").paging({
                nowPage: nowPage, // 当前页码
                pageNum: total==0?1:Math.ceil(total / 6), // 总页码
                buttonNum: Math.ceil(total / 6), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    var page = num;
                    $('#i_product_page').val(page);
                    list_designer_products();

                }
            });

        }

    </script>

    <script src="{{asset('/v1/js/site/designer/detail.js')}}"></script>

@endsection