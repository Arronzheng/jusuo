var product_info = null;
var product_collocations_data = null;
var product_albums_data = null;

$(document).ready(function(){
    getData();
    get_product_info();
    get_list_product_spaces();
    get_list_product_accessories();
    get_list_product_collocations();
    get_list_product_albums();
    get_list_product_qas();

    bindClickTab();
});

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

function get_product_info(){
    layer.load(1)
    $.get(product_info_api_url,function(res){
        console.log(res);
        if(res.status){
            var product = res.data;

            document.title = product.name;

            //产品图默认索引
            product.active = 0;

            product_info = product;
            var product_name = product_info.name + ' ' + product_info.code;
            //设置标题
            $('#product_name').html(product_name);

            //轮播图
            var product_images = template('product_images_tpl',{product: product});
            $("#product_photo").html(product_images);
            var mySwiper1 = new Swiper('.cover-swiper-outer',{
                pagination: {el:'.swiper-pagination',type:'fraction'},
            });


            var collect_info = template('collect_info_tpl',{product: product});
            $("#collect_info").html(collect_info);

            //品牌信息
            var brand_info = template('brand_info_tpl',{product: product});
            $("#brand_info").html(brand_info);

            //标签
            var product_category = product.product_category;

            var apply_categories_text = product.apply_categories_text;
            var apply_categories_arr = apply_categories_text.split('/');

            var technology_categories_text = product.technology_categories_text;
            var technology_categories_arr = technology_categories_text.split('/');

            var surface_features_text = product.surface_features_text;
            var surface_features_arr = surface_features_text.split('/');

            var styles_text = product.styles_text;
            var styles_arr = styles_text.split('/');

            var tag_info = template('tag_info_tpl',{product_category:product_category, apply_categories_arr: apply_categories_arr, technology_categories_arr:technology_categories_arr, surface_features_arr: surface_features_arr, styles_arr: styles_arr});
            $('#tag_info').html(tag_info);

            //介绍
            var section = template('section_tpl',{product: product});
            $("#section_outer").html(section);

            //实物图
            if(product.photo_practicality.length>0) {
                var section_content = template('section_content_tpl', {product: product});
                $("#section_content").html(section_content);
            }
            else{
                $('#section_content_div').remove();
            }

            //视频
            var product_video = template('product_video_tpl',{product:product});
            $("#product_video").html(product_video);

            wxShare(product.name+(product.site_title?'-'+product.site_title:''),
                product.customer_value?product.customer_value:product_name,
                'https://www.ijusuo.com'+product.photo_product[0]
            );

        }else{
            layer.msg(res.msg)
        }

    })
    layer.closeAll("loading");

}

//空间应用
function get_list_product_spaces(){
    $.get(product_list_space_url,function(res){
        if(res.status){
            var data = res.data;
            var space_images = template('space_images_tpl',{data: data});
            $("#space_images").html(space_images);
        }
    });
}


//配件
function get_list_product_accessories(){
    $.get(product_list_accessories,function(res){
        if(res.status){
            var data = res.data;

            var product_accessories = template('product_accessories_tpl',{data:data});
            $("#product_accessories").html(product_accessories);
        }
    });
}

//搭配
function get_list_product_collocations(){
    $.get(product_list_collocations,function(res){
        if(res.status){
            product_collocations_data = res.data;
            var data = res.data;
            var product_collocations = template('product_collocations_tpl',{data:data});
            $('#product_collocations').html(product_collocations);
        }
    })
}

//相关方案
function get_list_product_albums(){
    $.get(product_list_albums,function(res){

        if(res.status){
            var data = res.data;
            product_albums_data = res.data;
            var product_albums = template('product_albums_tpl',{data:data});
            $("#product_albums").html(product_albums);
        }
    })
}

//问答
function get_list_product_qas(){
    $.get(product_list_qas,function(res){
        if(res.status){
            var data = res.data.data;
            var product_qas = template('product_qas_tpl',{data:data});
            $("#product_qas").html(product_qas);
        }
    })
}

function bind_send_product_qa(){
    layer.load(1);
    var question = $("#product_queston").val();
    if(question.length == 0){
        layer.msg('请输入内容')
        return;
    }

    ajax_post(product_send_qa,{content: question},function(res){

        if(res.status){
            layer.msg(res.msg);
            $("#product_queston").val('');
            get_list_product_qas();
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

function bind_fav_product(){
    layer.load(1);
    var id = $("#product_fav_button").data('product_fav_id');
    var count = product_info.count_fav;

    if(product_info.collected == false){
        var operation = 1;
        ajax_post(product_fav,{aid: id,op: operation},function(res){
            if(res.status){
                $("#product_fav_button").addClass('active');
                product_info.collected = true;

                count = count + 1;
                product_info.count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $("#product_fav_button").html(html);

                layer.msg(res.msg);
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
        ajax_post(product_fav,{aid: id,op: operation},function(res){
            if(res.status){
                $("#product_fav_button").removeClass('active');
                product_info.collected = false;

                count = count - 1;
                product_info.count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $("#product_fav_button").html(html);

                layer.msg(res.msg);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){})
    }
}

function bind_fav_product_collocations(click){
    layer.load(1);
    var index = $(click).data('index');
    var id = product_collocations_data[index].web_id_code;
    var count = product_collocations_data[index].count_fav;

    if(product_collocations_data[index].collected == false){
        var operation = 1;
        ajax_post(product_fav,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                product_collocations_data[index].collected = true;

                count = count + 1;
                product_collocations_data[index].count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);

                layer.msg(res.msg);
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
        ajax_post(product_fav,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                product_collocations_data[index].collected = false;

                count = count - 1;
                product_collocations_data[index].count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);

                layer.msg(res.msg);
            }else{
                if(res.code == 2001){
                    m_go_login()
                }else{
                    layer.msg(res.msg)
                }
            }
            layer.closeAll("loading");
        },function(){})
    }
}

//方案点赞
function like_album(click){
    layer.load(1);
    var index = $(click).data('index');
    var id = product_albums_data[index].web_id_code;
    var count = product_albums_data[index].count_praise;

    if(product_albums_data[index].liked == false){
        var operation = 1;
        ajax_post(like_album_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                product_albums_data[index].liked = true;

                count = count + 1;
                product_albums_data[index].count_praise = count;
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
        ajax_post(like_album_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                product_albums_data[index].liked = false;

                count = count - 1;
                product_albums_data[index].count_praise = count;
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

function collect_album(click){
    layer.load(1);
    var index = $(click).data('index');
    var id = product_albums_data[index].web_id_code;
    var count = product_albums_data[index].count_fav;

    if(product_albums_data[index].collected == false){
        var operation = 1;
        ajax_post(album_collect_api_url,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                product_albums_data[index].collected = true;

                count = count + 1;
                product_albums_data[index].count_fav = count;
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
                product_albums_data[index].collected = false;

                count = count - 1;
                product_albums_data[index].count_fav = count;
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
    var id = product_albums_data[index].web_id_code;

    window.location.href='/mobile/album/s/'+id;
}

function bind_to_product(click){
    var index = $(click).data('index');

    var id = product_collocations_data[index].web_id_code;
    console.log(id);

    window.location.href= '/mobile/product/s/'+id;
}

function more_qa(){
    console.log(product_info);
    var id = product_info.web_id_code;

    window.location.href='/mobile/product/comment/'+id;
}

function bind_more_product(){
    console.log(product_info);
    var id = product_info.web_id_code;

    window.location.href='/mobile/product/more_product/'+id;
}

function bind_to_designer(click){
    var index = $(click).data('index');

    console.log(product_albums_data[index]);

    var id = product_albums_data[index].author_web_id_code;

    window.location.href='/mobile/designer/s/'+id;
}