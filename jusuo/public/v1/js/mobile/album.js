var album_info = null;
var album_section = null;
var album_comments = null;
var album_similiars = null;

$(document).ready(function(){
    getData();
    get_album_info();
    get_album_section();
    get_album_comments();
    get_album_similiars();

    //获取产品列表
    get_album_products();


});

//获取产品列表
function get_album_products() {

    ajax_get(album_product_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            $('#product-count').html(datas.total);

            if(datas.total<=0){
                $('#product-list').hide();
            }else{
                var html = template('list-product-tpl', {datas: datas.data});
                $('#product-list .item-list').html(html)
            }

        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }


    }, function () {
    })
}

function bindClickTab(){
    $('.tab-content-1').addClass('hidden');
    $('.tab-content-2').addClass('hidden');
    $('.container').on('click', '.section-tab', function(){
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $(this).parent().parent().find('.tab-content').addClass('hidden');
        $(this).parent().parent().find('.tab-content-'+$(this).attr('data-attr')).removeClass('hidden');
    });
}

function getData(){

}

//获取方案基本信息
function get_album_info() {
    layer.load(1)

    ajax_get(album_info_api_url, function (res) {
        console.log(res);
        layer.closeAll("loading");

        if (res.status) {

            var album = res.data;

            album_info = album;

            document.title = album_info.title;

            //基本信息
            $("#album_baseinfo").css("background-image","url("+ album.photo_cover +")");
            var base_info = template('album_baseinfo_tpl',{album:album});
            $("#album_baseinfo").html(base_info);

            //tag
            var style_text = album.style_text;
            var style_arr = style_text.split('/');
            var tag = template('album_tag_tpl',{album:album,style_arr:style_arr});
            $("#album_tag").html(tag);

            //简述
            if(album.description_design!='')
                $("#album_description").html(album.description_design);
            else{
                $("#album_description_title").addClass('hidden');
            }

            //户型
            if(album.photo_layout_data.length>0) {
                var album_huxing_photo = template('album_huxing_photo_tpl', {album: album});
                $("#album_huxing_photo").html(album_huxing_photo);
                $("#album_description_layout").html(album.description_layout);
            }
            else{
                $("#album_huxing_photo_title").addClass('hidden');
            }

            var mySwiper1 = new Swiper('.section-swiper-outer',{
                pagination: {el:'.swiper-pagination',type:'fraction'},
            });

            wxShare(album_info.title+(album.site_title?'-'+album.site_title:''),
                album.description_design?album.description_design:'',
                'https://www.ijusuo.com'+album.photo_cover,
                //https://www.ijusuo.com/storage/images/design/21/20/04/oI/rjtLEMkXOSqmynF3OBD6gkV5JMM4DXp2ePmhefMH.jpeg
            );

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

function get_album_section(){
    ajax_get(album_section_api_url, function (res) {
        console.log(res);

        if (res.status) {
            var section = res.data;
            album_section = section;

            var album_section_html = template('album_section_tpl',{sections: section});
            $("#album_section").html(album_section_html);

            var mySwiper1 = new Swiper('.section-swiper-outer',{
                pagination: {el:'.swiper-pagination',type:'fraction'},
            });
            bindClickTab();
        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

function get_album_comments(){
    ajax_get(album_comments_api_url, function (res) {
        console.log(res);

        if (res.status) {
            var data = res.data.data;
            album_comments = data;

            var album_comment_html = template('album_comments_tpl',{data:data});
            $("#album_comments").html(album_comment_html);

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

function get_album_similiars(){
    ajax_get(album_similiar_api_url, function (res) {
        console.log(res);

        if (res.status) {
            var data = res.data;
            album_similiars = data;
            console.log(album_similiars);

            var album_similiars_html = template('album_similiars_tpl',{data:data});
            $("#album_similiars").html(album_similiars_html);

        }else{
            layer.msg(res.msg)
        }


    }, function () {
    })
}

function bind_send_album_comment(){
    layer.load(1);
    var comment = $("#album_comment_input").val();
    if(comment.length == 0){
        layer.msg('请输入内容')
        return;
    }

    ajax_post(commit_comment_api_url,{content: comment},function(res){
        console.log(res);
        if(res.status){
            layer.msg(res.msg);
            $("#album_comment_input").val('');
            get_album_comments();
        }else{
            if(res.code == 2001){
                m_go_login()
            }else{
                layer.msg(res.msg)
            }
        }
        layer.closeAll("loading");
    },function(){});
}

//推荐方案点赞
function like_album(click){
    layer.load(1);
    var index = $(click).data('index');
    var id = album_similiars[index].web_id_code;
    var count = album_similiars[index].count_praise;

    if(album_similiars[index].liked == false){
        var operation = 1;
        ajax_post(album_like_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                album_similiars[index].liked = true;

                count = count + 1;
                album_similiars[index].count_praise = count;
                var html = '<span class="iconfont icon-dianzan2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){})
    }else{
        var operation = 2;
        ajax_post(album_like_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                album_similiars[index].liked = false;

                count = count - 1;
                album_similiars[index].count_praise = count;
                var html = '<span class="iconfont icon-dianzan2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){

        })
    }
}

//收藏推荐方案
function collect_album(click){
    layer.load(1);
    var index = $(click).data('index');
    var id = album_similiars[index].web_id_code;
    var count = album_similiars[index].count_fav;

    if(album_similiars[index].collected == false){
        var operation = 1;
        ajax_post(album_collect_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                album_similiars[index].collected = true;

                count = count + 1;
                album_similiars[index].count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){})
    }else{
        var operation = 2;
        ajax_post(album_collect_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                album_similiars[index].collected = false;

                count = count - 1;
                album_similiars[index].count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){

        })
    }
}

function bind_to_album(click){
    var index = $(click).data('index');
    var id = album_similiars[index].web_id_code;

    window.location.href='/mobile/album/s/'+id;
}

function more_comment(){
    console.log('more_')
    var id = album_info.web_id_code;

    window.location.href = '/mobile/album/comment/'+id;
}


function bind_to_product(click){
    var index = $(click).data('index');


    var id = product_collocations_data[index].web_id_code;

    window.location.href='/mobile/product/s/'+id;
}

function bind_to_designer(click){
    var index = $(click).data('index');

    console.log(album_similiars[index]);

    var id = album_similiars[index].designer.web_id_code;

    window.location.href='/mobile/designer/s/'+id;
}

function bind_to_album_designer(){
    var id = album_info.designer_info.web_id_code;

    window.location.href='/mobile/designer/s/'+id;
}

function bind_fav_album(click){
    layer.load(1);
    var id = album_info.web_id_code;
    var count = album_info.count_fav;

    if(album_info.collected == false){
        var operation = 1;
        ajax_post(album_collect_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                album_info.collected = true;
                count = count + 1;
                album_info.count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){})
    }else{
        var operation = 2;
        ajax_post(album_collect_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                album_info.collected = false;

                count = count - 1;
                album_info.count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){

        })
    }
}

function bind_like_album(click){
    layer.load(1);
    var id = album_info.web_id_code;
    var count = album_info.count_praise;

    if(album_info.liked == false){
        var operation = 1;
        ajax_post(album_like_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                album_info.liked = true;

                count = count + 1;
                album_info.count_praise = count;
                var html = '<span class="iconfont icon-dianzan2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){})
    }else{
        var operation = 2;
        ajax_post(album_like_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                album_info.liked = false;

                count = count - 1;
                album_info.count_praise = count;
                var html = '<span class="iconfont icon-dianzan2"></span>' + count;
                $(click).html(html);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){

        })
    }
}