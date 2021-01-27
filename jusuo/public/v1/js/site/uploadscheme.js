var tc_table={
    "colname":["","序号","名称","类型","产品编号","规格","缩略图"],
    "coldata":[{"check":"checked","id":1,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":2,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":3,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":4,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":5,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":6,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":7,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":8,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"},
        {"check":"","id":9,"name":"TOTO马桶","style":"产品","pid":"CH88MS038","size":"800x800mm","image":"images/cpk/7.png"}]
}
var kongjiannumber=0;//空间数量
var fengmian=[];
var huxing=[];
var kongjian=[];
var product=[];
var sg=[];

var open=false;
//友情链接
var bottom=['千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇','千名汇'];

// xlPaging.js 使用方法
var nowpage = $("#page").paging({
    nowPage: 1, // 当前页码
    pageNum: Math.ceil(tc_table.coldata.length / 5), // 总页码
    buttonNum: Math.ceil(tc_table.coldata.length / 5), //要展示的页码数量
    canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
    showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
    callback: function (num) { //回调函数
        console.log('sss'+num);
        //更多产品
        // $(function(e) {
        $("#produ").html("");
        var txt="<table border='0' id='tc_table'></table>"
        $("#produ").append(txt);
        var total=Math.min(num*5,tc_table.coldata.length)
        console.log(num+'sss'+total)
        moreproduct((num-1)*5,total);
    }
});
//选择产品弹窗table
$(function(e) {
    moreproduct(nowpage.options.nowPage-1,nowpage.options.nowPage*5)
})
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
    addplace();
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
//更多产品html
function moreproduct(begin,end){
    var str = '';
    str+="<tr>"
    for (var i = 0; i < tc_table.colname.length; i++) {
        var j=i+1;
        str+="<th class='c"+j+"'>"+tc_table.colname[i]+"</th>"
    }
    str+="</tr>"
    //var length=Math.min(5,tc_table.coldata.length)
    for(var i=begin;i<end;i++){
        str+="<tr>"
        str+="<th class='d1'>"
        str+="<input type='checkbox' name='tc_data' class='checkbox' id='box_innerInput1'/>"
        str+="<label for='box_innerInput1'>"+"</label>"
        str+="</th>"
        str+="<th class='d2'>"+tc_table.coldata[i].id+"</th>"
        str+="<th class='d3'>"+tc_table.coldata[i].name+"</th>"
        str+="<th class='d4'>"+tc_table.coldata[i].style+"</th>"
        str+="<th class='d5'>"+tc_table.coldata[i].pid+"</th>"
        str+="<th class='d6'>"+tc_table.coldata[i].size+"</th>"
        str+="<th class='d7'>"
        if(tc_table.coldata[i].image!=""){
            str+="<div class='data_image' id='data_image"+i+"'>"+"</div>"
        }
        str+="</th>"

        str+="</tr>"
    }
    $("#tc_table").append(str);
    for(var i=begin;i<end;i++) {
        $("#data_image"+i).css({"background-image":"url('"+tc_table.coldata[i].image+"')"});
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

//选择产品弹窗
function chooseproduct(){
    console.log('sss')
    $(".main").toggle();
}

function addplace(){
    kongjiannumber=kongjiannumber+1;
    var str = '';
    if(kongjiannumber<=1){
        str+="<div class='block2view'>"
    }else{
        str+="<div class='guodu'></div>"
        str+="<div class='block2view' style='margin-top:50px;'>"
    }
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext'>空间名称</span>"
    str+="<span class='btip'>2-6个字</span>"
    str+="<div><input type='text' name='placename'  class='titleinput4' placeholder='请输入' /></div>"
    str+="</div>"
    str+="<div class='block2label'>空间设计</div>"
    str+="<div class='btitle'>"
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext'>上传空间图</span>"
    str+="<span class='btip'>建议上传4:3比例图片，大小不超过500kb</span>"
    str+="</div>"
    str+="<div class='huxingcontainer'>"
    str+="<div class='huxingimage' id='kjimageview"+kongjiannumber+"'>"+"</div>"
    str+="<div class='uploadimage1'>"
    var a=1;
    var b=kongjiannumber;
    str+="<input style='width:185px;height:139px;position: absolute; top: 0; left: 0;opacity: 0;' type='file' accept='image/gif, image/jpg, image/png' onchange='showImg2(this,"+a+","+b+")' multiple/>"
    str+="</div>"
    str+="</div>"
   // str+="<div class='uploadimage1'></div>"
    str+="<div class='btitle2'>"
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext1'>设计说明</span>"
    str+="<textarea  name='styledecription'  class='titleinput3' placeholder='必填'></textarea>"
    str+="</div>"
    str+="<div class='block2label'>产品应用</div>"
    str+="<div class='btitle'>"
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext'>上传产品应用图</span>"
    str+="<span class='btip'>建议上传4:3比例图片，大小不超过500kb</span>"
    str+="</div>"

    str+="<div class='huxingcontainer'>"
    str+="<div class='huxingimage' id='prodimageview"+kongjiannumber+"'>"+"</div>"
    str+="<div class='uploadimage1'>"
    var a=2;
    var b=kongjiannumber;
    str+="<input style='width:185px;height:139px;position: absolute; top: 0; left: 0;opacity: 0;' type='file' accept='image/gif, image/jpg, image/png' onchange='showImg2(this,"+a+","+b+")' multiple/>"
    str+="</div>"
    str+="</div>"

    //str+="<div class='uploadimage1'></div>"
    str+="<div class='btitle2'>"
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext1'>设计说明</span>"
    str+="<textarea  name='styledecription'  class='titleinput3' placeholder='必填'></textarea>"
    str+="</div>"
    str+="<div class='block2label'>施工</div>"
    str+="<div class='btitle'>"
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext'>上传施工图</span>"
    str+="<span class='btip'>建议上传4:3比例图片，大小不超过500kb</span>"
    str+="</div>"

    str+="<div class='huxingcontainer'>"
    str+="<div class='huxingimage' id='sgimageview"+kongjiannumber+"'>"+"</div>"
    str+="<div class='uploadimage1'>"
    var a=3;
    var b=kongjiannumber;
    str+="<input style='width:185px;height:139px;position: absolute; top: 0; left: 0;opacity: 0;' type='file' accept='image/gif, image/jpg, image/png' onchange='showImg2(this,"+a+","+b+")' multiple/>"
    str+="</div>"
    str+="</div>"

   // str+="<div class='uploadimage1'></div>"
    str+="<div class='btitle2'>"
    str+="<span class='xinghao'>*</span>"
    str+="<span class='btext1'>设计说明</span>"
    str+="<textarea  name='styledecription'  class='titleinput3' placeholder='必填'></textarea>"
    str+="</div>"
    $("#addplace").append(str);
}

function showdel_fengmian(){
    if(fengmian.length>0){
        $(".del_fengmian").show().addClass('show');
        $("#upload-input").css({"height":"137px"})
    }else{
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-input").css({"height":"159px"})
    }
}
function showdel_fengmian1(){
    if(fengmian.length>0){
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-input").css({"height":"137px"})
    }else{
        $(".del_fengmian").hide().removeClass('show');
        $("#upload-input").css({"height":"159px"})
    }
}
function showImg(input) {
    var file = input.files[0];
    var url = window.URL.createObjectURL(file)
    console.log(url)
    fengmian.push(url)
    $("#preview").css({"background-image":"url('"+url+"')"});
}
function del_fengmian(){
    console.log('ss')
    fengmian.splice(0,1);
    $(".del_fengmian").hide().removeClass('show');
    $("#preview").css({"background-image":"url('"+"images/uploadscheme/b1.png"+"')"});
}
function showImg1(input) {
    var str="";
    $("#huxing").html("");
    for(var i=0;i<input.files.length;i++) {
        var file = input.files[i];
        var url = window.URL.createObjectURL(file)
        huxing.push(url)
        console.log(url)
    }
    for(var i=0;i<huxing.length;i++) {
        str += "<div class='huxingimages' id='huxing" + i + "'onmouseenter='showdel1("+i+")' onmouseleave='showdel1("+i+")'>"
        str+="<div class='del_img' id='del_huxing"+i+"' style='display: none;' onclick='del_image1("+i+")'>"+"删除"+"</div>"
        str+="</div>"
    }
    $("#huxing").append(str);
    for(var i=0;i<huxing.length;i++) {
        $("#huxing"+i).css({"background-image":"url('"+huxing[i]+"')"});
    }
}
function showImg2(input,a,b) {
    var str="";
    if(a==1){
        console.log('b'+b)
        var s="kjimageview"
        var z="kjimage"
        var kongjian1=[]
        $("#"+s+b).html("");
        if(kongjian.length<=b) {
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var url = window.URL.createObjectURL(file)
                kongjian1.push(url)
                console.log('sss' + i)
            }
            kongjian[b]=kongjian1
        }else{
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var url = window.URL.createObjectURL(file)
                kongjian[b].push(url)
            }
        }
        for(var i=0;i<kongjian[b].length;i++){
            str += "<div class='huxingimages' id='" +z+b+i + "' onmouseenter='showdel("+a+","+i+","+b+")' onmouseleave='showdel("+a+","+i+","+b+")'>"
            str+="<div class='del_img' id='del_"+z+b+i+"' style='display: none;' onclick='del_image("+a+","+b+","+i+")'>"+"删除"+"</div>"
            str+="</div>"
        }
        $("#"+s+b).append(str);
        for(var i=0;i<kongjian[b].length;i++){
            console.log('sasasas')
            console.log(kongjian[b][i])
            $("#"+z+b+i).css({"background-image":"url('"+kongjian[b][i]+"')"});
        }
    }else if(a==2){
        var s="prodimageview"
        var z="prodimage"
        $("#"+s+b).html("");
        var product1=[]
        if(product.length<=b) {
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var url = window.URL.createObjectURL(file)
                product1.push(url)
                console.log('sss' + i)
            }
            product[b]=product1
        }else{
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var url = window.URL.createObjectURL(file)
                product[b].push(url)
            }
        }
        for(var i=0;i<product[b].length;i++){
            str += "<div class='huxingimages' id='" +z+b+i + "' onmouseenter='showdel("+a+","+i+","+b+")' onmouseleave='showdel("+a+","+i+","+b+")'>"
            str+="<div class='del_img' id='del_"+z+b+i+"' style='display: none;' onclick='del_image("+a+","+b+","+i+")'>"+"删除"+"</div>"
            str+="</div>"
        }
        $("#"+s+b).append(str);
        for(var i=0;i<product[b].length;i++){
            console.log('sasasas')
            console.log(product[b][i])
            $("#"+z+b+i).css({"background-image":"url('"+product[b][i]+"')"});
        }
    }else if(a==3){
        var s="sgimageview"
        var z="sgimage"
        var sg1=[]
        $("#"+s+b).html("");
        if(sg.length<=b) {
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var url = window.URL.createObjectURL(file)
                sg1.push(url)
                console.log('sss' + i)
            }
            sg[b]=sg1
        }else{
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];
                var url = window.URL.createObjectURL(file)
                sg[b].push(url)
            }
        }
        for(var i=0;i<sg[b].length;i++){
            str += "<div class='huxingimages' id='" +z+b+i + "' onmouseenter='showdel("+a+","+i+","+b+")' onmouseleave='showdel("+a+","+i+","+b+")'>"
            str+="<div class='del_img' id='del_"+z+b+i+"' style='display: none;' onclick='del_image("+a+","+b+","+i+")'>"+"删除"+"</div>"
            str+="</div>"
        }
        $("#"+s+b).append(str);
        for(var i=0;i<sg[b].length;i++){
            $("#"+z+b+i).css({"background-image":"url('"+sg[b][i]+"')"});
        }
    }
    else{
        return false;
    }

}
function showdel(a,i,b) {
    console.log(z)
    var z=""
    if(a==1){
        z="kjimage"
    }else if(a==2){
        z="prodimage"
    }else if(a==3){
        z="sgimage"
    }
    var c=z+b+i
    $("#del_"+c).toggle();
}
function showdel1(i) {
    $("#del_huxing"+i).toggle();
}
function del_image(a,b,i) {
    if(a==1){
        kongjian[b].splice(i,1)
        console.log('del')
        console.log(kongjian[b])
        var s="kjimageview"
        var z="kjimage"
        $("#"+s+b).html("");
        var str="";
        for(var i=0;i<kongjian[b].length;i++){
            str += "<div class='huxingimages' id='" +z+b+i + "' onmouseenter='showdel("+a+","+i+","+b+")' onmouseleave='showdel("+a+","+i+","+b+")'>"
            str+="<div class='del_img' id='del_"+z+b+i+"' style='display: none;' onclick='del_image("+a+","+b+","+i+")'>"+"删除"+"</div>"
            str+="</div>"
        }
        $("#"+s+b).append(str);
        for(var i=0;i<kongjian[b].length;i++){
            $("#"+z+b+i).css({"background-image":"url('"+kongjian[b][i]+"')"});
        }
    }else if(a==2){
        product[b].splice(i,1)
        var s="prodimageview"
        var z="prodimage"
        $("#"+s+b).html("");
        var str="";
        for(var i=0;i<product[b].length;i++){
            str += "<div class='huxingimages' id='" +z+b+i + "' onmouseenter='showdel("+a+","+i+","+b+")' onmouseleave='showdel("+a+","+i+","+b+")'>"
            str+="<div class='del_img' id='del_"+z+b+i+"' style='display: none;' onclick='del_image("+a+","+b+","+i+")'>"+"删除"+"</div>"
            str+="</div>"
        }
        $("#"+s+b).append(str);
        for(var i=0;i<product[b].length;i++){
            $("#"+z+b+i).css({"background-image":"url('"+product[b][i]+"')"});
        }
    }else if(a==3){
        sg[b].splice(i,1)
        var s="sgimageview"
        var z="sgimage"
        $("#"+s+b).html("");
        var str="";
        for(var i=0;i<sg[b].length;i++){
            str += "<div class='huxingimages' id='" +z+b+i + "' onmouseenter='showdel("+a+","+i+","+b+")' onmouseleave='showdel("+a+","+i+","+b+")'>"
            str+="<div class='del_img' id='del_"+z+b+i+"' style='display: none;' onclick='del_image("+a+","+b+","+i+")'>"+"删除"+"</div>"
            str+="</div>"
        }
        $("#"+s+b).append(str);
        for(var i=0;i<sg[b].length;i++){
            $("#"+z+b+i).css({"background-image":"url('"+sg[b][i]+"')"});
        }
    }
}
function del_image1(i) {
    huxing.splice(i,1);
    var str="";
    $("#huxing").html("");
    for(var i=0;i<huxing.length;i++) {
        str += "<div class='huxingimages' id='huxing" + i + "'onmouseenter='showdel1("+i+")' onmouseleave='showdel1("+i+")'>"
        str+="<div class='del_img' id='del_huxing"+i+"' style='display: none;' onclick='del_image1("+i+")'>"+"删除"+"</div>"
        str+="</div>"
    }
    $("#huxing").append(str);
    for(var i=0;i<huxing.length;i++) {
        $("#huxing"+i).css({"background-image":"url('"+huxing[i]+"')"});
    }
}


//下拉列表
$(function(){
    selectMenu(0);
    selectMenu(1);
    selectMenu(2);
    selectMenu(3);
    selectMenu(4);
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