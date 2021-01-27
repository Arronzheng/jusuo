@extends('v1.admin_brand.layout',[])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>品牌信息</cite></a>
        </div>
    </div>
    @include('v1.admin_brand.components.brand_info_tabs')
    <div class="layui-fluid">
        <div class="layui-tab layui-tab-brief" >
            <div class="layui-tab-content">
                <form class="layui-form" action="#" >
                    <div class="layui-form-item">
                        <label class="layui-form-label">旧密码</label>
                        <div class="layui-input-inline">
                            <input type="password" name="old_password" lay-verify="required" placeholder="请输入旧密码" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">新密码</label>
                        <div class="layui-input-inline">
                            <input type="password" name="new_password" lay-verify="required|pass" placeholder="请输入新密码" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">请填写6到12位密码</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">确认密码</label>
                        <div class="layui-input-inline">
                            <input type="password" name="confirm_password" lay-verify="required|pass" placeholder="请确认新密码" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">请填写6到12位密码</div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>

                </form>
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
            pass: [/(.+){6,12}$/, '密码必须6到12位']
        });

        //最后一定要进行form的render，不然控件用不了
        form.render();

        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;

            if(form_field.new_password !== form_field.confirm_password){
                layer.msg('新密码与确认密码不一致！');return false;
            }

            ajax_post('{{url($url_prefix.'admin/brand/security_center/api/modify_pwd')}}',
                form_field,
                function(result){
                    if(result.status){
                        layer.msg('修改成功！')
                        location.reload();
                    }else{
                        layer.msg(result.msg)
                    }
                });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

    </script>
@endsection