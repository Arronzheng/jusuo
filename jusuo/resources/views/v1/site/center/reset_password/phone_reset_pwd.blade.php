@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/info_verify.css')}}">

    <div class="module-tab">
        @include('v1.site.center.components.reset_password.global_tab',[
        'active'=>'phone'
        ])

    </div>

    <div id="verify-form">

        <form class="layui-form" action="" lay-filter="component-form-group">
            {{csrf_field()}}

            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-inline">
                    <input type="text" name="phone" id="phone" value=""  lay-verify="required" autocomplete="off" placeholder="请输入手机号" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">验证码</label>
                <div class="layui-input-inline">
                    <input type="text" name="verification_code" value=""  lay-verify="required" autocomplete="off" placeholder="请输入验证码" class="layui-input">
                </div>
                <button type="button" class="layui-btn disable" id="captcha_btn" onclick="get_mobile_captcha(this,'reset')">发送验证码</button>
                {{--<div class="btn-msg" onclick="get_mobile_captcha(this,'login')">发送验证码</div>--}}
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">新密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="newpassword" value=""  lay-verify="required" autocomplete="off" placeholder="请输入新密码" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">确认新密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="newpassword_confirmation" value=""  lay-verify="required" autocomplete="off" placeholder="请再次输入新密码" class="layui-input">
                </div>
            </div>

            <hr style="margin:50px 0;"/>

            <div class="layui-form-item">
                <label class="layui-form-label">&nbsp;</label>
                <div class="layui-input-inline">
                    <button type="button" class="layui-btn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')

    <script>

        var mobile_rule = /^1[34578]\d{9}$/;

        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        layui.element.init();

        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({

        });

        //监听手机号注册的手机号输入框
        $("#phone").on('input',function(){
            var mobile = $(this).val();
            //var captcha_box = $(this).parents('.captcha-box');
            var captcha_btn = $('#captcha_btn');
            if (mobile_rule.test(mobile)) {
                captcha_btn.removeClass('disable');
                captcha_btn.addClass('active');
            }else{
                captcha_btn.removeClass('active');
                captcha_btn.addClass('disable');
            }
        });

        //获取短信验证码
        function get_mobile_captcha(elem,action) {

            var elem_obj = $(elem);
            var captcha_box = elem_obj.parents('.captcha-box');
            var mobile = $("#phone").val();

            if(!elem_obj.hasClass('active')){
                return false;
            }

            if (!mobile_rule.test(mobile)) {
                alert('请输入正确的手机号码！');
                return false;
            }
            var time = 60;
            var api_url = '{{ url('center/getResetSmsCode') }}' + '?login_mobile=' +mobile;
            ajax_get(api_url,function(result){
                if(result.status==1){
                    $(elem).html(time + '秒后重新获取').removeClass('active').addClass('disable');

                    var timer = setInterval(function () {
                        time--;
                        if (time == 1) {
                            $(elem).html('发送验证码').addClass('active').removeClass('disable');
                            clearInterval(timer);
                        } else {
                            $(elem).html(time + '秒后重新获取');
                        }
                    }, 1000);
                }
                layer.msg(result.msg)

            },true,true);

        }


        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            ajax_post('{{url($url_prefix.'center/reset_password/phone/reset')}}',
                form_field,
                function(result){
                    // console.log(result);
                    if(result.status){
                        layer.alert(result.msg,{closeBtn :0},function(){
                            window.location.reload();
                        });
                        // layer.msg(result.msg);

                    }else{
                        layer.msg(result.msg);
                    }
                });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

    </script>

@endsection