var designer;
var album;
var top;

function getDesignerInfo(){
    layer.load(1);
    $.get('/designer/get_info',{__bs:__cache_brand,designer_id:designer_id}, function(res) {
        if (res.status == 1) {
            designer = res.data.data;
            var str = '';
            str+="<div class='h_image' id='h_image'>";
            str+="<div class='person_image' id='person_image'>"+"</div>";
            str+="<div class='perback'>"+"</div>";
            str+="<div class='person'>";
            str+="<span class='personnanme'>"+designer.nickname+"</span>";
            if(designer.identity==true)
                str+="<span class='iconfont icon-shimingrenzheng'>"+"</span>";
            if(designer.title!='')
                str+="<div class='"+designer.title+"'>"+"</div>";
            str+="</div>";
            str+="</div>";
            str+="<div class='h_bottom'>";
            str+="<div class='h_left'>";
            str+="<div class='h_detail'>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan'>"+designer.album+"</div>";
            str+="<div class='h_blockspan1'>"+"方案数"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan'>"+designer.count_visit+"</div>";
            str+="<div class='h_blockspan1'>"+"浏览量"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan'>"+designer.count_praise+"</div>";
            str+="<div class='h_blockspan1'>"+"点赞数"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan' id='span-count-fav'>"+designer.count_fan+"</div>";
            str+="<div class='h_blockspan1'>"+"粉丝数"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="<div class='h_block'>";
            str+="<div class='h_blockspan'>"+designer.working_year+"</div>";
            str+="<div class='h_blockspan1'>"+"工作经验(年)"+"</div>";
            str+="</div>";
            str+="<div class='line'>"+"</div>";
            str+="</div>";
            str+="<div class='moredetail'>";
            str+="<div class='moredetailspan'>"+"擅长风格"+"</div>"
            str+="<div class='moredetailspan1'>"+designer.style+"</div>"
            str+="</div>"
            str+="<div class='moredetail1'>"
            str+="<div class='moredetailspan'>"+"擅长空间"+"</div>"
            str+="<div class='moredetailspan1'>"+designer.space+"</div>"
            str+="</div>"
            str+="<div class='moredetail1'>"
            str+="<div class='moredetailspan'>"+"服务区域"+"</div>"
            str+="<div class='moredetailspan1'>"+designer.serving_city+"</div>"
            str+="</div>"
            str+="</div>"
            str+="<div class='h_right'>"
            str+="<div class='guanzhubotton' id='btn-fav' data-attr='"+designer.web_id_code+"'>"+(designer.faved?'已关注':'关注')+"</div>";
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
            $("#h_image").css({"background-image":"url('"+designer.bg+"')"});
            $("#person_image").css({"background-image":"url('"+designer.avatar+"')"});

            $('#designer_nickname').html(designer.nickname+'的主页');
            $('#qrcodeCanvas').qrcode({width:100,height:100,text:"https://www.ijusuo.com/mobile/designer/s/"+designer.web_id_code});

            str = '';
            str+="<div class='melogo'>";
            str+="<span class='mespan'>"+"关于我"+"</span>";
            str+="<div class='triangle-right'>"+"</div>";
            str+="</div>";
            str+="<div class='personimage' id='personimage'>"+"</div>";
            str+="<div class='p_block'>";
            str+="<div class='com_logo'>"+"</div>";
            str+="<div class='ptext'>"+"工作单位"+"</div>";
            str+="</div>";
            str+="<div class='companytext'>"+designer.company+"</div>";
            if(designer.school!=''){
                str+="<div class='p_block'>";
                str+="<div class='school_logo'>"+"</div>"
                str+="<div class='ptext'>"+"毕业院校"+"</div>";
                str+="</div>";
                str+="<div class='companytext'>"+designer.school+"</div>";
            }
            if(designer.introduction!=null&&designer.introduction!=''){
                str+="<div class='p_block'>";
                str+="<div class='de_logo'>"+"</div>"
                str+="<div class='ptext'>"+"个人简介"+"</div>";
                str+="</div>";
                str+="<div class='companytext'>"+designer.introduction+"</div>";
            }
            if(designer.award!=''){
                str+="<div class='p_block'>";
                str+="<div class='pr_logo'>"+"</div>"
                str+="<div class='ptext'>"+"已获奖项"+"</div>";
                str+="</div>";
                str+="<div class='companytext'>"+designer.award+"</div>";
            }
            $("#aboutme").html(str);
            $("#personimage").css({"background-image":"url('"+designer.avatar+"')"});

            bindClickDesignerInfo();
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

function getAlbumTop(){
    layer.load(1);
    $.get('/designer/get_album_top',{__bs:__cache_brand,designer_id:designer_id}, function(res) {
        if (res.status == 1) {
            var top = res.data;
            if(top.length>0) {
                var str = '';
                var i;
                for (i = 0; i < top.length && i < 4; i++) {
                    str += "<div class='fa_container' data-attr='" + top[i].web_id_code + "'>";
                    str += "<div class='fa_image' id='fa_image" + i + "'>" + "</div>";
                    str += "<div class='fa_txt'>";
                    str += "<div class='fa_title'>" + top[i].title + "</div>";
                    str += "<div class='fa_text'>" + top[i].description_design + "</div>";
                    str += "<div class='fa_label'>";
                    str += "<div class='label'>" + top[i].count_area + "㎡</div>";
                    if (top[i].house_type != null && top[i].house_type.length > 0)
                        str += "<div class='label'>" + top[i].house_type[0] + "</div>";
                    if (top[i].style != null && top[i].style.length > 0)
                        str += "<div class='label'>" + top[i].style[0] + "</div>";
                    str += "<div class='d_detail'>";
                    str += "<span class='iconfont icon-chakan'>" + "</span>";
                    str += "<span class='looknumber'>" + top[i].count_visit + "</span>";
                    str += "<div class='like-outer' data-attr='" + top[i].id + "'>";
                    if (top[i].liked) {
                        str += "<span class='iconfont icon-dianzan' id='like_" + top[i].id + "'></span>";
                    }
                    else {
                        str += "<span class='iconfont icon-dianzan2' id='like_" + top[i].id + "'></span>";
                    }
                    str += "<span class='looknumber likenumber' id='likenumber_" + top[i].id + "'>" + top[i].count_praise + "</span>";
                    str += "</div>";
                    str += "<div class='fav-outer' data-attr='" + top[i].id + "'>";
                    if (top[i].collected) {
                        str += "<span class='iconfont icon-shoucang' id='collected_" + top[i].id + "'>" + "</span>";
                    }
                    else {
                        str += "<span class='iconfont icon-shoucang2' id='collected_" + top[i].id + "'>" + "</span>";
                    }
                    str += "<span class='looknumber' id='collectednumber_" + top[i].id + "'>" + top[i].count_fav + "</span>";
                    str += "</div>";
                    str += "</div>";
                    str += "</div>";
                    str += "</div>";
                    str += "</div>";
                }
                $("#designer_fa").html(str);
                for (i = 0; i < top.length && i < 4; i++) {
                    $("#fa_image" + i).css({"background-image": "url('" + top[i].photo_cover + "')"});
                }

                bindClickAlbumTop();
            }
            else{
                $('#b0').remove();
                $('#b1').attr('style','margin-top:0');
                $('#slideBar .branch').attr('style','height:12px;');
                $('#slideItem-0').remove();
            }
        }
        layer.closeAll('loading');
    });
}

function getAlbum(style){
    layer.load(1);
    $.get('/designer/get_album',{__bs:__cache_brand,designer_id:designer_id,style:style}, function(res) {
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
            $('#p_swiper'+res.data.params.styleId).addClass('active');
            bindClickAlbum();
        }
        layer.closeAll('loading');
    });
}

function bindClickAlbumLike(){
    $('#designer_fa').on('click', '.like-outer', function(e){
        var albumId = $(this).attr('data-attr');
        ajax_post('/index/post_like_album',{
            'id': albumId
        }, function(res){
            if (res.status == 1) {
                $('#like_'+albumId).removeClass('icon-dianzan');
                $('#like_'+albumId).removeClass('icon-dianzan2');
                if(res.data.liked){
                    $('#like_'+albumId).addClass('icon-dianzan');
                }
                else{
                    $('#like_'+albumId).addClass('icon-dianzan2');
                }
                $('#likenumber_'+albumId).html(res.data.count);
            }
            else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
        });
        e.stopPropagation();
    });
}

function bindClickAlbumFav(){
    $('#designer_fa').on('click', '.fav-outer', function(){
        var albumId = $(this).attr('data-attr');
        ajax_post('/index/post_fav_album',{
            'id': albumId
        }, function(res){
            if (res.status == 1) {
                $('#collected_'+albumId).removeClass('icon-shoucang');
                $('#collected_'+albumId).removeClass('icon-shoucang2');
                if(res.data.liked){
                    $('#collected_'+albumId).addClass('icon-shoucang');
                }
                else{
                    $('#collected_'+albumId).addClass('icon-shoucang2');
                }
                $('#collectednumber_'+albumId).html(res.data.count);
            }
            else{
                if(res.code == 2001){
                    showLoginReg(true)
                }else{
                    layer.msg(res.msg)
                }
            }
        });
        e.stopPropagation();
    });
}

function init(){
    getDesignerInfo();
    getAlbumTop();
    getStyle();
    bindClickAlbumLike();//方案点赞
    bindClickAlbumFav();//方案收藏
}

$(document).ready(function(){
    init();
});

function bindClickAlbumTop(){
    $('#designer_fa').on('click', '.fa_container', function(){
        goToAlbum($(this).attr('data-attr'));
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

function bindClickDesignerInfo(){
    $('#head').on('click', '#btn-fav', function(){
        var code = $(this).attr('data-attr');
        ajax_post('/index/post_fav_designer',{
            'code': code
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

$('#sidenav li').each(function() {
    $(this).click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });
});

var scroH,navH,navH1,navH2,slideBarFixed=false;
var nowTopIndex,topIndex;
$(function(){
    $(window).scroll(function(){
        scroH = $(this).scrollTop();
        navH = $("#slideBar").offset().top;
        navH1 = $("#aboutme").offset().top;
        navH2 = $("#aboutme").height();

        for(topIndex=0;topIndex<2;topIndex++){
            if($("#b"+topIndex).length>0&&$("#b"+topIndex).offset().top>scroH){
                if(topIndex>0&&$("#b"+(topIndex-1)).length>0&&$("#b"+(topIndex-1)).offset().top+$("#b"+(topIndex-1)).height()<scroH) {
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
        }
    });
});

var product_swiper=["不限","简约现代","新中式","北欧","欧式"]
//相关方案
var gproductdata1={"list":[{"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":219,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"},
        {"images":"/v1/images/site/designer_xq/8.png","area":"90㎡","name":"现代简约两居","personname":"岑岑岑生","looknumber":310,"personimage":"images/sjfa_xq/7.png"}
    ]};