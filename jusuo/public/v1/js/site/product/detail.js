var product_info = null;
var product_similiar_list = null;
var product_kind_list = null;
var style_list = null;
var style_id = 0;

var more_kind_url = '';
var more_similiar_url = '';

var styles_api_url = '';


$(function () {

    //二维码
    $('.qrimage').qrcode({width: 180,height: 180,text: current_url});


    get_product_info();

    list_product_collocations()

    list_product_spaces();

    list_product_similiars();

    list_product_kinds();

    list_product_qas();

});

//导航栏
var scroH,navH,navH1,navH2,slideBarH,footerTop,slideBarFixed=false;
var nowTopIndex,topIndex,navigator_location_length=4;
$(function(){
    $(window).scroll(function(){
        scroH = $(this).scrollTop();
        navH = $("#slideBar").offset().top;
        navH1 = $("#product-similiars-container").offset().top;
        navH2 = $("#product-similiars-container").height();
        slideBarH = $("#slideBar").height();
        footerTop = $("#footer").offset().top

        for(topIndex=0;topIndex<navigator_location_length;topIndex++){
            if($("#navigator-location-"+topIndex).length>0){
                var module_top = $("#navigator-location-"+topIndex).offset().top;
                if(topIndex >= (navigator_location_length-1)){
                    var next_module_top = $("#footer").offset().top;
                }else{
                    var next_module_top = $("#navigator-location-"+(topIndex+1)).offset().top;
                }
                /*console.log('module_top_'+topIndex+":"+module_top)
                console.log('next_module_top'+topIndex+":"+next_module_top)*/
                if(  (module_top <= scroH+5 ) && (scroH+5 < next_module_top) ) {
                    if (nowTopIndex !== topIndex) {
                        nowTopIndex = topIndex;
                        $('#sidenav li').removeClass('active');
                        $('#slideItem-' + topIndex).addClass('active');
                    }
                    break;
                }
            }

        }

        if(!slideBarFixed&&scroH>=navH1+navH2+70){
            $("#slideBar").addClass('fixed');
            slideBarFixed = true;
        }
        if(slideBarFixed&&scroH<navH1+navH2+70){
            $("#slideBar").removeClass('fixed');
            slideBarFixed = false;
        }

        //不超过底部以上70px
        if(scroH > (footerTop - slideBarH - 70)){
            var should_top = scroH - (footerTop - slideBarH);
            should_top = parseFloat(should_top)+70
            $("#slideBar").css('top','-'+should_top+'px');
        }else{
            $("#slideBar").css('top','10px');

        }
    });
});


//切换图片
function changepicture(i){
    var b=0;
    document.getElementById("p_img"+i).className = "p_img1";
    document.getElementById("p_img"+product_info.active).className = "p_img";
    product_info.active=i;
    $("#p_imagehref").attr("href",product_info.photo_product[product_info.active]);
    $("#p_image").css({"background-image": "url('" + product_info.photo_product[i] + "')"});

}

//去商家详情
function go_seller_detail(url){
    window.open(url+"?__bs="+__cache_brand)
}

//去产品详情
function go_product_detail(web_id_code){
    if(web_id_code){
        window.open('/product/s/'+web_id_code+"?__bs="+__cache_brand)
    }

}

//初始化分享
function init_share(){
    //弹出朋友圈和微信的弹窗
    $(document).on('mousemove','.share-hover-btn', function(e){
        var obj = $(e.target)
        if (obj.parents('#share-outer').length <= 0){
            $('#share-outer').addClass('active');
        }
    });
    $(document).on('mouseout','.share-hover-btn', function(e){
        $('#share-outer').removeClass('active');
    });
    $('#qrcodeCanvas').qrcode({width:100,height:100,text:"https://www.ijusuo.com/mobile/product/s/"+product_info.web_id_code});

}

//收藏当前产品
function current_product_collect(){
    layer.load(1);
    var product_id = product_info.web_id_code
    if(product_info.collected==false){
        ajax_post(product_collect_api_url,{op:1,aid:product_id},function(res){

            if(res.status){
                product_info.count_fav = product_info.count_fav + 1;
                document.getElementById("shoucangnumber").innerText = product_info.count_fav;
                $("#shoucang").css({"color": "#1582FF"})
                $("#shoucangnumber").css({"color": "#1582FF"})
                // layer.msg('已收藏！', {icon: 1});
                product_info.collected = true;
                layer.msg(res.msg)
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
        ajax_post(product_collect_api_url,{op:2,aid:product_id},function(res){
            if(res.status){
                product_info.count_fav = product_info.count_fav - 1;
                document.getElementById("shoucangnumber").innerText = product_info.count_fav;
                $("#shoucang").css({"color": "#777777"})
                $("#shoucangnumber").css({"color": "#777777"})
                //  layer.msg('已取消收藏！', {icon: 2});
                product_info.collected = false;
                layer.msg(res.msg)
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

//发表问答
function commit_qa() {
    var content = $('#qa-input').text();
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    var final_content = content.replace(/^\s*|(\s*$)/g,"");
    if(final_content<= 0){
        // layer.msg('评论内容不能为空或全为空格！', {icon: 2});
        return;
    } else{
        layer.load(1)
        ajax_post(commit_qa_api_url, {
            content:final_content
        }, function(result){
            layer.closeAll('loading')
            if(result.status){
                $('#qa-input').html('')
                //刷新问答列表
                refresh_qa();
                layer.msg(result.msg);
            }else{
                if(result.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(result.msg)
                }
            }

        });
    }
}

//刷新问答列表
function refresh_qa(){
    $('#product-qas-container').html('')
    $('#i_qa_page').val(1);
    list_product_qas();
}

//check问答输入内容
function check_qa_content() {
    var cont=document.getElementById("qa-input");
    var content=cont.innerText
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var valuestr = content.trim();
    var revaue = content.replace(/^\s*|(\s*$)/g,"");

    if(revaue<= 0){
        if(content.length<=0){
            $('.pl_placeholder').show().addClass('show');
        }else{
            $('.pl_placeholder').hide().removeClass('show');
        }
        $('#btnButton').attr('disabled', true);
        $('#btnButton').css({"background-color":"#D9D9D9","cursor":"not-allowed"})
    } else {
        $('.pl_placeholder').hide().removeClass('show');
        $('#btnButton').attr('disabled', false);
        $('#btnButton').css({"background-color":"#1582FF","cursor":"pointer"})

    }
}

//获取更多问答
function list_more_qa(){
    layer.load(1)
    var now_page = $('#i_qa_page').val();
    $('#i_qa_page').val(parseInt(now_page)+1);
    list_product_qas();
}


//获取问答列表
function list_product_qas() {

    var query_options = {};
    query_options.page = $("#i_qa_page").val();
    api_url = set_url_query(product_qas_api_url,query_options);

    ajax_get(api_url, function (res) {

        layer.closeAll('load')
        if (res.status) {

            var datas = res.data;

            var current_page = $('#i_qa_page').val();

            $('.qa-total').html(datas.total)



            if(datas.data.length>0){
                var html = template('product-qas-tpl', {datas: datas.data});

                $('#product-qas-container').append(html)

                if(datas.current_page == datas.last_page){
                    $('#b-list-more-qa').hide()
                }else{
                    $('#b-list-more-qa').hide()

                }
            }else{
                if(current_page<=1){
                    var html = template('list-empty-tpl');
                    $('#product-qas-container').html(html)
                }else{
                    $('#b-list-more-qa').hide()
                }

            }



        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//打开方案
function click_album(code){
    window.open('/album/s/'+code+"?__bs="+__cache_brand);
}

//获取更多相关方案
function get_more_album(){
    window.open('/album?stl='+style_id+"&__bs="+__cache_brand);
}

//获取相关方案列表
function list_product_albums() {

    ajax_get(product_albums_api_url+"?stl="+style_id+"&__bs="+__cache_brand, function (res) {

        if (res.status) {

            var datas = res.data;

            if(datas.length>0) {

                var html = template('product-albums-tpl', {datas: datas});

                $('#product-albums-container').html(html);
            }
            else{
                $('#product_lookall').html('暂时还没有此产品的方案，点击这里，查看其它精彩方案 >');
            }

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}


//相关方案导航
function change_pswiper(i,id){
    $('.m_swiper_span1').addClass('m_swiper_span');
    $('.m_swiper_span').removeClass('m_swiper_span1');
    $('#p_swiper'+i).addClass('m_swiper_span1');
    style_id = id;
    list_product_albums();
}

//获取同类列表
function list_styles() {

    ajax_get(styles_api_url, function (res) {

        if (res.status) {

            var datas = res.data;
            style_list = datas;

            var html = template('styles-tpl', {datas: datas});

            $('#product_swiper').html(html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//查看全部同类产品
function get_more_kind(){
    if(more_kind_url){
        window.open(more_kind_url)
    }
}

//查看全部相似产品
function get_more_similiar(){
    if(more_similiar_url){
        window.open(more_similiar_url)
    }
}

//同类的收藏
function collected(i){

    layer.load(1);
    var product_id = product_kind_list[i].web_id_code
    if(product_kind_list[i].collected==false){
        ajax_post(product_collect_api_url,{op:1,aid:product_id},function(res){
            if(res.status){
                var a=product_kind_list[i].count_fav+1;
                product_kind_list[i].count_fav=a;
                $("#collectednumber_"+i).html(product_kind_list[i].count_fav);

                $("#collectednumber_"+i).css({"color":"#1582FF"});
                document.getElementById("collected_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#collected_"+i).css({"color":"#1582FF"});
                product_kind_list[i].collected=true;
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
        ajax_post(product_collect_api_url,{op:2,aid:product_id},function(res){
            if(res.status){
                var a=product_kind_list[i].count_fav-1;
                product_kind_list[i].count_fav=a;
                $("#collectednumber_"+i).html(product_kind_list[i].count_fav);
                $("#collectednumber_"+i).css({"color":"#B7B7B7"});
                document.getElementById("collected_"+i).className = "iconfont icon-shoucang2";
                $("#collected_"+i).css({"color":"#B7B7B7"})
                product_kind_list[i].collected=false;
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

//相似产品的收藏
function collected1(i){
    layer.load(1);
    var product_id = product_similiar_list[i].web_id_code
    if(product_similiar_list[i].collected==false){
        ajax_post(product_collect_api_url,{op:1,aid:product_id},function(res){
            if(res.status){
                var a=product_similiar_list[i].count_fav+1;
                product_similiar_list[i].count_fav=a;
                $("#collectednumber1_"+i).html(product_similiar_list[i].count_fav);

                $("#collectednumber1_"+i).css({"color":"#1582FF"});
                document.getElementById("collected1_"+i).className = "iconfont icon-buoumaotubiao44";
                $("#collected1_"+i).css({"color":"#1582FF"});
                product_similiar_list[i].collected=true;
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
        ajax_post(product_collect_api_url,{op:2,aid:product_id},function(res){
            if(res.status){
                var a=product_similiar_list[i].count_fav-1;
                product_similiar_list[i].count_fav=a;
                $("#collectednumber1_"+i).html(product_similiar_list[i].count_fav);
                $("#collectednumber1_"+i).css({"color":"#B7B7B7"});
                document.getElementById("collected1_"+i).className = "iconfont icon-shoucang2";
                $("#collected1_"+i).css({"color":"#B7B7B7"})
                product_similiar_list[i].collected=false;
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

//获取同类列表
function list_product_kinds() {

    ajax_get(product_kinds_api_url, function (res) {

        if (res.status) {

            var datas = res.data;
            product_kind_list = datas.res;
            product_series = datas.series;

            var html = template('product-kinds-tpl', {datas: product_kind_list,series:product_series});

            $('#product-kinds-container').html(html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取相似产品列表
function list_product_similiars() {

    ajax_get(product_similiars_api_url, function (res) {

        if (res.status) {

            var datas = res.data;
            product_similiar_list = datas;

            var html = template('product-similiars-tpl', {datas: datas});

            $('#product-similiars-container').html(html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取产品搭配列表
function list_product_spaces() {

    ajax_get(product_spaces_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            var html = template('product-spaces-tpl', {datas: datas});

            $('#product-spaces-container').html(html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取产品搭配列表
function list_product_collocations() {

    ajax_get(product_collocations_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            var html = template('product-collocations-tpl', {datas: datas});

            $('#product-collocations-container').html(html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取产品配件列表
function list_product_accessories() {

    ajax_get(product_accessories_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            var html = template('product-accessories-tpl', {datas: datas});

            $('#product-accessories-container').html(html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取产品基本信息
function get_product_info() {
    layer.load(1)

    ajax_get(product_info_api_url, function (res) {

        layer.closeAll("loading");

        if (res.status) {

            var product = res.data;

            //产品图默认索引
            product.active = 0;

            product_info = product;

            //设置顶部标题
            $('.product-title').html(product_info.name)
            $('#product-category').html(product_info.product_category)

            var top_basic_html = template('top-basic-tpl', {product: product});
            var sales_detail_html = template('sales-detail-tpl', {product: product});
            var detail_table_html = template('detail-table-tpl', {product: product});
            var product_video_html = template('product-videos-tpl', {product: product});
            $('#p_detail').html(top_basic_html)
            $('#company').html(sales_detail_html)
            $('#detail-table').html(detail_table_html)
            $('#product-videos-container').html(product_video_html)

            //如果是产品，则查询产品配件
            if(product_info.is_product==1){
                list_product_accessories();
            }

            //设置同类产品查看全部url
            more_kind_url = product_info.more_kind_url
            more_similiar_url = product_info.more_similiar_url

            styles_api_url = list_styles_api_prefix;

            //加载相关方案的风格
            list_styles();

            //加载相关方案
            list_product_albums();

            //初始化分享
            init_share();

        } else {
            layer.msg(res.msg)
        }


    }, function () {
    })
}