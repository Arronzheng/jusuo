var prefix = '/dealer/';
var banner=[];
var category=[];
var brand=[];
var filter=[];
var dealer=[];
var working=false;

filter['b'] =
    filter['c'] =
        filter['sort'] =
            filter['direction'] = 0;


function getDealerByCity(){
    layer.load(1);
    $.get('/dealer/get_hot_dealer',{__bs:__cache_brand,count:6}, function(res) {
        if (res.status == 1) {
            dealer = res.data.dealer;
            var str = '';
            var i;
            for (i = 0; i < dealer.length; i++) {
                str+="<div class='companylogo' id='cclogo_"+i+"' data-attr='"+dealer[i].web_id_code+"'>"+"<div class='name'>"+dealer[i].name+"</div></div>"
            }
            $("#companylogo1").append(str);
            for (i = 0; i <dealer.length; i++) {
                $("#cclogo_"+i).css({"background-image":"url('"+dealer[i].url_avatar+"')"});
            }
            bindClickDealerLogo();
        }
        layer.closeAll('loading');
    });
}

function bindClickDealerLogo(){
    $('#companylogo1').on('click', '.companylogo', function(){
        goToDealer($(this).attr('data-attr'));
    });
}

function getDealerFilter() {
    $.get(prefix + 'get_filter', {__bs: __cache_brand}, function (res) {
        if (res.status == 1) {
            var filter = res.data;
            var i, j, str = '', parent_id, content;
            for (i = 0; i < filter.length; i++) {
                content = filter[i].content;
                if(content.length>0) {
                    parent_id = filter[i].parent_id;
                    parent_key = filter[i].parent_key;
                    str += "<div class='nav_container' id='nav-container-'" + parent_id + ">";
                    str += "<div class='nav_span1'>";
                    str += "<span class='nav_span'>" + filter[i].parent_name + "</span>";
                    str += "</div>";
                    str += "<div class='pnav_text' id='nav_text" + parent_id + "'>";
                    str += "<span class='nav_t active' id='nav_" + parent_id + "_0' data-id='0' data-key='" + parent_key + "'>";
                    str += '不限';
                    str += "</span>";
                    for (j = 0; j < content.length; j++) {
                        str += "<span class='nav_t' id='nav_" + parent_id + "_" + content[j].id + "' data-id='" + content[j].id + "' data-key='" + parent_key + "'>";
                        str += content[j].name;
                        str += "</span>";
                    }
                    str += "</div>";
                    //str += "<span class='nav_lookall' data-attr='" + parent_id + "'>展开></span>";
                    str += "</div>";
                }
            }
            $("#allnav").html(str);
            bindClickFilter();
            getDealerSorter();
        }
    });
}

function getDealerSorter() {
    $.get(prefix + 'get_sorter', {__bs: __cache_brand}, function (res) {
        if (res.status == 1) {
            var sorter = res.data;
            var i, str = '', content;
            for (i = 0; i < sorter.length; i++) {
                str+="<div class='sorter' data-attr='"+sorter[i].attr+"'>";
                str+="<span class='paixu_label' id='paixu_"+sorter[i].attr+"'>"+sorter[i].text+"</span>";
                str+="<div class='up_down'>";
                str+="<div class='up'>";
                str+="<span class='iconfont icon-paixu' id='paixu1_"+sorter[i].attr+"'></span>";
                str+="</div>";
                str+="<div class='down'>";
                str+="<span class='iconfont icon-paixu-1' id='paixu2_"+sorter[i].attr+"'></span>";
                str+="</div>";
                str+="</div>";
                str+="</div>";
            }
            $("#paixu").html(str);
            bindClickSorter();
            getDealerByFilter();
        }
    });
}

function bindClickSorter(){
    $('#paixu').on('click','.sorter',function(){
        if(working)
            return;
        var value = $(this).attr('data-attr');
        if(filter['sort']==value){
            filter['direction'] = (filter['direction']==0?1:0);
        }
        else {
            filter['sort'] = value;
            filter['direction'] = 0;
        }
        getDealerByFilter();
    })
}

function bindClickFilter(){
    $('#allnav').on('click','.nav_t',function(){
        if(working)
            return;
        var value = $(this).attr('data-id');
        var key = $(this).attr('data-key');
        filter[key] = value;
        getDealerByFilter();
    })
}

function bindClickDealerProduct(){
    $('#de_fangan').on('click','.dr_container',function(e){
        goToProduct($(this).attr('data-attr'));
        e.stopPropagation();
    });
}

function bindClickDealerDiv(){
    $('#de_fangan').on('click','.company_item',function(){
        goToDealer($(this).attr('data-attr'));
    });
}

function getDealerByFilter(){
    if(working)
        return;
    working = true;
    var params = {
        __bs: __cache_brand,
        b: filter['b'],
        c: filter['c'],
        sort: filter['sort'],
        direction: filter['direction']
    };
    layer.load(1);
    $.get(prefix + 'get_dealer_by_filter', params, function (res) {
        if (res.status == 1) {
            dealer = res.data.dealer;
            var str = '';
            var i,j,product;
            for(i=0;i<dealer.length;i++){
                str+="<div class='company_item' data-attr='"+dealer[i].web_id_code+"'>";
                str+="<div class='company_head'>";
                str+="<div class='company_logo' id='company_logo"+i+"'>"+"</div>";
                str+="<div class='company_middle'>";
                str+="<div class='cm_head'>";
                str+="<div class='cm_name'>"+dealer[i].name+"</div>";
                str+="</div>";
                str+="<div class='cm_middle'>";
                str+="<span class='cm_able'>"+"经营类别："+"</span>";
                str+="<div class='cm_label'>"+dealer[i].product_category+"</div>"
                str+="</div>";
                str+="<div class='cm_middle'>";
                str+="<span class='cm_able1'>"+"商家介绍："+dealer[i].self_introduction+"</span>";
                str+="</div>";
                str+="<div class='de_detail'>"
                str+="<div class='dm_fan'>";
                str+="<div class='dm_fanspan'>"+dealer[i].count_designer+"</div>"
                str+="<div class='dm_fanspan1'>"+"设计师"+"</div>"
                str+="</div>";
                str+="<div class='dm_line'>"+"</div>"
                str+="<div class='dm_fan1'>";
                str+="<div class='dm_fanspan'>"+dealer[i].count_album+"</div>"
                str+="<div class='dm_fanspan1'>"+"设计方案"+"</div>"
                str+="</div>";
                str+="<div class='dm_line1'>"+"</div>"
                str+="<div class='dm_fan2'>";
                str+="<div class='dm_fanspan'>"+dealer[i].count_fav+"</div>"
                str+="<div class='dm_fanspan1'>"+"粉丝"+"</div>"
                str+="</div>";
                str+="</div>";
                str+="</div>";
                str+="<div class='company_right'>"+"查看主页 >"+"</div>"
                str+="</div>";
                str+="<div class='company_tail'>";
                str+="<div class='de_right'>";
                product = dealer[i].product;
                for(j=0;j<product.length&&j<4;j++){
                    str+="<div class='dr_container' data-attr='"+product[j].web_id_code+"'>";
                    str+="<div class='dr_image' id='dr_image"+i+"_"+j+"'>"+"</div>";
                    str+="<div class='dr_text'>";
                    str+="<div class='dr_text2'>"+product[j].name+"</div>";
                    str+="<div class='dr_text3'>"+product[j].price+"</div>";
                    str+="</div>";
                    str+="</div>";
                }
                str+="</div>";
                str+="</div>";
                str+="</div>";
            }
            $("#de_fangan").html(str);
            for (i=0;i<dealer.length;i++) {
                $("#company_logo"+i).css({"background-image":"url('"+dealer[i].url_avatar+"')"});
                product = dealer[i].product;
                for(j=0;j<product.length&&j<4;j++){
                    $("#dr_image"+i+"_"+j).css({"background-image":"url('"+product[j].photo_product+"')"});
                }
            }
            $('#result-count').html(res.data.total);
            refreshFilterActive(res.data.param);
            bindClickDealerDiv();
            bindClickDealerProduct();
        }
        layer.closeAll('loading');
        working = false;
    });
}

function refreshFilterActive(param){
    var brand = param.brand;
    var category = param.category;
    var sort = param.sort;
    var direction = param.direction;
    $('.nav_t').removeClass('active');
    $('#nav_1_'+category).addClass('active');
    $('#nav_2_'+brand).addClass('active');
    $('.paixu_label, .up_down .iconfont').removeClass('active');
    $('#paixu_'+sort).addClass('active');
    var tag = (direction==0?1:2);
    $('#paixu'+tag+'_'+sort).addClass('active');
}

function init(){
    //getDealerFilter();
    getDealerSorter();
    getDealerByCity();
    getDealerBanner()
}

$(document).ready(function(){
    init();
});

//获取轮播图
function getDealerBanner(){
    $.get('/dealer/get_banner',{},function(res){
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

var data={
    "list":[
        {
            "span":"经营类别：",
            "nav":["不限","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖"
                ,"卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖"],
            "navnumber":0
        },
        {
            "span":"品牌：",
            "nav":["不限","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO"
                ,"鹰派","TOTO","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO"],
            "navnumber":0
        }
    ]
};

var paixu={
    "list":[

    ]
};

var companyitem=[{"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
    "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
        {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
        {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
        {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
    {"logo":"/v1/images/site/material_xq/b1.png","name":"TOTO卫浴","ablearea":["卫浴"],"designer":42,"fangan":63,"fans":42,"chanpin":"马桶、洗手盆、镜子、浴缸、马桶、洗手盆、镜子、浴缸...",
        "design":[{"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999},
            {"images":"/v1/images/site/cpk/7.png","name":"U型白色马桶","price":999}]},
        ]
var open=false;
//友情链接
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];

//排序html
$(function(e) {
    var str = '';
    for(var i=0;i<paixu.list.length;i++){
        if(i==0){
            str+="<span class='paixu_label active1' id='paixu_"+i+"' onclick='change_paixu("+i+")'>"+paixu.list[i].label+"</span>"
            str+="<div class='up_down' onclick='change_paixu("+i+")'>"
            str+="<div class='up'>"
            if(paixu.list[i].up==false){
                str+="<span class='iconfont icon-paixu' id='paixu1_"+i+"' style='color:#B7B7B7;font-size:14px;'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-paixu' id='paixu1_"+i+"' style='color:#1582FF;font-size:14px;'>"+"</span>";
            }
            str+="</div>"
            str+="<div class='down'>"
            if(paixu.list[i].down==false){
                str+="<span class='iconfont icon-paixu-1' id='paixu2_"+i+"' style='color:#B7B7B7;font-size:14px;'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-paixu-1' id='paixu2_"+i+"' style='color:#1582FF;font-size:14px;'>"+"</span>";
            }
            str+="</div>"
            str+="</div>"
        }else{
            str+="<span class='paixu_label' id='paixu_"+i+"' onclick='change_paixu("+i+")'>"+paixu.list[i].label+"</span>"
            str+="<div class='up_down' onclick='change_paixu("+i+")'>"
            str+="<div class='up'>"
            if(paixu.list[i].up==false){
                str+="<span class='iconfont icon-paixu' id='paixu1_"+i+"' style='color:#B7B7B7;font-size:14px;'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-paixu' id='paixu1_"+i+"' style='color:#1582FF;font-size:14px;'>"+"</span>";
            }
            str+="</div>"
            str+="<div class='down'>"
            if(paixu.list[i].down==false){
                str+="<span class='iconfont icon-paixu-1' id='paixu2_"+i+"' style='color:#B7B7B7;font-size:14px;'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-paixu-1' id='paixu2_"+i+"' style='color:#1582FF;font-size:14px;'>"+"</span>";
            }
            str+="</div>"
            str+="</div>"
        }

    }

    $("#paixu").append(str);
});

//切换导航的选择
function change_swiper(i,j){
    console.log(i)
    console.log(j)
    var b=0;
    document.getElementById("nav_"+i+"_"+j).className = "nav_t1";
    document.getElementById("nav_"+i+"_"+data.list[i].navnumber).className = "nav_t";
    data.list[i].navnumber=j
    // for (b = 0; b < data.list[i].nav.length; b++) {
    //     if(b==j){
    //         document.getElementById("nav_"+i+"_"+b).className = "nav_t1";
    //     }else{
    //         document.getElementById("nav_"+i+"_"+b).className = "nav_t";
    //     }
    // }
}
//切换排序的选择
function change_paixu(i){
    if(paixu.list[i].active==true){
        paixu.list[i].up=!paixu.list[i].up
        paixu.list[i].down=!paixu.list[i].down
        if(paixu.list[i].up==true){
            $("#paixu1_"+i).css({"color":"#1582FF"});
        }else{
            $("#paixu1_"+i).css({"color":"#B7B7B7"});
        }
        if(paixu.list[i].down==true){
            $("#paixu2_"+i).css({"color":"#1582FF"});
        }else{
            $("#paixu2_"+i).css({"color":"#B7B7B7"});
        }
    }else{
        for(var b=0;b<paixu.list.length;b++){
            if(b==i){
                paixu.list[b].active=true
                paixu.list[b].up=true
                paixu.list[b].down=false
                $("#paixu_"+b).css({"color":"#1582FF"});
                $("#paixu1_"+b).css({"color":"#1582FF"});
                $("#paixu2_"+b).css({"color":"#B7B7B7"});
            }else{
                paixu.list[b].active=false
                paixu.list[b].up=false
                paixu.list[b].down=false
                $("#paixu_"+b).css({"color":"#333333"});
                $("#paixu1_"+b).css({"color":"#B7B7B7"});
                $("#paixu2_"+b).css({"color":"#B7B7B7"});
            }
        }
    }
}
//底部导航的下拉和上拉
function xiala(){
    $(".m_middlet").html("");
    var str = '';
    for (var i = 0; i < bottom.length; i++) {
        str+="<div class='qmh'>"+bottom[i]+"</div>"
        if((i+1)%9!=0){
            str+="<div class='qmhline'>"+"</div>"
        }
    }
    $("#bottom_logo").append(str);
    open=true;
    $(".xiala").css({"cursor": "not-allowed"});
    $(".xiala1").css({"cursor": "pointer"});
}
function shangla(){
    $(".m_middlet").html("");
    var str = '';
    for (var i = 0; i < 9; i++) {
        str+="<div class='qmh'>"+bottom[i]+"</div>"
        if((i+1)%9!=0){
            str+="<div class='qmhline'>"+"</div>"
        }
    }
    $("#bottom_logo").append(str);
    open=false;
    $(".xiala").css({"cursor": "pointer"});
    $(".xiala1").css({"cursor": "not-allowed"});
}
function lookall(i){
    $("#nav_text"+i).html("");
    var str='';
    for(var j=0;j<data.list[i].nav.length;j++){
        if(j==data.list[i].navnumber){
            str+="<span class='nav_t active' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
        }else{
            str+="<span class='nav_t' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
        }
        str+=data.list[i].nav[j];
        str+="</span>"
    }
    str+="<span class='nav_lookall1' onclick='lookall1("+i+")' style='display: none;' id='up"+i+"'>"+"收起>"+"</span>"
    $("#nav_text"+i).append(str);
    $("#up"+i).show().addClass("show");
    $("#lookdown"+i).hide().removeClass("show");
    document.getElementById("nav_text"+i).className = "nav_text1";
}
function lookall1(i){
    $("#nav_text"+i).html("");
    var str='';
    var width=0;
    if(i==0){
        for(var j=0;j<data.list[i].nav.length;j++){
            width=width+data.list[i].nav[j].length*16+30;
            if(width>1890){
                break;
            }else{
                if(j==data.list[i].navnumber){
                    str+="<span class='nav_t active' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
                }else{
                    str+="<span class='nav_t' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
                }
                str+=data.list[i].nav[j];
                str+="</span>"
            }
        }
        if(width>1890){
            str+="<span class='nav_lookall' onclick='lookall("+i+")' id='lookdown"+i+"'>"+"展开>"+"</span>"
        }
    }else{
        for(var j=0;j<data.list[i].nav.length;j++){
            width=width+data.list[i].nav[j].length*16+30;
            if(width>1010){
                break;
            }else{
                if(j==data.list[i].navnumber){
                    str+="<span class='nav_t active' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
                }else{
                    str+="<span class='nav_t' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
                }
                str+=data.list[i].nav[j];
                str+="</span>"
            }
        }
        if(width>1010){
            str+="<span class='nav_lookall' onclick='lookall("+i+")' id='lookdown"+i+"'>"+"展开>"+"</span>"
        }
    }

    $("#nav_text"+i).append(str);
    //$("#lookdown"+i).show().addClass("show");
    $("#up"+i).hide().removeClass("show");
    document.getElementById("nav_text"+i).className = "nav_text";
}