var banner=[];
var style=[];
var album=[];
var brand=[];
var product=[];
var city=[];
var designer=[];
var category=[];
var technologyCategory=[];
var dealer=[];

function getIndexBanner(){
    $.get('/index/get_banner',{__bs:__cache_brand},function(res){
        if(res.status==1){
            banner = res.data;
            var str='';
            str+="<div class='swiper-container' id='swiper1'>"
            str+="<div class='swiper-wrapper swiper-no-swiping'>"
            for(var i=0;i<banner.length;i++){
                str+="<div class='swiper-slide'>"
                str+="<img src='"+banner[i].image+"'/>"
                str+="</div>"
            }
            str+="</div>"
            str+="</div>"
            str+="<div class='left'>"+"<img src='../../v1/images/site/index/bannerL.png'/></div>"
            str+="<div class='right'><img src='../../v1/images/site/index/bannerR.png'/></div>"
            $("#swipers").html(str);
            //轮播
            //最里层轮播
            var mySwiper1 = new Swiper('#swiper1',{
                loop: true,
                autoplay : 4000,
                speed : 600,
                autoplayDisableOnInteraction : false,
                pagination: '.swiper-pagination',
                paginationType: 'custom',//这里分页器类型必须设置为custom,即采用用户自定义配置
                //下面方法可以生成我们自定义的分页器到页面上
                paginationCustomRender: function(swiper, current, total) {
                    var customPaginationHtml = "";
                    for(var i = 0; i < total; i++) {
                        //判断哪个分页器此刻应该被激活
                        if(i== (current-1) ){
                            customPaginationHtml += '<span class="swiper-pagination-customs swiper-pagination-customs-active"></span>';
                        } else {
                            customPaginationHtml += '<span class="swiper-pagination-customs"></span>';
                        }
                    }
                    return customPaginationHtml;
                }
            });
            //前进后退按钮
            $(".left").click(function(){
                mySwiper1.slidePrev();
            })
            $(".right").click(function(){
                mySwiper1.slideNext();
            })
            $('.swiper-pagination').on('click','span',function(){
                var index = $(this).index() + 2;
                mySwiper1.slideTo(index-1, 500, false);//切换到第一个slide，速度为1秒
            })
        }
    });
}

function getIndexStyle(){
    $.get('/index/get_style',{__bs:__cache_brand},function(res) {
        if (res.status == 1) {
            style = res.data;
            var str = '';
            str += "<div class='d_swipwer'>";
            str += "<span id='d_swiper0' class='d_swipwer_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < style.length; i++) {
                if (i == 0) {
                    str += "<span id='d_swiper" + style[i].id + "' class='d_swipwer_span' data-attr='" + style[i].id + "'>" + style[i].name + "</span>"
                } else {
                    str += "<span id='d_swiper" + style[i].id + "' class='d_swipwer_span' data-attr='" + style[i].id + "'>" + style[i].name + "</span>"
                }
            }
            str += "</div>"
            $("#designer_swiper").html(str);
            bindClickStyleAblum();
            getAlbumByStyle(0);
        }
    });
}

function getIndexBrand(){
    $.get('/index/get_brand',{__bs:__cache_brand},function(res) {
        if (res.status == 1) {
            brand = res.data;
            var str = '';
            str+="<div class='d_swipwer'>";
            str += "<span id='p_swiper0' class='d_swipwer_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < brand.length; i++) {
                str+="<span id='p_swiper"+brand[i].id+"' class='d_swipwer_span' data-attr='"+brand[i].id+"'>"+brand[i].brand_name+"</span>"
            }
            str+="</div>";
            $("#product_swiper").html(str);
            //bindClickBrandProduct();
            //getProductByBrand(0);
        }
        else if(res.status == 2){//品牌主页发起的请求，要更改显示为品类，同时更改点击事件
            /*category = res.data;
            var str = '';
            str+="<div class='d_swipwer'>";
            str += "<span id='p_swiper0' class='d_swipwer_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < category.length; i++) {
                str+="<span id='p_swiper"+category[i].id+"' class='d_swipwer_span' data-attr='"+category[i].id+"'>"+category[i].name+"</span>"
            }
            str+="</div>";*/

        }
        else{
            alert(res.msg);
        }
    });
}

function getIndexCity(){
    $.get('/index/get_city',{__bs:__cache_brand},function(res) {
        if (res.status == 1) {
            city = res.data;
            var str = '';
            str+="<div class='d_swipwer'>";
            str += "<span id='hd_swiper0' class='d_swipwer_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < city.length; i++) {
                str+="<span id='hd_swiper"+city[i].id+"' class='d_swipwer_span' data-attr='"+city[i].id+"'>"+city[i].shortname+"</span>"
            }
            str+="</div>";
            $("#hotdesigner_swiper").html(str);
            bindClickCityDesigner();
            getDesignerByCity(0);
        }
    });
}

function getIndexCategory(){
    getDealerByCategory(0);
     /*$.get('/index/get_category',{__bs:__cache_brand},function(res) {
        if (res.status == 1) {
            category = res.data;
            var str = '';
            str+="<div class='d_swipwer'>";
            str += "<span id='m_swiper0' class='d_swipwer_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < category.length; i++) {
                str+="<span id='m_swiper"+category[i].id+"' class='d_swipwer_span' data-attr='"+category[i].id+"'>"+category[i].name+"</span>"
            }
            str+="</div>";
            $("#material_swiper").html(str);
            bindClickCategoryDealer();
            getDealerByCategory(0);
        }
    });*/
}

function getIndexTechnologyCategory(){
    $.get('/index/get_technology_category',{__bs:__cache_brand},function(res) {
        if (res.status == 1) {
            technologyCategory = res.data;
            var str = '';
            str+="<div class='d_swipwer'>";
            str += "<span id='p_swiper0' class='d_swipwer_span active' data-attr='0'>不限</span>";
            for (var i = 0; i < technologyCategory.length; i++) {
                str+="<span id='p_swiper"+technologyCategory[i].id+"' class='d_swipwer_span' data-attr='"+technologyCategory[i].id+"'>"+technologyCategory[i].name+"</span>"
            }
            str+="</div>";
            $("#product_swiper").html(str);
            bindClickTechnologyCategoryProduct();
            getProductByTechnologyCategory(0);
        }
    });
}

function getAlbumByStyle(style){
    layer.load(1);
    $.get('/index/get_album_by_style',{__bs:__cache_brand,s:style}, function(res) {
        if (res.status == 1) {
            album = res.data.album;
            style = res.data.style;
            var str = '';
            var tag;
            var i;
            for (i = 0; i < album.length; i++) {
                tag=(i==0?'':'1');
                str+="<div class='designer_content"+tag+"' data-attr='"+album[i].web_id_code+"'>";
                str+="<div class='imageview'>";
                str+="<div class='designer_image"+tag+"' id='designer_image"+i+"'/>";
                if(album[i].panorama){
                    str+="<div class='wholeview'>"+"全景图"+"</div>";
                }
                str+="</div>";
                str+="<div class='designer_text'>";
                str+="<span class='d_area'>"+album[i].count_area+"㎡&nbsp;|&nbsp;"+"</span>";
                str+="<span class='d_title' title='"+album[i].title+"'>"+album[i].title+"</span>";
                if(i==0) {
                    str += "<div class='d_person'>";
                    str += "<div class='d_personimage' id='d_personimage" + i + "'>" + "</div>";
                    str += "<span class='d_personname'>" + album[i].designer + "</span>";
                    if (album[i].identity == true) {
                        str += "<span class='iconfont icon-shimingrenzheng'>" + "</span>";
                    }
                    if (album[i].hot == true) {
                        str += "<span class='iconfont icon-renqiwang' id='hot'>" + "</span>";
                    }
                    str += "</div>";
                }
                str+="</div>";
                str+="<div class='d_detail'>";
                str+="<span class='iconfont icon-chakan'>"+"</span>";
                str+="<span class='looknumber'>"+album[i].count_visit+"</span>";
                str+="<div class='like-outer' data-attr='"+album[i].id+"'>";
                if(album[i].liked){
                    str+="<span class='iconfont icon-dianzan' id='like_"+album[i].id+"'></span>";
                }
                else{
                    str+="<span class='iconfont icon-dianzan2' id='like_"+album[i].id+"'></span>";
                }
                str+="<span class='looknumber likenumber' id='likenumber_"+album[i].id+"'>"+album[i].count_praise+"</span>";
                str+="</div>";
                str+="<div class='fav-outer' data-attr='"+album[i].id+"'>";
                if(album[i].collected) {
                    str += "<span class='iconfont icon-shoucang' id='collected_" + album[i].id + "'>" + "</span>";
                }
                else{
                    str+="<span class='iconfont icon-shoucang2' id='collected_"+album[i].id+"'>"+"</span>";
                }
                str+="<span class='looknumber' id='collectednumber_"+album[i].id+"'>"+album[i].count_fav+"</span>";
                str+="</div>";
                str+="</div>";
                if(i!=0) {
                    str+="<div class='d_line'>"+"</div>"
                    str+="<div class='d_person"+tag+"'>";
                    str+="<div class='d_personimage"+tag+"' id='d_personimage"+i+"'>"+"</div>"
                    str+="<span class='d_personname'>"+album[i].designer+"</span>";
                    if(album[i].identity==true)
                    {
                        str+="<span class='iconfont icon-shimingrenzheng'>"+"</span>";
                    }else{
                        //str+="<span class='iconfont icon-shimingrenzheng' style='color:#D2D1D1;font-size:16px;margin-left:10px;'>"+"</span>";
                    }
                    if(album[i].hot==true){
                        str+="<span class='iconfont icon-renqiwang' id='hot'>"+"</span>";
                    }
                    str+="</div>";
                }
                str+="</div>";
            }
            $("#designer").html(str);
            for (i = 0; i < album.length; i++) {
                $("#designer_image"+i).css({"background-image":"url('"+album[i].photo_cover+"')"});
                $("#d_personimage"+i).css({"background-image":"url('"+album[i].designerPhoto+"')"});
            }
            $('#d_swiper'+style).addClass('active');
            bindClickAlbum();
        }
        layer.closeAll('loading');
    });
}

function getProductByBrand(brand){
    layer.load(1);
    $.get('/index/get_product_by_brand',{__bs:__cache_brand,b:brand}, function(res) {
        if (res.status == 1) {
            product = res.data.product;
            brand = res.data.brand;
            var str = '';
            var i;
            for (i = 0; i < product.length; i++) {
                if(i==0 || i==5){
                    str+="<div class='productview' data-attr='"+product[i].web_id_code+"'>";
                    str+="<div class='p_left'>";
                    str+="<div class='p_name' title='"+product[i].name+"'>"+product[i].name+"</div>";
                    str+="<div class='p_line'>"+"</div>";
                    str+="<div class='p_type' title='"+product[i].code+"'>"+product[i].code+"</div>";
                    str+="<div class='p_lookmore'>"+"点击查看 >"+"</div>";
                    str+="</div>";
                    str+="<div class='p_image' id='p_image"+i+"'>"+"</div>"
                    if(product[i].new==true){
                        str+="<span class='iconfont icon-new4'>"+"</span>"
                    }
                    str+="</div>";
                }else{
                    str+="<div class='productview1' data-attr='"+product[i].web_id_code+"'>";
                    str+="<div class='p_imageview' id='p_image"+i+"'>"
                    str+="</div>"
                    if(product[i].new==true){
                        str+="<span class='iconfont icon-new4'>"+"</span>"
                    }
                    str+="<div class='p_name1' title='"+product[i].name+"'>"+product[i].name+"</div>"
                    str+="<div class='p_type1' title='"+product[i].code+"'>"+product[i].code+"</div>"
                    str+="</div>";
                }
            }
            $("#product").html(str);
            for (i = 0; i < product.length; i++) {
                $("#p_image"+i).css({"background-image":"url('"+product[i].photo_cover+"')"});
            }
            $('#p_swiper'+brand).addClass('active');
            bindClickProduct();
        }
        layer.closeAll('loading');
    });
}

function getProductByTechnologyCategory(category){
    layer.load(1);
    $.get('/index/get_product_by_category',{__bs:__cache_brand,c:category}, function(res) {
        if (res.status == 1) {
            product = res.data.product;
            category = res.data.category;
            var str = '';
            var i;
            if(product.length>0) {
                for (i = 0; i < product.length; i++) {
                    if (i == 0 || i == 5) {
                        str += "<div class='productview' data-attr='" + product[i].web_id_code + "'>";
                        str += "<div class='p_left'>";
                        str += "<div class='p_name' title='"+product[i].name+"'>" + product[i].name + "</div>";
                        str += "<div class='p_line'>" + "</div>";
                        str += "<div class='p_type' title='"+product[i].code+"'>" + product[i].code + "</div>";
                        str += "<div class='p_lookmore'>" + "点击查看 >" + "</div>";
                        str += "</div>";
                        str += "<div class='p_image' id='p_image" + i + "'>" + "</div>"
                        if (product[i].new == true) {
                            str += "<span class='iconfont icon-new4'>" + "</span>"
                        }
                        str += "</div>";
                    } else {
                        str += "<div class='productview1' data-attr='" + product[i].web_id_code + "'>";
                        str += "<div class='p_imageview' id='p_image" + i + "'>"
                        str += "</div>"
                        if (product[i].new == true) {
                            str += "<span class='iconfont icon-new4'>" + "</span>"
                        }
                        str += "<div class='p_name1' title='"+product[i].name+"'>" + product[i].name + "</div>"
                        str += "<div class='p_type1' title='"+product[i].code+"'>" + product[i].code + "</div>"
                        str += "</div>";
                    }
                }
                $("#product").html(str);
                for (i = 0; i < product.length; i++) {
                    $("#p_image"+i).css({"background-image":"url('"+product[i].photo_cover+"')"});
                }
            }
            else{
                $("#product").html('<div class="no-record">暂无相关数据</div>');
            }
            $('#p_swiper'+category).addClass('active');
            bindClickProduct();
        }
        layer.closeAll('loading');
    });
}

function getDesignerByCity(city){
    layer.load(1);
    $.get('/index/get_designer_by_city',{__bs:__cache_brand,c:city}, function(res) {
        if (res.status == 1) {
            designer = res.data.designer;
            city = res.data.city;
            var str = '';
            var i;
            for (i = 0; i < designer.length; i++) {
                if(i==2 || i==3){
                    str+="<div class='hotdesignerview' data-attr='"+designer[i].web_id_code+"'>";
                    str+="<div class='hot_left'>";
                    str+="<div class='hot_image' id='hot_image"+i+"'>"+"</div>"
                    str+="</div>";
                    str+="<div class='hot_left'>";;
                    str+="<div class='hd_person1'>";
                    str+="<span class='hd_personname'>"+designer[i].nickname+"</span>";
                    if(designer[i].identity==true)
                    {
                        str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-left:11px;'>"+"</span>";
                    }
                    if(designer[i].hot==true){
                        str+="<span class='iconfont icon-renqiwang' id='hot' style='color:#FFE115;margin-left:10px;'>"+"</span>";
                    }
                    str+="</div>";
                    str+="<div class='hd_company1 single-line' title='"+designer[i].self_organization+"'>"+designer[i].self_organization+"</div>";
                    str+="<div class='hd_detail1'>";
                    str+="<span class='hd_experience'>"+"从业经验："+designer[i].self_working_year+"</span>";
                    str+="<span class='hd_city'>"+"城市："+designer[i].city+"</span>";
                    str+="</div>";
                    str+="<div class='hd_lookmore'>"+"点击查看 >"+"</div>";
                    str+="</div>";
                    str+="</div>";
                }else if(i==0 ||i==1){
                    str+="<div class='hotdesignerview1' data-attr='"+designer[i].web_id_code+"'>";
                    str+="<div class='hotimage' id='hot_image"+i+"'>"+"</div>"
                    str+="<div class='hd_person'>";
                    str+="<span class='hd_personname'>"+designer[i].nickname+"</span>";
                    if(designer[i].identity==true)
                    {
                        str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-left:11px;'>"+"</span>";
                    }
                    if(designer[i].hot==true){
                        str+="<span class='iconfont icon-renqiwang' id='hot' style='color:#FFE115;margin-left:10px;'>"+"</span>";
                    }
                    str+="</div>";
                    str+="<div class='hd_company single-line' title='"+designer[i].self_organization+"'>"+designer[i].self_organization+"</div>";
                    str+="<div class='hd_detail'>";
                    str+="<span class='hd_experience'>"+"从业经验："+designer[i].self_working_year+"</span>";
                    str+="<span class='hd_city'>"+"城市："+designer[i].city+"</span>";
                    str+="</div>";
                    str+="</div>";
                }else{
                    str+="<div class='hotdesignerview2' data-attr='"+designer[i].web_id_code+"'>";
                    str+="<div class='hotimage' id='hot_image"+i+"'>"+"</div>"
                    str+="<div class='hd_person'>";
                    str+="<span class='hd_personname'>"+designer[i].nickname+"</span>";
                    if(designer[i].identity==true)
                    {
                        str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-left:11px;'>"+"</span>";
                    }
                    if(designer[i].hot==true){
                        str+="<span class='iconfont icon-renqiwang' id='hot' style='color:#FFE115;margin-left:10px;'>"+"</span>";
                    }
                    str+="</div>";
                    str+="<div class='hd_company single-line' title='"+designer[i].self_organization+"'>"+designer[i].self_organization+"</div>";
                    str+="<div class='hd_detail'>";
                    str+="<span class='hd_experience'>"+"从业经验："+designer[i].self_working_year+"</span>";
                    str+="<span class='hd_city'>"+"城市："+designer[i].city+"</span>";
                    str+="</div>";
                    str+="</div>";
                }
            }
            $("#hotdesigner").html(str);
            for (i = 0; i < designer.length; i++) {
                $("#hot_image"+i).css({"background-image":"url('"+designer[i].url_avatar+"')"});
            }
            $('#hd_swiper'+city).addClass('active');
            bindClickDesigner();
        }
        layer.closeAll('loading');
    });
}

function getDealerByCategory(category){
    layer.load(1);
    $.get('/index/get_dealer_by_category',{__bs:__cache_brand,c:category}, function(res) {
        if (res.status == 1) {
            dealer = res.data.dealer;
            category = res.data.category;
            var str = '';
            var i;
            for (i = 0; i < dealer.length; i++) {
                str+="<div class='materialview' data-attr='"+dealer[i].web_id_code+"'>";
                str+="<div class='m_left'>";
                str+="<div class='materialimage' id='materialimage"+i+"'>"+"</div>"
                str+="</div>";
                str+="<div class='hotcontainer' id='m_hotcontainer_"+dealer[i].id+"'>";
                str+="<span class='reduicon'>"+"</span>";
                str+="<span class='redu'>"+dealer[i].point_focus+"</span>"
                str+="</div>";
                str+="<div class='m_right'>";
                str+="<div class='m_person'>";
                str+="<div class='m_personimage' id='m_personimage"+i+"'>"+"</div>"
                str+="<div class='m_detail'>";
                str+="<div class='m_company' title='"+dealer[i].short_name+"'>"+dealer[i].short_name+"</div>";
                //str+="<div class='xingji1'>";
                str+="<div class='m_label'>"+"经营类别："+"</div>";
                str+="<div class='m_labelview'>";
                str+="<div class='m_labelview1'>"+dealer[i].category+"</div>";
                //str+="</div>";
                //str+="<div class='xingjiq'>";
                //str+="<span class='zuanshi' id='xingji'>"+"</span>";
                //str+="</div>";
                //str+="<span class='xingjinumber'>"+dealer[i].star_level+"</span>";
                str+="</div>";
                str+="</div>";
                str+="<div class='m_introduction'>"+dealer[i].self_introduction+"</div>";
                str+="</div>";
                //str+="<div class='m_lookmore'>"+"查看更多 >"+"</div>"
                str+="</div>";
                str+="</div>";
            }
            $("#material").html(str);
            for (i = 0; i <dealer.length; i++) {
                $("#materialimage"+i).css({"background-image":"url('"+dealer[i].self_photo+"')"});
                $("#m_personimage"+i).css({"background-image":"url('"+dealer[i].url_avatar+"')"});
            }
            $('#m_swiper'+category).addClass('active');
            bindClickDealer();
        }
        layer.closeAll('loading');
    });
}

function getDealerByCity(){
    layer.load(1);
    $.get('/index/get_dealer_by_city',{__bs:__cache_brand}, function(res) {
        if (res.status == 1) {
            dealer = res.data.dealer;
            var str = '';
            var i;
            for (i = 0; i < dealer.length; i++) {
                str+="<div class='companylogo' id='cclogo_"+i+"' data-attr='"+dealer[i].web_id_code+"'>"+"<div class='name'>"+dealer[i].name+"</div></div>"
            }
            $("#companylogo1").append(str);
            for (i = 0; i <dealer.length; i++) {
                $("#cclogo_"+i).css({"background-image":"url('"+dealer[i].url_avatar1+"')"});
            }
            bindClickDealerLogo();
        }
        layer.closeAll('loading');
    });
}

function init(){
    getIndexBanner();
    getIndexStyle();
    getIndexBrand();
    getIndexCity();
    getIndexCategory();
    getIndexTechnologyCategory();
    getDealerByCity();
    bindClickAlbumLike();//方案点赞
    bindClickAlbumFav();//方案收藏
}

$(document).ready(function(){
    init();
});

function bindClickDealerLogo(){
    $('#companylogo1').on('click', '.companylogo', function(){
        goToDealer($(this).attr('data-attr'));
    });
}

function bindClickDealer(){
    $('#material').on('click', '.materialview', function(){
        goToDealer($(this).attr('data-attr'));
    });
}

function bindClickProduct(){
    $('#product').on('click', '.productview, .productview1', function(){
        goToProduct($(this).attr('data-attr'));
    });
}

function bindClickDesigner(){
    $('#hotdesigner').on('click', '.hotdesignerview, .hotdesignerview1, .hotdesignerview2', function(){
        goToDesigner($(this).attr('data-attr'));
    });
}

function bindClickAlbum(){
    $('#designer').on('click', '.designer_content, .designer_content1', function(){
        goToAlbum($(this).attr('data-attr'));
    });
}

function bindClickCategoryDealer(){
    $('#material_swiper').on('click', '.d_swipwer_span', function(){
        $(this).siblings().removeClass('active');
        getDealerByCategory($(this).attr('data-attr'));
    });
}

function bindClickCityDesigner(){
    $('#hotdesigner_swiper').on('click', '.d_swipwer_span', function(){
        $(this).siblings().removeClass('active');
        getDesignerByCity($(this).attr('data-attr'));
    });
}

function bindClickBrandProduct(){
    $('#product_swiper').on('click', '.d_swipwer_span', function(){
        $(this).siblings().removeClass('active');
        getProductByBrand($(this).attr('data-attr'));
    });
}

function bindClickTechnologyCategoryProduct(){
    $('#product_swiper').on('click', '.d_swipwer_span', function(){
        $(this).siblings().removeClass('active');
        getProductByTechnologyCategory($(this).attr('data-attr'));
    });
}

function bindClickStyleAblum(){
    $('#designer_swiper').on('click', '.d_swipwer_span', function(){
        $(this).siblings().removeClass('active');
        getAlbumByStyle($(this).attr('data-attr'));
    });
}

function bindClickAlbumLike(){
    $('#designer').on('click', '.like-outer', function(e){
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
                showLoginReg(true);;
            }
        });
        e.stopPropagation();
    });
}

function bindClickAlbumFav(){
    $('#designer').on('click', '.fav-outer', function(){
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
                showLoginReg(true);;
            }
        });
        e.stopPropagation();
    });
}