//我的统计
var tongjinav=0
//top5方案浏览量
var top5view={
    "list":[{"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false},
        {"image":"images/sjfa/8.png","name":"现代简约两居","look":219,"collected":88, "collect":false}]
}

//统计
var chart_top_product_visit = [];
var chart_top_product_collect = [];


$(document).ready(function(){

    //我的统计
    get_album_chart();
    get_product_chart();
    get_product_chart_use();

    var str=""
    var a=["方案统计","产品统计"]
    for(var i=0;i<a.length;i++){
        if(tongjinav==i){
            str+="<div class='designtitle' id='g"+i+"' onclick='tjchangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='g"+i+"' onclick='tjchangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#tongjidaohang").append(str);
});


//获取产品筛选项
function get_album_chart(){
    $.get('/center/statistic/api/album_visit',function(res){
        console.log(res);
        if(res.status){
            var data = res.data;
            //方案统计折线图
            $('#chart_album_yes_visit_num').html(data.yes_num);
            $("#chart_album_month_visit_num").html(data.month_num);
            var lineChartData = {
                //labels: ["12/12", "12/13", "12/14", "12/15", "12/16", "12/17", "12/18"],
                labels: data.date,
                datasets: [
                    {
                        // label: "My First dataset",
                        fill: false,
                        lineTension: 0.1,
                        scaleGridLineColor : "rgba(248,248,248,1)",
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#1582FF",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "#1582FF",
                        pointBackgroundColor: "#1582FF",
                        pointBorderWidth: 4,
                        pointHoverRadius: 2,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 4,
                        pointRadius: 2,
                        pointHitRadius: 10,
                        //data: [30, 50, 30, 81, 74, 70, 52],
                        data: data.num,
                        spanGaps: false,
                        label: '浏览量',
                    }
                ],
            };
            var ctx = document.getElementById("lines-graph").getContext("2d");
            var LineChart = new Chart(ctx, {
                type: 'line',
                data: lineChartData,
                responsive: true,
                bezierCurve : false,
                scaleGridLineColor: "rgba(0,0,0,.05)",
            });
        }

    });

    $.get('/center/statistic/api/album_collect',function(res){
        if(res.status){
            var data = res.data;
            $('#chart_album_yes_collect_num').html(data.yes_num);
            $("#chart_album_month_collect_num").html(data.month_num);
            var lineChartData = {
                //labels: ["12/12", "12/13", "12/14", "12/15", "12/16", "12/17", "12/18"],
                labels: data.date,
                datasets: [
                    {
                        // label: "My First dataset",
                        fill: false,
                        lineTension: 0.1,
                        scaleGridLineColor : "rgba(248,248,248,1)",
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#1582FF",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "#1582FF",
                        pointBackgroundColor: "#1582FF",
                        pointBorderWidth: 4,
                        pointHoverRadius: 2,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 4,
                        pointRadius: 2,
                        pointHitRadius: 10,
                        //data: [30, 50, 30, 81, 74, 70, 52],
                        data: data.num,
                        spanGaps: false,
                        label: '收藏量',
                    }
                ],
            };
            var ctx1 = document.getElementById("lines-graph1").getContext("2d");
            var LineChart = new Chart(ctx1, {
                type: 'line',
                data: lineChartData,
                responsive: true,
                bezierCurve : false,
                scaleGridLineColor: "rgba(0,0,0,.05)",
            });
        }
    });

    $.get('/center/statistic/api/album_top',function(res){
        console.log(res);
        var data = res.data;

        //收藏量
        $("#top5view1").html('');
        var str="";
        for(var i=0;i<data.collect.length;i++){
            str+="<div class='topitem'>"
            //str+="<div class='topimage' id='top1image"+i+"' >"+"</div>"
            str+='<div class="topimage" id="top1image'+i+'" onclick="click_album(&quot;'+data.collect[i].web_id_code+'&quot;)"/>'
            str+="<div class='topname'>"+data.collect[i].title+"</div>"
            str+="<div class='d_detail1f'>";
            str+="<span class='iconfont icon-chakan'  style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str+="<span class='looknumber'>"+data.collect[i].count_visit+"</span>";
            str+="<span class='iconfont icon-shoucang2' id='topf1collected_"+i+"' style='color:#B7B7B7;margin-left:20px;'>"+"</span>";
            str+="<span class='looknumber' id='topf1collectednumber_"+i+"'>"+data.collect[i].count_fav+"</span>";
            str+="</div>";
            str+="</div>"
        }
        $("#top5view1").append(str);
        for(var i=0;i<data.collect.length;i++){
            $("#top1image"+i).css({"background-image":"url('"+data.collect[i].photo_cover+"')"});
        }

        //浏览量
        $("#top5view").html("");
        var str1="";
        for(var i=0;i<data.visit.length;i++){
            str1+="<div class='topitem'>"
            str1+='<div class="topimage" id="topimage'+i+'" onclick="click_album(&quot;'+data.visit[i].web_id_code+'&quot;)"/>'
            str1+="<div class='topname'>"+data.visit[i].title+"</div>"
            str1+="<div class='d_detail1f'>";
            str1+="<span class='iconfont icon-chakan' style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str1+="<span class='looknumber'>"+data.visit[i].count_visit+"</span>";
            str1+="<span class='iconfont icon-shoucang2' id='topfcollected_"+i+"' style='color:#B7B7B7;margin-left:20px;'>"+"</span>";
            str1+="<span class='looknumber' id='topfcollectednumber_"+i+"' >"+data.visit[i].count_fav+"</span>";
            str1+="</div>";
            str1+="</div>"
        }
        $("#top5view").append(str1);
        for(var i=0;i<data.visit.length;i++){
            $("#topimage"+i).css({"background-image":"url('"+data.visit[i].photo_cover+"')"});
        }

    });
}

function get_product_chart(){
    $.get('/center/statistic/api/product_top',function(res){
        var data = res.data;
        chart_top_product_visit = res.data.visit;
        chart_top_product_collect = res.data.collect;

        //浏览
        $("#top5viewpro").html("");
        var str="";
        for(var i=0;i<chart_top_product_visit.length;i++){
            str+="<div class='topitem1'>"
            //str+="<div class='topimage1' id='topproimage"+i+"'>"+"</div>"
            str+='<div class="topimage1" id="topproimage'+i+'" onclick="go_detail(&quot;'+chart_top_product_visit[i].web_id_code+'&quot;)"/>'
            str+="<div class='topname'>"+chart_top_product_visit[i].productTitle+"</div>"
            str+="<div class='d_detail1f'>";
            str+="<span class='iconfont icon-chakan'  style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str+="<span class='looknumber'>"+chart_top_product_visit[i].count_visit+"</span>";
            if(chart_top_product_visit[i].collected){
                str+="<span class='iconfont icon-buoumaotubiao44' id='toppcollected_"+i+"' style='color:#1582FF;margin-left:20px;' onclick='toppcollected("+i+")'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-shoucang2' id='toppcollected_"+i+"' style='color:#B7B7B7;margin-left:20px;' onclick='toppcollected("+i+")'>"+"</span>";
            }
            str+="<span class='looknumber' id='toppcollectednumber_"+i+"' >"+chart_top_product_visit[i].count_fav+"</span>";
            str+="</div>";
            str+="</div>"
        }
        $("#top5viewpro").append(str);
        for(var i=0;i<chart_top_product_visit.length;i++){
            $("#topproimage"+i).css({"background-image":"url('"+chart_top_product_visit[i].cover+"')"});
        }

        //收藏
        $("#top5viewpro1").html('');
        var str="";
        for(var i=0;i<chart_top_product_collect.length;i++){
            str+="<div class='topitem1'>"
            //str+="<div class='topimage1' id='topimage1"+i+"'>"+"</div>"
            str+='<div class="topimage1" id="toppro1image'+i+'" onclick="go_detail(&quot;'+chart_top_product_collect[i].web_id_code+'&quot;)"/>'
            str+="<div class='topname'>"+chart_top_product_collect[i].productTitle+"</div>"
            str+="<div class='d_detail1f'>";
            str+="<span class='iconfont icon-chakan'  style='color:#B7B7B7;margin-left:14px;'>"+"</span>";
            str+="<span class='looknumber'>"+chart_top_product_collect[i].count_visit+"</span>";
            if(chart_top_product_collect[i].collected){
                str+="<span class='iconfont icon-buoumaotubiao44' id='topp1collected_"+i+"' style='color:#1582FF;margin-left:20px;' onclick='topp1collected("+i+")'>"+"</span>";
            }else{
                str+="<span class='iconfont icon-shoucang2' id='topp1collected_"+i+"' style='color:#B7B7B7;margin-left:20px;' onclick='topp1collected("+i+")'>"+"</span>";
            }
            str+="<span class='looknumber' id='topp1collectednumber_"+i+"' )'>"+chart_top_product_collect[i].count_fav+"</span>";
            str+="</div>";
            str+="</div>"
        }
        $("#top5viewpro1").append(str);
        for(var i=0;i<chart_top_product_collect.length;i++){
            $("#toppro1image"+i).css({"background-image":"url('"+chart_top_product_collect[i].cover+"')"});
        }

    })
}

function get_product_chart_use(){
    $.get('/center/statistic/api/product_use',function(res){
        console.log(res);
        if(res.status){
            var data = res.data;
            //产品条形图
            var ctx2 = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx2, {
                type: 'horizontalBar',
                data: {
                    labels: data.name,
                    datasets: [{
                        label: '百分比',
                        data: data.time,
                        borderColor:'#1582FF',
                        backgroundColor:'#1582FF',
                        borderWidth: 1,
                    }]
                }
            });
        }

    })
}

//我的统计浏览量产品的收藏
function toppcollected(i){
    layer.load(1);
    var product_id = chart_top_product_visit[i].web_id_code;

    if(chart_top_product_visit[i].collected==false){


        ajax_post('/product/api/collect',{op:1,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_visit[i].count_fav + 1;
                chart_top_product_visit[i].count_fav = a;
                $("#toppcollectednumber_"+i).html(a);

                $("#toppcollectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("toppcollected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#toppcollected_"+i).css({"color":"#1582FF"});

                chart_top_product_visit[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post('/product/api/collect',{op:2,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_visit[i].count_fav - 1;
                chart_top_product_visit[i].count_fav = a;
                $("#toppcollectednumber_"+i).html(a);
                $("#toppcollectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("toppcollected_"+i).className = "iconfont icon-shoucang2";
                $("#toppcollected_"+i).css({"color":"#B7B7B7"})

                chart_top_product_visit[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
    console.log(chart_top_product_visit[i].collected);

}

//我的统计收藏量产品的收藏
function topp1collected(i){

    layer.load(1);
    var product_id = chart_top_product_collect[i].web_id_code;

    if(chart_top_product_collect[i].collected==false){


        ajax_post('/product/api/collect',{op:1,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_collect[i].count_fav + 1;
                chart_top_product_collect[i].count_fav = a;
                $("#topp1collectednumber_"+i).html(a);

                $("#topp1collectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("topp1collected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#topp1collected_"+i).css({"color":"#1582FF"});

                chart_top_product_collect[i].collected = true;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post('/product/api/collect',{op:2,aid:product_id},function(res){

            if(res.status){
                var a = chart_top_product_collect[i].count_fav - 1;
                chart_top_product_collect[i].count_fav = a;
                $("#topp1collectednumber_"+i).html(a);

                $("#topp1collectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("topp1collected_"+i).className = "iconfont icon-shoucang2";
                $("#topp1collected_"+i).css({"color":"#B7B7B7"})

                chart_top_product_collect[i].collected = false;
            }else{
                layer.msg(res.msg)
            }
            layer.closeAll("loading");

        },function(){})
    }
}

function tjchangenav(i){
    if(tongjinav==i){
        return;
    }else{
        console.log(i)
        for(var a=0;a<2;a++){
            if(a!=i){
                document.getElementById("g"+a).className = "designtitle1";
                $('#g'+a+'content').hide().removeClass('show')
            }else{
                tongjinav=i;
                document.getElementById("g"+a).className = "designtitle";
                $('#g'+a+'content').show().addClass('show')
            }

        }
    }
}
