@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 200px 0;">
        <form>
            <div class="layui-form-item">
                <label class="layui-form-label">登录账号</label>
                <div class="layui-input-inline">
                    @if(isset($data))
                        <div class="layui-form-mid layui-word-aux">{{$data->login_account}}</div>
                    @else
                        <div class="layui-form-mid layui-word-aux">{{$account_name}}</div>
                        <input type="hidden" name="login_account" value="{{$account_name}}"/>
                    @endif
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">登录用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="login_username" value="{{$data->login_username or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                    <div class="help-block">登录账号和登录用户名均可登录系统</div>

                </div>
            </div>
            @if(!isset($data))
            <div class="layui-form-item">
                <label class="layui-form-label">手机号码</label>
                <div class="layui-input-inline">
                    <input type="text" name="login_mobile" value="{{$data->login_mobile or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">登录初始密码</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">默认为上方填写的手机号码</div>
                </div>
            </div>
            @endif
            <div class="layui-form-item">
                <label class="layui-form-label">管理员姓名</label>
                <div class="layui-input-inline">
                    <input type="text" name="realname" value="{{$data->realname or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">管理员部门</label>
                <div class="layui-input-inline">
                    <input type="text" name="self_department" lay-verify="" value="{{$data->self_department or ''}}" autocomplete="off" class="layui-input">

                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">管理员职位</label>
                <div class="layui-input-inline">
                    <input type="text" name="self_position" lay-verify="" value="{{$data->self_position or ''}}" autocomplete="off"  class="layui-input">

                </div>
            </div>
            @if(!isset($data) || (!$data->is_super_admin && auth()->guard('brand')->user()->id!=$data->id))
            <div class="layui-form-item">
                <label class="layui-form-label">设置角色</label>
                <div class="layui-input-inline">
                    <select name="role_id" lay-filter="aihao">
                        <option value="0">请选择角色</option>
                        @foreach($roles as $role)
                            <option value="{{$role['id']}}" @if(isset($data->roles[0]) && $data->roles[0]->id==$role['id']) selected @endif>{{$role['display_name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
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
    <script type="text/javascript" src="{{asset('plugins/layui-extend/tableSelect.js')}}"></script>

    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form;
        layui.element.init();

        /*日期控件*/
        var laydate = layui.laydate;
        laydate.render({
            elem: '#date_start',
            value:'{{request()->input('date_start') ? request()->input('date_start') :''}}'
        });
        laydate.render({
            elem: '#date_end',
            value:'{{request()->input('date_end') ? request()->input('date_end') :''}}'

        });
        /*日期控件*/


        /*自定义form验证*/
        form.verify({

        });
        /*自定义form验证*/

        /*表格选择控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend')}}/'
            });
            layui.use(['jquery','tableSelect'], function () {
                var tableSelect = layui.tableSelect;
                tableSelect.render({
                    elem: '#parent-privilege-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '权限关键词搜索',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'admin/admin/privilege/api/ajax_parent_privilege') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'display_name', title: '权限名称' },
                            { field: 'name', title: '标识符' },
                            { field: 'description', title: '详细描述' },
                            { field: 'organization_type', title: '所属组织' },

                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-parent-privilege').val(0);
                            $('#parent-privilege-select').val('');

                        }
                        console.log(select_data);
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['display_name'];
                            $('#h-parent-privilege').val(privilege_id);
                            $('#parent-privilege-select').val(display_name);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/brand/sub_admin/api/account')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/brand/sub_admin/api/account')}}/{{$data->id}}";
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