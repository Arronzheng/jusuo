@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 200px 0;">
        <form>

            <div class="layui-form-item">
                <label class="layui-form-label">新密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="new_password" value="" lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">确认新密码</label>
                <div class="layui-input-inline">
                    <input type="password" name="confirm_password" value="" lay-verify="required|confirmPass" autocomplete="off" class="layui-input">
                </div>
            </div>


            <div class="layui-form-item submit-container">
                <div class="layui-input-block">
                    {{csrf_field()}}
                    @if(isset($data))
                        <input type="hidden" name="id" value="{{$data->id}}" />
                    @endif
                    <button class="layui-btn layui-btn-custom-blue" id="submitBtn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                    {{--<button type="reset" class="layui-btn layui-btn-primary">重置</button>--}}
                </div>
            </div>
        </form>

    </div>
@endsection

@section('script')

    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form;
        layui.element.init();

        /*自定义form验证*/
        form.verify({
            confirmPass:function(value){
                if($('input[name=new_password]').val() !== value)
                    return '两次密码输入不一致！';
            }
        });
        /*自定义form验证*/

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/platform/sub_admin/brand/api/account')}}/{{$data->id}}/modify_pwd";
        let submitMethod = "PUT";

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;
            //处理开关控件：需要将其转换成数值
            if(!form_field.status) {
                form_field.status = "0";
            }
            //提交方式
            if(submitMethod){
                form_field._method = submitMethod;
            }
            ajax_post(submitAction,
                form_field,
                function(result){
                    if(result.status){
                        layer.msg(result.msg, {
                            time: 1500
                        }, function(){
                            parent.reloadTable();
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭

                        });
                    }else{
                        //将提交按钮恢复
                        $('#submitBtn').attr('disabled',false);
                        layer.msg(result.msg);
                    }
                },function(result){
                    //将提交按钮恢复
                    $('#submitBtn').attr('disabled',false);
                    layer.msg('操作失败');
                });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
    </script>
@endsection