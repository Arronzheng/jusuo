$(function () {
    var queryString = window.location.search.slice(1);
    queryString = queryString.split('#')[0];
    queryString = encodeURIComponent(queryString)

    //获取筛选类型数据
    ajax_get(filter_types_api_url+"&query="+queryString,function(res){

        if(res.status && res.data.length>0){

            filter_types = res.data;
            console.log(filter_types);
            var html = template('filter-type-tpl', {data:res.data});

            $('#allnav').html(html)

        }

    },function(){})


    //获取设计方案列表数据
    get_album_list();

    $('#clear-keyword').click(function(){
        $('#i_kw').val('');
        get_album_list();
    });

});

//去方案详情
function click_album(id_code){
    window.open("/album/s/"+id_code+"?__bs="+__cache_brand)
}

//跳转本页
function href_page(){
    var query_options = {};
    query_options.stl = $("#i_stl").val();
    query_options.ht = $("#i_ht").val();
    query_options.spt = $("#i_spt").val();
    query_options.ca = $("#i_ca").val();
    query_options.order = $("#i_sort_order").val();
    query_options.isrw = $("#i_isrw").val();
    query_options.page = $("#i_page").val();
    page_url = set_url_query(album_page_api,query_options);
    location.href=page_url
}

//获取设计方案列表数据
function get_album_list(scrollTop){
    var query_options = {};
    query_options.k = $("#i_kw").val();
    query_options.stl = $("#i_stl").val();
    query_options.ht = $("#i_ht").val();
    query_options.spt = $("#i_spt").val();
    query_options.ca = $("#i_ca").val();
    query_options.order = $("#i_sort_order").val();
    query_options.isrw = $("#i_isrw").val();
    query_options.page = $("#i_page").val();
    query_options.dsn = dsn_param;
    query_options.dlr = dlr_param;
    query_options.__bs = __cache_brand;
    api_url = set_url_query(album_list_api,query_options);

    layer.load(1)
    ajax_get(api_url,function(res){

        layer.closeAll("loading");

        if(res.status){
            $('#data-count').html(res.data.total)
            var current_page = res.data.current_page;
            var total = res.data.total;
            $('#i_page').val(current_page)
            //初始化页码
            init_pager(current_page,total)

            album_list = res.data.data;

            if(res.data.data.length>0){
                var html = template('album-list-tpl', {data:res.data.data});
                $('#project').html(html)
            }else{
                var html = template('album-list-empty');
                $('#project').html(html)
            }

            if($('#i_kw').val()!=''){
                $('#keyword').html('，包含关键字 "'+$('#i_kw').val()+'"');
                $('#clear-keyword').removeClass('hidden');
            }
            else{
                $('#keyword').html('');
                $('#clear-keyword').addClass('hidden');
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

//切换精选方案的选择
function changechooselabel(obj){
    var choosejinxuan = $('#i_isrw').val();
    console.log(parseInt(choosejinxuan))
    if(parseInt(choosejinxuan)==0){
        $(obj).removeClass('icon-xuankuang1').addClass('icon-xuankuang');
        $("#chooselabel").css({"color":"#1582FF"});
        $('#i_isrw').val(1)
    }else{
        $(obj).removeClass('icon-xuankuang').addClass('icon-xuankuang1');
        $("#chooselabel").css({"color":"#333333"});

        $('#i_isrw').val(0)
    }

    get_album_list();
}

//切换排序的选择
function change_paixu(obj){
    var paixu_block = $(obj).parents('.paixu-block');
    var type = paixu_block.attr('data-type');
    var sort_order = $('#i_sort_order').val()
    sort_order_array = sort_order.split('_')
    var sort = sort_order_array[0]
    var order = sort_order_array[1]
    //变更了排序类型，则从降序开始
    if(sort != type){
        sort = type;
        order = 'desc'
    }else{
        if(order=='desc'){
            order = 'asc'
        }else{
            order = 'desc'
        }
    }

    //设置排序值
    $('#i_sort_order').val(sort+"_"+order)
    //修改排序title
    $('#paixu').find('.paixu_label').removeClass('active1')
    paixu_block.find('.paixu_label').addClass('active1')
    //修改排序icon
    $('#paixu').find('.iconfont').removeClass('active')
    paixu_block.find('.iconfont.'+order).addClass('active')
    $('#i_page').val(1)

    get_album_list();
}

//切换筛选类型的选择
function change_filter_type(obj,type,value){
    var option_item = $(obj);
    var row = option_item.parent('.nav_text')
    row.find('.nav_t').removeClass('active')
    option_item.addClass('active')
    $('#i_'+type).val(value)
    //重设页数
    $('#i_page').val(1)
    get_album_list();
}

//设计方案的收藏
function collected(i){
    layer.load(1);
    var album_id = album_list[i].web_id_code
    if(album_list[i].collected==false){
        ajax_post(album_collect_api_url,{op:1,aid:album_id},function(res){

            if(res.status){
                var a=album_list[i].count_fav+1;
                album_list[i].count_fav=a;
                $("#collectednumber_"+i).html(album_list[i].count_fav);

                $("#collectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("collected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#collected_"+i).css({"color":"#1582FF"});
                album_list[i].collected=true;
            }else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }

            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post(album_collect_api_url,{op:2,aid:album_id},function(res){
            if(res.status){
                var a=album_list[i].count_fav-1;
                album_list[i].count_fav=a;
                $("#collectednumber_"+i).html(a);
                $("#collectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("collected_"+i).className = "iconfont icon-shoucang2";
                $("#collected_"+i).css({"color":"#B7B7B7"})
                album_list[i].collected=false;
            }else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");

        },function(){})

    }
}

//设计方案的点赞
function like(i){
    layer.load(1);
    var album_id = album_list[i].web_id_code
    if(album_list[i].liked==false){
        ajax_post(album_like_api_url,{op:1,aid:album_id},function(res){

            if(res.status){
                var a=album_list[i].count_praise+1;
                album_list[i].count_praise=a;
                $("#likenumber_"+i).html(album_list[i].count_praise);
                $("#likenumber_"+i).css({"color":"#1582FF"});
                document.getElementById("like_"+i).className = "iconfont icon-dianzan";
                $("#like_"+i).css({"color":"#1582FF","font-size":"16px"});
                album_list[i].liked=true;
            }else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");

        },function(){})

    }else{
        ajax_post(album_like_api_url,{op:2,aid:album_id},function(res){
            if(res.status){
                var a=album_list[i].count_praise-1;
                album_list[i].count_praise=a;
                $("#likenumber_"+i).html(a);
                $("#likenumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("like_"+i).className = "iconfont icon-dianzan2";
                $("#like_"+i).css({"color":"#B7B7B7","font-size":"16px"})
                album_list[i].liked=false;
            }else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");

        },function(){})

    }
}

function init_pager(nowPage,total){
    // xlPaging.js 使用方法
    var nowpage = $("#pager").paging({
        nowPage: nowPage, // 当前页码
        pageNum: total==0?1:Math.ceil(total / 30), // 总页码
        buttonNum: Math.ceil(total / 30), //要展示的页码数量
        canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
        showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
        callback: function (num) { //回调函数
            var page = num;
            $('#i_page').val(page);
            get_album_list(true);

        }
    });
}