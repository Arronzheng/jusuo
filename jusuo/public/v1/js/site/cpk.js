var data={
    "list":[
        {
            "span":"经营类别：",
            "nav":["不限","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴"
                ,"瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴" ,"瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴","瓷砖","卫浴"],
            "navnumber":0
        },{
            "span":"品牌：",
            "nav":["不限","鹰派","TOTO","鹰派","TOTO","鹰派","TOTO"],
            "navnumber":0
        }
    ]
};
var zuopindetail={
    "images":["images/index/cookingroom_3.jpg","images/index/dinningroom.jpg","images/index/dinningroom_4.jpg"],
}
var paixu={
    "list":[
        {"label":"综合", "up":true, "down":false, "active":true},
        {"label":"人气", "up":false, "down":false, "active":false},
        {"label":"浏览量", "up":false, "down":false, "active":false},
        {"label":"最新", "up":false, "down":false, "active":false},
        {"label":"价格", "up":false, "down":false, "active":false}
    ]
};
var productdata={"list":[{"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"马桶马桶马桶马桶马桶麻烦玛法","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false},
        {"images":"images/cpk/7.png","position":"南海","collectionnumber":88,"name":"U型马桶-MF2142","company":"TOTO南海店","price":999,"collect":false}
    ]};
var open=false;
//友情链接
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];

//推荐作品的轮播
$(function(e) {
    var str='';
    str+="<div class='swiper-container' id='swiper1'>"
    str+="<div class='swiper-wrapper swiper-no-swiping'>"
    for(var i=0;i<zuopindetail.images.length;i++){
        str+="<div class='swiper-slide'>"
        str+="<img src='"+zuopindetail.images[i]+"'/>"
        str+="</div>"
    }
    str+="</div>"
    str+="</div>"
    str+="<div class='left'>"+"<img src='images/index/bannerL.png'/></div>"
    str+="<div class='right'><img src='images/index/bannerR.png'/></div>"
    $("#swipers").append(str);
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
    $(".left").click(function(){
        mySwiper1.slidePrev();
    })
    $(".right").click(function(){
        mySwiper1.slideNext();
    })
    $('.swiper-pagination').on('click','span',function(){
        var index = $(this).index() + 2;
        mySwiper1.slideTo(index-1, 500, false);//切换到第一个slide，速度为1秒
    })
})

//导航html
$(function(e) {
    // if (window.innerWidth){
    //     var winWidth = window.innerWidth;
    // } else if ((document.body) && (document.body.clientWidth)){
    //     var winWidth = document.body.clientWidth;
    // }
    // $(".bottom").css({"width":winWidth,"overflow":"hidden"});
    var str = '';
    for(var i=0;i<data.list.length;i++){
        str+="<div class='nav_container'>";
        str+="<div class='nav_span1'>";
        str+="<span class='nav_span'>"+data.list[i].span+"</span>"
        str+="</div>"
        var width=0;
        str+="<div class='nav_text' id='nav_text"+i+"'>";
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
        console.log('end')
        if(width>1890){
            console.log('ss')
            str+="<span class='nav_lookall' onclick='lookall("+i+")' id='lookdown"+i+"'>"+"显示全部>"+"</span>"
        }


        str+="</div>"
        str+="</div>"
    }

    $("#allnav").append(str);
});

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
//底部导航的千名汇
$(function(e) {
    var str = '';
    for (var i = 0; i < 9; i++) {
        str+="<div class='qmh'>"+bottom[i]+"</div>"
        if((i+1)%9!=0){
            str+="<div class='qmhline'>"+"</div>"
        }
    }
    $("#bottom_logo").append(str);
})
$(function(e) {
    if(open==false){
        $(".xiala1").css({"cursor": "not-allowed"});
        $(".xiala").css({"cursor": "pointer"});
    }else{
        $(".xiala1").css({"cursor": "pointer"});
        $(".xiala").css({"cursor": "not-allowed"});
    }
})

// xlPaging.js 使用方法
var nowpage = $("#page").paging({
    nowPage: 1, // 当前页码
    pageNum: Math.ceil(productdata.list.length / 12), // 总页码
    buttonNum: Math.ceil(productdata.list.length / 12), //要展示的页码数量
    canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
    showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
    callback: function (num) { //回调函数
        console.log('sss'+num);
        //更多产品
        // $(function(e) {
        $("#produ").html("");
        var txt="<div class='productcontainer' id='product'></div>"
        $("#produ").append(txt);
        var total=Math.min(num*12,productdata.list.length)
        console.log(num+'sss'+total)
        moreproduct((num-1)*12,total);
    }
});
console.log(nowpage.options.nowPage)
//更多产品
$(function(e) {
    //热门产品导航html
    moreproduct(nowpage.options.nowPage-1,nowpage.options.nowPage*12)
})
//切换导航的选择
function change_swiper(i,j){
    console.log(i)
    var b=0;
    data.list[i].navnumber=j
    for (b = 0; b < data.list[i].nav.length; b++) {
        if(b==j){
            document.getElementById("nav_"+i+"_"+b).className = "nav_t1";
        }else{
            document.getElementById("nav_"+i+"_"+b).className = "nav_t";
        }
    }
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
//更多产品html
function moreproduct(begin,end){
    var str1 = '';
    for (var i = begin; i < end; i++) {
        str1+="<div class='productview'>"
        str1+="<div class='pimage' id='primage_"+i+"'>"+"</div>";
        str1+="<div class='productname'>"+productdata.list[i].name+"</div>";
        str1+="<div class='priceview'>";
        str1+="<span class='pricetxt'>"+"¥"+productdata.list[i].price+"</span>"
        str1+="<div class='lookview'>";
        str1+="<span class='iconfont icon-shoucang2' id='collected_"+i+"' style='color:#B7B7B7;' onclick='collected("+i+")'>"+"</span>";
        str1+="<span class='looknumber' id='collectednumber_"+i+"' onclick='collected("+i+")'>"+productdata.list[i].collectionnumber+"</span>";
        str1+="</div>";
        str1+="</div>";
        str1+="<div class='details'>";
        str1+="<span class='companytext'>"+productdata.list[i].company+"</span>"
        str1+="<div class='lookview'>";
        str1+="<div class='dingwei'>"+"</div>";
        str1+="<span class='areatxt'>"+productdata.list[i].position+"</span>";
        str1+="</div>";
        str1+="</div>"
        str1+="</div>";
    }
    $("#product").append(str1);
    for (var i = begin; i < end; i++) {
        $("#primage_"+i).css({"background-image":"url('"+productdata.list[i].images+"')"});
    }
}
//设计方案的收藏
function collected(i){
    console.log('sss'+i)
    if(productdata.list[i].collect==false){
        var a=productdata.list[i].collectionnumber+1;
        productdata.list[i].collectionnumber=a;
        $("#collectednumber_"+i).html(productdata.list[i].collectionnumber);

        $("#collectednumber_"+i).css({"color":"#1582FF"});
        document.getElementById("collected_"+i).className = "iconfont icon-buoumaotubiao44";
        $("#collected_"+i).css({"color":"#1582FF"});
        productdata.list[i].collect=true;
    }else{
        var a=productdata.list[i].collectionnumber-1;
        productdata.list[i].collectionnumber=a;
        $("#collectednumber_"+i).html(productdata.list[i].collectionnumber);
        $("#collectednumber_"+i).css({"color":"#B7B7B7"});
        document.getElementById("collected_"+i).className = "iconfont icon-shoucang2";
        $("#collected_"+i).css({"color":"#B7B7B7"})
        productdata.list[i].collect=false;
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
        console.log('ss')
        str+="<span class='nav_lookall' onclick='lookall("+i+")' id='lookdown"+i+"'>"+"显示全部>"+"</span>"
    }
    $("#nav_text"+i).append(str);
    //$("#lookdown"+i).show().addClass("show");
    $("#up"+i).hide().removeClass("show");
    document.getElementById("nav_text"+i).className = "nav_text";
}