@extends('v1.site.center.layout',[
    'css' => [],
    'js'=>[
        '/v1/js/site/center/common/common.js',
    ]
])

@section('main-content')
        <!-- 安全中心-->
<div class="detailview" id="b6" >
    <div class="desigplan" id="safedaohang"></div>
    <div id="e0content" style="padding-top:10px;">
        <div id="bypsw">
            <div class="safelabel">原始密码</div>
            <input type="password" name="orignalpsw" id="by_pwd_original_pwd"  class="tc_input" placeholder="请输入原密码" />
            <div class="safelabel">新密码</div>
            <input type="password" name="newpsw"  class="tc_input" id="by_pwd_new_pwd" placeholder="请输入新密码" />
            <div class="safelabel">确认密码</div>
            <input type="password" name="confirmpsw"  class="tc_input" id="by_pwd_confirm_pwd" placeholder="请确认新密码" />
            <div class="safebttom">
                <div class="confirmbutton" onclick="bindSubmitChangePwdByPwd()">确认</div>
                <div class="pswbyphone" onclick="changepsw()">使用手机验证码修改</div>
            </div>
        </div>
        <div id="byphone" style="display: none;">
            <div class="safelabel">手机号码</div>
            <div id="tel"></div>
            <div class="safelabel">验证码</div>
            <div class="confirmcode">
                <input type="password" name="code"  class="tc_input1" id="by_phone_smscode" placeholder="请输入验证码" />
                <input type="hidden" id="by_phone_phone" value="">
                <div class="sendcode" onclick="bindChangePwdSendCode()">发送验证码</div>
            </div>
            <div class="safelabel">新密码</div>
            <input type="password" name="newpsw1" id="by_phone_newpassword"  class="tc_input" placeholder="请输入密码" />
            <div class="safelabel">确认密码</div>
            <input type="password" name="confirmpsw1" id="by_phone_confirmpassword"  class="tc_input" placeholder="请确认新密码" />
            <div class="safebttom">
                <div class="confirmbutton" onclick="bindSubmitChangePwdByPhone()">确认</div>
                <div class="pswbyphone" onclick="changepsw()">使用原密码修改</div>
            </div>
        </div>
    </div>
    <div id="e1content" style="padding-top:10px;display: none;">
        <div class="safelabel">密码</div>
        <input type="password" name="psw"  class="tc_input" id="change_phone_pwd" placeholder="请输入密码" />
        <div class="safelabel">新手机号</div>
        <input type="text" name="phone"  class="tc_input" id="change_phone_new_phone" placeholder="请确认新手机号" />
        <div class="safelabel">验证码</div>
        <div class="confirmcode">
            <input type="password" name="code"  class="tc_input1" id="change_phone_code" placeholder="请输入验证码" />
            <div class="sendcode" onclick="bindChangePhoneSendCode()">发送验证码</div>
        </div>
        <div class="safebttom">
            <div class="confirmbutton" onclick="bindSubmitChangePhone()">确认</div>
        </div>
    </div>
    <div id="e2content" style="padding-top:10px;display: none;">
        <div id="bind-wechat-box" style="margin-left:30px;@if(\Illuminate\Support\Facades\Auth::user()->login_wx_openid) display:none; @endif" >
            <div class="qrcode-img" id="qrcode" onclick="refresh()"></div>
            <div class="tips">
                请使用微信扫描二维码，进行绑定
            </div>

        </div>
        <div id="already-bind-box" style="@if(!\Illuminate\Support\Facades\Auth::user()->login_wx_openid) display:none; @endif">
            <div class="tips" style="margin-left:30px;margin-top:30px;font-size:16px;">
                已绑定微信号
            </div>
            <div class="safebttom">
                <div class="confirmbutton" onclick="bindSubmitUnbindWx()">解除绑定</div>
            </div>
        </div>

    </div>
</div>


@endsection


@section('script')

    <script src="{{ asset('/v1/js/jquery.qrcode.min.js') }}"></script>

    {{--绑定微信--}}
    <script>
        var count = 37;
        var over = true;

        function showQrcode() {
            count = 36;
            over = false;
            setTimeout('check_wechat_bind()', 5000);
            setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
            $('#qrcode').empty();
            var qrcode_url = "{{ url('bind',\Illuminate\Support\Facades\Auth::user()->remember_token) }}?type={{\App\Http\Services\common\WechatService::DESIGNER}}";
            console.log(qrcode_url);
            $('#qrcode').qrcode({width:150,height:150,text:qrcode_url});
        }

        function timeOut() {
            qrcode_disable();
            over = true;
        }

        function refresh() {
            count--;
            qrcode_disable();
            setTimeout('check_wechat_bind()', 5000);
            setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
            over = false;
        }

        function qrcode_disable(){
            $('#qrcode').toggleClass('disabled');
        }

        function check_wechat_bind(){
            if (over){
                return false;
            }
            ajax_post('{{url('check_wechat_bind')}}',{'t':'{{ Auth::user()->remember_token }}','openid':'{{ Auth::user()->login_wx_openid }}','type':'{{\App\Http\Services\common\WechatService::DESIGNER}}'},function (json) {
                if (json.status) {
                    layer.msg('绑定成功！');
                    $('#already-bind-box').show();
                    $('#bind-wechat-box').hide();
                }
                else{
                    if(count){
                        if(count<37){
                            count--;
                            setTimeout('check_wechat_bind()', 5000);
                        }
                        else{
                            count = 36;
                        }
                    }
                }
            })
        }

    </script>


    <script src="{{ asset('/v1/js/site/center/security_center/index.js') }}"></script>




@endsection


