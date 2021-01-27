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
                <label class="layui-form-label"><span class="required">*</span> 礼品图集</label>
                <div class="layui-input-block" style="" >
                    <div style="margin-bottom:10px;">
                        <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-check="photo" data-tpl="photo-tpl"> + 添加礼品图</button>
                    </div>
                    <div class="info-list">
                        @if(isset($data) && $data->photo != NULL)
                            @foreach (unserialize($data->photo) as $item)
                                @include("v1.admin_platform.integral.components.photo-tpl",['data'=>$item])
                            @endforeach
                        @endif
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 品牌</label>
                <div class="layui-input-inline">
                    {{--<input type="text" autocomplete="off" id="brand-select"  value="{{$data->brand_name or ''}}" readonly placeholder="点击搜索并选择" class="layui-input">
                    <input type="hidden" name="brand_id" value="{{$data->brand_id or ''}}" id="h-belong-brand">
--}}
                    <div class="layui-input-inline">
                        <select name="integral_brand_id" >
                            <option value="0">无</option>
                            @foreach($brands as $item)
                                <option value="{{$item->id}}" @if(isset($data) && $data->integral_brand_id == $item->id) selected @endif>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 分类</label>
                <div class="layui-input-inline">
                    <div class="layui-input-inline">
                        <select name="category_id_2" >
                            <option value="0">无</option>
                            @foreach($categories as $item)
                                <option value="{{$item->id}}" @if($item->pid==0) disabled @endif @if(isset($data) && $data->category_id_2 == $item->id) selected @endif>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 商品名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" value="{{$data->name or ''}}" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"> 排序(大者靠前)</label>
                <div class="layui-input-inline">
                    <input type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="sort" value="{{$data->sort or 0}}" placeholder="请输入排序" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 市场价</label>
                <div class="layui-input-inline">
                    <input type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="market_price" value="{{$data->market_price or 0}}" placeholder="请输入市场价" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="required">*</span> 兑换所需积分</label>
                <div class="layui-input-inline">
                    <input type="number" oninput="this.value = this.value.replace(/[^0-9\.]/g, '');" name="integral" value="{{$data->integral or 0}}" placeholder="请输入积分" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">商品详情</label>
                <div class="layui-input-inline" style="width:700px">
                    <textarea class="layui-textarea layui-hide"  name="detail" id="LAY_demo_editor">{!! $data->detail or '' !!}</textarea>
                </div>
            </div>
            <div class="layui-form-item" id="param-form-item">
                <label class="layui-form-label">商品参数</label>
                <div class="layui-input-block" style="" >
                    <div style="margin-bottom:10px;">
                        <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-check="goods-param" data-tpl="goods-param-tpl"> + 添加商品参数</button>
                    </div>
                    <div class="info-list">
                        @if(isset($data) && $data->param_data != NULL)
                            @foreach ($data->param_data as $item)
                                @include("v1.admin_brand.integral.components.goods-param-tpl",['data'=>$item])
                            @endforeach
                        @endif
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">推荐图</label>
                <div class="layui-input-inline">
                    <div class="layui-upload-drag" id="photo-promote-image">
                        <i class="layui-icon"></i>
                        <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                        <input type="hidden" name="photo_promote"  value="{{$data->photo_promote or ''}}"/>
                        <div class="upload-img-preview" style="background-image:url('{{$data->photo_promote or ''}}')"></div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">商品视频</label>
                <div class="layui-input-inline">
                    <div class="layui-upload-drag" id="photo-video-upload">
                        <i class="layui-icon"></i>
                        <p>仅支持MP4格式，每张图片大小限制50M以内</p>
                        <input type="hidden" class="i-photo-video" name="photo_video"  value="{{$data->photo_video or ''}}"/>
                        <div class="upload-video-preview" @if(!isset($data) || !$data->photo_video)style="display:none;" @endif ><a target="_blank" href="{{$data->photo_video or ''}}">已上传（查看）</a></div>
                    </div>
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
    <script type="text/html" id="goods-param-tpl">
        @include("v1.admin_brand.integral.components.goods-param-tpl",['data'=>null])
    </script>
    <script type="text/html" id="photo-tpl">
        @include("v1.admin_brand.integral.components.photo-tpl",['data'=>null])
    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{asset('plugins/layui-extend/tableSelect.js')}}"></script>

    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form,upload = layui.upload;
        var layedit = layui.layedit

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

        /*表格选择控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend')}}/'
            });
            layui.use(['jquery','tableSelect'], function () {
                var tableSelect = layui.tableSelect;
                tableSelect.render({
                    elem: '#brand-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '品牌关键词搜索',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'admin/brand/info_statistics/album/api/get_brands') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'name', title: '公司名称' },
                            { field: 'brand_name', title: '品牌名称' },
                            { field: 'organization_account', title: '组织账号' },
                            { field: 'contact_name', title: '联系人' },
                            { field: 'contact_telephone', title: '联系电话' },
                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-belong-brand').val(0);
                            $('#brand-select').val('');

                        }
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['brand_name'];
                            $('#h-belong-brand').val(privilege_id);
                            $('#brand-select').val(display_name);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/



        var avatar_size = 2*1024;

        var uploadAvatar = upload.render({
            elem: '#b-upload-image'
            ,url: '{{url($url_prefix.'admin/brand/integral/goods/api/upload_image')}}'
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

        var uploadPhotoPromote = upload.render({
            elem: '#photo-promote-image'
            ,url: '{{url($url_prefix.'admin/brand/integral/goods/api/upload_image')}}'
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
                $('#photo-promote-image .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#photo-promote-image input').val(res.data.access_path);
            }
        });


        //上传视频
        var uploadPhotoVideo = upload.render({
            elem: '#photo-video-upload'
            ,url: '{{url($url_prefix.'admin/brand/integral/goods/api/upload_video')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size: 50000//KB
            ,accept:'video'
            ,acceptMime: 'video/mp4'
            ,before: function(obj){layer.load(1);}
            ,done: function(res){
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){
                    layer.msg(res.msg);
                    console.log(res);
                }
                //上传成功
                $('#photo-video-upload').find('.upload-video-preview a').attr('href',res.data.access_path);
                $('#photo-video-upload').find('.upload-video-preview').show();
                $('#photo-video-upload').find('.i-photo-video').val(res.data.access_path);
            }
        });

        //创建一个编辑器
        layedit.set({
            uploadImage: {
                url: '{{url($url_prefix.'admin/api/upload_editor_img')}}' //接口url
                ,type: '' //默认post
            }
        });

        var editIndex = layedit.build('LAY_demo_editor');

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/brand/integral/goods/api')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/brand/integral/goods/api')}}/{{$data->id}}";
            submitMethod = "PUT";
        @endif

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;

            //详情
            form_field.detail = layedit.getContent(editIndex);

            /*-------产品图-------*/
            var photo = [];
            $(".n-photo").each(function(){
                photo.push($(this).val());
            })
            form_field.photo = photo;
            //必填判断
            if(photo.length ==0) {
                layer.msg('请至少上传一个礼品图');
                $('#submitBtn').attr('disabled',false);
                layer.closeAll('loading');
                return false;
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

    <script>
        //打开页面时执行一次上传块初始化
        init_upload_block();

        //添加自定义信息块
        function add_custom_info_block(obj){
            var check_flag =$(obj).attr('data-check')
            var check_result = check_add_custom_info_block(check_flag);
            if(!check_result){
                return false;
            }
            //最后一定要进行form的render，不然控件用不了
            var form_item = $(obj).parents('.layui-form-item')[0];
            var info_list = $(form_item).find('.info-list')[0];
            var tpl_name = $(obj).attr('data-tpl');
            var block_tpl = $('#'+tpl_name).html();
            $(info_list).append(block_tpl);
            form.render();
            init_upload_block();
        }

        function check_add_custom_info_block(check){
            if(check){
                switch(check){
                    case 'photo-practicality':
                        break;
                    default:break;
                }
            }
            return true;
        }

        //移除自定义信息块
        function remove_custom_info_block(obj){
            var info_block = $(obj).parents('.info-block')[0];
            info_block.remove();
        }

        //自定义信息块初始化上传图片/视频
        function init_upload_block(){
            layui.each($(".info-block .image-upload-drag"),function(index, elem){
                var is_init = $(elem).attr('data-is-init');
                var name_class = $(elem).attr('data-name-class');
                var upload_url = $(elem).attr('data-upload-url');
                var upload_size = 2000; //KB
                if(is_init==0){
                    upload.render({
                        elem: elem
                        ,url: upload_url
                        ,data: {'_token':'{{csrf_token()}}'}
                        ,size:upload_size  //KB
                        ,acceptMime: 'image/jpeg,image/jpg,image/png'
                        ,before: function(obj){
                            layer.load(1);
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
                            $(elem).find('.'+name_class).val(res.data.access_path);
                        }
                    });
                    $(elem).attr('data-is-init','1');
                }


            });

            layui.each($(".info-block .video-upload-drag"),function(index, elem){
                var is_init = $(elem).attr('data-is-init');
                var name_class = $(elem).attr('data-name-class');
                var upload_url = $(elem).attr('data-upload-url');
                var upload_size = 50000;//KB
                if(is_init==0){
                    upload.render({
                        elem: elem
                        ,url: upload_url
                        ,data: {'_token':'{{csrf_token()}}'}
                        ,size:upload_size  //KB
                        ,accept:'video'
                        ,acceptMime: 'video/mp4'
                        ,before: function(obj){
                            layer.load(1);
                        }
                        ,done: function(res){
                            layer.closeAll('loading');
                            //如果上传失败
                            if(!res.status){
                                // console.log(res);
                                return layer.msg(res.msg);
                            }
                            //上传成功
                            $(elem).find('.upload-video-preview').show();
                            $(elem).find('.'+name_class).val(res.data.access_path);
                        }
                    });
                    $(elem).attr('data-is-init','1');
                }


            });

        }

    </script>
@endsection