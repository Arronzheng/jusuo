var login=true;
//点赞，浏览，复制，查看
var dianzan=false,fuzhi=false,shoucang=false;
var dianzannumber=153;
var fuzhinumber=35;
var shoucangnumber=100;
var liulannumber=246;
var guan=false;
var ping_id=2;
var lookall_product=false;
var followperson="";
var open=false;
//空间介绍
var data = { //数据
    "list":[{"title1":"户型","text":"文字说明文字说明文字说明文字说明","detailsimages":["images/sjfa_xq/2.png"],"label":[]},
        {"title1":"客厅",
            "text":"文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明",
            "detailsimages":["images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png"],
            "label":["50㎡","简约风格"]},
        {"title1":"主卧",
            "text":"文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明",
            "detailsimages":["images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png"],
            "label":["50㎡","简约风格"]},
        {"title1":"次卧",
            "text":"文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明",
            "detailsimages":["images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png"],
            "label":[]},
        {"title1":"客厅",
            "text":"文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明",
            "detailsimages":["images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png"],
            "label":[]}
    ]
};
var sg_data={"images":["images/sjfa_xq/sg.png","images/sjfa_xq/sg.png","images/sjfa_xq/sg.png"],"text":"施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工施工"};
var detail_data={"images":["images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png"],"text":"文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明文字说明"};
var detail_data1={"images":["images/sjfa_xq/4.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png","images/sjfa_xq/3.png","images/sjfa_xq/4.png"],"text":"文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字"};
var product_swiper=["不限","瓷砖","卫浴"]
//更多产品
var productdata={"list":[{"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":300},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":300},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":300},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":300},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":300},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":300},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":200},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":200},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":200},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":200},
        {"images":"images/cpk/7.png","position":"佛山南海","collectionnumber":28,"name":"TOTO圆形马桶","company":"富贵卫浴公司","price":200}
    ]};
//相似方案
var productdata1={"list":[{"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":29,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":30,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":30,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":30,"personimage":"images/sjfa_xq/7.png"},
        {"images":"images/sjfa_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":30,"personimage":"images/sjfa_xq/7.png"}
    ]};
//评论区
var productdata2={"list":[{"id":0,"followperson":"","time":"15分钟前","personname":"薛华少","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容","personimage":"images/sjfa_xq/7.png"},
        {"id":1,"followperson":"张效瑞","time":"30分钟前","personname":"李小龙","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容","personimage":"images/sjfa_xq/7.png"},
        {"id":2,"followperson":"","time":"2019-10-10 21:42","personname":"张效瑞","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容","personimage":"images/sjfa_xq/7.png"}
    ]};
//友情链接
var data1={
    "list":["千名汇","千名汇","千名汇","千名汇","千名汇","千名汇","千名汇","千名汇"]
};
var nav=["空间设计","产品应用","施工"]
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];

// xlPaging.js 使用方法
var nowpage = $("#page").paging({
    nowPage: 1, // 当前页码
    pageNum: Math.ceil(productdata.list.length / 6), // 总页码
    buttonNum: Math.ceil(productdata.list.length / 6), //要展示的页码数量
    canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
    showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
    callback: function (num) { //回调函数
        console.log('sss'+num);
        //更多产品
       // $(function(e) {
        $("#produ").html("");
        var txt="<div class='productcontainer' id='product'></div>"
        $("#produ").append(txt);
        var total=Math.min(num*6,productdata.list.length)
        console.log(num+'sss'+total)
        moreproduct((num-1)*6,total);
    }
});
console.log(nowpage.options.nowPage)

//是否登录
$(function(e) {
    if(login==false){
        $("#view").css({"height":"1225px","overflow":"hidden"})
        var str='';
        str+="<div class='jianbian'>"+"</div>"
        str+="<div class='jianbian1'>"+"</div>"
        str+="<div class='tip1'>"
        str+="<span class='tipstext'>"+"登录&nbsp"+"</span>"
        str+="<span class='tipstext1'>"+" / "+"</span>"
        str+="<span class='tipstext'>"+"&nbsp注册"+"</span>"
        str+="<span class='tipstext1'>"+" 后展开完整方案"+"</span>"
        str+="</div>"
        str+="<div class='tip2'>"
        str+="<div class='tipimage'/>"
        str+="<span>"+"著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。"+"</span>"
        str+="</div>"
        $("#view").append(str);

    }
})

$(document).ready(function(){
    //点赞，浏览，复制，查看
    document.getElementById("dianzannumber").innerText = dianzannumber;
    document.getElementById("fuzhinumber").innerText = fuzhinumber;
    document.getElementById("shoucangnumber").innerText = shoucangnumber;
    document.getElementById("liulannumber").innerText = liulannumber;
    $("#dianzan").click(function(){
        if(dianzan==false){
            dianzannumber=dianzannumber+1;
            document.getElementById("dianzannumber").innerText = dianzannumber;
            $("#dianzan").css({"color":"#1582FF"})
          //  layer.msg('已点赞！', {icon: 1});
            dianzan=true;
        }else{
            dianzannumber=dianzannumber-1;
            document.getElementById("dianzannumber").innerText = dianzannumber;
            $("#dianzan").css({"color":"#777777"})
           // layer.msg('已取消点赞！', {icon: 2});
            dianzan=false;
        }
    });
    $("#fuzhi").click(function(){
        if(fuzhi==false) {
            fuzhinumber=fuzhinumber+1;
            document.getElementById("fuzhinumber").innerText = fuzhinumber;
            $("#fuzhi").css({"color": "#1582FF"})
            //layer.msg('已复制！', {icon: 1});
            fuzhi=true;
        }else{
            fuzhinumber=fuzhinumber-1;
            document.getElementById("fuzhinumber").innerText = fuzhinumber;
            $("#fuzhi").css({"color": "#777777"})
           // layer.msg('已取消复制！', {icon: 2});
            fuzhi=false;
        }
    });
    $("#shoucang").click(function(){
        if(shoucang==false){
            shoucangnumber=shoucangnumber+1;
            document.getElementById("shoucangnumber").innerText = shoucangnumber;
            $("#shoucang").css({"color":"#1582FF"})
           // layer.msg('已收藏！', {icon: 1});
            shoucang=true;
        }else{
            shoucangnumber=shoucangnumber-1;
            document.getElementById("shoucangnumber").innerText = shoucangnumber;
            $("#shoucang").css({"color":"#777777"})
          //  layer.msg('已取消收藏！', {icon: 2});
            shoucang=false;
        }
    });


//空间介绍
    $(function(e) {
        // if (window.innerWidth){
        //     var winWidth = window.innerWidth;
        // } else if ((document.body) && (document.body.clientWidth)){
        //     var winWidth = document.body.clientWidth;
        // }
        // $(".bottom").css({"width":winWidth,"overflow":"hidden"});
        var str = '';
        for (var i = 0; i < data.list.length; i++) {

            if (i == 0) {
                str += "<div class='title2view' id='b1'>"
            } else {
                str += "<div class='title2view' id='b2'>"
            }
            str+="<span class='linea'>"+"</span>"
            str += "<label class='title2'>" + data.list[i].title1 + "</label>"
            // if (data.list[i].title1 != "") {
            //     str += "<label class='title3'>" + "&nbsp;" + "·&nbsp;" + data.list[i].title1 + "</label>"
            // }
            str += "</div>";
            if(i!=0){
                str+="<div class='productnav'>"
                for(var z=0;z<nav.length;z++) {
                    var width=nav[z].length*16+40
                    if(z==0){
                        str+="<div id='d_swiper_"+i+"_"+z+"' class='d_swipwer_span1' onclick='change_dswiper("+i+","+z+")' style='width:"+width+"px;'>"+nav[z]+"</div>"
                    }else{
                        str+="<div id='d_swiper_"+i+"_"+z+"' class='d_swipwer_span' onclick='change_dswiper("+i+","+z+")' style='width:"+width+"px;'>"+nav[z]+"</div>"
                    }
                }
                str+="</div>"
            }

            str += "<div class='image2view'>"
            str += "<a href='" + data.list[i].detailsimages[0] + "'>"
            str += "<div class='image2' id='image_" + i + "'/>"
            str += "</a>"
            if (data.list[i].detailsimages.length > 1) {
                str += "<div class='detailsimageview' id='detailimage_"+i+"'>"
                for (var j = 0; j < data.list[i].detailsimages.length; j++) {
                    if(j==0){
                        str += "<a href='" + data.list[i].detailsimages[j] + "'>"
                        str += "<div class='image3 active' id='image_" + i  + "_" + j + "' onmouseover='changepicture(" + i + "," + j + ")' data-src='" + data.list[i].detailsimages[j] + "'/>"
                        str += "</a>"
                    }else{
                        str += "<a href='" + data.list[i].detailsimages[j] + "'>"
                        str += "<div class='image3' id='image_" + i + "_" + j + "' onmouseover='changepicture(" + i + "," + j + ")' data-src='" + data.list[i].detailsimages[j] + "'/>"
                        str += "</a>"
                    }
                }
                str += "</div>"
            }
            if (data.list[i].label.length > 0) {
                str += "<div class='labelview'>"
                for (var j = 0; j < data.list[i].label.length; j++) {
                    str += "<div class='labelblock' style='width:" + data.list[i].label[j].length * 20 + "px'" + ">" + data.list[i].label[j] + "</div>"
                }
                str += "</div>"
            }
            str += "</div>"
            // if (i != data.list.length - 1) {
            str += "<label class='detailsd' id='detailsd"+i+"'>" + data.list[i].text + "</label>"
       //     } else {
          //      str += "<label class='detailsd1'>" + data.list[i].text + "</label>"
          //  }

        }
        if(login==true){
            str+="<div class='tip'>"
            str+="<div class='tipimage'/>"
            str+="<span>"+"著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。"+"</span>"
            str+="</div>"
        }
        $("#view").append(str);
        for (var i = 0; i < data.list.length; i++){
            $("#image_"+i).css({"background-image":"url('"+data.list[i].detailsimages[0]+"')"});
            if (data.list[i].detailsimages.length > 1) {
                for (var j = 0; j < data.list[i].detailsimages.length; j++) {
                   // $("#image_0_1").css({"background-image": "url('" + data.list[i].detailsimages[j] + "')"});
                    $("#image_"+i+"_"+j).css({"background-image": "url('" + data.list[i].detailsimages[j] + "')"});
                }
            }
        }



    });

    $(window).scroll(function(){
        var winheight = document.body.clientHeight;
        var top =$(".readview").offset().top;
      //  var height=$("#slideBar").clientHeight;
        var b=winheight-top-348;
        console.log('ssssss'+b)
        if(b<560){
            var a=-(560-b)
            console.log('a'+a)
            $("#slideBar").animate({"margin-top":a+"px"},100)//如果此时光标移出
         //   $(".readview").css({"margin-top":a+"px"})
        }else{
            $("#slideBar").css({"margin-top":0})//如果此时光标移出
        }
    });

    //更多产品
     $(function(e) {
         //热门产品导航html
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
         moreproduct(nowpage.options.nowPage-1,nowpage.options.nowPage*6)
    })

    //相似方案
    $(function(e) {
        moredesigner(3);

    });

    //评论区
    $(function(e) {
        //加载数据
        var str1=''
        str1+="<div class='pinglunblock' placeholder='写下您的评论...' id='ping' contentEditable='true' onkeyup='checkContent()'>"
       // str1+="<span class='content' id='content'>"+"</span>"
       // str1+="<span class='content'>"+"<span id='follow'>"+followperson+"</span>"+"</span>"
        str1+="</div>"
        str1+="<div class='button2' type='submit' onclick='commit1("+followperson+")' id='btnButton'>"+"发表"+"</div>";
        $("#fb_pinglun").append(str1);
        addCommentList({data:productdata2,add:""});
        // //为了页面滚动对应导航
        var mainTopArr = new Array();
        for(var i=0;i<6;i++){
            var top =$("#b"+i).offset().top-50;
            console.log(top)
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
    });


//友情链接
//     $(function(e) {
//         var str1 = '';
//         var i = 0
//         for (i = 0; i < data1.list.length; i++) {
//             var y=i+1
//             if(y%5!=0 && i!=data1.list.length-1){
//                 str1+="<label class='connecttext'>"+data1.list[i]+"</label>"
//             }else{
//                 str1+="<label class='connecttext1'>"+data1.list[i]+"</label>"
//             }
//         }
//         $("#connect").append(str1);
//     })

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
        })
    });
});

//相似方案html
function moredesigner(total){
    var str1 = '';
    for (var i = 0; i < total; i++) {
        str1+="<div class='productview'>"
        str1+="<div class='imageview'>"
        str1+="<div class='areaview1'>"+"</div>"
        str1+="<div class='areaview1t'>"
        str1+="<label class='positext1'>"+productdata1.list[i].area+"</label>"
        str1+="</div>"
        str1+="<div class='pimage' id='pimage_"+i+"'/>"
        str1+="<div class='nametext'>"+productdata1.list[i].name+"</div>"
        str1+="<div class='deview'>"
        str1+="<div class='perimage' id='perimage_"+i+"'/>"
        str1+="<label class='pertext'>"+productdata1.list[i].personname+"</label>"
        str1+="<div class='lookview'>"
        //str1+="<span class='iconfont icon-liulan-copy' style='color:#777777;'></span>"
        str1+="<img src='./images/sjfa_xq/相似方案-浏览量icon.png' class='xiconimage'/>"
        str1+="<label class='viewtext'>"+productdata1.list[i].looknumber+"</label>"
        str1+="</div>"
        str1+="</div>"
        str1+="</div>"
        str1+="</div>"
    }
    $("#product1").append(str1);
    for (var i = 0; i < total; i++) {
        $("#pimage_"+i).css({"background-image":"url('"+productdata1.list[i].images+"')"});
        $("#perimage_"+i).css({"background-image":"url('"+productdata1.list[i].personimage+"')"});
    }
}

//更多产品html
function moreproduct(begin,end){
    var str1 = '';
    for (var i = begin; i < end; i++) {
        str1+="<div class='productview'>"
        str1+="<div class='imageview'>"
        str1+="<div class='positionview' style='width:"+(productdata.list[i].position.length*14+22)+"px;'"+">"+"</div>"
        str1+="<div class='positionviewt'style='width:"+(productdata.list[i].position.length*14+22)+"px;'"+">"
        str1+="<img src='./images/sjfa_xq/产品清单-定位icon.png' class='posiimage'/>"
        //str1+="<span class='iconfont icon-dingwei1' style='color:#FFFFFF;'/>"
        str1+="<label class='positext'>"+productdata.list[i].position+"</label>"
        str1+="</div>"
        str1+="<div class='positionview1'>"+"</div>"
        str1+="<div class='positionview1t'>"
        //str1+="<span class='iconfont icon-shoucang' style='color:#FFFFFF;'/>"
        str1+="<img src='./images/sjfa_xq/产品清单-收藏icon.png' class='posiimage1'/>"
        str1+="<label class='positext1'>"+productdata.list[i].collectionnumber+"</label>"
        str1+="</div>"
        str1+="<div class='pimage' id='primage_"+i+"'/>"
        str1+="<div class='nametext'>"+productdata.list[i].name+"</div>"
        str1+="<div class='companyview'>"
        str1+="<label class='companytext'>"+productdata.list[i].company+"</label>"
        str1+="<label class='pricetext'>"+"¥"+productdata.list[i].price+"</label>"
        str1+="</div>"
        str1+="</div>"
        str1+="</div>"
    }
    $("#product").append(str1);
    for (var i = begin; i < end; i++) {
        $("#primage_"+i).css({"background-image":"url('"+productdata.list[i].images+"')"});
    }
}

//空间介绍导航导航
function change_dswiper(i,z){
    console.log(i)
    var b=0;
    $("#detailsd"+i).html("");
    var str1="";
    if(z==2){
        data.list[i].detailsimages=sg_data.images;
       str1+=sg_data.text
    }else if(z==1){
        data.list[i].detailsimages=detail_data1.images;
        str1+=detail_data1.text
    }else{
        data.list[i].detailsimages=detail_data.images;
        str1+=detail_data.text
    }
    $("#detailsd"+i).append(str1);
    $("#image_"+i).css({"background-image":"url('"+data.list[i].detailsimages[0]+"')"});

    $("#detailimage_"+i).html("");
    var str=''
    if (data.list[i].detailsimages.length > 1) {

        for (var j = 0; j < data.list[i].detailsimages.length; j++) {
            if(j==0){
                str += "<a href='" + data.list[i].detailsimages[j] + "'>"
                str += "<div class='image3 active' id='image_" + i  + "_" + j + "' onmouseover='changepicture(" + i + "," + j + ")' data-src='" + data.list[i].detailsimages[j] + "'/>"
                str += "</a>"
            }else{
                str += "<a href='" + data.list[i].detailsimages[j] + "'>"
                str += "<div class='image3' id='image_" + i + "_" + j + "' onmouseover='changepicture(" + i + "," + j + ")' data-src='" + data.list[i].detailsimages[j] + "'/>"
                str += "</a>"
            }
        }
    }
    $("#detailimage_"+i).append(str);
    if (data.list[i].detailsimages.length > 1) {
        for (var j = 0; j < data.list[i].detailsimages.length; j++) {
            // $("#image_0_1").css({"background-image": "url('" + data.list[i].detailsimages[j] + "')"});
            $("#image_"+i+"_"+j).css({"background-image": "url('" + data.list[i].detailsimages[j] + "')"});
        }
    }
    for (b = 0; b < nav.length; b++) {
        if(b==z){
            console.log('sssss'+i+z)
            // console.log('dfdssdfds'+a+b)
            document.getElementById("d_swiper_"+i+"_"+b).className = "d_swipwer_span1";

        }else{
            console.log('s'+i+z)

            document.getElementById("d_swiper_"+i+"_"+b).className = "d_swipwer_span";
        }
    }

    //不同tab的文字说明


}

//热门产品导航
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
//获取现在时间
function getCurrentDate(format) {
    var now = new Date();
    var year = now.getFullYear(); //得到年份
    var month = now.getMonth();//得到月份
    var date = now.getDate();//得到日期
    var day = now.getDay();//得到周几
    var hour = now.getHours();//得到小时
    var minu = now.getMinutes();//得到分钟
    var sec = now.getSeconds();//得到秒
    month = month + 1;
    if (month < 10) month = "0" + month;
    if (date < 10) date = "0" + date;
    if (hour < 10) hour = "0" + hour;
    if (minu < 10) minu = "0" + minu;
    if (sec < 10) sec = "0" + sec;
    var time = "";
    //精确到天
    if(format==1){
        time = year + "-" + month + "-" + date;
    }
    //精确到分
    else if(format==2){
        time = year + "-" + month + "-" + date+ " " + hour + ":" + minu + ":" + sec;
    }
    return time;
}

//添加评论的html
function addpinglun(a){
    var str1 = '';
    //console.log(a.list[i].followperson)
    for (var i = 0; i < a.list.length; i++) {
        // str1+="<div class='linew'>"+"</div>"
        str1+="<div class='pinglunview'>"
        str1+="<div class='lpersonimage' id='lpersonimage_"+i+"'/>"
        str1+="<div class='pinglunrview'>"
        str1+="<div class='pinglunrhead'>"
        str1+="<label class='lpertext'>"+a.list[i].personname+"</label>"
        str1+="<label class='ltimetext'>"+a.list[i].time+"</label>"
        str1+="</div>"
        str1+="<div class='pinglunmhead'>"
        console.log('follow'+a.list[i].followperson)
        if(a.list[i].followperson!=""){
            str1+="<label class='lfollowtext'>"+"@跟帖："+a.list[i].followperson+"</label>"
        }
        str1+="<label class='lcontenttext'>"+a.list[i].content+"</label>"
        str1+="</div>"
        str1+="<div class='pinglunthead' onclick='pinlunt("+a.list[i].id+")'>"
        str1+="<label class='lfollowtext1'>"+"@跟帖"+"</label>"
        str1+="</div>"
        str1+="</div>"
        str1+="</div>"
        if(a.list[i].id!=2){
            str1+="<div class='linew'>"+"</div>"
        }

    }
    return str1;
    //$("#pinglun").append(str1);
}

//发表评论
function commit1(i) {
    console.log('sssss'+followperson)
    ping_id=ping_id+1;
    var cont=document.getElementById("ping");
    var content=cont.innerText;
    console.log('i'+i)
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    var revaue = content.replace(/^\s*|(\s*$)/g,"");
    if(revaue<= 0){
       // layer.msg('评论内容不能为空或全为空格！', {icon: 2});
        return;
    } else{
        if(document.getElementById("follow")){
            cont=document.getElementById("follow");
            content=content.replace(cont.innerText,"");
            console.log('content'+content)
            //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
            valuestr = content.trim();
            revaue = content.replace(/^\s*|(\s*$)/g,"");
        }
        var personname="tracy";
        var personimage="images/sjfa_xq/7.png";
        var time=getCurrentDate(2);
        var a={"list":[{"id":ping_id,"personname":personname,"personimage":personimage,"followperson":followperson,"time":time,"content":content}]};
        $("#ping").html("");
        $('#btnButton').attr('disabled', true);
        $('#btnButton').css({"background-color":"#D9D9D9","cursor":"not-allowed"})
        console.log('success')
        addCommentList({data:{"list":[]},add:a});
        followperson="";
        // console.log(a)
        // $.ajax({
        //     type: "post",
        //     data: a,
        //     url: "",
        //     success: function (data) {
        //
        //     }
        // })
    }
}

//点击跟帖
function pinlunt(i) {
    console.log('gentie'+i)
    followperson=followperson+productdata2.list[i].personname
    console.log('pinlunt'+followperson)
   // document.getElementById("ping").innerText = "@"+followperson;
    var str1=''
    var width=(followperson.length+1)*16;
    str1+="<span id='follow' style='width:'"+width+"px;contentEditable='false';'>"+"@"+followperson+"</span>"
    str1+="<span class='content' id='content1'>"+"</span>"
    $("#ping").append(str1);
}
//弹出朋友圈和微信的弹窗
function pic() {
    $(".shareview1").show().addClass("show");
    $(".shareview").show().addClass("show");
}
function pict() {
    $(".shareview1").hide().removeClass("show");
    $(".shareview").hide().removeClass("show");
}
//弹出二维码
function pic1(){
    $(".shareview1").hide().removeClass("show");
    $(".shareview").hide().removeClass("show");
    $("#qrcode").show().addClass("show");
}
//弹出二维码
function pic1t(){
    $(".shareview1").hide().removeClass("show");
    $(".shareview").hide().removeClass("show");
    $("#qrcode1").show().addClass("show");
}
//关闭朋友圈弹窗
function closefriend(){
    $(".shareview1").hide().removeClass("show");
    $(".shareview").hide().removeClass("show");
    $("#qrcode").hide().removeClass("show");
}
function closefriend1(){
    $(".shareview1").hide().removeClass("show");
    $(".shareview").hide().removeClass("show");
    $("#qrcode1").hide().removeClass("show");
}
//切换图片
function changepicture(i,j){
    console.log(i)
    console.log(j)
    var b=0;
        for (b = 0; b < data.list[i].detailsimages.length; b++) {
            console.log('a'+a+'b'+b)
            if(b==j){

                console.log('sssss')
                // console.log('dfdssdfds'+a+b)
                 document.getElementById("image_"+i+"_"+b).className = "image4";
            }else{
                console.log('ssss'+a+b)
                document.getElementById("image_"+i+"_"+b).className = "image3";
            }
        }
    const getId = document.getElementById('image_'+i+'_'+j);
    $("#image_"+i).css({"background-image": "url('" + data.list[i].detailsimages[j] + "')"});
    var src = getId.getAttribute("data-src")
    console.log(src)
    var a="#image_"+i
    $(a).attr("src", src);//设置#bigimg元素的src属性
}
//弹出朋友圈和微信的弹窗
function guanzhu() {
    if(guan==false){
        //layer.msg('关注成功！', {icon: 1});
        guan=true;
    }else{
       // layer.msg('已取消成功！', {icon: 2});
        guan=false
    }
    $(".guanzhu").toggle();
    $(".guanzhu1").toggle();
}
function addCommentList(options){
    console.log('add')
    var defaults = {
        data:{"list":[]},
        add:""
    }
    var option = $.extend(defaults, options);
    //加载数据
    console.log('ssssss'+option.data.length)
    if(option.data.list.length > 0 ){
        var dataList = option.data;
        var totalString = "";
        var str=addpinglun(dataList);

        $("#pinglun").append(str);
        for (var i = 0; i < dataList.list.length; i++) {
            $("#lpersonimage_"+i).css({"background-image":"url('"+dataList.list[i].personimage+"')"});
        }
    }

    //添加新数据
    if(option.add != ""){
        obj = option.add;
        console.log('s1'+obj.list[0])
        var str= addpinglun(obj);
        productdata2.list.push(obj.list[0])
        console.log('pro'+productdata2.list.length)
        $("#pinglun").prepend(str);
        for (var i = 0; i < obj.list.length; i++) {
            $("#lpersonimage_"+i).css({"background-image":"url('"+obj.list[i].personimage+"')"});
        }
    }
}

function product_lookall(){
    lookall_product=!lookall_product;
    if(lookall_product==false){
        $("#produ1").html("");
        var txt="<div class='productcontainer' id='product1'></div>"
        $("#produ1").append(txt);
        moredesigner(6);
    }else{
        $("#produ1").html("");
        var txt="<div class='productcontainer' id='product1'></div>"
        $("#produ1").append(txt);
        moredesigner(productdata1.list.length)
    }
    $("#product_lookall").toggle();
    $("#product_lookclose").toggle();

}
function pinglun_lookall(){
    $("#lookall_up").toggle();
    $("#lookall_close").toggle();
}


function checkContent() {
    console.log('sssss')
    var cont=document.getElementById("ping");
    var content=cont.innerText
    console.log(cont.innerText);
    //var content=a.val();
  //  console.log('a'+content)
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    var revaue = content.replace(/^\s*|(\s*$)/g,"");
    if(revaue<= 0){
            $('#btnButton').attr('disabled', true);
        $('#btnButton').css({"background-color":"#D9D9D9","cursor":"not-allowed"})
        } else {
            $('#btnButton').attr('disabled', false);
        $('#btnButton').css({"background-color":"#1582FF","cursor":"pointer"})

        }
}
function checkContent1(i) {
    var content=$("#pin_"+i).val();
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    var revaue = content.replace(/^\s*|(\s*$)/g,"");
    if(revaue<= 0){
        $('#button2t_'+i).attr('disabled', true);
        $('#button2t_'+i).css({"opacity":.5,"cursor":"not-allowed"})
    } else {
        $('#button2t_'+i).attr('disabled', false);
        $('#button2t_'+i).css({"opacity":1,"cursor":"pointer"})

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