var company={
    "image":"images/designer_xq/1.png",
    "perimage":"images/company_xq/1.png",
    "name":"良工装饰有限公司",
    "identity":true,
    "xingji":4,
    "designer":10,
    "fans":63,
    "design":99,
    "ablearea":"家居住宅、别墅豪宅、会所",
    "style":"简约现代、新中式",
    "area":"顺德区、南海区",
    "promise":"免费量房、专业设计、售后保障",
    "guanzhu":false
};
var zuopindetail={
    "images":["images/index/cookingroom_3.jpg","images/index/dinningroom.jpg","images/index/dinningroom_4.jpg"],
    "title":"现代简约两居",
    "detail":"大都市繁华而拥挤，现代大多年轻人背负着工作生活的双重压力，想要一所大房子的愿望离现实太遥远，但是小房子也同样能给你大房子的惊喜。",
    "personname":"周杰伦",
    "perimage":"images/designer/1.png"
}
var company_profile=["良工装饰公司是一家集设计与施工为一体装饰公司，主要经营住宅装饰装修和酒店、会所、商场、展厅、办公楼等公共空间设计和施工。" ,
" 公司以雄厚的设计功底和精湛的施工技术为基础，集设计师和经验丰富的项目经理,及经过专业培训，技术过硬的施工团队，为客户提供人性化,专业化,个性化的装修服务。" ,
" 公司以\"美居设计，勇攀高峰\"为企业的核心价值观，致力促进装饰设计行业发展，以提高人居品质为目标，积极进取，勇攀设计高峰!"]
var product_swiper=["不限","简约现代","新中式","北欧","欧式"]
//相关方案
var gproductdata1={"list":[{"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"}
    ]};
var design_team=[{"name":"周杰伦","image":"images/designer/1.png","fangan":53,"experience":"金牌"},
    {"name":"周杰伦","image":"images/designer/1.png","fangan":53,"experience":"金牌"},
    {"name":"周杰伦","image":"images/designer/1.png","fangan":53,"experience":"金牌"},
    {"name":"周杰伦","image":"images/designer/1.png","fangan":53,"experience":"金牌"},
    {"name":"周杰伦","image":"images/designer/1.png","fangan":53,"experience":"金牌"},
    {"name":"周杰伦","image":"images/designer/1.png","fangan":53,"experience":"金牌"}];
var conpany_qua=[{"image":"images/company_xq/2.png","name":"建筑资质证书"},{"image":"images/company_xq/2.png","name":"建筑资质证书"},{"image":"images/company_xq/2.png","name":"建筑资质证书"},{"image":"images/company_xq/2.png","name":"建筑资质证书"}];
var daohang={"lng": "116.412222", "lat":"39.912345","name":"良工装饰有限公司","addr":"广东省佛山市南海区桂澜北路28号","tele":"15015298765"}
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];
var open=false;

//head html
$(function(e) {
    var str = '';
    str+="<div class='h_image' id='h_image'>"
    str+="<div class='person_image' id='person_image'>"+"</div>"
    str+="<div class='perback'>"+"</div>"
    str+="<div class='person'>"
    str+="<span class='personnanme'>"+company.name+"</span>"
    if(company.identity==true)
    {
        str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-left:10px;'>"+"</span>";
    }else{
        str+="<span class='iconfont icon-shimingrenzheng' style='color:#D2D1D1;font-size:16px;margin-left:10px;'>"+"</span>";
    }
    str+="<div class='xingji'>";
    str+="<div class='xingjiq'>";
    str+="<span class='zuanshi' id='xingji'>"+"</span>";
    str+="</div>";
    str+="<span class='xingjinumber'>"+company.xingji+"级"+"</span>";
    str+="</div>"
    str+="</div>"
    str+="</div>"
    str+="<div class='h_bottom'>"
    str+="<div class='h_left'>"
    str+="<div class='h_detail'>";
    str+="<div class='h_block1'>";
    str+="<div class='h_blockspan'>"+company.designer+"</div>"
    str+="<div class='h_blockspan1'>"+"设计师"+"</div>"
    str+="</div>"
    str+="<div class='line'>"+"</div>"
    str+="<div class='h_block'>";
    str+="<div class='h_blockspan'>"+company.design+"</div>"
    str+="<div class='h_blockspan1'>"+"设计方案"+"</div>"
    str+="</div>"
    str+="<div class='line'>"+"</div>"
    str+="<div class='h_block'>";
    str+="<div class='h_blockspan'>"+company.fans+"</div>"
    str+="<div class='h_blockspan1'>"+"粉丝"+"</div>"
    str+="</div>"
    str+="</div>"
    str+="<div class='moredetail'>"
    str+="<div class='moredetailspan'>"+"擅长风格"+"</div>"
    str+="<div class='moredetailspan1'>"+company.style+"</div>"
    str+="</div>"
    str+="<div class='moredetail1'>"
    str+="<div class='moredetailspan'>"+"擅长空间"+"</div>"
    str+="<div class='moredetailspan1'>"+company.ablearea+"</div>"
    str+="</div>"
    str+="<div class='moredetail1'>"
    str+="<div class='moredetailspan'>"+"服务区域"+"</div>"
    str+="<div class='moredetailspan1'>"+company.area+"</div>"
    str+="</div>"
    str+="<div class='moredetail1'>"
    str+="<div class='moredetailspan'>"+"服务承诺"+"</div>"
    str+="<div class='moredetailspan1'>"+company.promise+"</div>"
    str+="</div>"
    str+="</div>"
    str+="<div class='h_right'>"
    if(company.guanzhu==false){
        str+="<div class='guanzhubotton' onclick='guanzhu()'>"+"关注"+"</div>"
    }else{
        str+="<div class='guanzhubotton' onclick='guanzhu()'>"+"已关注"+"</div>"
    }
    str+="<div class='guanzhubotton1'>"+"分享"+"</div>"
    str+="</div>";
    str+="</div>";
    $("#head").append(str);
    $("#h_image").css({"background-image":"url('"+company.image+"')"});
    $("#person_image").css({"background-image":"url('"+company.perimage+"')"});
});
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

//推荐作品的下面介绍部分
$(function(e) {
    var str='';
    str+="<div class='zp_left'>"
    str+="<div class='zp_title'>"+zuopindetail.title+"</div>";
    str+="<div class='zp_detail'>"+zuopindetail.detail+"</div>"
    str+="</div>"
    str+="<div class='zp_line'>"+"</div>"
    str+="<div class='zp_person'>"
    str+="<div class='zp_pimage' id='zp_pimage'>"+"</div>"
    str+="<div class='zp_pname'>"+zuopindetail.personname+"</div>"
    str+="</div>"
    $("#zuopin_detail").append(str);
    $("#zp_pimage").css({"background-image":"url('"+zuopindetail.perimage+"')"});
});
//公司简介html
$(function(e) {
    var str = '';
    str+="<div class='melogo'>";
    str+="<span class='mespan'>"+"公司简介"+"</span>"
    str+="<div class='triangle-right'>"+"</div>";
    str+="</div>";
    for(var i=0;i<company_profile.length;i++){
        str+="<div class='pro_text'>"+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+company_profile[i]+"</div>"
    }
    $("#aboutme").append(str);
});
//设计案例html
$(function(e) {
    var str = '';
    str+="<div class='m_swiper'>"
    for (var i = 0; i < product_swiper.length; i++) {
        if(i==0){
            str+="<span id='p_swiper"+i+"' class='m_swiper_span1' onclick='change_pswiper("+i+")'>"+product_swiper[i]+"</span>"
        }else{
            str+="<span id='p_swiper"+i+"' class='m_swiper_span' onclick='change_pswiper("+i+")'>"+product_swiper[i]+"</span>"
        }
    }
    str+="</div>"
    $("#product_swiper").append(str);
    morefangan(nowpage.options.nowPage-1,nowpage.options.nowPage*6)
});

//设计团队
$(function(e) {
    var str = '';
    var length=Math.min(5,design_team.length);
    for(var i=0;i<length;i++){
        str+="<div class='team_content'>";
        str+="<div class='team_image' id='team_image"+i+"'>"+"</div>";
        if(design_team[i].experience=="金牌"){
            str+="<div class='jinpai'>"+"</div>"
        }else if(design_team[i].experience=="资深"){
            str+="<div class='zishen'>"+"</div>"
        }else if(design_team[i].experience=="新手"){
            str+="<div class='xinshou'>"+"</div>"
        }else if(design_team[i].experience=="见习"){
            str+="<div class='jianxi'>"+"</div>"
        }else if(design_team[i].experience=="专业"){
            str+="<div class='zhuanye'>"+"</div>"
        }
        str+="<div class='team_name'>"+design_team[i].name+"</div>";
        str+="<div class='team_fangan'>"+design_team[i].fangan+"套方案"+"</div>"
        str+="</div>";
    }
    $("#design_team").append(str);
    for(var i=0;i<length;i++){
        $("#team_image"+i).css({"background-image":"url('"+design_team[i].image+"')"});
    }
    // 时间轴导航栏
    // //为了页面滚动对应导航
    var mainTopArr = new Array();
    for(var i=0;i<4;i++){
        const top = $("#b" + i).offset().top-20;
        mainTopArr.push(top);
        console.log(top);
    }

    $(window).scroll(function(){
        var scrollTop = $(this).scrollTop();
        console.log(scrollTop)
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

});
//公司资质
$(function(e) {
    var str = '';
    var length=Math.min(4,conpany_qua.length);
    for(var i=0;i<length;i++){
        str+="<div class='qua'>";
        str+="<div class='qua_image' id='qua_image"+i+"'>"+"</div>";
        str+="<div class='qua_name'>"+conpany_qua[i].name+"</div>";
        str+="</div>";
    }
    $("#qua_content").append(str);
    for(var i=0;i<length;i++){
        $("#qua_image"+i).css({"background-image":"url('"+conpany_qua[i].image+"')"});
    }

});
//底部导航的千名汇
$(function(e) {
    var str = '';
    var length=Math.min(9,bottom.length);
    for (var i = 0; i < length; i++) {
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


// xlPaging.js 分页使用方法
var nowpage = $("#page").paging({
    nowPage: 1, // 当前页码
    pageNum: Math.ceil(gproductdata1.list.length / 6), // 总页码
    buttonNum: Math.ceil(gproductdata1.list.length / 6), //要展示的页码数量
    canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
    showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
    callback: function (num) { //回调函数
        console.log('sss'+num);
        //更多产品
        // $(function(e) {
        $("#produ").html("");
        var txt="<div class='productcontainer' id='fangan'></div>"
        $("#produ").append(txt);
        var total=Math.min(num*6,gproductdata1.list.length)
        console.log(num+'sss'+total)
        morefangan((num-1)*6,total);
    }
});
function morefangan(begin,end){
    var str1 = '';
    for (var i = begin; i < end; i++) {
        str1+="<div class='g_productview'>"
        str1+="<div class='g_imageview'>"
        str1+="<div class='g_areaview1'>"+"</div>"
        str1+="<div class='g_areaview1t'>"
        str1+="<label class='g_positext1'>"+gproductdata1.list[i].area+"</label>"
        str1+="</div>"
        str1+="<div class='g_pimage' id='pimage_"+i+"'>"+"</div>"
        str1+="<div class='g_nametext'>"+gproductdata1.list[i].name+"</div>"
        str1+="<div class='g_deview'>"
        str1+="<div class='g_perimage' id='perimage_"+i+"'>"+"</div>"
        str1+="<label class='g_pertext'>"+gproductdata1.list[i].personname+"</label>"
        str1+="<div class='g_lookview'>"
        //str1+="<span class='iconfont icon-liulan-copy' style='color:#777777;'></span>"
        str1+="<img src='./images/sjfa_xq/相似方案-浏览量icon.png' class='g_xiconimage'/>"
        str1+="<label class='g_viewtext'>"+gproductdata1.list[i].looknumber+"</label>"
        str1+="</div>"
        str1+="</div>"
        str1+="</div>"
        str1+="</div>"
    }
    $("#fangan").append(str1);
    for (var i = begin; i < end; i++) {
        $("#pimage_"+i).css({"background-image":"url('"+gproductdata1.list[i].images+"')"});
        $("#perimage_"+i).css({"background-image":"url('"+gproductdata1.list[i].personimage+"')"});
    }
}

// 时间轴导航栏
$('#sidenav li:first').siblings().find('i').addClass('dark'); //当前选中样式
var links = $('#sidenav li').find('a'); //赋予变量links 为元素a
links.each(function(i) { //遍历所有元素a
    $(this).click(function() { //点击事件
        links.eq(i).next().addClass('point').removeClass('dark'); //当前圆点从暗变亮，移除类dark，添加point。
        links.eq(i).css('color', '#1582FF'); //当前a元素变亮
        links.eq(i).css('font-weight', 'bold'); //当前a元素变亮
        var links_par = links.eq(i).parent() //赋予变量links_par 为当前a元素的父元素li
        links_par.siblings().find('a').next().addClass('dark').removeClass('point'); //当前li元素的其他兄弟元素下的i元素变暗
        links_par.siblings().find('a').css('color', '');
        links_par.siblings().find('a').css('font-weight', 'normal');
        links_par.siblings().removeClass('point_a'); //移除初始选中默认的样式
    });
});


$(window).scroll(function(){
    var winheight = document.body.clientHeight;
    var top =$(".readview").offset().top;
    //  var height=$("#slideBar").clientHeight;
    var b=winheight-top-260;
    console.log('ssssss'+b)
    if(b<550){
        var a=-(550-b)
        console.log('a'+a)
        $("#slideBar").animate({"margin-top":a+"px"},100)//如果此时光标移出
        //   $(".readview").css({"margin-top":a+"px"})
    }else{
        $("#slideBar").css({"margin-top":0})//如果此时光标移出
    }
});
//导航栏吸顶效果
$(function(){
//获取要定位元素距离浏览器顶部的距离
    var navH = $(".readview").offset().top;
//滚动条事件
    $(window).scroll(function(){
// //获取滚动条的滑动距离
        var scroH = $(this).scrollTop();
        //     var a=document.body.clientHeight-scroH
        // console.log('s'+a)
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
    });
});


$(function(){
    new BaiduMap({
        id: "daohang",
        title: {
            text: daohang.name,
            className: "title"
        },
        content: {
            className: "content",
            text: [""]
        },
        point: {
            lng:daohang.lng,
            lat: daohang.lat
        },
        level: 15,
        zoom: true,
        type: ["地图", "卫星"],

    });

})
//地图导航下面的内容
$(function(e) {
    var str = '';
   if(daohang.name!=""){
        str+="<div class='p_block'>";
        str+="<div class='com_logo'>"+"</div>"
        str+="<div class='ptext'>"+"公司名称"+"</div>";
        str+="</div>";
        str+="<div class='companytext'>"+daohang.name+"</div>";
    }
    if(daohang.addr!=""){
        str+="<div class='p_block'>";
        str+="<div class='addr_logo'>"+"</div>"
        str+="<div class='ptext'>"+"详细地址"+"</div>";
        str+="</div>";
        str+="<div class='companytext'>"+daohang.addr+"</div>";
    }
    if(daohang.tele!=""){
        str+="<div class='p_block'>";
        str+="<div class='tele_logo'>"+"</div>"
        str+="<div class='ptext'>"+"电话"+"</div>";
        str+="</div>";
        str+="<div class='companytext'>"+daohang.tele+"</div>";
    }
    $("#companydetail").append(str);
})

//设计案例导航
function change_pswiper(i){
    console.log(i)
    var b=0;
    for (b = 0; b < product_swiper.length; b++) {
        if(b==i){
            console.log('sssss')
            // console.log('dfdssdfds'+a+b)
            document.getElementById("p_swiper"+b).className = "m_swiper_span1";
        }else{
            document.getElementById("p_swiper"+b).className = "m_swiper_span";
        }
    }
}
function guanzhu(){
    company.guanzhu=!company.guanzhu;
    if(company.guanzhu){
        $(".guanzhubotton").html('已关注');
    }else{
        $(".guanzhubotton").html('关注');
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
