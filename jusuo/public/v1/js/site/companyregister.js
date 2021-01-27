var open=false;
//友情链接
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];
var id_front=[]
var id_back=[]
var yingye=[]
var gonghan=[]

//下拉列表
$(function(){
    selectMenu(0);
    selectMenu(1);
    selectMenu(2);
    selectMenu(3);
    function selectMenu(index){
        $(".select-menu-input").eq(index).val($(".select-this").eq(index).html());//在输入框中自动填充第一个选项的值
        $(".select-menu-div").eq(index).on("click",function(e){
            e.stopPropagation();
            if($(".select-menu-ul").eq(index).css("display")==="block"){
                $(".select-menu-ul").eq(index).hide();
                $(".select-menu-div").eq(index).find("i").removeClass("select-menu-i");
                $(".select-menu-ul").eq(index).animate({marginTop:"50px",opacity:"0"},"fast");
            }else{
                $(".select-menu-ul").eq(index).show();
                $(".select-menu-div").eq(index).find("i").addClass("select-menu-i");
                $(".select-menu-ul").eq(index).animate({marginTop:"2px",opacity:"1"},"fast");
            }
            for(var i=0;i<$(".select-menu-ul").length;i++){
                if(i!==index&& $(".select-menu-ul").eq(i).css("display")==="block"){
                    $(".select-menu-ul").eq(i).hide();
                    $(".select-menu-div").eq(i).find("i").removeClass("select-menu-i");
                    $(".select-menu-ul").eq(i).animate({marginTop:"50px",opacity:"0"},"fast");
                }
            }

        });
        $(".select-menu-ul").eq(index).on("click","li",function(){//给下拉选项绑定点击事件
            $(".select-menu-input").eq(index).val($(this).html());//把被点击的选项的值填入输入框中
            $(".select-menu-div").eq(index).click();
            $(this).siblings(".select-this").removeClass("select-this");
            $(this).addClass("select-this");
        });
        $("body").on("click",function(event){
            event.stopPropagation();
            if($(".select-menu-ul").eq(index).css("display")==="block"){
                console.log(1);
                $(".select-menu-ul").eq(index).hide();
                $(".select-menu-div").eq(index).find("i").removeClass("select-menu-i");
                $(".select-menu-ul").eq(index).animate({marginTop:"50px",opacity:"0"},"fast");

            }
        });
    }
})

//底部导航的千名汇
$(function(e) {
    var str = '';
    for (var i = 0; i < 9; i++) {
        str+="<div class='qmh'>"+bottom[i]+"</div>"
        if((i+1)%9!==0){
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

//身份证正面
function showImgfront(input) {
    var file = input.files[0];
    var url = window.URL.createObjectURL(file)
    console.log(url)
    id_front.push(url)
    $(".id_front").hide().removeClass('show')
    $("#preview").css({"background-image":"url('"+url+"')"});
}
function del_idfront(){
    console.log('ss')
    id_front.splice(0,1);
    $(".del_fengmian").hide().removeClass('show');
    $(".id_front").show().addClass('show')
    $("#preview").css({"background-image":"url()"});
}
function showdel_idfront(){
    if(id_front.length>0){
        $(".del_fengmian").show().addClass('show');
        $("#upload-input").css({"height":"134px"})
    }else{
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-input").css({"height":"156px"})
    }
}
function showdel_idfront1(){
    if(id_front.length>0){
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-input").css({"height":"134px"})
    }else{
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-input").css({"height":"156px"})
    }
}

//身份证反面
function showImgback(input) {
    var file = input.files[0];
    var url = window.URL.createObjectURL(file)
    console.log(url)
    id_back.push(url)
    $(".id_back").hide().removeClass('show')
    $("#preview1").css({"background-image":"url('"+url+"')"});
}
function del_idback(){
    console.log('ss')
    id_back.splice(0,1);
    $(".del_fengmian1").hide().removeClass('show');
    $(".id_back").show().addClass('show')
    $("#preview1").css({"background-image":"url()"});
}
function showdel_idback(){
    if(id_back.length>0){
        $(".del_fengmian1").show().addClass('show');
        $("#upload-input1").css({"height":"134px"})
    }else{
        $(".del_fengmian1").hide().removeClass('show');
        $("#upload-input1").css({"height":"156px"})
    }
}
function showdel_idback1(){
    if(id_back.length>0){
        $(".del_fengmian1").hide().removeClass('show');
        $("#upload-input1").css({"height":"134px"})
    }else{
        $(".del_fengmian1").hide().removeClass('show');
        $("#upload-input1").css({"height":"156px"})
    }
}

//营业执照
function showImgyingye(input) {
    var str="";
    $("#yingye").html("");
    for(var i=0;i<input.files.length;i++) {
        var file = input.files[i];
        var url = window.URL.createObjectURL(file)
        yingye.push(url)
        console.log(url)
    }
    for(var i=0;i<yingye.length;i++) {
        str += "<div class='huxingimages' id='yingye" + i + "'onmouseenter='showdel1("+i+")' onmouseleave='showdel1("+i+")'>"
        str+="<div class='del_img' id='del_yingye"+i+"' style='display: none;' onclick='del_image1("+i+")'>"+"删除"+"</div>"
        str+="</div>"
    }
    $("#yingye").append(str);
    for(var i=0;i<yingye.length;i++) {
        $("#yingye"+i).css({"background-image":"url('"+yingye[i]+"')"});
    }
}
function showdel1(i) {
    $("#del_yingye"+i).toggle();
}
function del_image1(i) {
    yingye.splice(i,1);
    var str="";
    $("#yingye").html("");
    for(var i=0;i<yingye.length;i++) {
        str += "<div class='huxingimages' id='yingye" + i + "'onmouseenter='showdel1("+i+")' onmouseleave='showdel1("+i+")'>"
        str+="<div class='del_img' id='del_yingye"+i+"' style='display: none;' onclick='del_image1("+i+")'>"+"删除"+"</div>"
        str+="</div>"
    }
    $("#yingye").append(str);
    for(var i=0;i<yingye.length;i++) {
        $("#yingye"+i).css({"background-image":"url('"+yingye[i]+"')"});
    }
}

//授权公函照片
function showImggonghan(input) {
    var str="";
    $("#gonghan").html("");
    for(var i=0;i<input.files.length;i++) {
        var file = input.files[i];
        var url = window.URL.createObjectURL(file)
        gonghan.push(url)
        console.log(url)
    }
    for(var i=0;i<gonghan.length;i++) {
        str += "<div class='huxingimages' id='gonghan" + i + "'onmouseenter='showdel2("+i+")' onmouseleave='showdel2("+i+")'>"
        str+="<div class='del_img' id='del_gonghan"+i+"' style='display: none;' onclick='del_image2("+i+")'>"+"删除"+"</div>"
        str+="</div>"
    }
    $("#gonghan").append(str);
    for(var i=0;i<gonghan.length;i++) {
        $("#gonghan"+i).css({"background-image":"url('"+gonghan[i]+"')"});
    }
}
function showdel2(i) {
    $("#del_gonghan"+i).toggle();
}
function del_image2(i) {
    gonghan.splice(i,1);
    var str="";
    $("#gonghan").html("");
    for(var i=0;i<gonghan.length;i++) {
        str += "<div class='huxingimages' id='gonghan" + i + "'onmouseenter='showdel2("+i+")' onmouseleave='showdel2("+i+")'>"
        str+="<div class='del_img' id='del_gonghan"+i+"' style='display: none;' onclick='del_image2("+i+")'>"+"删除"+"</div>"
        str+="</div>"
    }
    $("#gonghan").append(str);
    for(var i=0;i<gonghan.length;i++) {
        $("#gonghan"+i).css({"background-image":"url('"+gonghan[i]+"')"});
    }
}

