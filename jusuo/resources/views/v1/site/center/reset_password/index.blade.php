@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/info_verify.css')}}">

    <div class="module-tab">
        @include('v1.site.center.components.reset_password.global_tab',[
        'active'=>'oldpwd'
        ])

    </div>

    <div id="verify-form">

        <form class="layui-form" action="" lay-filter="component-form-group">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layui-form-label">原密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="oldpassword" value=""  lay-verify="required" autocomplete="off" placeholder="请输入原密码" class="layui-input">
                </div>
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
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        layui.element.init();

        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({

        });


        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            ajax_post('{{url($url_prefix.'center/reset_password/reset')}}',
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