var designer_info = null;

$(document).ready(function(){
    //获取设计师基本信息
    get_designer_info();

    //获取优秀方案信息
    list_nice_album();

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

function get_designer_info(){

    ajax_get(designer_info_api_url, function (res) {


        if (res.status) {

            designer_info = res.data;

            document.title = designer_info.detail.nickname;

            if(designer_info.bg!=''){
                //$('#cover').attr('style','background-image:url('+designer_info.bg+');background-size:cover;');
                $('#cover-bg').attr('style','background-image:url('+designer_info.bg+')');
            }

            var designer_profile_html = template('designer-profile-tpl', {designer: designer_info});

            $('#designer-profile').html(designer_profile_html)
            $('#cover-avatar').css("background-image","url('"+designer_info.detail.url_avatar+"')")

            wxShare(designer_info.detail.nickname+'的主页'+(designer_info.site_title?'-'+designer_info.site_title:''),
                designer_info.detail.self_introduction?designer_info.detail.self_introduction:'',
                'https://www.ijusuo.com'+designer_info.detail.url_avatar
            );

        }else{
            if(result.code == 2001){
                m_go_login()
            }else{
                layer.msg(result.msg)
            }
        }


    }, function () {
    })
}

//获取优秀方案列表
function list_nice_album() {

    ajax_get(nice_album_api_url, function (res) {

        if (res.status) {

            var datas = res.data;

            if(datas.length<=0){
                $('#nice-album').hide();
            }else{
                var html = template('nice-album-tpl', {datas: datas});
                $('#nice-album').html(html)
            }

            var mySwiper1 = new Swiper('.section-swiper-outer',{
                pagination: {el:'.swiper-pagination',type:'fraction'},
            });

        }else{
            if(res.code == 2001){
                m_go_login()
            }else{
                layer.msg(res.msg)
            }
        }

    }, function () {
    })
}

function designer_focus(){
    layer.load(1);
    var designer_id = designer_info.web_id_code;
    if(designer_info.focused==true){
        ajax_post(designer_focus_api_url,{op:2,aid:designer_id},function(res){
            //取消关注
            if(res.status){
                $("#focus-block .action").html("+关注")
                designer_info.focused = false;
                $('#fan-count').html(parseInt($('#fan-count').html())-1);
                layer.msg(res.msg)
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
        //关注
        ajax_post(designer_focus_api_url,{op:1,aid:designer_id},function(res){
            if(res.status){
                $("#focus-block .action").html("取消关注")
                designer_info.focused = true
                $('#fan-count').html(parseInt($('#fan-count').html())+1);
                layer.msg(res.msg)
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