@extends('v1.admin_platform.layout',[
    'css' => ['/v1/css/admin/brand/bind_wechat.css'],
    'js'  => ['/v1/js/jquery.qrcode.min.js']
])

@section('content')
    <div class="layui-tab layui-tab-brief"  lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li onclick="location.href='{{url($url_prefix.'admin/platform/security_center/bind_wechat')}}'" class="layui-this">绑定微信</li>
            <li onclick="location.href='{{url($url_prefix.'admin/platform/security_center/modify_pwd')}}'" >修改密码</li>
        </ul>
        <div class="layui-tab-content" >
            <div class="bind-container">
                @if(!Auth::guard('platform')->user()->login_wx_openid)
                    <span class="layui-breadcrumb" lay-separator="——">
                      <a class="layui-this" href="javascript:;">1、验证登录密码</a>
                      <a href="javascript:;">2、绑定新微信号</a>
                    </span>

                <form class="layui-form" id="confirm-pwd-form" action="#" >
                    <label class="form-label">登录密码</label>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="password" name="password" lay-verify="required" placeholder="请输入登录密码" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <button style="width:120px;margin-top:20px;" class="layui-btn layui-btn-sm btn" lay-submit lay-filter="confirmPwd">确定</button>
                    </div>
                </form>
                @else
                    <div id="already-bind-box" style="">
                        <div class="tips" style="margin-top:30px;">
                            已绑定管理员微信号
                        </div>
                        <button style="width:120px;margin-top:20px;" class="layui-btn layui-btn-sm btn" onclick="show_change_wechat_box()">改绑微信</button>
                    </div>
                    <div id="change_wechat" style="display: none">
                        <span class="layui-breadcrumb" lay-separator="——">
                            <a class="layui-this" href="javascript:;">1、验证登录密码</a>
                            <a href="javascript:;">2、绑定新微信号</a>
                        </span>

                        <form class="layui-form" id="confirm-pwd-form" action="#" >
                            <label class="form-label">登录密码</label>
                            <div class="layui-form-item">
                                <div class="layui-input-inline">
                                    <input type="password" name="password" lay-verify="required" placeholder="请输入登录密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <button style="width:120px;margin-top:20px;" class="layui-btn layui-btn-sm btn" lay-submit lay-filter="confirmPwd">确定</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div id="bind-wechat-box" style="display:none;">
                    <div class="qrcode-img" id="qrcode" onclick="refresh()"></div>
                    <div class="tips">
                        请使用微信扫描二维码，进行绑定
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection


@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        layui.element.init();

        var form = layui.form;

        //自定义验证规则
        form.verify({
            question: function(value){
                if(value.length > 50){
                    return '密保问题限50字以内';
                }
            },
            answer: function(value){
                if(value.length > 50){
                    return '密保答案限50字以内';
                }
            }
        });

        //最后一定要进行form的render，不然控件用不了
        form.render();

        //用form监听submit，可以用到validate的功能
        form.on('submit(confirmPwd)', function(form_info){
            var form_field = form_info.field;

            ajax_post('{{url($url_prefix.'admin/platform/security_center/api/verify_pwd')}}',
                form_field,
                function(result){
                    if(result.status){
                        layer.msg('密码验证成功');
                        show_bind_wechat_box();
                    }else{
                        layer.msg(result.msg)
                    }
                });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

        function show_bind_wechat_box(){
            $('#confirm-pwd-form').hide();
            $('#bind-wechat-box').show();
            $('.layui-breadcrumb a').removeClass('layui-this');
            $('.layui-breadcrumb a').eq(1).addClass('layui-this');
            showQrcode();
        }

        var count = 37;
        var over = true;
        
        function showQrcode() {
            count = 36;
            over = false;
            setTimeout('check_wechat_bind()', 5000);
            setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
            $('#qrcode').qrcode({width:150,height:150,text:"{{ url('bind',Auth::guard('platform')->user()->remember_token) }}?type={{\App\Http\Services\common\WechatService::PLATFORM}}"});
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
            ajax_post('{{url('check_wechat_bind')}}',{'t':'{{ Auth::guard('platform')->user()->remember_token }}','openid':'{{ Auth::guard('platform')->user()->login_wx_openid }}','type':'{{\App\Http\Services\common\WechatService::PLATFORM}}'},function (json) {
                if (json.status) {
                    layer.msg('绑定成功！');
                    location.reload();
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
        
        function show_change_wechat_box() {
            $('#already-bind-box').hide();
            $('#change_wechat').show();
        }

    </script>
@endsection