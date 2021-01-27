var product_list = [];
var filter_types = [];

var queryString = window.location.search.slice(1);
queryString = queryString.split('#')[0];
queryString = encodeURIComponent(queryString)

$(function(){


    //获取筛选类型数据
    ajax_get(filter_types_api_url+"?__bs="+__cache_brand+"&query="+queryString,function(res){

        if(res.status && res.data.length>0){

            filter_types = res.data;
            var html = template('filter-type-tpl', {data:res.data});

            $('#allnav').html(html)

        }

    },function(){})

    get_product_list();

    $('#clear-keyword').click(function(){
        $('#i_kw').val('');
        get_product_list();
    });

    getBanner()

});

//获取轮播图
function getBanner(){
    $.get('/product/api/get_banner',{},function(res){
        if(res.status==1){
            banner = res.data;
            var str='';
            str+="<div class='swiper-container' id='swiper1'>"
            str+="<div class='swiper-wrapper swiper-no-swiping'>"
            for(var i=0;i<banner.length;i++){
                str+="<div class='swiper-slide'>"
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

//去产品详情
function go_detail(web_id_code){
    window.open('/product/s/'+web_id_code+"?__bs="+__cache_brand);
}

//收藏产品
function collected(i){
    layer.load(1);
    var product_id = product_list[i].web_id_code
    if(product_list[i].collected==false){
        ajax_post(product_collect_api_url,{op:1,aid:product_id},function(res){

            if(res.status){
                var a=product_list[i].count_fav+1;
                product_list[i].count_fav=a;
                $("#collectednumber_"+i).html(product_list[i].count_fav);

                $("#collectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("collected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#collected_"+i).css({"color":"#1582FF"});
                product_list[i].collected=true;

            }else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post(product_collect_api_url,{op:2,aid:product_id},function(res){
            if(res.status){
                var a=product_list[i].count_fav-1;
                product_list[i].count_fav=a;
                $("#collectednumber_"+i).html(a);
                $("#collectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("collected_"+i).className = "iconfont icon-shoucang2";
                $("#collected_"+i).css({"color":"#B7B7B7"})
                product_list[i].collected=false;
            }else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");

        },function(){})

    }

}

//提交搜索筛选
function submit_search(){
    get_product_list();
}

//切换排序的选择
function change_paixu(obj){
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
    //重置页码
    $('#i_page').val(1)

    get_product_list();
}

//切换筛选类型的选择
function change_filter_type(obj,type,value){
    var option_item = $(obj);
    var row = option_item.parent('.nav_text')
    row.find('.nav_t').removeClass('active')
    option_item.addClass('active')
    $('#i_'+type).val(value)
    //重设页数
    $('#i_page').val(1)
    get_product_list();
}

//获取设计方案列表数据
function get_product_list(scrollTop){
    var query_options = {};
    //query_options.pc = $("#i_pc").val();
    query_options.k = $("#i_kw").val();
    query_options.bd = $("#i_bd").val();
    query_options.series = $("#i_series").val();
    query_options.stl = $("#i_stl").val();
    query_options.clr = $("#i_clr").val();
    query_options.tc = $("#i_tc").val();
    query_options.sc = $("#i_sc").val();
    query_options.mip = $("#i_min_price").val();
    query_options.map = $("#i_max_price").val();
    query_options.search = $("#i_search").val();
    query_options.order = $("#i_sort_order").val();
    query_options.page = $("#i_page").val();
    query_options.dlr = dlr_param;
    query_options.__bs = __cache_brand;
    api_url = set_url_query(product_list_api,query_options);

    layer.load(1)
    ajax_get(api_url,function(res){

        layer.closeAll("loading");

        if(res.status){
            $('#data-count').html(res.data.total)
            var current_page = res.data.current_page;
            var total = res.data.total;
            $('#i_page').val(current_page)
            //初始化页码
            init_pager(current_page,total)

            product_list = res.data.data;

            if(res.data.data.length>0){
                var html = template('product-list-tpl', {data:res.data.data});
                $('#product').html(html)
            }else{
                var html = template('product-list-empty');
                $('#product').html(html)
            }

            if($('#i_kw').val()!=''){
                $('#keyword').html('，包含关键字 "'+$('#i_kw').val()+'"');
                $('#clear-keyword').removeClass('hidden');
            }
            else{
                $('#keyword').html('');
                $('#clear-keyword').addClass('hidden');
            }

            if(scrollTop){
                //获取目标元素距离屏幕顶部的高度
                var target_roll_height = $('#product').offset().top;
                //滚动
                $("html,body").animate({scrollTop: target_roll_height}, 300);
            }


        }


    },function(){})
}

function init_pager(nowPage,total){
    // xlPaging.js 使用方法
    var nowpage = $("#pager").paging({
        nowPage: nowPage, // 当前页码
        pageNum: total==0?1:Math.ceil(total / 40), // 总页码
        buttonNum: Math.ceil(total / 40), //要展示的页码数量
        canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
        showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
        callback: function (num) { //回调函数
            var page = num;
            $('#i_page').val(page);
            get_product_list(true);

        }
    });
}

//跳转本页
function href_page(){
    var query_options = {};
    //query_options.pc = $("#i_pc").val();
    query_options.bd = $("#i_bd").val();
    query_options.stl = $("#i_stl").val();
    query_options.series = $("#i_series").val();
    query_options.clr = $("#i_clr").val();
    query_options.tc = $("#i_tc").val();
    query_options.sc = $("#i_sc").val();
    query_options.order = $("#i_sort_order").val();
    query_options.page = $("#i_page").val();
    query_options.mip = $("#i_min_price").val();
    query_options.map = $("#i_max_price").val();
    query_options.search = $("#i_search").val();
    page_url = set_url_query(product_page_api,query_options);
    location.href=page_url
}



//轮播信息
/*
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
})*/
