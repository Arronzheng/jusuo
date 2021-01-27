var album_info = null;

var all_navigator_location_loaded = false;

var product_list = [];

var lookall_album_similiar_switch = false;

//跟帖信息
var current_follow_id = 0;
var current_follow_person = '';

var navigator_init_time = 5;
var navigator_init_count = 0;
var navigator_location_length = 6;
var navigator_slide_items = [
    '方案详情','户型图','产品清单','相似方案','评论'
];
var album_comment_count = 0;

$(function () {

    //二维码
    $('.qrimage').qrcode({width: 180,height: 180,text: current_url});

    get_album_info();

    list_album_sections();

    list_album_products();

    list_album_similars();

    list_album_comments();


    $(".comment-input").on("input propertychange", function() {
        filter_content();
    });

    $(document).on('mouseover','.details1view',function() {
        var content = $(this).html();
        layer.tips("<div style='color:#333;font-size:14px;'>"+content+"</div>", this ,{
            area:'280px',
            tips:[1,'#fdfdfd'],
            time:0
        })
    }).on('mouseout','.details1view',function() {
        layer.closeAll('tips');
    });

});



//导航栏
var scroH,navH,navH1,navH2,slideBarH,footerTop,slideBarFixed=false;
var nowTopIndex,topIndex;
$(function(){
    $(window).scroll(function(){
        scroH = $(this).scrollTop();
        navH = $("#slideBar").offset().top;
        navH1 = $("#designer-profile").offset().top;
        navH2 = $("#designer-profile").height();
        slideBarH = $("#slideBar").height();
        footerTop = $("#footer").offset().top

        for(topIndex=0;topIndex<navigator_location_length;topIndex++){
            if($("#navigator-location-"+topIndex).length>0){
                var module_top = $("#navigator-location-"+topIndex).offset().top;
                if(topIndex >= (navigator_location_length-1)){
                    var next_module_top = $("#footer").offset().top;
                }else{
                    if($("#navigator-location-"+(topIndex+1)).length>0) {
                        var next_module_top = $("#navigator-location-" + (topIndex + 1)).offset().top;
                    }
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

        //更新评论数
        $('.comment-total').html(album_comment_count);
    });
});

//格式化评论内容
function filter_content(){
    $("#comment-input").contents().filter(function(){
        var class_name = $(this).attr('class');
        return class_name != 's-follow';
    }).removeAttr("class").removeAttr("style");
}

//去产品详情
function click_product(id_code){
    window.open('/product/s/'+id_code+'?__bs='+__cache_brand)
}

//关注设计师
function guanzhu() {
    layer.load(1);
    var designer_id = album_info.designer_info.web_id_code;


    if(album_info.designer_info.focused !==false && album_info.designer_info.focused!==true){
        return false;
    }

    if(album_info.designer_info.focused==true){
        ajax_post(designer_focus_api_url,{op:2,aid:designer_id},function(res){
            //取消关注
            if(res.status){
                $(".guanzhu").toggle();
                $(".guanzhu1").toggle();

                album_info.designer_info.focused = false
                album_info.designer_info.fans-=1;
                $('.fans-count').html(album_info.designer_info.fans)
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
        //关注
        ajax_post(designer_focus_api_url,{op:1,aid:designer_id},function(res){
            if(res.status){
                $(".guanzhu").toggle();
                $(".guanzhu1").toggle();
                album_info.designer_info.focused = true
                album_info.designer_info.fans+=1;
                $('.fans-count').html(album_info.designer_info.fans)
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
    $('#qrcodeCanvas').qrcode({width:100,height:100,text:"https://www.ijusuo.com/mobile/album/s/"+album_info.web_id_code});

}

//设计方案的收藏
function collect(){
    layer.load(1);
    var album_id = album_info.web_id_code
    if(album_info.collected==false){
        ajax_post(album_collect_api_url,{op:1,aid:album_id},function(res){

            if(res.status){
                var shoucangnumber = $('#shoucangnumber').html();
                shoucangnumber=parseInt(shoucangnumber)+1;
                $('#shoucangnumber').html(shoucangnumber)
                $("#shoucang").css({"color":"#1582FF"})
                album_info.collected=true;
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
                var shoucangnumber = $('#shoucangnumber').html();
                shoucangnumber=parseInt(shoucangnumber)-1;
                $('#shoucangnumber').html(shoucangnumber)
                $("#shoucang").css({"color":"#777777"})
                album_info.collected=false;
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
function like(){
    layer.load(1);

    var album_id = album_info.web_id_code
    if(album_info.liked==false){
        ajax_post(album_like_api_url,{op:1,aid:album_id},function(res){

            if(res.status){
                var dianzannumber = $('#dianzannumber').html();
                dianzannumber=parseInt(dianzannumber)+1;
                $('#dianzannumber').html(dianzannumber);
                $("#dianzan").css({"color":"#1582FF"})
                //  layer.msg('已点赞！', {icon: 1});
                album_info.liked=true;
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
                var dianzannumber = $('#dianzannumber').html();
                dianzannumber=parseInt(dianzannumber)-1;
                $('#dianzannumber').html(dianzannumber);
                $("#dianzan").css({"color":"#777777"})
                //  layer.msg('已点赞！', {icon: 1});
                album_info.liked=false;

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



//空间介绍导航导航
function change_section_tab(i,z){

    $('.album-section-block').eq(i).find('.section-tab-title').removeClass('d_swipwer_span1').addClass('d_swipwer_span');
    $('.album-section-block').eq(i).find('.section-tab-title').eq(z).addClass('d_swipwer_span1');


    //切换tab-info显示
    $('.album-section-block').eq(i).find('.section-tab-content').hide()
    $('.album-section-block').eq(i).find('.section-tab-content').eq(z).show()

}

//切换图片
function changepicture(i,type,j){

    const section_type = $('.album-section-block').eq(i).find('.section-'+type);
    const img_thumbs = section_type.find('.img-thumb');
    const img_thumb = section_type.find('#image_'+i+'_'+j);
    const img_big = section_type.find("#image_"+i);
    var src = img_thumb.attr("data-src")

    img_thumbs.removeClass('image4');
    img_thumbs.addClass('image3');
    img_thumb.removeClass('image3');
    img_thumb.addClass('image4');

    img_big.css("background-image","url('" + src + "')").attr('src',src);
    img_big.parent().attr('href',src);
}

//获取方案基本信息
function get_album_info() {
    layer.load(1)

    ajax_get(album_info_api_url, function (res) {

        layer.closeAll("loading");

        if (res.status) {

            var album = res.data;

            album_info = album;

            //设置顶部标题
            $('.album-title').html(album_info.title)

            //设置更多相似方案地址
            $('#album_similiar_lookall').attr('data-url',album.more_similiar_url)

            var photo_cover_html = template('photo-cover-tpl', {album: album});
            var photo_layout_html = template('photo-layout-tpl', {album: album});
            var album_basic_info_html = template('album-basic-info-tpl', {album: album});
            var designer_profile_html = template('designer-profile-tpl', {designer: album.designer_info});

            $('#photo-cover').html(photo_cover_html)
            $('#photo-layout').html(photo_layout_html)
            $('#album-basic-info').html(album_basic_info_html)
            $('#designer-profile').html(designer_profile_html)

            if(album.photo_layout_data.length==0){
                $('#photo-layout').remove();
                $('#navigator-location-1').remove();
                $('#slideItem-1').remove();
            }

            //初始化分享
            init_share();

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取方案空间列表
function list_album_sections() {

    ajax_get(album_section_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            var album_sections_html = template('album-sections-tpl', {datas: datas});

            $('#album-sections').html(album_sections_html)
            $('#album-sections-tpl').remove();

            //获取最新的导航点
            var navigator_locations = $('.navigator-location');
            navigator_location_length = navigator_locations.length;
            navigator_locations.each(function(index,elem){
                $(this).attr('id','navigator-location-'+index);
            });

            //导航栏item
            var new_slide_items = [];
            for(var i=0;i<datas.length;i++){
                navigator_slide_items.splice(2+i,0,datas[i].space_type_text)
            }
            var navigator_slides_html = template('navigator-slides-tpl', {datas: navigator_slide_items});
            $('#sidenav').html(navigator_slides_html);
            var branch_height = navigator_location_length*40+"px"
            $('#slideBar .branch').css({'height':branch_height});
        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取方案产品清单列表
function list_album_products() {

    var query_options = {};
    query_options.page = $("#i_product_page").val();
    api_url = set_url_query(album_product_api_url,query_options);

    ajax_get(api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            var current_page = res.data.current_page;
            var total = res.data.total;

            $('#i_product_page').val(current_page)

            product_list = res.data.data;

            if(res.data.data.length>0){
                var album_products_html = template('album-products-tpl', {datas: datas});
                $('#album-products').html(album_products_html)
            }else{
                var html = template('album-products-empty');
                $('#album-products').html(html)
            }

            //初始化页码
            init_pager(current_page,total)



        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取相似方案列表
function list_album_similars() {

    ajax_get(album_similiar_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            console.log(datas)

            var album_similiars_html = template('album-similiars-tpl', {datas: datas});

            console.log(album_similiars_html)

            $('#album-similiars').html(album_similiars_html)

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

//获取更多评论
function list_more_comment(){
    layer.load(1)
    var now_page = $('#i_comment_page').val();
    $('#i_comment_page').val(parseInt(now_page)+1);
    list_album_comments();
}

//刷新评论列表
function refresh_comment(){
    $('#album-comments').html('')
    $('#i_comment_page').val(1);
    list_album_comments();
}

//获取评论列表
function list_album_comments() {


    var query_options = {};
    query_options.page = $("#i_comment_page").val();
    api_url = set_url_query(album_comments_api_url,query_options);

    ajax_get(api_url, function (res) {

        layer.closeAll('load')
        if (res.status) {

            var datas = res.data;

            var current_page = $('#i_comment_page').val();

            //$('.comment-total').html(datas.total)
            album_comment_count = datas.total;

            if(datas.data.length>0){
                var html = template('album-comments-tpl', {datas: datas});

                $('#album-comments').append(html)

                if(datas.current_page == datas.last_page){
                    $('#b-list-more-comment').hide()
                }
            }else{
                if(current_page<=1){
                    var html = template('list-empty-tpl');
                    $('#project').html(html)
                    $('#b-list-more-comment').hide()
                }else{
                    $('#b-list-more-comment').hide()
                }

            }



        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}


function click_more_similiar(obj){
    var url = $(obj).attr('data-url');
    if(url){
        window.open(url);
    }

}

function init_pager(nowPage,total){
    // xlPaging.js 使用方法
    var nowpage = $("#pager").paging({
        nowPage: nowPage, // 当前页码
        pageNum: total==0?1:Math.ceil(total / 6), // 总页码
        buttonNum: Math.ceil(total / 6), //要展示的页码数量
        canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
        showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
        callback: function (num) { //回调函数
            var page = num;
            $('#i_product_page').val(page);
            list_album_products();

        }
    });

}

//检测评论输入内容
function check_comment_content() {
    var content_text = $('#comment-input').text().replace(/^\s*|(\s*$)/g,"").replace(/[\r\n]/g,"");

    if(!content_text){
        $('#btnButton').attr('disabled', true);
        $('#btnButton').css({"background-color":"#D9D9D9","cursor":"not-allowed"})
    } else {
        $('#btnButton').attr('disabled', false);
        $('#btnButton').css({"background-color":"#1582FF","cursor":"pointer"})

    }

    //格式化过滤内容
    filter_content();
}

//点击跟帖
function click_follow(obj) {
    //判断是否已有被跟帖信息
    var exist = $('#comment-input').find('.s-follow');
    if(exist.length>0){
        layer.msg('已有跟帖信息~')
        return false;
    }
    var followperson = $(obj).parents('.pinglunrview').find('.lpertext').html();
    var comment_id = $(obj).parents('.pinglunrview').attr('data-id');
    //设置当前跟帖id
    current_follow_id = comment_id
    current_follow_person = followperson

    var str1 = get_follow_html();
    $("#comment-input").prepend(str1);
}

//获取跟帖html
function get_follow_html(){
    var str1=''
    str1+="<span class='s-follow' style='contentEditable='false';'>"+"@"+current_follow_person+"</span>"
    str1+="&nbsp;"

    return str1;
}

//发表评论
function commit_comment() {

    //用另一个元素先存放着输入内容，再进行处理
    var need_follow = false;
    var comment_content = $('#comment-input').html();
    if(comment_content.indexOf('@') != -1){
        need_follow = true;
    }
    $('#comment-input-shadow').html($('#comment-input').html());
    $('#comment-input-shadow').find('.s-follow').remove();
    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    var content_text = $('#comment-input-shadow').text().replace(/^\s*|(\s*$)/g,"").replace(/[\r\n]/g,"");

    if(!content_text){
        layer.msg('评论内容不能为空或全为空格！', {icon: 2});
        return;
    } else{
        layer.load(1)
        //提交评论
        //console.log(final_content)
        //跟帖信息
        var follow_text = get_follow_html();
        var final_content = content_text
        if(need_follow){
            final_content = follow_text +final_content
        }

        ajax_post(commit_comment_api_url, {
            content:final_content,
            follow:current_follow_id
        }, function(result){
            layer.closeAll('loading')
            if(result.status){
                $('#comment-input').html('')
                current_follow_id = 0
                //刷新评论列表
                refresh_comment();
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