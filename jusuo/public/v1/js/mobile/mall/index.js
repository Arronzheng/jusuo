var banner = [];
var goodList = [];
var cat1List = [];
var selectedCat1 = 0;
var filterValues = [];
var recommendGoodList = [];

$(function(){

    //首页banner
    getIndexBanner();
    //首页分类
    getCategory();

    $('#filter-content-mask').click(function(){
        $('#filter-content-outer').addClass('hidden');
        $('#filter-content-mask').addClass('hidden');
    });

    $('#filter-content-outer').on('click', '#filter-outer-close',function(){
        $('#filter-content-outer').addClass('hidden');
        $('#filter-content-mask').addClass('hidden');
    });

    $('#top-category-service').click(function(){
        $('#filter-content-outer').removeClass('hidden');
        $('#filter-content-mask').removeClass('hidden');
    });

});

//去商品详情
function goDetail(web_id_code){
    window.location.href = "mall/s/"+web_id_code;
}

//选择分类1
function selectCat1(id){
    $('.top-category').removeClass('active');
    $('#cat1-'+id).addClass('active');
    selectedCat1 = id;
    $('#i_c1').val(id);
    getFilterOptions();
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
        row.find('.nav_t').removeClass('checked')
        option_item.addClass('checked')
        filterValues[type] = [];
        $('#i_'+type).val([])
    }else{
        //20200719改成单选
        /*
        //如果点击的是非不限
        if(option_item.hasClass('active')){
            //如果该选项已选，则取消选择，并在input记录处去掉这个值
            filterValue = filterValues[type]
            removeByValue(filterValue,value)
            filterValues[type] = filterValue;
            $('#i_'+type).val(filterValue)
            option_item.removeClass('checked')
            //如果取消完了，则将不限选上
            if(filterValue.length<=0){
                row.find('.all-option').addClass('checked')
            }else{
                row.find('.all-option').removeClass('checked')

            }
        }else{
            option_item.addClass('checked')
            filterValues[type].push(value)
            $('#i_'+type).val(filterValues[type])
            //重设页数
            $('#i_page').val(1)
            row.find('.all-option').removeClass('checked')

        }*/
        row.find('.nav_t').removeClass('checked');
        option_item.addClass('checked');
        filterValues[type] = [];
        filterValues[type].push(value);
        $('#i_'+type).val(filterValues[type]);
        $('#i_page').val(1);
        row.find('.all-option').removeClass('checked');
    }

    getGoodList();
}

//获取商品列表
function getGoodList(scrollTop){
    var query_options = {};
    query_options.b = $("#i_b").val();
    query_options.r = $("#i_r").val();
    query_options.c1 = $("#i_c1").val();
    query_options.c2 = $("#i_c2").val();
    query_options.order = $("#i_sort_order").val();
    query_options.page = $("#i_page").val();
    api_url = set_url_query("/mall/api/index_good_list",query_options);

    layer.load(1)
    ajax_get(api_url,function(res){

        layer.closeAll("loading");

        if(res.status){
            goodList = res.data.data;

            if(res.data.data.length>0){
                var html = template('good-list-tpl', {data:res.data.data});
                $('#product-list-outer').html(html)
            }else{
                var html = template('good-list-empty');
                $('#product-list-outer').html(html)
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

            $('#filter-content-outer').html(html)

            //刷新首页商品数据
            getGoodList();

        }

    },function(){})
}

//分类
function getCategory(){
    ajax_get("/mall/api/index_cat1s",function(res){

        if(res.status && res.data.length>0){

            cat1List = res.data;
            var html = template('cat1-list-tpl', {data:res.data});
            $('#top-category-content').html(html)
            //将第一个一级分类默认选中
            selectCat1(cat1List[0].id);

        }

    },function(){})
}

//banner
function getIndexBanner(){
    $.get('mall/api/index_banner',{},function(res){
        if(res.status==1){
            banner = res.data;
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

            $('.bannerBox .swiper-pagination').on('click','span',function(){
                var index = $(this).index() + 2;
                mySwiper1.slideTo(index-1, 500, false);//切换到第一个slide，速度为1秒
            })
        }
    });
}
