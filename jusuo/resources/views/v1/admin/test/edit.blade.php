@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
        <form>
            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" value="{{$data->name or ''}}" lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">简介</label>
                <div class="layui-input-inline">
                    <textarea name="intro" lay-verify="required" style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$data->intro or ''}}</textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-inline">
                    <select name="type" lay-verify="required">
                        <option value="">请选择标签</option>
                        @foreach(\App\Models\TestData::$typeGroup as $key=>$item)
                        <option value="{{$key}}" @if(isset($data) && $data->type==$key) selected @endif>{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">复选框</label>
                <div class="layui-input-block">
                    @foreach(\App\Models\TestData::$hobbyGroup as $id=>$hobby)
                        <input type="checkbox" lay-skin="primary" class="cbHobby" value="{{$id}}" title="{{$hobby}}" @if(isset($data) && in_array($id,$data->hobby)) checked @endif>
                    @endforeach
                </div>
            </div>
            {{--开关控件暂用单选框代替--}}
            {{--<div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用" value="1" @if(isset($data) && $data->status==\App\Models\TestData::STATUS_ON) checked @endif>
                </div>
            </div>--}}
            <div class="layui-form-item">
                <label class="layui-form-label">是否超级管理员角色</label>
                <div class="layui-input-block">
                    <input type="radio" name="is_super_admin" value="1" title="是" @if(isset($data) && $data->status==\App\Models\TestData::STATUS_ON) checked @endif>
                    <input type="radio" name="is_super_admin" value="0" title="否" @if(isset($data) && $data->status==\App\Models\TestData::STATUS_OFF) checked @endif>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">上传图片</label>
                <div class="layui-input-block" >
                    <div class="layui-upload-drag" id="b-upload-img">
                        <i class="layui-icon"></i>
                        <p>图片<br/>大小控制在2M以内</p>
                        <input type="hidden" name="avatar" lay-verify="avatar_photo" value="{{$data->avatar or ''}}"/>
                        <div class="upload-img-preview" style="background-image:url('{{$data->avatar or ''}}')"></div>
                    </div>

                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">富文本编辑</label>
                <div class="layui-input-block" >
                        <textarea class="layui-textarea layui-hide" id="editor-instance">{!! $data->desc or '' !!}</textarea>
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
        
        /*文件上传*/
        let upload = layui.upload;
        var img_size = 2*1024;
        var uploadImg = upload.render({
            elem: '#b-upload-img'
            ,url: '{{url($url_prefix.'admin/test/api/upload_img')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size:img_size  //KB
            ,acceptMime: 'image/jpeg,image/jpg,image/png'
            ,before: function(obj){layer.load(1);}
            ,done: function(res){
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){
                    layer.msg(res.msg);
                }
                //上传成功
                var upload_btn = $($(this)[0].item[0]);//点击触发的当前元素
                var access_path = res.data.access_path;  //本地访问url
                upload_btn.find('.upload-img-preview').css('background-image','url('+access_path+')');
                upload_btn.find('input').val(access_path);
            }
        });
        /*文件上传*/

        /*富文本编辑器*/
        let layedit = layui.layedit;
        //创建一个编辑器
        layedit.set({
            uploadImage: {
                url: '{{url($url_prefix.'admin/api/upload_editor_img')}}' //接口url
                ,type: '' //默认post
            }
        });
        var editor1 = layedit.build('editor-instance');
        /*富文本编辑器*/

        /*自定义form验证*/
        form.verify({
            avatar_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传图片';
                }
            },
        });
        /*自定义form验证*/

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/test/api')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/test/api')}}/{{$data->id}}";
            submitMethod = "PUT";
        @endif

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;
            //处理富文本：将富文本内容同步到表单域汇总
            form_field.desc = layedit.getContent(editor1);
            //处理开关控件：需要将其转换成数值
            if(!form_field.status) {
                form_field.status = "0";
            }
            //处理复选框：原layui的checkbox获取值机制比较麻烦，所以使用jq方式获取
            var chk_value =[];
            $('.cbHobby:checked').each(function(){
                chk_value.push(this.value); //push 进数组
            });
            form_field.hobby = chk_value;

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