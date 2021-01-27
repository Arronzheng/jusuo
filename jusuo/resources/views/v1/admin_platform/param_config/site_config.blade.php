@extends('v1.admin_platform.layout',[
     'css' => ['/v1/css/admin/brand/brand_info.css','/v1/css/admin/brand/service_info.css'],
])

@section('content')
    <style>
        .layui-layout-admin .layui-form-label{
            width:120px;
        }
    </style>
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>平台设置</cite></a><span lay-separator="">/</span>
            <a><cite>主页设置</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 15px;">
                    <form class="layui-form" action="" lay-filter="component-form-group">
                        <div class="layui-form-item">
                            <label class="layui-form-label">主页logo</label>
                            <div class="layui-input-inline">
                                <div class="layui-upload-drag" id="b-upload-avatar">
                                    <i class="layui-icon"></i>
                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                    <input type="hidden" name="logo" lay-verify="avatar_photo" value="{{$content['logo'] or ''}}"/>
                                    <div class="upload-img-preview" style="background-image:url('{{$content['logo'] or ''}}')"></div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系电话</label>
                            <div class="layui-input-inline">
                                <input type="text" name="phone" value="{{$content['phone'] or ''}}"   autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系方式</label>
                            <div class="layui-input-inline" style="width:250px;">
                                <input type="text" style="margin-bottom:8px;" name="contact[0]" value="{{$content['contact'][0] or ''}}"   autocomplete="off" placeholder="如“邮箱:xxx”" class="layui-input">
                                <input type="text" style="margin-bottom:8px;" name="contact[1]" value="{{$content['contact'][1] or ''}}"   autocomplete="off" placeholder="如“微信:xxx”" class="layui-input">
                                <input type="text" style="margin-bottom:8px;" name="contact[2]" value="{{$content['contact'][2] or ''}}"   autocomplete="off" placeholder="如“QQ:xxx”" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">自定义链接1</label>
                            <div class="layui-input-inline" style="width:450px;">
                                <div class="info-block" style="margin-bottom:15px;overflow:initial">
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" >
                                            <input type="text" class="layui-input" name="link[0][0][text]" value="{{$content['link'][0][0]['text'] or ''}}" placeholder="标题" maxlength="8" >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[0][1][text]" value="{{$content['link'][0][1]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[0][1][link]" value="{{$content['link'][0][1]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[0][2][text]" value="{{$content['link'][0][2]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[0][2][link]" value="{{$content['link'][0][2]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[0][3][text]" value="{{$content['link'][0][3]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[0][3][link]" value="{{$content['link'][0][3]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[0][4][text]" value="{{$content['link'][0][4]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[0][4][link]" value="{{$content['link'][0][4]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[0][5][text]" value="{{$content['link'][0][5]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[0][5][link]" value="{{$content['link'][0][5]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">自定义链接2</label>
                            <div class="layui-input-inline" style="width:450px;">
                                <div class="info-block" style="margin-bottom:15px;overflow:initial">
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" >
                                            <input type="text" class="layui-input" name="link[1][0][text]" value="{{$content['link'][1][0]['text'] or ''}}" placeholder="标题" maxlength="8" >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[1][1][text]" value="{{$content['link'][1][1]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[1][1][link]" value="{{$content['link'][1][1]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[1][2][text]" value="{{$content['link'][1][2]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[1][2][link]" value="{{$content['link'][1][2]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[1][3][text]" value="{{$content['link'][1][3]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[1][3][link]" value="{{$content['link'][1][3]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[1][4][text]" value="{{$content['link'][1][4]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[1][4][link]" value="{{$content['link'][1][4]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="link[1][5][text]" value="{{$content['link'][1][5]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="link[1][5][link]" value="{{$content['link'][1][5]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">联系二维码</label>
                            <div class="layui-input-inline multiple-stuff-list" style="width:800px;">
                                <div class="multiple-stuff-block">

                                    <input type="text" name="qrcode[0][text]" value="{{$content['qrcode'][0]['text'] or ''}}" autocomplete="off" placeholder="二维码描述" class="layui-input">
                                    <input type="text" name="qrcode[0][image]" value="{{$content['qrcode'][0]['image'] or ''}}" hidden>
                                    <input type="text" name="uoload_type" value="{!! url('admin/platform/param_config/api/upload_image') !!}" hidden>
                                    <div class="layui-upload-drag" style="width:168px;height: 168px;">
                                        <i class="layui-icon"></i>
                                        <p>仅支持JPG/PNG格式，每张图片大小限制0.2M以内</p>
                                        <div class="upload-img-preview" style="background-image:url('{{$content['qrcode'][0]['image'] or ''}}')"></div>
                                    </div>
                                </div>
                                <div class="multiple-stuff-block">

                                    <input type="text" name="qrcode[1][text]" value="{{$content['qrcode'][1]['text'] or ''}}" autocomplete="off" placeholder="二维码描述" class="layui-input">
                                    <input type="text" name="qrcode[1][image]" value="{{$content['qrcode'][1]['image'] or ''}}" hidden>
                                    <input type="text" name="uoload_type" value="{!! url('admin/platform/param_config/api/upload_image') !!}" hidden>
                                    <div class="layui-upload-drag" style="width:168px;height: 168px;">
                                        <i class="layui-icon"></i>
                                        <p>仅支持JPG/PNG格式，每张图片大小限制0.2M以内</p>
                                        <div class="upload-img-preview" style="background-image:url('{{$content['qrcode'][1]['image'] or ''}}')"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">友情链接</label>
                            <div class="layui-input-inline" style="width:450px;">
                                <div class="info-block" style="margin-bottom:15px;overflow:initial">
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="relate[0][text]" value="{{$content['relate'][0]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="relate[0][link]" value="{{$content['relate'][0]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="relate[1][text]" value="{{$content['relate'][1]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="relate[1][link]" value="{{$content['relate'][1]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="relate[2][text]" value="{{$content['relate'][2]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="relate[2][link]" value="{{$content['relate'][2]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="relate[3][text]" value="{{$content['relate'][3]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="relate[3][link]" value="{{$content['relate'][3]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline" style="width:120px">
                                            <input type="text" class="layui-input" name="relate[4][text]" value="{{$content['relate'][4]['text'] or ''}}" placeholder="链接名称" maxlength="8" >
                                        </div>
                                        <div class="layui-input-inline" style="width:300px;">
                                            <input type="text" class="layui-input" name="relate[4][link]" value="{{$content['relate'][4]['link'] or ''}}" placeholder="链接跳转地址"  >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                </div>
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



                        {{csrf_field()}}
                        <input type="hidden" name="id" value="1"/>

                    </form>
                </div>
            </div>
        </div>


    </div>


@endsection

@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        layui.element.init();

        var laydate = layui.laydate;

        //初始化日期日期控件
        laydate.render({
            elem: '#self_establish_time',
            value:''
        });

        var avatar_size = 2*1024;
        //品牌LOGO图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-avatar'
            ,url: '{{url($url_prefix.'admin/platform/param_config/api/upload_image')}}'
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
                $('#b-upload-avatar .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-avatar input').val(res.data.access_path);
            }
        });


        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({
            avatar_photo: function(value){ //value：表单的值、item：表单的DOM对象
                /*if(value == ''){
                    return '请上传品牌LOGO图片';
                }*/
            },
            brand_glory_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传品牌荣誉图片';
                }
            },
            team_building_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传团队建设图片';
                }
            },
            brandPath: [/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{1,8}$/, '路径必须为字母+数字组合，最多8个字符']

        });


        //添加材料多选块
        function add_multiple_stuff_block(obj){
            var type = $(obj).attr('add_type');
            var num = $(obj).parents('.layui-input-block').find('.multiple-stuff-block').length;
            switch(type)
            {
                case 'honor':
                    if(num>=1){
                        layer.msg("抱歉，品牌荣誉只能添加1个！");
                        return false;
                    }
                    break;
                case 'team_building':
                    if(num>=1){
                        layer.msg("抱歉，团队建设只能添加1个！");
                        return false;
                    }
                    break;
            }
            var tpl_name = $(obj).parents('.layui-input-block').find('.multiple-stuff-list').attr('data-tpl');
            var block_tpl = $('#'+tpl_name).html();
            $(obj).parents('.layui-input-block').find('.multiple-stuff-list').append(block_tpl);

            init_upload_block(type);
        }

        function close_multiple_stuff_block(obj){
            $(obj).parents('.multiple-stuff-block').remove();
        }


        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;

            ajax_post('{{url($url_prefix.'admin/platform/param_config/api/submit_site_config')}}',
                form_field,
                function(result){
                    // console.log(result);
                    if(result.status){
                        layer.alert(result.msg,function(){
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

    <script>
        $(document).ready(function(){

            init_upload_block();

        });

        function init_upload_block(type){
            layui.each($(".multiple-stuff-list .layui-upload-drag"),function(index, elem){
                var is_init = $(elem).attr('data-is-init');
                var upload_size = 200;
                if(!type){
                    type = $(elem).attr('type');
                }
                if(!is_init){
                    upload.render({
                        elem: elem
                        ,url: $(elem).prev().val()
                        ,data: {'_token':'{{csrf_token()}}'}
                        ,size:upload_size  //KB
                        ,acceptMime: 'image/jpeg,image/jpg,image/png'
                        ,before: function(obj){
                            layer.load(1);
                            console.log(upload_size);
                        }
                        ,done: function(res){
                            layer.closeAll('loading');
                            //如果上传失败
                            if(!res.status){
                                // console.log(res);
                                return layer.msg(res.msg);
                            }
                            //上传成功
                            $(elem).find('.upload-img-preview').css('background-image','url('+res.data.access_path+')');
                            $(elem).prev().prev().val(res.data.access_path);
                        }
                    });
                    $(elem).attr('data-is-init','1');
                }


            });


        }

    </script>
@endsection
