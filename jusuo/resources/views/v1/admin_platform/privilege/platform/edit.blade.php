@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
        <form>
            <div class="layui-form-item">
                <label class="layui-form-label">展示名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="display_name" value="{{$data->display_name or ''}}" lay-verify="required" placeholder="请输入名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">识别字符串</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" value="{{$data->name or ''}}" lay-verify="required" placeholder="请输入识别字符串" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">上级权限</label>
                <div class="layui-input-inline">
                    <input type="text" autocomplete="off" id="parent-privilege-select"  value="{{$data->parent_display_name or ''}}" readonly placeholder="默认无，点击搜索并选择" class="layui-input">
                    <input type="hidden" name="parent_id" value="{{$data->parent_id or 0}}" id="h-parent-privilege">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">路径url</label>
                <div class="layui-input-inline">
                    <input type="text" name="url" lay-verify="" value="{{$data->url or ''}}" autocomplete="off" placeholder="请输入路径url" class="layui-input">

                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">是否启用</label>
                <div class="layui-input-inline">
                    <input type="radio" name="shown" value="0" title="否" @if(isset($data) && !$data->shown) checked @endif >
                    <input type="radio" name="shown" value="1" title="是" @if(isset($data) && $data->shown) checked @else @if(!isset($data)) checked @endif @endif>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">是否显示在菜单</label>
                <div class="layui-input-inline">
                    <input type="radio" name="is_menu" value="0" title="否" @if(isset($data) && !$data->is_menu) checked @endif  >
                    <input type="radio" name="is_menu" value="1" title="是" @if(isset($data) && $data->is_menu) checked @else @if(!isset($data)) checked @endif @endif >
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">是否超级管理员专用</label>
                <div class="layui-input-inline">
                    <input type="radio" name="is_super_admin" value="0" title="否" @if(isset($data) && !$data->is_super_admin) checked @else @if(!isset($data)) checked @endif @endif >
                    <input type="radio" name="is_super_admin" value="1" title="是" @if(isset($data) && $data->is_super_admin) checked @endif>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限描述</label>
                <div class="layui-input-inline">
                    <textarea name="description"  style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$data->description or ''}}</textarea>
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
                        url:'{!! url($url_prefix.'admin/platform/privilege_platform/api/ajax_parent_privilege') !!}',
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

        let submitAction = "{{url('/admin/platform/privilege_platform/api')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/platform/privilege_platform/api')}}/{{$data->id}}";
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