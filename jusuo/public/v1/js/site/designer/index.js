var designer_list = [];
var filter_types = [];


var queryString = window.location.search.slice(1);
queryString = queryString.split('#')[0];
queryString = encodeURIComponent(queryString)


$(function(){
    //获取筛选类型数据
    ajax_get(filter_types_api_url+"?query="+queryString,function(res){

        if(res.status && res.data.length>0){

            filter_types = res.data;
            var html = template('filter-type-tpl', {data:res.data});

            $('#allnav').html(html)

        }

    },function(){})

    get_designer_list();

    list_nice_designer();

    $('#clear-keyword').click(function(){
        $('#i_kw').val('');
        get_designer_list();
    });

    //获取轮播图
    getBanner()

});

//获取轮播图
function getBanner(){
    $.get('/designer/api/get_banner',{},function(res){
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

//设计师详情
function go_designer_detail(id_code){
    window.open('/designer/s/'+id_code+"?__bs="+__cache_brand)
}

//获取优秀设计师列表
function list_nice_designer() {

    ajax_get(nice_designer_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            if(datas.length<=0){
                $('.designercontainer').hide();
            }else{
                var html = template('nice-designer-tpl', {datas: datas});

                $('#designer').html(html)
            }



        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }


    }, function () {
    })
}

//优秀设计师查看详情
function look_detail(i) {
    $("#d_normal"+i).toggle();
    $("#d_zhezhao"+i).toggle();
}

//关注
function de_guanzhu(i){

    layer.load(1);
    var designer_id = designer_list[i].web_id_code;
    if(designer_list[i].focused==true){
        ajax_post(designer_focus_api_url,{op:2,aid:designer_id},function(res){
            //取消关注
            if(res.status){
                document.getElementById("desi_guanzhu"+i).className = "design_guanzhu";
                $("#desi_guanzhu"+i).html("关注")
                designer_list[i].focused = false
                layer.msg(res.msg)
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
        //关注
        ajax_post(designer_focus_api_url,{op:1,aid:designer_id},function(res){
            if(res.status){
                document.getElementById("desi_guanzhu"+i).className = "design_guanzhu1";
                $("#desi_guanzhu"+i).html("已关注")
                designer_list[i].focused = true

                layer.msg(res.msg)
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
    $('#i_page').val(1);
    get_designer_list();
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
    get_designer_list();
}

//获取设计方案列表数据
function get_designer_list(scrollTop){
    var query_options = {};
    query_options.k = $("#i_kw").val();
    query_options.stl = $("#i_stl").val();
    query_options.sp = $("#i_sp").val();
    query_options.lv = $("#i_lv").val();
    query_options.order = $("#i_sort_order").val();
    query_options.page = $("#i_page").val();
    query_options.__bs = __cache_brand;
    api_url = set_url_query(designer_list_api,query_options);

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

            designer_list = res.data.data;

            if(res.data.data.length>0){
                var html = template('designer-list-tpl', {data:res.data.data});
                $('#designer-list-container').html(html)
            }else{
                var html = template('designer-list-empty');
                $('#designer-list-container').html(html)
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
                var target_roll_height = $('#designer-list-container').offset().top;
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
        pageNum: total==0?1:Math.ceil(total / 12), // 总页码
        buttonNum: Math.ceil(total / 12), //要展示的页码数量
        canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
        showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
        callback: function (num) { //回调函数
            var page = num;
            $('#i_page').val(page);
            get_designer_list(true);

        }
    });
}

//跳转本页
function href_page(){
    var query_options = {};
    query_options.stl = $("#i_stl").val();
    query_options.sp = $("#i_sp").val();
    query_options.lv = $("#i_lv").val();
    query_options.order = $("#i_sort_order").val();
    query_options.page = $("#i_page").val();
    page_url = set_url_query(designer_page_api,query_options);
    location.href=page_url
}


/*
//轮播信息
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
