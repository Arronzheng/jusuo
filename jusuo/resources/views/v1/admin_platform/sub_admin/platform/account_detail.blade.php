@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 20px 0;">
        <form>

            <div class="layui-form-item">
                <label class="layui-form-label">登录账号</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->login_account}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">登录用户名</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->login_username}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">登录手机号</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->login_mobile}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真实姓名</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->realname}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所属部门</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->self_department}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">担任职位</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->self_position}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{\App\Models\AdministratorBrand::statusGroup($data->status)}}
                    </div>
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

        var laydate = layui.laydate;
        laydate.render({
            elem: '#expired_at',
            type:'datetime',
            value:'{{request()->input('expired_at') ? request()->input('expired_at') :''}}'
        });

        /*自定义form验证*/
        form.verify({

        });
        /*自定义form验证*/

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/platform/sub_admin/brand/api/account')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/platform/sub_admin/brand/api/account')}}/{{$data->id}}";
        submitMethod = "PUT";
        @endif

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