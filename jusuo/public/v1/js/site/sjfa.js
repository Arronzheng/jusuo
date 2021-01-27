var data={
    "list":[
        {
            "span":"风格：",
            "nav":["不限","简约现代","新中式","北欧","欧式","简欧","日式","地中海","田园","美式","简美","混搭","后现代","工业风"]
        },{
            "span":"户型：",
            "nav":["不限","一居","两居","三居","四居","五居及以上"]
        }, {
            "span": "空间：",
            "nav":["不限","客厅","餐厅","客餐厅","厨房","卫生间","主卧","次卧","书房","儿童房","老人房","多功能室","储物间","衣帽间","玄关","走廊","阳台","露台","入户花园"]
        }, {
            "span": "面积：",
            "nav":["不限","50㎡及以下","50-80㎡","80-100㎡","100-130㎡","130㎡及以上"]
        }, {
            "span": "设计方案类型：",
            "nav": ["不限","全屋定制","全屋硬装","精选工装"]
        }
    ]
};
var paixu={
    "list":[
        {
            "label":"综合",
            "up":true,
            "down":false,
            "active":true
        },
        {
            "label":"人气",
            "up":false,
            "down":false,
            "active":false
        },
        {
            "label":"时间排序",
            "up":false,
            "down":false,
            "active":false
        }
    ]
}
var choosejinxuan=true;//精选方案的是否选中
var leadlabel=["引导关键词1","引导关键词2","引导关键词3"]
var project={
    "list":[
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,
        "identity":true,"hot":true,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":true,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居-奥园华庭","designerimage":"images/sjfa/8.png","designer":"岑岑岑生","area":"90㎡","look":219,"like":135,"collected":88,"identity":true,"hot":false,"liked":false,"collect":false},
    ]
};
//友情链接
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];
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
        var s=data.list[i].span.length*16;
        console.log(s)
        str+="<div class='nav_span1' style='width:"+s+"px;'>";
        str+="<span class='nav_span'>"+data.list[i].span+"</span>"
        str+="</div>"
        var b=1100-s-30;
        str+="<div class='nav_text' style='width:"+b+"px;'>";
        for(var j=0;j<data.list[i].nav.length;j++){
            if(j==0){
                str+="<span class='nav_t active' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
            }else{
                str+="<span class='nav_t' id='nav_"+i+"_"+j+"' onclick='change_swiper("+i+","+j+")'>"
            }


            str+=data.list[i].nav[j];
            str+="</span>"
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

//引导词的html
$(function(e) {
    var str = '';
    for(var i=0;i<leadlabel.length;i++){
        str+="<span class='leadspan'>"+leadlabel[i]+"</span>"
    }
    $("#lead").append(str);
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
//设计方案的html
// $(function(e) {
//     var str = '';
//     for(var i=0;i<9;i++){
//         str+="<div class='projectitem'>";
//         str+="<img class='designimage' src='"+project.list[i].image+"'/>";
//         str+="<div class='wholeview'>"+"全景图"+"</div>"
//         str+="<div class='namelabel'>"+project.list[i].name+"</div>"
//         str+="<div class='detail'>";
//         str+="<img class='personimage' src='"+project.list[i].designerimage+"'/>";
//         str+="<div class='designername'>"+project.list[i].designer_xq+"</div>";
//         str+="<div class='areaview'>"+project.list[i].area+"</div>";
//         str+="<div class='lookview'>";
//         str+="<span class='iconfont icon-liulan-copy' style='color:#777777;font-size:12px;'/>"
//         str+="<span class='looknumber'>"+project.list[i].looknuber+"</span>"
//         str+="</div>";
//         str+="</div>";
//         str+="</div>";
//     }
//     $("#project").append(str);
// });


//切换导航的选择
function change_swiper(i,j){
    console.log(i)
    var b=0;
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

//切换精选方案的选择
function changechooselabel(){
    choosejinxuan=!choosejinxuan;
    if(choosejinxuan==false){
        document.getElementById("chooselabel").className = "iconfont icon-xuankuang1";
        $("#chooselabel").css({"color":"#333333"});
    }else{
        document.getElementById("chooselabel").className = "iconfont icon-xuankuang";
        $("#chooselabel").css({"color":"#1582FF"});

    }
}

// xlPaging.js 使用方法
var nowpage = $("#page").paging({
    nowPage: 1, // 当前页码
    pageNum: Math.ceil(project.list.length / 9), // 总页码
    buttonNum: Math.ceil(project.list.length / 9), //要展示的页码数量
    canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
    showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
    callback: function (num) { //回调函数
        console.log('sss'+num);
        //更多产品
        // $(function(e) {
       $("#project").html("");
       var txt="<div class='projectcontainer' id='project'></div>"
       $("#project").append(txt);
        var total=Math.min(num*9,project.list.length)
        console.log(num+'sss'+total)
        moreproduct((num-1)*9,total);
    }
});
console.log(nowpage.options.nowPage)
//更多产品
$(function(e) {
    moreproduct(nowpage.options.nowPage-1,nowpage.options.nowPage*9)
})

//更多产品html
function moreproduct(begin,end){
    var str = '';
    for (var i = begin; i < end; i++) {
        str+="<div class='projectitem'>";
        str+="<div class='designimage' id='image_"+i+"'/>";
        str+="<div class='wholeview'>"+"全景图"+"</div>"
        str+="<div class='designer_text'>";
        str+="<span class='d_area'>"+project.list[i].area+"&nbsp;|"+"</span>";
        str+="<span class='d_title'>"+"&nbsp;"+project.list[i].name+"</span>";
        str+="</div>";
        str+="<div class='d_detail1'>";
        str+="<span class='iconfont icon-chakan' id='look' style='color:#B7B7B7;margin-left:21px;'>"+"</span>";
        str+="<span class='looknumber'>"+project.list[i].look+"</span>";
        str+="<span class='iconfont icon-dianzan2' id='like_"+i+"' style='color:#B7B7B7;margin-left:42px;' onclick='like("+i+")'>"+"</span>";
        str+="<span class='looknumber' id='likenumber_"+i+"' onclick='like("+i+")'>"+project.list[i].like+"</span>";
        str+="<span class='iconfont icon-shoucang2' id='collected_"+i+"' style='color:#B7B7B7;margin-left:42px;' onclick='collected("+i+")'>"+"</span>";
        str+="<span class='looknumber' id='collectednumber_"+i+"' onclick='collected("+i+")'>"+project.list[i].collected+"</span>";
        str+="</div>";
        str+="<div class='d_line'>"+"</div>"
        str+="<div class='d_person1'>";
        str+="<div class='d_personimage1' id='d_personimage"+i+"'>"+"</div>"
        str+="<span class='d_personname'>"+project.list[i].designer+"</span>";
        if(project.list[i].identity==true)
        {
            str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-left:10px;margin-top:4px;'>"+"</span>";
        }else{
            str+="<span class='iconfont icon-shimingrenzheng' style='color:#D2D1D1;font-size:16px;margin-left:10px;margin-top:4px;'>"+"</span>";
        }
        if(project.list[i].hot==true) {
            str += "<span class='iconfont icon-renqiwang' id='hot' style='color:#FFE115;margin-left:10px;margin-top:4px;'>" + "</span>"
        }
        str+="</div>";
        str+="</div>";;
        str+="</div>";
    }
    $("#project").append(str);
    for(var i = begin; i < end; i++){
        $("#image_"+i).css({"background-image":"url('"+project.list[i].image+"')"});
        $("#d_personimage"+i).css({"background-image":"url('"+project.list[i].designerimage+"')"});
       // document.getElementById("image_"+i).style.backgroundImage="url('"+"../image/8.png"+"')";
    }
}
//设计方案的点赞
function like(i){
    console.log('sss'+i)
    if(project.list[i].liked==false){
        var a=project.list[i].like+1;
        project.list[i].like=a;
        $("#likenumber_"+i).html(project.list[i].like);
        $("#likenumber_"+i).css({"color":"#1582FF"});
        document.getElementById("like_"+i).className = "iconfont icon-dianzan";
        $("#like_"+i).css({"color":"#1582FF","font-size":"16px"});
        project.list[i].liked=true;
    }else{
        var a=project.list[i].like-1;
        project.list[i].like=a;
        $("#likenumber_"+i).html(a);
        $("#likenumber_"+i).css({"color":"#B7B7B7"});
        document.getElementById("like_"+i).className = "iconfont icon-dianzan2";
        $("#like_"+i).css({"color":"#B7B7B7","font-size":"16px"})
        project.list[i].liked=false;
    }
}
//设计方案的收藏
function collected(i){
    console.log('sss'+i)
    if(project.list[i].collect==false){
        var a=project.list[i].collected+1;
        project.list[i].collected=a;
        $("#collectednumber_"+i).html(project.list[i].collected);

        $("#collectednumber_"+i).css({"color":"#1582FF"});
        document.getElementById("collected_"+i).className = "iconfont icon-buoumaotubiao44";
        $("#collected_"+i).css({"color":"#1582FF"});
        project.list[i].collect=true;
    }else{
        var a=project.list[i].collected-1;
        project.list[i].collected=a;
        $("#collectednumber_"+i).html(a);
        $("#collectednumber_"+i).css({"color":"#B7B7B7"});
        document.getElementById("collected_"+i).className = "iconfont icon-shoucang2";
        $("#collected_"+i).css({"color":"#B7B7B7"})
        project.list[i].collect=false;
    }
}