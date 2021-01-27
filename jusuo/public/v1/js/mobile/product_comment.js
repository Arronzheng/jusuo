$(document).ready(function(){

    list_product_comments();

});

//获取更多评论
function list_more_comment(){
    layer.load(1)
    var now_page = $('#i_comment_page').val();
    $('#i_comment_page').val(parseInt(now_page)+1);
    list_product_comments();
}

//刷新评论列表
function refresh_comment(){
    $('#product-comments').html('')
    $('#i_comment_page').val(1);
    list_product_comments();
}

//获取评论列表
function list_product_comments() {


    var query_options = {};
    query_options.page = $("#i_comment_page").val();
    api_url = set_url_query(product_comments_api_url,query_options);

    ajax_get(api_url, function (res) {

        //layer.closeAll('load')
        if (res.status) {

            var datas = res.data;

            var current_page = $('#i_comment_page').val();

            //$('.comment-total').html(datas.total)
            product_comment_count = datas.total;

            $('.comment-count').html(product_comment_count)

            if(datas.data.length>0){
                var html = template('product-comments-tpl', {datas: datas});

                $('#product-comments').append(html)

                if(datas.current_page == datas.last_page){
                    $('#b-list-more-comment').hide()
                }else{
                    $('#b-list-more-comment').show()
                }
            }else{
                if(current_page<=1){
                    var html = template('list-empty-tpl');
                    $('#list-tips').html(html)
                    $('#b-list-more-comment').hide()
                }else{
                    $('#b-list-more-comment').hide()
                }
            }

        }else{
            if(result.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(result.msg)
            }
        }

    }, function () {
    })
}

//发表评论
function commit_comment() {

    var comment_content = $('#comment-input input').val();

    //将输入的内容去掉开头和结尾的空格，若长度大于0，则说明不全是空格，若长度为0则全是空格
    comment_content = comment_content.replace(/(^\s*)|(\s*$)/g, '');//去除空格;

    if (comment_content == '' || comment_content == undefined || comment_content == null) {
        layer.msg('评论内容不能为空或全为空格！', {icon: 2});
        return false;
    } else{
        layer.load(1)
        //提交评论

        ajax_post(commit_comment_api_url, {
            content:comment_content,
        }, function(result){
            layer.closeAll('loading')
            if(result.status){
                $('#comment-input input').val('')
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