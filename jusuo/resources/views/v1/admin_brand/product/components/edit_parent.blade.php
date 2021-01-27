@extends(isset($data)?'v1.admin.components.layout.form_layout':'v1.admin_platform.layout',[])

@section('content')
    <link rel="stylesheet" href="{{asset('v1/css/admin/module/form.css')}}">

    <style>
        .required{color:#FB3A3A}
        .layui-form-label{
            width:100px;
        }
        .layui-input-block {
            margin-left: 130px;
        }
        .collocation-product-search{height:30px;position:relative;}
        .collocation-product-search input{height:30px;width:300px;}
        .collocation-product-search .collocation-product-result{
            height:300px;width:500px;position:absolute;top:30px;left:0;display:none;z-index:999;
        }
        .collocation-product-selected{overflow:hidden;margin-top:15px;}
        .collocation-product-selected .selected-block{float:left;margin-right:15px;height:30px;line-height:30px;padding:0 15px;font-size:14px;border:1px solid #dedede;}
        .upload-video-preview{
            position: absolute;
            background-color:#ffffff;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            font-size:18px;
            display:flex;justify-content: center;align-items: center;
        }

    </style>
    @if(!isset($data))
        <div class="layui-card layadmin-header">
            <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
                <a><cite>产品管理</cite></a><span lay-separator="">/</span>
                <a><cite>新建产品</cite></a>
                @can('product_manage.series_index')
                <div class="right">
                    <button onclick="location.href='{{url('/admin/brand/product/series')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" style="margin-right:20px;" >
                        <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>
                        产品系列
                    </button>
                </div>
                @endcan
            </div>
        </div>
    @endif
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 400px 0;">

        @yield('edit_form')

    </div>

    <script type="text/html" id="physical-chemical-property-tpl">
        @include("v1.admin_brand.product.components.physical-chemical-property-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="function-feature-tpl">
        @include("v1.admin_brand.product.components.function-feature-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="photo-product-tpl">
        @include("v1.admin_brand.product.components.photo-product-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="photo-practicality-tpl">
        @include("v1.admin_brand.product.components.photo-practicality-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="product-accessory-tpl">
        @include("v1.admin_brand.product.components.product-accessory-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="product-accessory-photo-tpl">
        @include("v1.admin_brand.product.components.product-accessory-photo-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="product-collocation-tpl">
        @include("v1.admin_brand.product.components.product-collocation-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="product-collocation-photo-tpl">
        @include("v1.admin_brand.product.components.product-collocation-photo-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="space-application-tpl">
        @include("v1.admin_brand.product.components.space-application-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script type="text/html" id="photo-video-tpl">
        @include("v1.admin_brand.product.components.photo-video-tpl",['data'=>null,'cf'=>$cf])
    </script>
    <script id="collocation-product-tr" type="text/html">
        @{{#  layui.each(d, function(index, item){ }}
        <tr>
            <td class="product-name">@{{item.name}}</td>
            <td class="product-code">@{{item.code}}</td>
            <td class="choose-block">
                <button type="button" data-id="@{{item.id}}" class="collocation-product-select-btn layui-btn layui-btn-sm layui-btn-primary">选择</button>
            </td>
        </tr>
        @{{#  }); }}
        @{{#  if(d.length === 0){ }}
        <tr>
            <td colspan="2" class="result-text">无数据</td>
        </tr>
        @{{#  } }}
    </script>
    <div id="tpl-temp" style="display:none;"></div>
@endsection

@section('script')
    <script type="text/javascript" src="{{asset('plugins/layui-extend/tableSelect.js')}}"></script>

    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form
            ,upload = layui.upload;
        layui.element.init();


        /*自定义form验证*/
        var regDecimal = /^[+]?\d+(\.\d{0,2})?$/;
        form.verify({
            photo_required: function(value,item){ //value：表单的值、item：表单的DOM对象
                var obj = $(item)
                var photo_type = obj.attr('data-photo-type');
                if(value == ''){
                    return photo_type+'未上传完成';
                }
            },
            price: function(value,item){ //value：表单的值、item：表单的DOM对象
                if (!regDecimal.test(value)) {
                    return '请输入正确的价格';
                }
            },
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
                    elem: '#parent_product-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '产品关键词搜索',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'admin/brand/product/api/get_parent_product') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'name', title: '产品名称' },
                            { field: 'code', title: '产品编码' },
                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-parent-product').val(0);
                            $('#parent_product-select').val('');

                        }
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['name'];
                            $('#h-parent-product').val(privilege_id);
                            $('#parent_product-select').val(display_name);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/

        form.render();

        //监听产品类别变化
        form.on('radio(typeRadio)', function(data){
            if(data.value==1){
                //是配件，则需要选择父产品
                $('#i-is-accessory').val(1);
                $('#parent-id-form-item').show();
                $('#accessory-form-item').hide();
            }else{
                $('#i-is-accessory').val(0);
                $('#parent-id-form-item').hide();
                $('#accessory-form-item').show();
            }
        });

        $(document).on('focus','.collocation-product-search input',function(){
            var keyword = $(this).val();
            if(keyword){
                $(this).siblings('.collocation-product-result').show();

            }
        });
        $(document).on('click',function(e){
            $('.collocation-product-result').hide();
        });
        $(document).on('click','.collocation-product-search',function(e){
            e.stopPropagation();
        });


        $(document).on('input','.collocation-product-search input',function(){
            var laytpl = layui.laytpl;
            var keyword = $(this).val();
            var search_input = $(this)

            layui.use('laytpl', function(){
                $.ajax({
                    url: '{{url($url_prefix.'admin/brand/product/api/search_product')}}',
                    dataType: 'json',
                    data: {keyword:keyword},
                    success: function(res){
                        if(res.status){
                            var getTpl = $('#collocation-product-tr').html()
                            var result_data = res.data;
                            laytpl(getTpl).render(result_data, function(html){
                                search_input.siblings('.collocation-product-result').show()
                                search_input.siblings('.collocation-product-result').find('table tbody').html(html)
                            });
                        }else{
                            $('#initBlock').html('暂无权限');
                        }

                    }
                });
            });

        });

        $(document).on('click','.collocation-product-select-btn',function(){
            var product_id = $(this).attr('data-id');
            //判断是否已经选择过
            var selected_block = $(this).parents('.collocation-product-search').siblings('.collocation-product-selected');
            var exist_block = selected_block.find("div[data-id='"+product_id+"']");
            if(exist_block.length>0){
                $(this).parents('.collocation-product-result').hide()
                return false;
            }
            var product_name = $(this).parents('tr').find('.product-name').text();
            var product_code = $(this).parents('tr').find('.product-code').text();
            var html = '<div class="selected-block" data-id="'+product_id+'">已选产品：'+product_name+'</div>';
            $(this).parents('.collocation-product-search').siblings('.collocation-product-selected').html(html);
            $(this).parents('.collocation-product-result').hide()

        });

    </script>

    {{--页面方法专用script--}}
    <script>

        var submitAction = "{{url('/admin/brand/product/api')}}";
        var submitMethod = "";
        var edit_type = '';


        @if(isset($data))
             edit_type = "{{$data->edit_type or ''}}";
            if(edit_type=='nopass'){
                submitAction = "{{url('/admin/brand/product/api/update_nopass')}}/{{$data->log_id}}";
                submitMethod = "PUT";
            }else{
                submitAction = "{{url('/admin/brand/product/api')}}/{{$data->id}}";
                submitMethod = "PUT";
            }
        @endif

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            var obj_id = $(this).attr('id');
            if(obj_id == 'submitBtn'){
                //立即提交
            }else if(obj_id == 'tempSaveBtn'){
                //暂存，修改提交的api
                if(!edit_type){
                    //没有编辑标识，则是首次提交暂存
                    submitAction = "{{url('/admin/brand/product/api/temp_save')}}"
                    submitMethod = "POST";
                }else{
                    if(edit_type == 'temp'){
                        submitAction = "@if(isset($data)){{url('/admin/brand/product/api/temp_update')}}/{{$data->log_id}}@endif";
                        submitMethod = "PUT";
                    }
                }
            }
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;

            /*-------产品图-------*/
            var photo_product = [];
            $(".n-photo-product").each(function(){
                photo_product.push($(this).val());
            })
            form_field.photo_product = photo_product;

            /*-------产品图-------*/

            /*-------实物图-------*/
            var photo_practicality = [];
            $(".n-photo-practicality").each(function(){
                photo_practicality.push($(this).val());
            })
            form_field.photo_practicality = photo_practicality;

            //产品配件/配件图
            var is_accessory =  $('#i-is-accessory').val();
            if(is_accessory==0){
                var product_accessory = $(".product-accessory-block");
                var product_accessory_photo = [];
                $(".product-accessory-block").each(function(index){
                    product_accessory_photo[index] = [];
                    $(this).find(".n-product-accessory-photo").each(function(){
                        product_accessory_photo[index].push($(this).val());
                    })
                })
                form_field.product_accessory_photo = product_accessory_photo;
            }

            //产品搭配产品/搭配图
            var product_collocation = $(".product-collocation-block");

            //搭配产品
            var collocation_product = [];
            $(".product-collocation-block .collocation-product-selected .selected-block").each(function(){
                collocation_product.push($(this).attr('data-id'));
            })
            form_field.collocation_product = collocation_product;
            //产品搭配图
            var product_collocation_photo = [];
            $(".product-collocation-block").each(function(index){
                product_collocation_photo[index] = [];
                $(this).find(".n-product-collocation-photo").each(function(){
                    product_collocation_photo[index].push($(this).val());
                })
            })
            form_field.product_collocation_photo = product_collocation_photo;

            //产品空间应用说明图片
            var space_application_photo = [];
            $(".n-space-application-photo").each(function(){
                space_application_photo.push($(this).val());
            })
                form_field.space_application_photo = space_application_photo;

            /*-------产品视频-------*/
            var photo_video = [];
            $(".n-photo-video").each(function(){
                photo_video.push($(this).val());
            })
                form_field.photo_video = photo_video;


            //提交方式
            if(submitMethod){
                form_field._method = submitMethod;
            }
            ajax_post(submitAction,
                form_field,
                function(result){
                    if(result.status){
                        console.log(window.name)
                        var index = parent.layer.getFrameIndex(window.name);
                        if(index){
                            layer.msg(result.msg, {
                                time: 1500
                            }, function(){
                                parent.reloadTable();
                                parent.layer.close(index); //再执行关闭
                            });
                        }else{
                            layer.alert(result.msg,{closeBtn :0},function(){
                                window.location.reload();
                            });
                        }


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
                        //实物图校验
                        var photo_practicality_length = $(".n-photo-practicality").length;
                        @if(isset($cf['photo_practicality.limit']))
                        if(photo_practicality_length>0){
                            var photo_practicality_lower_limit = '{{$cf['photo_practicality.limit']['lower_limit']}}';
                            var photo_practicality_upper_limit = '{{$cf['photo_practicality.limit']['upper_limit']}}';
                            if(photo_practicality_length < photo_practicality_lower_limit ||
                                photo_practicality_length >= photo_practicality_upper_limit
                            ){
                                if(photo_practicality_lower_limit==photo_practicality_upper_limit){
                                    layer.msg('实物图的数量只能为'+photo_practicality_lower_limit+"个");

                                }else{
                                    layer.msg('实物图的数量需在'+photo_practicality_lower_limit+"与"+photo_practicality_upper_limit+"之间");

                                }
                                return false;
                            }
                        }
                        @endif
                            break;

                    case 'photo-product':
                        //实物图校验
                        var photo_product_length = $(".n-photo-product").length;
                        @if(isset($cf['photo_product.limit']))
                        if(photo_product_length>0){
                            var photo_product_lower_limit = '{{$cf['photo_product.limit']['lower_limit']}}';
                            var photo_product_upper_limit = '{{$cf['photo_product.limit']['upper_limit']}}';
                            if(photo_product_length < photo_product_lower_limit ||
                                photo_product_length >= photo_product_upper_limit
                            ){
                                if(photo_product_lower_limit==photo_product_upper_limit){
                                    layer.msg('产品图的数量只能'+photo_product_lower_limit+"个");

                                }else{
                                    layer.msg('产品图的数量需在'+photo_product_lower_limit+"与"+photo_product_upper_limit+"之间");

                                }
                                return false;
                            }
                        }
                        @endif
                            break;
                    case 'photo-video':
                        //产品视频校验
                        var block_length = $(".n-photo-video").length;
                        @if(isset($cf['photo_video.limit']))
                        if(block_length>0){
                            var lower_limit = '{{$cf['photo_video.limit']['lower_limit']}}';
                            var upper_limit = '{{$cf['photo_video.limit']['upper_limit']}}';
                            if(block_length < lower_limit ||
                                    block_length >= upper_limit
                            ){
                                if(lower_limit==upper_limit){
                                    layer.msg('产品视频的数量只能'+lower_limit+'个');
                                }else{
                                    layer.msg('产品视频的数量需在'+lower_limit+"与"+upper_limit+"之间");
                                }

                                return false;
                            }
                        }
                        @endif
                        break;
                    case 'product-accessory':
                        //产品配件校验
                        var block_length = $(".product-accessory-block").length;
                        if(block_length>0){
                            var lower_limit = '{{$cf['accessory.limit']['lower_limit']}}';
                            var upper_limit = '{{$cf['accessory.limit']['upper_limit']}}';
                            if(block_length < lower_limit ||
                                    block_length >= upper_limit
                            ){
                                if(lower_limit==upper_limit){
                                    layer.msg('产品配件的数量只能'+lower_limit+'个');
                                }else{
                                    layer.msg('产品配件的数量需在'+lower_limit+"与"+upper_limit+"之间");
                                }
                                return false;
                            }
                        }
                                break;
                    case 'product-collocation':
                        //产品搭配校验
                        var block_length = $(".product-collocation-block").length;
                        if(block_length>0){
                            var lower_limit = '{{$cf['collocation.limit']['lower_limit']}}';
                            var upper_limit = '{{$cf['collocation.limit']['upper_limit']}}';
                            if(block_length < lower_limit ||
                                    block_length >= upper_limit
                            ){
                                if(lower_limit==upper_limit){
                                    layer.msg('产品搭配的数量只能'+lower_limit+'个');
                                }else{
                                    layer.msg('产品搭配的数量需在'+lower_limit+"与"+upper_limit+"之间");
                                }
                                return false;
                            }
                        }
                                break;
                    case 'space-application':
                        //空间应用说明校验
                        var block_length = $(".space-application-block").length;
                        if(block_length>0){
                            var lower_limit = '{{$cf['space.limit']['lower_limit']}}';
                            var upper_limit = '{{$cf['space.limit']['upper_limit']}}';
                            if(block_length < lower_limit ||
                                    block_length >= upper_limit
                            ){
                                if(lower_limit==upper_limit){
                                    layer.msg('空间应用的数量只能'+lower_limit+'个');
                                }else{
                                    layer.msg('空间应用的数量需在'+lower_limit+"与"+upper_limit+"之间");
                                }
                                return false;
                            }
                        }
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