var noticenav=0;
//消息通知——系统消息
var notice={
    "list":[{"label":"系统通知","time":"15分钟前","content":["系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知系统公共通知"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]},
        {"label":"方案通知","time":"40分钟前","content":["恭喜您！您的方案《保利简约设计》已通过审核，成功发布！"]},
        {"label":"账号通知","time":"40分钟前","content":["抱歉！您的方案《鳌园中式设计》未通过审核，请修改后再发布！","修改意见：XXXXXXXX！"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]},
        {"label":"账号通知","time":"40分钟前","content":["您的账号密码已成功修改！"]}],
    "list1":[{"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false},
        {"name":"薛华少","time":"15分钟前","image":"images/designer/1.png","content":"点赞了您的作品《保利简约设计》","guanzhu":false}],
    "list2":[{"name":"薛华少1","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少2","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少3","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少4","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少5","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少6","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少7","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少8","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"},
        {"name":"薛华少9","time":"15分钟前","image":"images/designer/1.png","article":"《保利简约设计》","content":"评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容评论内容"}]
}

var comment = [];
var fav_notify = [];
var sys_notify = [];

$(document).ready(function(){
    var str=""
    var a=["系统消息"/*,"互动消息","评论"*/]
    for(var i=0;i<a.length;i++){
        if(noticenav==i){
            str+="<div class='designtitle' id='c"+i+"' onclick='noticechangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='c"+i+"' onclick='noticechangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#noticedaohang").append(str);

    //消息通知
    get_comment_list();
    get_fav_list();
    get_sysNotify();
});

function get_comment_list(){
    $.get('/center/info_notify/api/comment_notify',function(res){
        if(res.status){
            comment = res.data;
            // xlPaging.js 消息通知——评论分页使用方法
            var nowpage4 = $("#page4").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(comment.length / 4), // 总页码
                buttonNum: Math.ceil(comment.length / 4), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ4").html("");
                    var txt="<div class='c0container' id='c2container'></div>"
                    $("#produ4").append(txt);
                    var total=Math.min(num*4,comment.length)
                    noticepinglun((num-1)*4,total);

                }
            });
            var endPage = 4;
            if(comment.length < 4){
                endPage =  comment.length;
            }
            noticepinglun(nowpage4.options.nowPage-1,endPage);
        }
    });
}

function get_fav_list(){
    $.get('/center/info_notify/api/favList',function(res){
        console.log(res);
        if(res.status){
            fav_notify = res.data;

            // xlPaging.js 消息通知——互动消息分页使用方法
            var nowpage3 = $("#page3").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(fav_notify.length / 4), // 总页码
                buttonNum: Math.ceil(fav_notify.length / 4), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    //更多产品
                    // $(function(e) {
                    $("#produ3").html("");
                    var txt="<div class='c0container' id='c1container'></div>"
                    $("#produ3").append(txt);
                    var total=Math.min(num*4,fav_notify.length);
                    acticenotice((num-1)*4,total);
                }
            });
            var endPage = 4;
            if(fav_notify.length < 4){
                endPage =  fav_notify.length;
            }

            acticenotice(nowpage3.options.nowPage-1,endPage);

        }

    })
}

function get_sysNotify(){
    $.get('/center/info_notify/api/sysNotify',function(res){
        console.log(res);
        if(res.status){
            sys_notify = res.data;

            // xlPaging.js 消息通知——系统通知分页使用方法
            var nowpage2 = $("#page2").paging({
                nowPage: 1, // 当前页码
                pageNum: Math.ceil(sys_notify.length / 4), // 总页码
                buttonNum: Math.ceil(sys_notify.length / 4), //要展示的页码数量
                canJump: 0,// 是否能跳转。0=不显示（默认），1=显示
                showOne: 0,//只有一页时，是否显示。0=不显示,1=显示（默认）
                callback: function (num) { //回调函数
                    console.log('sss'+num);
                    //更多产品
                    // $(function(e) {
                    $("#produ2").html("");
                    var txt="<div class='c0container' id='c0container'></div>"
                    $("#produ2").append(txt);
                    var total=Math.min(num*4,sys_notify.length)
                    console.log(num+'sss'+total)
                    systemnotice((num-1)*4,total);
                }
            });

            var endPage = 4;
            if(sys_notify.length < 4){
                endPage =  sys_notify.length;
            }

            systemnotice(nowpage2.options.nowPage-1,endPage);
        }
    });
}

// 消息通知——系统消息
function systemnotice(begin,end){
    $("#c0container").html("");
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='systemitem'>"
        str+="<div class='systemhead'>"
        str+="<div class='systemlabel'>"+"【"+sys_notify[i].type_text+"】"+"</div>"
        str+="<div class='systemtime'>"+sys_notify[i].time+"</div>"
        str+="</div>"
        str+="<div class='systemcontent'>"+ sys_notify[i].content +"</div>"
        str+="</div>"
    }
    $("#c0container").append(str);
}
// 消息通知——互动消息
function acticenotice(begin,end){
    $("#c1container").html('');
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='systemitem1'>"
        str+="<div class='noticeimage' id='noticeimage"+i+"'>"+"</div>"
        str+="<div class='noticemiddle'>"
        str+="<div class='noticedetail'>"
        str+="<label class='noticename'>"+fav_notify[i].sender_name+"</label>"
        str+="<label class='noticetime'>"+fav_notify[i].time+"</label>"
        str+="</div>"
        if(fav_notify[i].notify_type == 0){
            //关注
            str+="<div class='noticedetail1'>" + "关注了你" + "</div>"
        }
        if(fav_notify[i].notify_type == 1){
            str+="<div class='noticedetail1'>" + "点赞了你的作品《" + fav_notify[i].album_title + "》" + "</div>"
        }
        if(fav_notify[i].notify_type == 2){
            str+="<div class='noticedetail1'>" + "收藏了你的作品《" + fav_notify[i].album_title + "》" + "</div>"
        }
        //str+="<div class='noticedetail1'>"+notice.list1[i].content+"</div>"
        str+="</div>"
        if(fav_notify[i].fav==false){
            str+="<div class='noticeguanzhu' id='noticeguanzhu"+i+"' onclick='noticeguanzhu("+i+")'>"+"关注"+"</div>"
        }else{
            str+="<div class='noticeguanzhu' id='noticeguanzhu"+i+"' onclick='noticeguanzhu("+i+")'>"+"已关注"+"</div>"
        }
        str+="</div>"
    }
    $("#c1container").append(str);
    for(var i=begin;i<end;i++){
        $("#noticeimage"+i).css({"background-image":"url('"+fav_notify[i].sender_avatar+"')"});
    }
}
// 消息通知——评论
function noticepinglun(begin,end){
    console.log(end);
    $("#c2container").html('');
    var str="";
    for(var i=begin;i<end;i++){
        str+="<div class='systemitem2' id='noticepinglun"+i+"'>"
        str+="<div class='noticeimage' id='noticepimage"+i+"'>"+"</div>"
        str+="<div class='noticemiddle'>"
        str+="<div class='noticedetail'>"
        str+="<label class='noticename'>"+comment[i].sender_name+"</label>"
        str+="<label class='noticetime1'>"+comment[i].time+"</label>"
        str+="</div>"
        str+="<div class='noticedetail2'>"
        if(comment[i].target_comment_id != 0){
            str+="<label class='pinlunno'>"+"回复"+notice.list2[i].reviewperson+"评论您的"+notice.list2[i].article+"："+"</label>"
        }else{
            str+="<label class='pinlunno'>"+"评论您的《"+comment[i].album_title+"》："+"</label>"
        }
        str+=comment[i].content
        str+="</div>"
        str+="<div class='review' id='review"+i+"'onclick='review("+i+")'>"+"回复"+"</div>"
        str+="<div id='noticepl"+i+"' class='noticepl' style='display: none;'>"
        str+="<div class='review1' id='review1"+i+"'onclick='review("+i+")'>"+"回复"+"</div>"
        str+="<div class='pinglunblock' id='ping"+i+"' contentEditable='true' onkeyup='checkContent("+i+")'>"+"</div>"
        str+="<div class='pl_placeholder'>"+"写下您的评论..."+"</div>"
        str+="<div class='button2' type='submit' onclick='commit1("+i+")' id='btnButton"+i+"'>"+"回复"+"</div>";
        str+="</div>"
        str+="</div>"
        str+="</div>"
    }
    $("#c2container").append(str);
    for(var i=begin;i<end;i++){
        $("#noticepimage"+i).css({"background-image":"url('"+comment[i].sender_avatar+"')"});
    }
}
