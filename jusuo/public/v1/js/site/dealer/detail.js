var company;

function getDealerInfo(){
    layer.load(1);
    $.get('/dealer/get_info_login',{__bs:__cache_brand,dealer_id:dealer_id}, function(res) {
        if (res.status == 1) {
            company = res.data.data;

            //设置版头形象照

            var str = '';
            str+="<div class='h_image' id='h_image'>";
            str+="<div class='person_image' id='person_image'>"+"</div>";
            str+="<div class='perback'>"+"</div>";
            str+="<div class='person'>";
            str+="<span class='personnanme'>"+company.name+"</span>";
            //if(company.identity==true)
                str+="<span class='iconfont icon-shimingrenzheng'>"+"</span>";
            str+="<div class='xingji'>";
            str+="<div class='xingjiq'>";
            str+="<span class='zuanshi' id='xingji'>"+"</span>";
            str+="</div>";
            str+="<span class='xingjinumber'>"+company.level+"级"+"</span>";
            str+="</div>";
            str+="</div>";
            str+="</div>";
            str+="<div class='h_bottom'>";
            str+="<div class='h_left'>";
            str+="<div class='h_detail'>";
            str+="<div class='h_block1'>";
            str+="<div class='h_blockspan'>"+company.designer+"</div>";
            str+="<div class='h_blockspan1'>"+"设计师"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan'>"+company.album+"</div>";
            str+="<div class='h_blockspan1'>"+"设计方案"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan' id='span-count-fav'>"+company.fav+"</div>";
            str+="<div class='h_blockspan1'>"+"粉丝"+"</div>";
            str+="</div>";
            str+="</div>";
            str+="<div class='moredetail'>";
            str+="<div class='moredetailspan'>"+"经营产品"+"</div>";
            str+="<div class='labelwidth'>"
            str+="<div class='moredetailspan3'>"+company.product_category+"</div>";
            str+="</div>";
            str+="<div class='ma_position'>"+"</div>";
            str+="<div class='ma_position1'>"+company.city+"</div>";
            if(company.phone!='') {
                str += "<div class='ma_phone'>" + "</div>";
                str += "<div class='ma_position1'>" + company.phone + "</div>";
            }
            str+="</div>";
            /*str+="<div class='moredetail1'>";
            str+="<div class='moredetailspan'>"+"主营产品"+"</div>";
            str+="<div class='moredetailspan1'>"+company.ablearea+"</div>";
            str+="</div>";*/
            str+="<div class='moredetail1'>";
            str+="<div class='moredetailspan'>"+"商家介绍"+"</div>";
            str+="<div class='moredetailspan2'>"+(company.introduction?company.introduction:'')+"</div>";
            str+="</div>";
            str+="<div class='moredetail1'>";
            str+="<div class='moredetailspan'>"+"服务承诺"+"</div>";
            str+="<div class='moredetailspan2'>"+(company.promise?company.promise:'')+"</div>";
            str+="</div>";
            str+="</div>";
            str+="<div class='h_right'>";
            str+="<div class='guanzhubotton' id='btn-fav' data-attr='"+company.web_id_code+"'>"+(company.faved?'已关注':'关注')+"</div>";
            str+="<div class='guanzhubotton1' id='btn-share'>"+"分享"+"</div>";
            str+='<div id="share-outer">';
            str+='<div class="angle"></div>';
            str+='<div class="weixin-box">'+
                '<div id="qrcodeCanvas" style="display:inline-block"></div>'+
                '<p style="top:-30px">打开微信“扫一扫”，将本页分享到朋友圈</p>'+
                '<a id="weixin-box-close"><i class="icon-close iconfont icon-delete"></i></a></div>';
            str+='<div class="angle-1"></div>';
            str+="</div>";
            str+="</div>";
            str+="</div>";
            $("#head").html(str);
            $("#h_image").css({"background-image":"url('"+company.bg+"')"});
            $("#person_image").css({"background-image":"url('"+company.avatar+"')"});

            $('#qrcodeCanvas').qrcode({width:100,height:100,text:"https://www.ijusuo.com/mobile/dealer/s/"+company.web_id_code});

            str = '';
            str+="<div class='melogo'>";
            str+="<span class='mespan'>"+"品牌介绍"+"</span>"
            str+="<div class='triangle-right'>"+"</div>";
            str+="</div>";
            str+="<div class='pro_image' id='pro_image'>"+"</div>"
            str+="<div class='pro_text'>"+company.brand_introduction+"</div>"
            $("#aboutme").html(str);
            $("#pro_image").css({"background-image":"url('"+company.self_photo[0]+"')"});

            str = '';
            if(company.short_name!=""){
                str+="<div class='p_block'>";
                str+="<div class='com_logo'>"+"</div>"
                str+="<div class='ptext'>"+"商家名称"+"</div>";
                str+="</div>";
                //str+="<div class='companytext'>"+company.name+"</div>";
                str+="<div class='companytext'>"+company.short_name+"</div>";
            }
            if(company.self_address!=""){
                str+="<div class='p_block'>";
                str+="<div class='addr_logo'>"+"</div>";
                str+="<div class='ptext'>"+"详细地址"+"</div>";
                str+="</div>";
                str+="<div class='companytext'>"+company.self_address+"</div>";
            }
            if(company.phone!=""){
                str+="<div class='p_block'>";
                str+="<div class='tele_logo'>"+"</div>"
                str+="<div class='ptext'>"+"电话"+"</div>";
                str+="</div>";
                str+="<div class='companytext'>"+company.phone+"</div>";
            }
            $("#companydetail").html(str);

            $('#dealer_short_name').html(company.name);

            if(!company.self_promotion){
                $('#b3').remove();
                $('#slideItem-3').remove();
            }
            else {
                $('#cuxiao').html(company.self_promotion);
            }

            new BaiduMap({
                id: "daohang",
                title: {
                    text: company.short_name,
                    className: "title"
                },
                content: {
                    className: "content",
                    text: [""]
                },
                point: {
                    lng:company.lng,
                    lat: company.lat
                },
                level: 15,
                zoom: true,
                type: ["地图", "卫星"],
            });

            bindClickDealerInfo();
        }
        layer.closeAll('loading');
    });
}

function getDesigner(){
    layer.load(1);
    $.get('/dealer/get_designer',{__bs:__cache_brand,dealer_id:dealer_id}, function(res) {
        if (res.status == 1) {
            var designer = res.data.data;
            var str = '';
            var i;
            for(i=0;i<designer.length&&i<5;i++){
                str+="<div class='team_content' data-attr='"+designer[i].web_id_code+"'>";
                str+="<div class='team_image' id='team_image"+i+"'>"+"</div>";
                if(designer[i].title>0)
                    str+="<div class='"+designer[i].title+"'>"+"</div>";
                str+="<div class='team_name'>"+designer[i].nickname+"</div>";
                str+="<div class='team_fangan'>"+designer[i].count_album+"套方案"+"</div>";
                str+="</div>";
            }
            $("#design_team").append(str);
            for(i=0;i<designer.length&&i<5;i++){
                $("#team_image"+i).css({"background-image":"url('"+designer[i].url_avatar+"')"});
            }
            bindClickDesigner();
        }
        layer.closeAll('loading');
    });
}

function getStyle(){
    $.get('/index/get_style',{__bs:__cache_brand},function(res) {
        if (res.status == 1) {
            style = res.data;
            var str = '';
            str+="<div class='m_swiper'>";
            str += "<span id='p_swiper0' class='m_swiper_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < style.length; i++) {
                str+="<span id='p_swiper"+style[i].id+"' class='m_swiper_span' data-attr='" + style[i].id + "'>" + style[i].name + "</span>";
            }
            str+="</div>";
            $("#album_swiper").html(str);
            bindClickStyleAblum();
            getAlbum(0);
        }
    });
}

function getAlbum(style){
    layer.load(1);
    $.get('/dealer/get_album',{__bs:__cache_brand,dealer_id:dealer_id,style:style}, function(res) {
        if (res.status == 1) {
            var album = res.data.data;
            var str = '';
            var i;
            for (i = 0; i < album.length&&i<6; i++) {
                str+="<div class='g_productview' data-attr='"+album[i].web_id_code+"'>";
                str+="<div class='g_imageview'>";
                str+="<div class='g_areaview1'>"+"</div>";
                str+="<div class='g_areaview1t'>";
                str+="<label class='g_positext1'>"+album[i].count_area+"㎡</label>";
                str+="</div>";
                str+="<div class='g_pimage' id='pimage_"+i+"'>"+"</div>";
                str+="<div class='g_nametext'>"+album[i].title+"</div>";
                str+="<div class='g_deview'>";
                str+="<div class='g_perimage' id='perimage_"+i+"'>"+"</div>";
                str+="<label class='g_pertext'>"+album[i].designer+"</label>";
                str+="<div class='g_lookview'>";
                str+="<img src='/v1/images/site/dealer/view.png' class='g_xiconimage'/>";
                str+="<label class='g_viewtext'>"+album[i].count_visit+"</label>";
                str+="</div>";
                str+="</div>";
                str+="</div>";
                str+="</div>";
            }
            $("#fangan").html(str);
            for (i = 0; i < album.length&&i<6; i++) {
                $("#pimage_"+i).css({"background-image":"url('"+album[i].photo_cover+"')"});
                $("#perimage_"+i).css({"background-image":"url('"+album[i].designerPhoto+"')"});
            }
            bindClickAlbum();
            $('#p_swiper'+res.data.params.styleId).addClass('active');
        }
        layer.closeAll('loading');
    });
}

function getCategory(){
    $.get('/dealer/get_category',{dealer_id:dealer_id},function(res) {
        if (res.status == 1) {
            category = res.data;
            var str = '';
            str+="<div class='m_swiper'>"
            str+="<span id='pr_swiper0' class='m_swiper_span active' data-attr='0'>不限</span>";
            str+="<span id='pr_swiper"+category.id+"' class='m_swiper_span' data-attr='"+category.id+"'>"+category.name+"</span>";
            str+="</div>";
            $("#product_swiper1").html(str);
            bindClickCategoryProduct();
            getProduct(0);
        }
    });
}

function getProduct(category){
    layer.load(1);
    $.get('/dealer/get_product',{__bs:__cache_brand,dealer_id:dealer_id,category:category}, function(res) {
        if (res.status == 1) {
            var product = res.data.data;
            var str = '';
            var i;
            for (i = 0; i < product.length&&i<6; i++) {
                str+="<div class='productview' data-attr='"+product[i].web_id_code+"'>";
                str+="<div class='imageview'>";
                str+="<div class='pimage' id='primage_"+i+"'/>";
                if(product[i].new==true){
                    str+="<div class='new'>"+"</div>";
                }
                str+="</div>";
                str+="<div class='nametext'>"+product[i].name+"</div>";
                str+="<div class='companyview'>";
                str+="<label class='pcompanytext'>"+product[i].code+"</label>";
                str+="<label class='pricetext'>"+product[i].price+"</label>";
                str+="</div>";
                str+="</div>";
            }
            $("#product").html(str);
            for (i = 0; i < product.length&&i<6; i++) {
                $("#primage_"+i).css({"background-image":"url('"+product[i].photo_product+"')"});
            }
            bindClickProduct();
            $('#pr_swiper'+res.data.categoryId).addClass('active');
        }
        layer.closeAll('loading');
    });
}

function bindClickDealerInfo(){
    $('#head').on('click', '#btn-fav', function(){
        var code = $(this).attr('data-attr');
        ajax_post('/index/post_fav_dealer',{
            'code': code,
            __bs:__cache_brand
        }, function(res){
            if (res.status == 1) {
                if(res.data.faved){
                    $('#btn-fav').html('已关注');
                }
                else{
                    $('#btn-fav').html('关注');
                }
                $('#span-count-fav').html(res.data.count);
            }
            else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
        });
    });
    $('#head').on('mousemove','#btn-share', function(){
        $('#share-outer').addClass('active');
    });
    $('#head').on('mouseout','#btn-share', function(){
        $('#share-outer').removeClass('active');
    });
}

function bindClickDesigner(){
    $('#design_team').on('click', '.team_content', function(){
        goToDesigner($(this).attr('data-attr'));
    });
}

function bindClickAlbum(){
    $('#fangan').on('click', '.g_productview', function(){
        goToAlbum($(this).attr('data-attr'));
    });
}

function bindClickStyleAblum(){
    $('#album_swiper').on('click', '.m_swiper_span', function(){
        $(this).siblings().removeClass('active');
        getAlbum($(this).attr('data-attr'));
    });
}

function bindClickCategoryProduct(){
    $('#product_swiper1').on('click', '.m_swiper_span', function(){
        $(this).siblings().removeClass('active');
        getProduct($(this).attr('data-attr'));
    });
}

function bindClickProduct(){
    $('#product').on('click', '.productview, .productview1', function(){
        goToProduct($(this).attr('data-attr'));
    });
}

function init(){
    getDealerInfo();
    getDesigner();
    getStyle();
    getCategory();
}

$(document).ready(function(){
    init();
});

$('#sidenav li').each(function() {
    $(this).click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });
});

var scroH,navH,navH1,navH2,slideBarH,footerTop,module_top,should_top,next_module_top,slideBarFixed=false;
var nowTopIndex,topIndex;
$(function(){
    $(window).scroll(function(){
        scroH = $(this).scrollTop();
        navH = $("#slideBar").offset().top;
        navH1 = $("#daohang-outer").offset().top;
        navH2 = $("#daohang-outer").height();
        slideBarH = $("#slideBar").height();
        footerTop = $("#footer").offset().top;

        for(topIndex=0;topIndex<4;topIndex++){
            if($("#b"+topIndex).length>0){
                module_top = $("#b"+topIndex).offset().top;
                if(topIndex >= 3){
                    next_module_top = $("#footer").offset().top;
                }else if($("#b"+(topIndex+1)).length>0){
                    next_module_top = $("#b"+(topIndex+1)).offset().top;
                }
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
            should_top = scroH - (footerTop - slideBarH);
            should_top = parseFloat(should_top)+70
            $("#slideBar").css('top','-'+should_top+'px');
        }else{
            $("#slideBar").css('top','10px');

        }

        /*for(topIndex=0;topIndex<4;topIndex++){
            if($("#b"+topIndex).offset().top>scroH){
                if(topIndex>0&&$("#b"+(topIndex-1)).offset().top+$("#b"+(topIndex-1)).height()<scroH) {
                    if (nowTopIndex != topIndex) {
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
        }*/
    });
});
