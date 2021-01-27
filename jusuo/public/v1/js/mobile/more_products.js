var products = null;

$(document).ready(function(){
    get_more_products();
})

function get_more_products(){
    layer.load(1);
    $.get(product_list_collocations,function(res){
        if(res.status){
            products = res.data;
            var data = res.data;
            var product_collocations = template('product_list_tpl',{data:data});
            $('#product_list').html(product_collocations);
        }
        layer.closeAll("loading");
    })
}

function bind_fav_product_collocations(click){
    layer.load(1);
    var index = $(click).data('index');
    var id = products[index].web_id_code;
    var count = products[index].count_fav;

    if(products[index].collected == false){
        var operation = 1;
        ajax_post(product_fav,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).addClass('active');
                products[index].collected = true;

                count = count + 1;
                products[index].count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);

                layer.msg(res.msg);
            }else{
                layer.msg(res.msg);
            }
            layer.closeAll("loading");
        },function(){})
    }else{
        var operation = 2;
        ajax_post(product_fav,{aid: id,op: operation},function(res){
            if(res.status){
                $(click).removeClass('active');
                products[index].collected = false;

                count = count - 1;
                products[index].count_fav = count;
                var html = '<span class="iconfont icon-shoucang2"></span>' + count;
                $(click).html(html);

                layer.msg(res.msg);
            }else{
                layer.msg(res.msg);
            }
            layer.closeAll("loading");
        },function(){})
    }
}