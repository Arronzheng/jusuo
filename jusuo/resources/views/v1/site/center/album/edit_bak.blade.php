@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/album/create.css')}}">
    <style>
        .pass-tips{
            margin-bottom:20px;color:#3e82f7;margin-left:20px;
        }
        .layui-layer-page .layui-layer-content {
            position: relative;
            overflow: inherit!important;
        }
    </style>

    <div id="verify-form">
        <form class="layui-form" action="" lay-filter="component-form-group">

            <div class="layui-form-item">
                <label class="layui-form-label">方案标题</label>
                <div class="layui-input-inline">
                    <input type="text" name="title" value="{{$album->title or ''}}"  @if($config['title_required'])lay-verify="required"@endif maxlength="{{$config['title_char_limit']}}" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">封面图</label>
                <div class="layui-input-inline">
                    <div class="layui-upload-drag" id="b-upload-cover">
                        <i class="layui-icon"></i>
                        <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                        <input type="hidden" name="photo_cover" lay-verify="photo_cover" value="{{$album->photo_cover or ''}}"/>
                        <div class="upload-img-preview" style="background-image:url('{{$album->photo_cover or ''}}')"></div>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">所在城市</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <select id="company_province_id" name="address_province_id" lay-verify="required" lay-filter="areaBelongProvinceId">
                            <option value="">请选择省</option>
                            @foreach($provinces as $item)
                                <option value="{{$item->id}}" @if(isset($album) && $album->address_province_id && $item->id==$album->address_province_id) selected @endif>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select id="company_city_id" name="address_city_id" lay-verify="required" lay-filter="areaBelongCityId">
                            <option value="">请选择城市</option>
                            @if(isset($cities))
                                @foreach($cities as $item)
                                    <option value="{{$item->id}}" @if(isset($album) && $album->address_city_id && $item->id==$album->address_city_id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="address_area_id" lay-verify="required" id="company_district_id" lay-filter="companyDistrictId">
                            <option value="">请选择区/县</option>
                            @if(isset($districts))
                                @foreach($districts as $item)
                                    <option value="{{$item->id}}" @if(isset($album) && $album->address_area_id && $item->id==$album->address_area_id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">街道</label>
                <div class="layui-input-inline">
                    <input type="text" name="address_street" value="{{$album->address_street or ''}}"  @if($config['street_required'])lay-verify="required"@endif maxlength="{{$config['street_char_limit']}}" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">小区</label>
                <div class="layui-input-inline">
                    <input type="text" name="address_residential_quarter" value="{{$album->address_residential_quarter or ''}}"  @if($config['residential_quarter_required'])lay-verify="required"@endif maxlength="{{$config['residential_quarter_char_limit']}}" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">所在楼栋</label>
                <div class="layui-input-inline">
                    <input type="text" name="address_building" value="{{$album->address_building or ''}}"  @if($config['building_required'])lay-verify="required"@endif maxlength="{{$config['building_char_limit']}}" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">所在户型号</label>
                <div class="layui-input-inline">
                    <input type="text" name="address_layout_number" value="{{$album->address_layout_number or ''}}"  @if($config['layout_number_required'])lay-verify="required"@endif maxlength="{{$config['layout_number_char_limit']}}" autocomplete="off" placeholder="" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">户型</label>
                <div class="layui-input-block">
                    @foreach($vdata['house_types'] as $item)
                        <input type="radio" name="house_type_id" lay-verify="required" value="{{$item->id}}"  title="{{$item->name}}" @if(isset($album) && in_array($item->id,$album->house_type_ids)) checked @endif>
                    @endforeach
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">建筑面积</label>
                <div class="layui-input-inline">
                    <?php
                    $count_area_verify = ['total_area_range'];
                    if($config['total_area_required']){
                        array_push($count_area_verify,'required');
                    }
                    $count_area_verify = implode('|',$count_area_verify);
                    ?>
                    <input type="text" name="count_area" placeholder="请输入数值" value="{{$album->count_area or ''}}"  lay-verify="{{$count_area_verify}}" maxlength="20" autocomplete="off" placeholder="" class="layui-input">

                </div>
                <div class="layui-form-mid layui-word-aux" style="text-align:center;width:50px">
                    平方米
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">风格</label>
                <div class="layui-input-block">
                    @foreach($vdata['styles'] as $item)
                        <input type="checkbox" class="n-style-id" lay-verify="required"  lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($album) && in_array($item->id,$album->style_ids)) checked @endif>
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">设计说明</label>
                <div class="layui-input-inline">
                    <textarea name="description_design" @if($config['description_design_required'])lay-verify="required"@endif maxlength="{{$config['description_design_char_limit']}}" style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$album->description_design or ''}}</textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">户型图</label>

                <div class="layui-input-block" style="width:900px" >
                    <div style="margin-bottom:10px;">
                        <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="photo-layout-tpl" > + 添加户型图</button>
                    </div>
                    <div class="info-list">
                        @if(isset($album) && $album->photo_layout != NULL)
                            @foreach (unserialize($album->photo_layout) as $item)
                                @include('v1.site.center.album.components.photo-layout-tpl',['data'=>$item])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">户型说明</label>
                <div class="layui-input-inline">
                    <textarea name="description_layout" @if($config['description_layout_required'])lay-verify="required"@endif maxlength="{{$config['description_layout_char_limit']}}" style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$album->description_layout or ''}}</textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">空间图</label>
                <div class="layui-input-block" style="" >
                    <div style="margin-bottom:10px;">
                        <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="album-section-tpl" data-add-type="album_section"> + 添加空间图</button>
                    </div>
                    <div class="info-list">
                        @if(isset($album))
                            @foreach($album->sections as $data)
                                @include('v1.site.center.album.components.album-section-tpl',['section'=>$data,'config'=>$config,'styles'=>$vdata['styles'],'space_types'=>$vdata['space_types']])
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">产品清单</label>
                <div class="layui-input-block">
                    <div style="width:190px;">
                        <button class="layui-btn layui-btn-sm" id="product-select" type="button" >点击搜索并选择</button>
                    </div>

                    <div>
                        <table class="layui-table" lay-size="sm" style="width:600px;">
                            <thead>
                            <tr>
                                <th>名称</th>
                                <th>类型</th>
                                <th>产品编码</th>
                                <th>规格</th>
                                <th>缩略图</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody id="product-list-body">
                            @if(isset($album))
                                @foreach($album->products as $data)
                                    @include('v1.site.center.album.components.product-tr-tpl',['data'=>$data])
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr style="margin:50px 0 ;"/>

            <div class="layui-form-item" style="margin-bottom:200px;">
                <label class="layui-form-label">&nbsp;</label>
                <div class="layui-input-inline">
                    <button type="button" id="submit-btn" class="layui-btn" lay-submit lay-filter="submitFormBtn">发布</button>
                    <button type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="submitFormTempBtn">暂存</button>
                </div>
            </div>

            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$album->id or ''}}"/>

        </form>
        <div style="clear:both;"></div>

    </div>




@endsection

@section('body')


    <script type="text/html" id="product-tr-tpl">
        @include('v1.site.center.album.components.product-tr-tpl',['data'=>''])
    </script>

    <script type="text/html" id="album-section-tpl">
        @include('v1.site.center.album.components.album-section-tpl',[
        'data'=>'','config'=>$config,'styles'=>$vdata['styles'],'space_types'=>$vdata['space_types']
        ])
    </script>

    <script type="text/html" id="photo-layout-tpl">
        @include('v1.site.center.album.components.photo-layout-tpl',['data'=>''])
    </script>

    <script type="text/html" id="section-design-photo-tpl">
        @include('v1.site.center.album.components.section-design-photo-tpl',['data'=>''])
    </script>

    <script type="text/html" id="section-product-photo-tpl">
        @include('v1.site.center.album.components.section-product-photo-tpl',['data'=>''])
    </script>

    <script type="text/html" id="section-build-photo-tpl">
        @include('v1.site.center.album.components.section-build-photo-tpl',['data'=>''])
    </script>
@endsection

@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        var laydate = layui.laydate;

        laydate.render({
            elem: '#self_birth_time',
            value:''
        });

        layui.element.init();

        var avatar_size = 2*1024;
        //LOGO图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-cover'
            ,url: '{{url($url_prefix.'center/app_info/upload_avatar')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size:avatar_size  //KB
            ,acceptMime: 'image/jpeg,image/jpg,image/png'
            ,before: function(obj){layer.load(1);}
            ,done: function(res){
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){
                    layer.msg(res.msg);
                }
                //上传成功
                $('#b-upload-cover .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-cover input').val(res.data.access_path);
            }
        });


        /*表格选择控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend')}}/'
            });
            layui.use(['jquery','tableSelect'], function () {
                var tableSelect = layui.tableSelect;
                tableSelect.render({
                    elem: '#product-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '产品关键词搜索',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'center/album/api/ajax_get_product') !!}',
                        cols: [[
                            { type: 'checkbox' },
                            { field: 'name', title: '产品名称' },
                            { field: 'code', title: '产品编码' },

                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        /*if(select_data.length==0){
                            //取消选择
                            $('#h-parent-privilege').val(0);
                            $('#product-select').val('');

                        }*/
                        //已选产品
                        var selected_product_ids = [];
                        $('.n-product-id').each(function(){
                            var product_id = $(this).val();
                            if(product_id){
                                selected_product_ids.push($(this).val())
                            }
                        });
                        var product_tr_tpl = $('#product-tr-tpl').html();
                        for(var i=0;i<select_data.length;i++){
                            var product_id = select_data[i]['id'];
                            if(selected_product_ids.length>0 && $.inArray(product_id.toString(),selected_product_ids)>=0){
                                continue;
                            }
                            var $product_tr_tpl = $(product_tr_tpl)
                            var product_name = select_data[i]['name'];
                            var product_code = select_data[i]['code'];
                            var product_type = select_data[i]['type_text'];
                            var product_spec = select_data[i]['spec_text'];
                            var product_img = select_data[i]['photo_cover'];
                            $product_tr_tpl.find('.n-product-id').val(product_id)
                            $product_tr_tpl.find('.product-name').html(product_name)
                            $product_tr_tpl.find('.product-type').html(product_type)
                            $product_tr_tpl.find('.product-code').html(product_code)
                            $product_tr_tpl.find('.product-spec').html(product_spec)
                            $product_tr_tpl.find('.product-img a').attr('href',product_img)
                            $product_tr_tpl.find('.product-img img').attr('src',product_img)
                            $('#product-list-body').append($product_tr_tpl);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/

        //删除产品清单
        function remove_product_tr(obj){
            $(obj).parents('tr').remove();
        }

        //最后一定要进行form的render，不然控件用不了
        form.render();

        //表单验证
        form.verify({
            photo_required: function(value,item){ //value：表单的值、item：表单的DOM对象
                var obj = $(item)
                var photo_type = obj.attr('data-photo-type');
                if(value == ''){
                    return photo_type+'未上传完成';
                }
            },
            photo_cover: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传封面图';
                }
            },
            total_area_range:function(value){
                var lower_limit = '{{$config['total_area_range']['lower_limit']}}'
                var upper_limit = '{{$config['total_area_range']['upper_limit']}}'
                lower_limit = parseFloat(lower_limit);
                upper_limit = parseFloat(upper_limit);
                value = parseFloat(value)
                if(lower_limit>=0 && upper_limit>=0){
                    if(value<lower_limit){
                        return '总面积不能小于'+lower_limit;
                    }else if(value > upper_limit){
                        return '总面积不能大于'+upper_limit;
                    }
                }
            },
            section_area_range:function(value){
                var lower_limit = '{{$config['section_area_range']['lower_limit']}}'
                var upper_limit = '{{$config['section_area_range']['upper_limit']}}'
                lower_limit = parseFloat(lower_limit);
                upper_limit = parseFloat(upper_limit);
                value = parseFloat(value)
                if(lower_limit>=0 && upper_limit>=0){
                    if(value<lower_limit){
                        return '空间面积不能小于'+lower_limit;
                    }else if(value > upper_limit) {
                        return '空间面积不能大于' + upper_limit;
                    }
                }
            }
        });

        //暂存
        function save_temp(){

        }

        //用form监听submit，可以用到validate的功能
        let submitAction = "{{url('/center/album/api')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/center/album/api')}}/{{$data->id}}";
        submitMethod = "PUT";
        @endif
        form.on('submit(submitFormBtn)', function(form_info){
            layer.load(1);
            form_info.field.submit_type = 'save';
            return handle_submit_form(form_info);
        });

        form.on('submit(submitFormTempBtn)', function(form_info){
            layer.load(1);
            form_info.field.submit_type = 'temp';
            return handle_submit_form(form_info);
        });

        //处理表单提交
        function handle_submit_form(form_info){
            var form_field = form_info.field;
            //处理户型
            if(!form_field.house_type_id){
                layer.alert('请选择户型！');
                layer.closeAll('loading');
                return false;
            }
            //处理风格
            var styles = [];
            $('.n-style-id[type=checkbox]:checked').each(function(){
                styles.push($(this).val());
            });
            form_field.style_ids = styles;
            //处理户型图字段
            var photo_layout_min_limit = "{{$config['photo_layout_min_limit']}}";
            photo_layout_min_limit = parseFloat(photo_layout_min_limit);
            var photo_layouts = [];
            $('.n-photo-layout').each(function(){
                photo_layouts.push($(this).val());
            });
            if(photo_layout_min_limit>0 && photo_layouts.length < photo_layout_min_limit){
                layer.alert('请至少添加'+photo_layout_min_limit+'个户型图');
                layer.closeAll('loading');
                return false;
            }
            form_field.photo_layouts = photo_layouts;
            //处理章节字段
            var section_min_limit = "{{$config['space_min_limit']}}";
            section_min_limit = parseFloat(section_min_limit);
            var section_column = handle_section_column();
            if(section_column['status']==0){
                layer.alert(section_column['msg']);
                layer.closeAll('loading');
                return false;
            }
            if(section_min_limit>0 && section_column.section_style_ids.length < section_min_limit){
                layer.alert('请至少添加'+section_min_limit+'个空间图');
                layer.closeAll('loading');
                return false;
            }
            form_field.sections = JSON.stringify(section_column);
            //处理产品清单字段
            var product_min_limit = "{{$config['product_min_limit']}}";
            product_min_limit = parseFloat(product_min_limit);
            var product_column = handle_product_column();
            if(product_min_limit>0 && product_column.length<product_min_limit){
                layer.alert('请至少添加'+product_min_limit+'个产品');
                layer.closeAll('loading');
                return false;
            }
            form_field.product_ids = product_column;

            //提交方式
            if(submitMethod){
                form_field._method = submitMethod;
            }

            ajax_post(submitAction,
                form_field,
                function(result){
                    if(result.status){
                        layer.alert(result.msg,{closeBtn :0},function(){
                            window.location.href="{{url('/center/album')}}";
                        });
                    }else{
                        layer.closeAll('loading');
                        layer.msg(result.msg);
                    }
                });
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        }

        //处理章节字段
        function handle_section_column(){
            var result = [];
            result['status'] = 1;
            result['msg'] = 'success';
            result['data'] = [];
            var columns ={};
            var section_space_type = [];
            var section_title = [];
            var section_area = [];
            //空间类别
            $('.n-section-space-type').each(function(){
                section_space_type.push($(this).val());
            });
            columns.section_space_type = section_space_type;
            //空间名称
            $('.n-section-title').each(function(){
                section_title.push($(this).val());
            });
            columns.section_title = section_title;
            //空间面积
            $('.n-section-area').each(function(){
                section_area.push($(this).val());
            });
            columns.section_area = section_area;
            //各章节block
            var section_style_ids = [];
            var section_design_description = [];
            var section_design_photos = [];
            var section_product_description = [];
            var section_product_photos = [];
            var section_build_description = [];
            var section_build_photos = [];
            $('.section-info-row').each(function(index){
                section_style_ids[index] = [];
                section_design_photos[index] = [];
                section_product_photos[index] = [];
                section_build_photos[index] = [];
                //空间风格
                $(this).find("input.n-section-style-id[type=checkbox]:checked").each(function(){
                    section_style_ids[index].push($(this).val());
                })
                //空间设计说明
                $(this).find('.n-section-design-description').each(function(){
                    section_design_description.push($(this).val());
                });
                //空间设计配图
                $(this).find('.n-section-design-photo').each(function(){
                    section_design_photos[index].push($(this).val());
                });
                //产品应用说明
                $(this).find('.n-section-product-description').each(function(){
                    section_product_description.push($(this).val());
                });
                //产品应用配图
                $(this).find('.n-section-product-photo').each(function(){
                    section_product_photos[index].push($(this).val());
                });
                //空间施工说明
                $(this).find('.n-section-build-description').each(function(){
                    section_build_description.push($(this).val());
                });
                //空间施工配图
                $(this).find('.n-section-build-photo').each(function(){
                    section_build_photos[index].push($(this).val());
                });
                //check章节空间配图
                var section_design_photo_min_limit = '{{$config['section_design_photo_min_limit']}}';
                section_design_photo_min_limit = parseFloat(section_design_photo_min_limit);
                if(section_design_photo_min_limit>0 && section_design_photos[index].length<section_design_photo_min_limit){
                    result['status'] = 0;
                    result['msg'] = '每个空间请至少添加'+section_design_photo_min_limit+'个空间设计配图';
                    return false;
                }
                //check章节产品应用配图
                var section_product_photo_min_limit = '{{$config['section_product_photo_min_limit']}}';
                section_product_photo_min_limit = parseFloat(section_product_photo_min_limit);
                if(section_product_photo_min_limit>0 && section_product_photos[index].length<section_product_photo_min_limit){
                    result['status'] = 0;
                    result['msg'] = '每个空间请至少添加'+section_product_photo_min_limit+'个产品应用配图';
                    return false;
                }
                //check章节产品应用配图
                var section_build_photo_min_limit = '{{$config['section_build_photo_min_limit']}}';
                section_build_photo_min_limit = parseFloat(section_build_photo_min_limit);
                if(section_build_photo_min_limit>0 && section_build_photos[index].length<section_build_photo_min_limit){
                    result['status'] = 0;
                    result['msg'] = '每个空间请至少添加'+section_build_photo_min_limit+'个施工配图';
                    return false;
                }
            });

            if(result['status']==0){
                return result;
            }

            columns.section_style_ids = section_style_ids;
            columns.section_design_description = section_design_description;
            columns.section_design_photos = section_design_photos;
            columns.section_product_description = section_product_description;
            columns.section_product_photos = section_product_photos;
            columns.section_build_description = section_build_description;
            columns.section_build_photos = section_build_photos;

            return columns;
        }

        //处理产品清单字段
        function handle_product_column(){
            var product_ids =[];
            //空间类别
            $('.n-product-id').each(function(){
                product_ids.push($(this).val());
            });
            $.unique(product_ids);
            return product_ids;
        }

        $(document).ready(function(){
            //监听省份变化
            form.on('select(areaBelongProvinceId)', function(data){
                var province_id = data.value;
                get_area(province_id,'company_city_id','城市');
            });

            //监听城市变化
            form.on('select(areaBelongCityId)', function(data){
                var city_id = data.value;
                get_area(city_id,'company_district_id','区/县');
            });
        });

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url('/common/get_area_children?pi=')}}'+area_id,function(res){
                if(res.status && res.data.length>0){
                    $.each(res.data,function(k,v){
                        options+='<option value="'+ v.id+'">'+ v.name+'</option>';
                    });
                    $('#'+next_elem).html(options);
                    form.render('select');
                }
            });
        }
    </script>

    <script>
        //添加自定义信息块
        function add_custom_info_block(obj){
            var form_item = $(obj).parents('.layui-form-item')[0];
            var info_list = $(form_item).find('.info-list')[0];
            var tpl_name = $(obj).attr('data-tpl');
            var block_tpl = $('#'+tpl_name).html();
            $(info_list).append(block_tpl);
            //最后一定要进行form的render，不然控件用不了
            form.render();
            init_upload_block();
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
                var upload_size = 2000;
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


