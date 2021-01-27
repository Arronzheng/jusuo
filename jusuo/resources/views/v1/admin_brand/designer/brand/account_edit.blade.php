@extends('v1.admin.components.layout.form_layout',[])

@section('content')
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
        <form method="post" class="layui-form" action="#" >
            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-inline">
                    <input type="text" name="login_mobile" lay-verify="required|phone" autocomplete="off" placeholder="请输入设计师的手机号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">设计师类型</label>
                <div class="layui-input-inline">
                    <select name="self_designer_type" lay-verify="required">
                        <option value=''>请选择设计师类型</option>
                        @foreach(\App\Models\DesignerDetail::designerTypeGroup() as $key=>$item)
                            <option value="{{$key}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="margin-top: 15px">
                <label class="layui-form-label">&nbsp;</label>
                <div class="layui-input-inline">
                    {{csrf_field()}}

                    <button class="layui-btn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>

        </form>

    </div>

@endsection

@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form

        layui.element.init();

        //最后一定要进行form的render，不然控件用不了
        form.render();

        //用form监听submit，可以用到validate的功能
        let submitAction = "{{url('/admin/brand/brand_designer/api/account')}}";

        form.on('submit(submitFormBtn)', function(form_info){
            var field = form_info.field;
            ajax_post(submitAction,field,function(result){
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
            });

            return false;
        });


    </script>
@endsection