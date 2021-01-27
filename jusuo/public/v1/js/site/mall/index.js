var banner = [];
var goodList = [];
var cat1List = [];
var selectedCat1 = 0;
var filterValues = [];
var recommendGoodList = [];

$(function(){

    $('.sale-image, .productview').click(function(){
        window.location.href = 'https://www.ijusuo.com/mall/detail/1';
    });

    //首页banner
    getIndexBanner();

    //获取推荐
    getRecommendGoods();

    //首页分类
    getCategory();


});

//去商品详情
function goDetail(web_id_code){
    window.open("/mall/detail/"+web_id_code);
}

//推荐商品
function getRecommendGoods(){
    ajax_get("/mall/api/index_recommend_goods",function(res){

        if(res.status && res.data.length>0){

            recommendGoodList = res.data;
            var html = template('recommend-goods-list-tpl', {data:res.data});
            $('#recommend-goods-list').html(html)

        }

    },function(){})
}

//选择分类1
function selectCat1(id){
    selectedCat1 = id;
    getFilterOptions();
}

//获取商品列表
function getGoodList(scrollTop){
    var query_options = {};
    query_options.b = $("#i_b").val();
    query_options.r = $("#i_r").val();
    query_options.c2 = $("#i_c2").val();
    query_options.order = $("#i_sort_order").val();
    query_options.page = $("#i_page").val();
    api_url = set_url_query("/mall/api/index_good_list",query_options);

    layer.load(1)
    ajax_get(api_url,function(res){

        layer.closeAll("loading");

        if(res.status){
            $('#data-count').html(res.data.total)
            var current_page = res.data.current_page;
            var total = res.data.total;
            $('#i_page').val(current_page)
            //初始化页码
            initPager(current_page,total)

            goodList = res.data.data;

            if(res.data.data.length>0){
                var html = template('good-list-tpl', {data:res.data.data});
                $('#good-list').html(html)
            }else{
                var html = template('good-list-empty');
                $('#good-list').html(html)
            }

            if(scrollTop){
                //获取目标元素距离屏幕顶部的高度
                var target_roll_height = $('#project').offset().top;
                //滚动
                $("html,body").animate({scrollTop: target_roll_height}, 300);
            }


        }


    },function(){})
}

//切换排序的选择
function changePaixu(obj){
    var paixu_block = $(obj).parents('.paixu-block');
    var type = paixu_block.attr('data-type');
    var sort_order = $('#i_sort_order').val()
    sort_order_array = sort_order.split('_')
    var sort = sort_order_array[0]
    var order = sort_order_array[1]
    //变更了排序类型，则从降序开始
    if(sort != type){
        sort = type;
        order = 'desc'
    }else{
        if(order=='desc'){
            order = 'asc'
        }else{
            order = 'desc'
        }
    }

    //设置排序值
    $('#i_sort_order').val(sort+"_"+order)
    //修改排序title
    $('#paixu').find('.paixu_label').removeClass('active1')
    paixu_block.find('.paixu_label').addClass('active1')
    //修改排序icon
    $('#paixu').find('.iconfont').removeClass('active')
    paixu_block.find('.iconfont.'+order).addClass('active')
    $('#i_page').val(1)

    getGoodList();
}

//切换筛选类型的选择
function changeFilterType(obj,type,value){
    var option_item = $(obj);
    if(!filterValues[type]){
        filterValues[type] = [];
    }

    var row = option_item.parent('.nav_text')

    if(option_item.hasClass('all-option')){
        //如果点击的是不限，取消本行其他选择
        row.find('.nav_t').removeClass('active')
        option_item.addClass('active')
        filterValues[type] = [];
        $('#i_'+type).val([])
    }else{
        //如果点击的是非不限
        if(option_item.hasClass('active')){
            //如果该选项已选，则取消选择，并在input记录处去掉这个值
            filterValue = filterValues[type]
            removeByValue(filterValue,value)
            filterValues[type] = filterValue;
            $('#i_'+type).val(filterValue)
            option_item.removeClass('active')
            //如果取消完了，则将不限选上
            if(filterValue.length<=0){
                row.find('.all-option').addClass('active')
            }else{
                row.find('.all-option').removeClass('active')

            }
        }else{
            option_item.addClass('active')
            filterValues[type].push(value)
            $('#i_'+type).val(filterValues[type])
            //重设页数
            $('#i_page').val(1)
            row.find('.all-option').removeClass('active')

        }
    }

    getGoodList();
}

//分类
function getCategory(){
    ajax_get("/mall/api/index_cat1s",function(res){

        if(res.status && res.data.length>0){

            cat1List = res.data;
            var html = template('cat1-list-tpl', {data:res.data});

            //将第一个一级分类默认选中
            selectedCat1 = cat1List[0].id;

            //获取筛选项
            getFilterOptions();

            $('#cat-1-list').html(html)

        }

    },function(){})
}

//筛选项
function getFilterOptions(){
    layer.load(1)
    //获取筛选类型数据
    var queryString = window.location.search.slice(1);
    queryString = queryString.split('#')[0];
    queryString+="&c1="+selectedCat1;
    queryString = encodeURIComponent(queryString)
    ajax_get("/mall/api/index_filter_options?query="+queryString,function(res){
        layer.closeAll("loading");

        if(res.status && res.data.length>0){

            filter_types = res.data;
            var html = template('filter-type-tpl', {data:res.data});

            $('#allnav').html(html)

            //刷新首页商品数据
            getGoodList();

        }

    },function(){})
}

//banner
function getIndexBanner(){
    $.get('/mall/api/index_banner',{},function(res){
        if(res.status==1){
            banner = res.data;
            if(banner.length<=0){
                $('.bannerBox').hide();
            }
            var str='';
            str+="<div class='swiper-container' id='swiper1'>"
            str+="<div class='swiper-wrapper swiper-no-swiping'>"
            for(var i=0;i<banner.length;i++){
                str+="<div class='swiper-slide' onclick='clickBanner(\""+banner[i].url+"\")'>"
                str+="<img src='"+banner[i].image+"'/>"
                str+="</div>"
            }
            str+="</div>"
            str+="</div>"
            str+="<div class='left'>"+"<img src='../../v1/images/site/index/bannerL.png'/></div>"
            str+="<div class='right'><img src='../../v1/images/site/index/bannerR.png'/></div>"
            $("#swipers").html(str);
            //轮播
            //最里层轮播
            var mySwiper1 = new Swiper('#swiper1',{
                loop: true,
                autoplay : 4000,
                speed : 600,
                autoplayDisableOnInteraction : false,
                pagination: '.swiper-pagination',
                paginationType: 'custom',//这里分页器类型必须设置为custom,即采用用户自定义配置
                //下面方法可以生成我们自定义的分页器到页面上
                paginationCustomRender: function(swiper, current, total) {
                    var customPaginationHtml = "";
                    for(var i = 0; i < total; i++) {
                        //判断哪个分页器此刻应该被激活
                        if(i== (current-1) ){
                            customPaginationHtml += '<span class="swiper-pagination-customs swiper-pagination-customs-active"></span>';
                        } else {
                            customPaginationHtml += '<span class="swiper-pagination-customs"></span>';
                        }
                    }
                    return customPaginationHtml;
                }
            });

            //前进后退按钮
            $(".bannerBox .left").click(function(){
                mySwiper1.slidePrev();
            })
            $(".bannerBox .right").click(function(){
                mySwiper1.slideNext();
            })
            $('.bannerBox .swiper-pagination').on('click','span',function(){
                var index = $(this).index() + 2;
                mySwiper1.slideTo(index-1, 500, false);//切换到第一个slide，速度为1秒
            })
        }
    });
}

function clickBanner(url){
    if(url){
        location.href = url
    }
}

function initPager(nowPage,total){
    // xlPaging.js 使用方法
    var nowpage = $("#pager").paging({
        nowPage: nowPage, // 当前页码
        pageNum: total == 0 ? 1 : Math.ceil(total / 30), // 总页码
        buttonNum: Math.ceil(total / 30), //要展示的页码数量
        canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
        showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
        callback: function (num) { //回调函数
            var page = num;
            $('#i_page').val(page);
            getGoodList(true);
        }
    });
}

function removeByValue(arr, val) {
    for(var i=0; i<arr.length; i++) {
        if(arr[i] == val) {
            arr.splice(i, 1);
            break;
        }
    }
}