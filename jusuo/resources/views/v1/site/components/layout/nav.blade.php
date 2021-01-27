@include('v1.site.components.layout.top_nav',[
    'hide_top_right'=>isset($hide_top_right)?$hide_top_right:null
])

@if(!isset($hide_nav_row) || !$hide_nav_row)
    @include('v1.site.components.layout.nav_row')
@endif

<div class="login-reg-modal hidden" id="login-reg-model">
    <div class="modal-box">
        <div class="modal-main">
            <div class="login-reg-tab">
                <span class="tab-item active" data-attr="login" onclick="refreshWechatLoginQrcode()" id="tab-item-login">登录</span>
                {{--<span class="tab-item" data-attr="reg" onclick="refreshWechatRegQrcode()" id="tab-item-reg">注册</span>--}}
                <span class="tab-bar moving" style="left: 160px;"></span>
            </div>
            {{--<div class="login-reg-close">
                <span class="iconfont" id="btn-login-reg-close">&#xe62c;</span>
            </div>--}}
            <div id="login-cont" class="modal-cont">
                {{--用户名+密码登录--}}
                <div id="login-tel-pw" class="login-cont-outer">
                    <div class="input-outer"><input name="login_mobile" placeholder="手机号/昵称/账号"></div>
                    <div class="input-outer">
                        <input placeholder="密码" name="password" type="password">
                    </div>
                    <div class="btn-primary" onclick="login_by_pwd();">登录</div>
                </div>
                {{--手机号+短信验证码登录--}}
                <div id="login-tel-msg" class="login-cont-outer hidden captcha-box">
                    <div class="input-outer"><input name="login_mobile" placeholder="手机号"></div>
                    <div class="input-outer">
                        <input name="verification_code" placeholder="验证码">
                        <div class="btn-msg" onclick="get_mobile_captcha(this,'login')">发送验证码</div>
                    </div>
                    <div class="btn-primary" onclick="login_by_sms();">登录</div>
                </div>
                <div id="login-wx" class="login-cont-outer hidden">
                    <div class="qrcode" id="login-qrcode" onclick="refreshWechatLoginQrcode()"></div>
                    <div class="hint">使用微信扫码，即可登录</div>
                </div>
                <div class="btn-cont-outer">
                    <div class="btn-cont-divider"></div>
                    <div class="btn-cont-list">
                        <span class="iconfont active" id="pw-login" data-attr="login-tel-pw">&#xe60d;</span>
                        <span class="iconfont" id="msg-login" data-attr="login-tel-msg">&#xe600;</span>
                        <span class="iconfont" id="wx-login" data-attr="login-wx">&#xe632;</span>
                    </div>
                </div>
            </div>
            <div id="reg-cont" class="modal-cont hidden">
                <div id="reg-tel-pw" class="login-cont-outer captcha-box">
                    <div class="input-outer"><input name="login_mobile" placeholder="手机号"></div>
                    <div class="input-outer">
                        <input placeholder="验证码" name="verification_code">
                        <div class="btn-msg" onclick="get_mobile_captcha(this,'register')">发送验证码</div>
                    </div>
                    <div class="input-outer">
                        <input placeholder="密码" name="password" type="password">
                    </div>
                    <div class="btn-primary" onclick="submitMobileRegister(this);">注册</div>
                </div>
                <div id="reg-wx" class="login-cont-outer hidden">
                    {{--微信注册--}}
                    <div class="qrcode" id="reg-qrcode"  onclick="refreshWechatRegQrcode()"></div>
                    <div class="hint">使用微信扫码，完成注册</div>
                </div>
                {{--手机号+短信验证码绑定微信登录--}}
                <div id="bind-tel-msg" class="login-cont-outer hidden captcha-box">
                    <div class="tips">微信登录成功，绑定手机后即可注册成功！</div>
                    <div class="input-outer"><input name="login_mobile" placeholder="手机号"></div>
                    <div class="input-outer">
                        <input name="verification_code" placeholder="验证码">
                        <div class="btn-msg" onclick="get_mobile_captcha(this,'bind')">发送验证码</div>
                    </div>
                    <div class="btn-primary" onclick="bind_by_sms();">登录</div>
                </div>
                <div class="btn-cont-outer">
                    <div class="btn-cont-divider"></div>
                    <div class="btn-cont-list">
                        <span class="iconfont active" id="pw-reg" data-attr="reg-tel-pw">&#xe60d;</span>
                        <span class="iconfont" id="wx-reg"  data-attr="reg-wx">&#xe632;</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('/v1/js/jquery.qrcode.min.js') }}"></script>
<script src="{{ asset('/v1/js/site/nav.js') }}"></script>
<script>

    var mobile_rule = /^1[34578]\d{9}$/;
    @if ($errors->any())
    layer.msg('{{ $errors->first() }}');
    @endif

    $(document).ready(function(){

        $('#wx-login').on('click',function(){
            refreshWechatLoginQrcode();
        });

    });


    var wechatRegcount = 36;
    var wechatLogincount = 36;
    var wechatRegtoken = '';
    var wechatLoginToken = '';
    var wechatRegFinalToken = '';

    //微信注册二维码
    function refreshWechatRegQrcode() {
        if(wechatRegcount<=0){
            return false;
        }
        if ($('#reg-qrcode').hasClass('disabled')){
            return false;
        }
        wechatRegcount--;
        ajax_get('{{url('/account/getRandomToken')}}',function (json) {
            wechatRegtoken=json.data;
            $('#reg-qrcode').empty();
            $('#reg-qrcode').qrcode({width:200,height:200,text:'{{url("/account/wechatRegister")}}/'+wechatRegtoken});
            $('#reg-qrcode').addClass('disabled');
            setTimeout('checkWechatRegisterStatus()', 3000);
            setTimeout(function(){
                $('#reg-qrcode').removeClass('disabled');
            },'{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
        });
    }

    function checkWechatRegisterStatus(){
        if(wechatRegcount<=0){
            return false;
        }
        ajax_post('{{url('/account/checkWechatRegister')}}',{'t':wechatRegtoken,'_token':'{{csrf_token()}}'},function (json) {
            if (json.status==1) {
                wechatRegFinalToken = json.data;
                openBindMobile();
            }
            else {
                if(json.code==-1){
                    //该微信用户已注册成功，请登录
                    layer.msg('用户已注册过，请直接登录')
                }else{
                    if(wechatRegcount>0){
                        setTimeout('checkWechatRegisterStatus()', 3000);
                    }
                }

            }
        });

    }

    //微信登录二维码
    function refreshWechatLoginQrcode() {
        if(wechatLogincount<=0){
            return false;
        }
        if ($('#login-qrcode').hasClass('disabled')){
            return false;
        }
        wechatLogincount--;
        ajax_get('{{url('/account/getRandomToken')}}',function (json) {
            wechatLoginToken=json.data;
            $('#login-qrcode').empty();
            $('#login-qrcode').qrcode({width:200,height:200,text:'{{url("/account/wechatLogin")}}/'+wechatLoginToken});
            $('#login-qrcode').addClass('disabled');
            setTimeout('checkWechatLoginStatus()', 3000);
            setTimeout(function(){
                $('#login-qrcode').removeClass('disabled');
            },'{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
        });
    }

    function login_redirect(){
        var full_url = window.location.href;
        if(full_url.indexOf('/error') != -1){
            location.href = '/index?__bs='+__cache_brand;
        }else{
            location.reload();
        }
    }

    function checkWechatLoginStatus(){
        if(wechatLogincount<=0){
            return false;
        }
        ajax_post('{{url('/account/checkWechatLogin')}}',{'t':wechatLoginToken,'_token':'{{csrf_token()}}'},function (json) {
            if (json.status==1) {
                //刷新页面
                login_redirect();
            }
            else {
                if(wechatLogincount>0){
                    setTimeout('checkWechatLoginStatus()', 3000);
                }

            }
        });

    }


    //微信注册后绑定手机
    function openBindMobile(){
        $('#reg-cont').children().hide();
        $('#bind-tel-msg').removeClass('hidden');
        $('#bind-tel-msg').show();
    }

    //监听手机号注册的手机号输入框
    $(".captcha-box input[name='login_mobile']").on('input',function(){
        var mobile = $(this).val();
        var captcha_box = $(this).parents('.captcha-box');
        var captcha_btn = captcha_box.find('.btn-msg');
        if (mobile_rule.test(mobile)) {
            captcha_btn.addClass('active');
        }else{
            captcha_btn.removeClass('active');
        }
    });

    //获取短信验证码
    function get_mobile_captcha(elem,action) {

        var elem_obj = $(elem);
        var captcha_box = elem_obj.parents('.captcha-box');
        var mobile = captcha_box.find("input[name='login_mobile']").val();

        if(!elem_obj.hasClass('active')){
            return false;
        }

        if (!mobile_rule.test(mobile)) {
            alert('请输入正确的手机号码！');
            return false;
        }
        var time = 60;
        var api_url = '/account/getRegisterSmsCode?login_mobile='+mobile;
        if(action=="login"){
            api_url = '/account/getLoginSmsCode?login_mobile='+mobile;
        }
        else if(action=="bind"){
            api_url = '/account/getBindSmsCode?login_mobile='+mobile;
        }
        ajax_get(api_url,function(result){
            if(result.status==1){
                $(elem).html(time + '秒后重新获取').removeClass('active');

                var timer = setInterval(function () {
                    time--;
                    if (time == 1) {
                        $(elem).html('发送验证码').addClass('active');
                        clearInterval(timer);
                    } else {
                        $(elem).html(time + '秒后重新获取');
                    }
                }, 1000);
            }
            layer.msg(result.msg)

        },true,true);

    }

    //提交手机注册
    function submitMobileRegister(elem){
        var elem_obj = $(elem);
        var captcha_box = elem_obj.parents('.captcha-box');
        var login_mobile = captcha_box.find("input[name='login_mobile']").val();
        var verification_code = captcha_box.find("input[name='verification_code']").val();
        var password = captcha_box.find("input[name='password']").val();
        if(login_mobile==''){
            alert('手机号码不能为空！');
            return false;
        }
        if(!mobile_rule.test(login_mobile)){
            alert('请输入正确的手机号码！');
            return false;
        }
        if(verification_code==''){
            alert('短信验证码不能为空！');
            return false;
        }
        if(password==''){
            alert('密码不能为空！');
            return false;
        }
        ajax_post('/account/register',{
            login_mobile:login_mobile,
            verification_code:verification_code,
            password:password
        },function(data){
            if(data.status==1){
                layer.msg('注册成功！');
                if(data.data.login_redirect){
                    location.href=data.data.login_redirect
                    return false;
                }
                login_redirect();
            }else{
                layer.msg(data.msg);
            }

        },true,true)

    }

    //提交用户名+密码登录
    function login_by_pwd(){
        var login_mobile = $('#login-tel-pw').find("input[name='login_mobile']").val();
        var password = $('#login-tel-pw').find("input[name='password']").val();
        if(login_mobile==''){
            alert('用户名不能为空！');
            return false;
        }
        /*if(!mobile_rule.test(login_mobile)){
            alert('请输入正确的手机号码！');
            return false;
        }*/
        if(password==''){
            alert('密码不能为空！');
            return false;
        }
        ajax_post('/account/login_by_pwd',{
            login_mobile:login_mobile,
            password:password
        },function(data){
            if(data.status==1){
                layer.msg('登录成功！');
                if(data.data.login_redirect){
                    location.href=data.data.login_redirect
                    return false;
                }
                login_redirect();
            }else{
                layer.msg(data.msg);
            }

        },true,true)
    }

    //提交用户名+密码登录
    function login_by_sms(){
        var login_mobile = $('#login-tel-msg').find("input[name='login_mobile']").val();
        var verification_code = $('#login-tel-msg').find("input[name='verification_code']").val();
        if(login_mobile==''){
            alert('手机号不能为空！');
            return false;
        }
        if(!mobile_rule.test(login_mobile)){
            alert('请输入正确的手机号码！');
            return false;
        }
        if(verification_code==''){
            alert('验证码不能为空！');
            return false;
        }
        ajax_post('/account/login_by_sms',{
            login_mobile:login_mobile,
            verification_code:verification_code
        },function(data){
            if(data.status==1){
                layer.msg('登录成功！');
                if(data.data.login_redirect){
                    location.href=data.data.login_redirect
                    return false;
                }
                login_redirect();
            }else{
                layer.msg(data.msg);
            }

        },true,true)
    }

    //微信登录后绑定手机注册
    function bind_by_sms(){
        var login_mobile = $('#bind-tel-msg').find("input[name='login_mobile']").val();
        var verification_code = $('#bind-tel-msg').find("input[name='verification_code']").val();
        if(login_mobile==''){
            alert('手机号不能为空！');
            return false;
        }
        if(!mobile_rule.test(login_mobile)){
            alert('请输入正确的手机号码！');
            return false;
        }
        if(verification_code==''){
            alert('验证码不能为空！');
            return false;
        }
        ajax_post('/account/bind_by_sms',{
            login_mobile:login_mobile,
            verification_code:verification_code,
            token:wechatRegFinalToken,
        },function(data){
            if(data.status==1){
                layer.msg('注册成功！正在登录...');
                if(data.data.login_redirect){
                    location.href=data.data.login_redirect
                    return false;
                }
                login_redirect();
            }else{
                layer.msg(data.msg);
            }

        },true,true)
    }
</script>