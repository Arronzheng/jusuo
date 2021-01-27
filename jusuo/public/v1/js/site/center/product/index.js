//产品列表的表格
var colname = ["序号","名称","产品型号","系列名","工艺类别","色系","规格","产品状态",'显示状态',"缩略图","收藏"]
if(show_structure==1){
    colname = ["序号","名称","产品型号","系列名","工艺类别","色系","规格","产品状态",'显示状态',"产品结构","缩略图","收藏"]
}
var tc_table={
    "colname":colname,
    "coldata":[{"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
        "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":true},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":false},
        {"name":"TOTO马桶","appstyle":"按需显示","gystyle":"按需显示","color":"白色","size":"800x800mm","status":"正常",
            "jiegou":"按需显示","image":"images/cpk/7.png","shoucang":true},]
}



$(document).ready(function(){
    selectMenu(0);
    selectMenu(1);
    selectMenu(2);
    selectMenu(3);
    selectMenu(4);
    selectMenu(5);

    //产品列表
    get_product_list_filter_types();
    get_user_brand();
    get_product_list();
});


//获取产品筛选项
function get_product_list_filter_types(){
    var queryString = window.location.search.slice(1);
    queryString = queryString.split('#')[0];
    queryString = encodeURIComponent(queryString)

    ajax_get('/center/product/api/list_filter_types?query='+queryString,function(res){

        if(res.status){
            product_filter_types = res.data;
            console.log(product_filter_types);
            for(var i=0;i<product_filter_types.length;i++){
                //色系
                if(product_filter_types[i].value=='clr'){
                    $("#product_clr_ul").empty()
                    var type = 'clr';
                    var product_clr_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_clr_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_clr_ul").append(product_clr_li);
                }

                //工艺类别
                if(product_filter_types[i].value=='tc'){
                    $("#product_tc_ul").empty();
                    var type = 'tc';
                    var product_tc_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_tc_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_tc_ul").append(product_tc_li);
                }

                //应用类别
                if(product_filter_types[i].value=='ac'){
                    $("#product_ac_ul").empty()
                    var type = 'ac';
                    var product_ac_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_ac_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_ac_ul").append(product_ac_li)
                }

                //产品规格
                if(product_filter_types[i].value=='spec'){
                    $("#product_spec_ul").empty();
                    var type = 'spec';
                    var product_spec_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_spec_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_spec_ul").append(product_spec_li)
                }

                //状态
                if(product_filter_types[i].value=='status'){
                    $("#product_status_ul").empty();
                    var type = 'status';
                    var product_status_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_status_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_status_ul").append(product_status_li)
                }

                //产品结构
                if(product_filter_types[i].value=='str'){
                    $("product_str_ul").empty();
                    var type = 'str';
                    var product_str_li = '<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+ '' +'&quot;)">'+"全部"+"</li>";
                    for(var j=0;j<product_filter_types[i].data.length;j++){
                        var value = product_filter_types[i].data[j].id;
                        product_str_li+='<li onclick="change_product_filter_type( &quot;'+type+'&quot;,&quot;'+value+'&quot;)">'+ product_filter_types[i].data[j].name +'</li>'
                    }
                    $("#product_str_ul").append(product_str_li)
                }
            }
        }
    });
}

function change_product_filter_type(type,value){
    var id = 'product_'+type+'_value';
    $("#"+id).val(value)
}

function get_user_brand(){
    ajax_get('/center/product/api/get_designer_brand',function(res){
            console.log(res.data);
            if(res.status){
                product_brand_data = res.data;
                $("#product_brand_id").html(product_brand_data.brand_name);

            }
        },
        function(){});
}

function get_product_list(){
    var query_options = {};

    query_options.name = $('#product_name_value').val();
    query_options.ac = $('#product_ac_value').val();
    query_options.tc = $("#product_tc_value").val();
    query_options.clr = $("#product_clr_value").val();
    query_options.spec = $("#product_spec_value").val();
    query_options.status = $("#product_status_value").val();
    query_options.str = $("#product_str_value").val();

    layer.load(1)
    $.get('/center/product/api/product_list',query_options,function(res){
        layer.closeAll("loading");
        console.log(res);
        if(res.status==1){
            product_page_list = res.data;
            console.log(product_page_list);

            // xlPaging.js 我的产品列表分页使用方法
            var nowpage1 = $("#page1").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(product_page_list.length / 6), // 总页码
                buttonNum: Math.ceil(product_page_list.length / 6), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ1").html("");
                    var txt="<table border='0' id='tc_table'></table>"
                    $("#produ1").append(txt);
                    var total=Math.min(num*6,product_page_list.length)
                    product((num-1)*6,total);
                }
            });
            var endPage = 6;
            if(product_page_list.length < 6){
                endPage =  product_page_list.length;
            }
            product(nowpage1.options.nowPage-1,endPage)
        }
    });
}

//我的产品列表html
function product(begin,end){
    /*console.log(begin)
    console.log(end)*/
    $("#tc_table").html('');
    var str = '';
    str+="<tr>"
    for (var i = 0; i < tc_table.colname.length; i++) {
        var j=i+1;
        str+="<th class='c1'>"+tc_table.colname[i]+"</th>"
    }
    str+="</tr>"
    for(var i=begin;i<end;i++){
        str+="<tr>"
        str+="<th class='d1'>"+(i+1)+"</th>"
        str+="<th class='d1'>"+product_page_list[i].productTitle+"</th>"
        str+="<th class='d1'>"+product_page_list[i].code+"</th>"
        str+="<th class='d1'>"+product_page_list[i].series_text+"</th>"
        /*str+="<th class='d1'>"+product_page_list[i].ac_text+"</th>"*/
        str+="<th class='d1'>"+product_page_list[i].tc_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].colors_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].spec_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].status_text+"</th>"
        str+="<th class='d1'>"+product_page_list[i].visible_text+"</th>"
        if(show_structure==1) {
            str += "<th class='d1'>" + product_page_list[i].str_text + "</th>"
        }
        str+="<th class='d1'>"
        /*if(product_page_list[i].cover!=""){
        }*/
        str+="<a href='"+product_page_list[i].product_detail_href+"' target='_blank'><div class='data_image' id='data_image"+i+"'>"+"</div></a>"

        str+="</th>"
        str+="<th class='d1'>"
        if(product_page_list[i].collected==true){
            str+="<div class='scbotton' onclick='proshoucang("+i+")' id='proshoucang"+i+"'>"+"取消收藏"+"</div>"
        }else{
            str+="<div class='scbotton1' onclick='proshoucang("+i+")' id='proshoucang"+i+"'>"+"收藏"+"</div>"
        }
        str+="</th>"

        str+="</tr>"
    }

    $("#tc_table").append(str);
    for(var i=begin;i<end;i++) {
        $("#data_image"+i).css({"background-image":"url('"+product_page_list[i].cover+"')"});
    }
}

//收藏产品
function proshoucang(i){

    var album_id = product_page_list[i].web_id_code;
    if(product_page_list[i].collected == false){
        //点击时未收藏
        ajax_post('/product/api/collect',{op:1,aid:album_id},function(res){

            if(res.status){
                $("#proshoucang"+i).html("取消");
                document.getElementById("proshoucang"+i).className = "scbotton";
                product_page_list[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }else{
        ajax_post('/product/api/collect',{op:2,aid:album_id},function(res){

            if(res.status){
                $("#proshoucang"+i).html("收藏");
                document.getElementById("proshoucang"+i).className = "scbotton1";
                product_page_list[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}