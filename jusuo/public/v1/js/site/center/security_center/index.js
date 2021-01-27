//安全中心
var safenav=0;
var weixinid="allaaa14";

//个人中心
var personnav=0;
var persondetail=[];
var realnameDetail = [];

var list=['我的方案','产品列表','我的统计','收藏关注','消息通知','个人中心','安全中心']
var navactive=0;

$(document).ready(function(){


    //收藏关注
    showPersonTabs();
    showSafeTabs();
});


function showPersonTabs(){
    var str=""
    var a=["个人资料","实名认证","设计师认证"]
    for(var i=0;i<a.length;i++){
        if(personnav==i){
            str+="<div class='designtitle' id='f"+i+"' onclick='personchangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='f"+i+"' onclick='personchangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#persondaohang").append(str);


}


function showSafeTabs(){
    var str=""
    var a=["修改密码","修改手机","绑定微信"]
    for(var i=0;i<a.length;i++){
        if(safenav==i){
            str+="<div class='designtitle' id='e"+i+"' onclick='safechangenav("+i+")'>"+a[i]+"</div>"
        }else{
            str+="<div class='designtitle1' id='e"+i+"' onclick='safechangenav("+i+")'>"+a[i]+"</div>"
        }
    }
    $("#safedaohang").append(str);

    $.get('/center/get_user_phone',function(res){
        if(res.status == 1){
            //手机号码的显示
            var tel = document.getElementById('tel');
            var phone=res.data.phone;
            var hidephone=phone[0]+phone[1]+phone[2]+"****"+phone[9]+phone[10]
            tel.innerText = hidephone;

            $("#by_phone_phone").val(phone);
        }
    });
}

function getUserInfo(){
    $("#f0content").html('');
    $.get('/center/user_info/get',function(res){
        if(res.status == 1){
            persondetail = res.data;
            console.log(persondetail);
            //个人资料
            var str="";
            str+="<div class='pd_imageview'>"
            str+="<div class='pd_image' id='pd_image'>"+"</div>"
            str+="<div class='change_pdcon'>"
            str+="<input id='upload-input' style=' width:82px;height:24px; cursor: pointer;position: absolute; top: 0; left: 0;opacity: 0;' accept='image/*' type='file' onchange='uploadAvatar(this)'/>"
            str+="<input id='avatar_url' type='hidden' value='"+persondetail.url_avatar+"'/>"
            str+="<div class='change_pdimage'>"+"更改头像"+"</div>"
            str+="</div>"
            str+="</div>"
            str+="<div class='pddetail'>"+"用户账号"+"</div>"
            str+="<div class='pdid'>"+persondetail.login_username+"</div>"
            str+="<div class='pddetail'>"+"昵称"+"</div>"
            str+="<input type='text' name='nicheng' id='nickname' class='nc_input' placeholder='请输入昵称' value='"+persondetail.nickname+"'/>"
            str+="<div class='pddetail'>"+"性别"+"</div>"
            str+="<div class='danxuan'>"
            if(persondetail.gender==1){
                str+="<input type='radio' name='gender' id='paixu1' checked value='1'>"
                str+="<label for='paixu1' style='cursor:pointer;'>男</label>"
                str+="<input type='radio' name='gender' id='paixu2' value='2'>"
                str+="<label for='paixu2' style='cursor:pointer;'>女</label>"
            }else{
                str+="<input type='radio' name='gender' id='paixu1' value='1'>"
                str+="<label for='paixu1' style='cursor:pointer;'>男</label>"
                str+="<input type='radio' name='gender' id='paixu2' checked value='2'>"
                str+="<label for='paixu2' style='cursor:pointer;'>女</label>"
            }
            str+="</div>"
            str+="<div class='pddetail'>"+"实名认证"+"</div>"
            if(persondetail.approve_realname==1){
                str+="<div style='margin-top:10px;height:44px;'>"
                str+="<span class='iconfont icon-shimingrenzheng' style='color:#1582FF;font-size:16px;margin-top:2px;'>"+"</span>";
                str+="<span class='identext'>"+"已认证"+"</span>"
                str+="</div>"
            }else{
                str+="<div class='idbutton' onclick='personchangenav(1)'>"+"去认证"+"</div>"
            }
            str+="<div class='saveperson' id='submitUserInfo' onclick='bindSubmitUserInfo()'>"+"保存"+"</div>"
            $("#f0content").append(str);
            if(persondetail.url_avatar != null){
                $("#pd_image").css({"background-image":"url('"+persondetail.url_avatar+"')"});
            }

        }

    });
}

//安全中心导航
function safechangenav(i){
    if(safenav==i){
        console.log('s'+i)
        return;
    }else{
        console.log(i)
        for(var a=0;a<3;a++){
            if(a!=i){
                document.getElementById("e"+a).className = "designtitle1";
                $('#e'+a+'content').hide().removeClass('show')
            }else{
                safenav=i;
                document.getElementById("e"+a).className = "designtitle";
                $('#e'+a+'content').show().addClass('show')
            }

        }
        //如果是绑定微信
        if(i==2){
            showQrcode();
        }
    }
}

//-------------安全中心——修改密码----------------
function changepsw() {
    $("#bypsw").toggle();
    $("#byphone").toggle();
}
//修改密码通过密码修改
function bindSubmitChangePwdByPwd(){
    var oldpassword = $("#by_pwd_original_pwd").val();
    var newpassword = $("#by_pwd_new_pwd").val();
    var confirmpassword = $("#by_pwd_confirm_pwd").val();

    if(newpassword !== confirmpassword){
        layer.msg('两次输入的密码不一致')
        return false
    }

    var data = {
        oldpassword: oldpassword,
        newpassword: newpassword,
    }

    ajax_post('/center/reset_by_pwd',data,function(res){
        console.log(res);
        if(res.status == 1){
            layer.msg(res.msg)
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

//获取验证码
function bindChangePwdSendCode(){
    var phone = $("#by_phone_phone").val();

    var data = {
        login_mobile: phone,
    }

    $.get('/center/getResetSmsCode',data,function(res){
        console.log(res);
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}

//修改密码通过手机修改
function bindSubmitChangePwdByPhone(){

    var newpassword = $("#by_phone_newpassword").val();
    var phone = $("#by_phone_phone").val();
    var verification_code = $("#by_phone_smscode").val();

    var data = {
        newpassword: newpassword,
        phone: phone,
        verification_code: verification_code,
    }

    ajax_post('/center/reset_by_phone',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });

}

//-------------安全中心——修改密码----------------


//-------------安全中心——修改手机----------------
//修改手机 发送验证码
function bindChangePhoneSendCode(){
    var phone = $("#change_phone_new_phone").val();
    var pwd = $("#change_phone_pwd").val();

    var data = {
        new_phone: phone,
        pwd: pwd
    }

    ajax_post('/center/getResetPhoneCode',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });


}

//修改手机
function bindSubmitChangePhone(){
    var new_phone = $("#change_phone_new_phone").val();
    var verification_code = $("#change_phone_code").val();
    var password = $("#change_phone_pwd").val();

    var data = {
        new_phone: new_phone,
        verification_code: verification_code,
        password: password,
    }

    ajax_post('/center/resetUserPhone',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
            $('#change_phone_pwd').val('');
            $('#change_phone_new_phone').val('');
            $('#change_phone_code').val('');
        }else{
            if(res.code == 2001){
                showLoginReg(true)
            }else{
                layer.msg(res.msg)
            }
        }
    });
}
//-------------安全中心——修改手机----------------


//解绑微信
function bindSubmitUnbindWx(){
    var data = {};
    ajax_post('/center/UnbindWx',data,function(res){
        if(res.status == 1){
            layer.msg(res.msg);
            window.location.reload();
        }else{
            layer.msg(res.msg)
        }
    });
}