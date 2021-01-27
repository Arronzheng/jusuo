@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <style>
        .layui-form-label{
            width:100px;
        }
        .required{
            color:red;
        }
    </style>
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 0 0;">
        <form>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 轮播图位置</label>
                <div class="layui-input-inline">
                    <select name="position">
                        @foreach(\App\Models\Banner::positionGroup() as $key=>$item)
                            <option value="{{$key}}" @if(isset($data) && $key == $data->position) selected @endif>{{$item}}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 轮播图片</label>
                <div class="layui-input-inline">
                    <div class="layui-upload-drag" id="b-upload-image">
                        <i class="layui-icon"></i>
                        <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                        <input type="hidden" name="photo" lay-verify="avatar_photo" value="{{$data->photo or ''}}"/>
                        <div class="upload-img-preview" style="background-image:url('{{$data->photo or ''}}')"></div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">跳转地址</label>
                <div class="layui-input-inline">
                    <input type="text" name="url" value="{{$data->url or ''}}" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">排序(大者靠前)</label>
                <div class="layui-input-inline">
                    <input type="text" name="sort" value="{{$data->sort or 0}}" placeholder="请输入简称" autocomplete="off" class="layui-input">
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
        var form = layui.form,upload = layui.upload;
        layui.element.init();


        /*自定义form验证*/
        form.verify({
            avatar_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传图片';
                }
            }

        });
        /*自定义form验证*/

        var avatar_size = 2*1024;
        //品牌LOGO图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-image'
            ,url: '{{url($url_prefix.'admin/brand/banner/api/upload_image')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size:avatar_size  //KB
            ,acceptMime: 'image/jpeg,image/jpg,image/png'
            ,before: function(obj){layer.load(1);}
            ,done: function(res){
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){
                    layer.msg(res.msg);
                    console.log(res);
                }
                //上传成功
                $('#b-upload-image .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-image input').val(res.data.access_path);
            }
        });



        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/brand/banner/api')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/brand/banner/api')}}/{{$data->id}}";
            submitMethod = "PUT";
        @endif

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;

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