@extends('v1.admin_brand.layout',[
     'css' => ['/v1/css/admin/seller/brand_info.css','/v1/css/admin/seller/service_info.css'],
])

@section('content')
    <style>
        .layui-form-label {
            width:100px;
        }
    </style>
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>销售商资料</cite></a><span lay-separator="">/</span>
            <a><cite>应用信息</cite></a>
            <div class="right" style="margin-right:15px;">
                @can('info_manage.account_info.basic_info')
                <button onclick="location.href='{{url('/admin/seller/basic_info')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>实名信息
                </button>
                @endcan

            </div>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">

            <div class="layui-card">
                <div class="layui-card-body" style="padding: 15px;">
                    <form class="layui-form" action="" lay-filter="component-form-group">
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司名称</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux" style="padding-top: 5px!important;">
                                    {{$seller->name or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">LOGO(正方形)</label>
                            <div class="layui-input-inline">
                                <div class="layui-upload-drag"
                                     style="width: 188px;height: 188px;display:flex;flex-direction: column;
                                     justify-content: center;padding: 0;"
                                     id="b-upload-avatar">
                                    <i class="layui-icon"></i>
                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                    <input type="hidden" name="url_avatar" @if($required_config['avatar_required'])lay-verify="avatar_photo"@endif value="{{$sellerDetail->url_avatar or ''}}"/>
                                    <div class="upload-img-preview" style="background-image:url('{{$sellerDetail->url_avatar or ''}}')"></div>

                                </div>
                                <div class="help-block">建议尺寸为900*900像素(正方形)</div>

                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">LOGO(长方形)</label>
                            <div class="layui-input-inline">
                                <div class="layui-upload-drag"
                                     style="width: 188px;height: 109px;display:flex;flex-direction: column;
                                     justify-content: center;padding: 0;"
                                     id="b-upload-avatar1">
                                    <i class="layui-icon"></i>
                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                    <input type="hidden" name="url_avatar1" @if($required_config['avatar_required'])lay-verify="avatar_photo"@endif value="{{$sellerDetail->url_avatar1 or ''}}"/>
                                    <div class="upload-img-preview" style="background-image:url('{{$sellerDetail->url_avatar1 or ''}}')"></div>
                                </div>
                                <div class="help-block">建议尺寸为860*500像素(长方形)</div>

                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">主页版头形象照</label>
                            <div class="layui-input-inline">
                                <div class="layui-upload-drag"
                                     style="width: 188px;height: 109px;display:flex;flex-direction: column;
                                     justify-content: center;padding: 0;"
                                     id="b-upload-index-photo">
                                    <i class="layui-icon"></i>
                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                    <input type="hidden" name="index_photo"  value="{{$sellerDetail->index_photo or ''}}"/>
                                    <div class="upload-img-preview" style="background-image:url('{{$sellerDetail->index_photo or ''}}')"></div>
                                </div>
                                <div class="help-block">建议尺寸为1100*282像素</div>

                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">主页路径</label>
                            @if($sellerDetail->dealer_domain)
                                <div class="layui-form-mid layui-word-aux" style="padding-top: 5px!important;">
                                    {{$sellerDetail->dealer_domain or ''}}
                                </div>
                            @else
                                <div class="layui-input-inline">
                                    <input type="text" id="brand-domain-input" name="dealer_domain" maxlength="8" @if($required_config['dealer_domain_required'])lay-verify="brandPath|required"@endif autocomplete="off" value="" placeholder="请输入主页路径" class="layui-input">
                                </div>
                                <div class="help-block">仅能设置一次，请慎重设置。格式为字母+数字组合，最多10个字符</div>

                            @endif
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">所在城市</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <select @if($required_config['area_belong_required'])lay-verify="required"@endif lay-filter="areaBelongProvinceId">
                                        <option value="">请选择省</option>
                                        @foreach($provinces as $item)
                                            <option value="{{$item->id}}" @if(isset($sellerDetail) && $sellerDetail->area_belong_province_id==$item->id) selected @endif >{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select id="area_belong_city_id"  @if($required_config['area_belong_required'])lay-verify="required"@endif lay-filter="areaBelongCityId">
                                        <option value="">请选择城市</option>
                                        @if(isset($cities))
                                            @foreach($cities as $item)
                                                <option value="{{$item->id}}" @if(isset($sellerDetail) && $sellerDetail->area_belong_city_id==$item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select name="area_belong_id" @if($required_config['area_belong_required'])lay-verify="required"@endif id="area_belong_district_id" lay-filter="areaBelongDistrictId">
                                        <option value="">请选择区/县</option>
                                        @if(isset($districts))
                                            @foreach($districts as $item)
                                                <option value="{{$item->id}}" @if(isset($sellerDetail) && $sellerDetail->area_belong_district_id==$item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系人</label>
                            <div class="layui-input-inline" style="width:400px">
                                <input type="text" name="contact_name" value="{{$seller->contact_name or ''}}"  @if($required_config['contact_name_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label" >联系电话</label>
                            <div class="layui-input-inline" style="width:400px">
                                <input type="text" name="contact_telephone" value="{{$seller->contact_telephone or ''}}"  @if($required_config['contact_telephone_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        {{--<div class="layui-form-item">
                            <label class="layui-form-label" >联系地址</label>
                            <div class="layui-input-inline" style="width:400px">
                                <input type="text" name="contact_address" value="{{$seller->contact_address or ''}}"  lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>--}}
                        <div class="layui-form-item">
                            <label class="layui-form-label">邮政编码</label>
                            <div class="layui-input-inline" style="width:400px">
                                <input type="text" name="contact_zip_code" value="{{$seller->contact_zip_code or ''}}"  @if($required_config['contact_zip_code_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司地址</label>
                            <div class="layui-input-inline" style="width:400px">
                                <input type="text" name="company_address" value="{{$sellerDetail->company_address or ''}}"  @if($required_config['company_address_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">商家介绍</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction" @if($required_config['self_introduction_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$sellerDetail->self_introduction or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务理念</label>
                            <div class="layui-input-inline">
                                <textarea name="self_promise" @if($required_config['self_promise_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$sellerDetail->self_promise or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">店面地区</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <select name="self_province_id" id="self_province_id" lay-filter="selfProvinceId">
                                        <option value="">请选择省</option>
                                        @foreach($provinces as $item)
                                            <option value="{{$item->id}}" @if(isset($sellerDetail) && $sellerDetail->self_province_id==$item->id) selected @endif >{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select name="self_city_id" id="self_city_id" lay-filter="selfCityId" >
                                        <option value="">请选择城市</option>
                                        @if(isset($cities))
                                            @foreach($cities as $item)
                                                <option value="{{$item->id}}" @if(isset($sellerDetail) && $sellerDetail->self_city_id==$item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select name="self_district_id" lay-filter="selfDistrictId" id="self_district_id" >
                                        <option value="">请选择区/县</option>
                                        @if(isset($districts))
                                            @foreach($districts as $item)
                                                <option value="{{$item->id}}" @if(isset($sellerDetail) && $sellerDetail->self_district_id==$item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">店面地址</label>
                            <div class="layui-input-inline" style="width: 390px;">
                                <input type="text" id="self_address" name="self_address" value="{{$sellerDetail->self_address or ''}}"  @if($required_config['self_address_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                            <div class="layui-btn " onclick="map_search()">搜索定位</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">店面定位</label>
                            <div class="layui-input-inline" style="width:900px">
                                <div style="overflow:hidden;">
                                    <div id="baidu-map" style="float:left;width:450px;height:300px"></div>
                                    <div id="map-result" style="float:left;width:350px;height:300px;">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">店面形象照</label>

                            <div class="layui-input-block" style="width:900px" >
                                <div style="margin-bottom:10px;">
                                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_multiple_stuff_block(this)" add_type="self_photo"> + 添加店面形象照</button>
                                </div>
                                <div class="multiple-stuff-list" id="self-photo-list" data-tpl="self-photo-block-tpl">
                                    @if($sellerDetail->self_photo != NULL)
                                        @foreach (unserialize($sellerDetail->self_photo) as $award)
                                            <div class="multiple-stuff-block">
                                                <div class="top">
                                                    <div class="close-btn" onclick="close_multiple_stuff_block(this)">
                                                        ×
                                                    </div>
                                                </div>
                                                <input type="hidden" lay-verify="self_photo" class="self_photo"  value="{{$award}}" >
                                                <input type="hidden" name="upload_api" value="{!! url('admin/seller/api/upload_avatar') !!}" >

                                                <div class="layui-upload-drag" style="width:168px">
                                                    <i class="layui-icon"></i>
                                                    <p>仅支持JPG/PNG格式，每张图片大小限制0.2M以内</p>
                                                    <div class="upload-img-preview" style="background-image: url({{$award}});"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">近期促销</label>
                            <div class="layui-input-inline" style="width:800px">
                                <textarea class="layui-textarea layui-hide"   name="self_promotion" id="LAY_demo_editor">{!! $sellerDetail->self_promotion !!}</textarea>
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
                        <input type="hidden" value="{{$sellerDetail->self_longitude or ''}}" id="self_longitude" name="self_longitude" />
                        <input type="hidden" value="{{$sellerDetail->self_latitude or ''}}" id="self_latitude" name="self_latitude" />

                    </form>
                </div>
            </div>
        </div>


    </div>

    <script type="text/html" id="self-photo-block-tpl">
        <div class="multiple-stuff-block">
            <div class="top">
                <div class="close-btn" onclick="close_multiple_stuff_block(this)">
                    ×
                </div>
            </div>
            <input type="hidden" lay-verify="self_photo" class="self_photo" >
            <input type="hidden" name="upload_api" value="{!! url('admin/seller/api/upload_avatar') !!}">

            <div class="layui-upload-drag" style="width:168px">
                <i class="layui-icon"></i>
                <p>仅支持JPG/PNG格式，每张图片大小限制0.2M以内</p>
                <div class="upload-img-preview" style=""></div>
            </div>
        </div>
    </script>


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
        //LOGO图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-avatar'
            ,url: '{{url($url_prefix.'admin/seller/api/upload_avatar')}}'
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

        //LOGO1图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-avatar1'
            ,url: '{{url($url_prefix.'admin/seller/api/upload_avatar')}}'
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
                $('#b-upload-avatar1 .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-avatar1 input').val(res.data.access_path);
            }
        });

        //主页版头形象照图片上传
        var uploadIndexPhoto = upload.render({
            elem: '#b-upload-index-photo'
            ,url: '{{url($url_prefix.'admin/seller/api/upload_avatar')}}'
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
                $('#b-upload-index-photo .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-index-photo input').val(res.data.access_path);
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

        //最后一定要进行form的render，不然控件用不了
        form.render();

        //监听所在地区省份变化
        form.on('select(areaBelongProvinceId)', function(data){
            var province_id = data.value;
            get_area(province_id,'area_belong_city_id','城市');
        });

        //监听所在地区城市变化
        form.on('select(areaBelongCityId)', function(data){
            var city_id = data.value;
            get_area(city_id,'area_belong_district_id','区/县');
        });

        //监听店面省份
        form.on('select(selfProvinceId)', function(data){
            var province_id = data.value;
            get_area(province_id,'self_city_id','城市');
        });

        //监听店面城市
        form.on('select(selfCityId)', function(data){
            var city_id = data.value;
            get_area(city_id,'self_district_id','区/县');
        });

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url($url_prefix.'admin/api/get_area_children?pi=')}}'+area_id,function(res){
                if(res.status && res.data.length>0){
                    $.each(res.data,function(k,v){
                        options+='<option value="'+ v.id+'">'+ v.name+'</option>';
                    });
                    $('#'+next_elem).html(options);
                    form.render('select');
                }
            });
        }

        form.verify({
            avatar_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传LOGO图片';
                }
            },
            brand_glory_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传品牌荣誉图片';
                }
            },
            self_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传店面形象图';
                }
            },
            brandPath: [/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{1,8}$/, '路径必须为字母+数字组合，最多8个字符']

        });


        var self_photo_required = "{{$required_config['self_photo_required']}}";
        var self_promotion_required = "{{$required_config['self_promotion_required']}}";
        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;

            //店面形象照数据组织
            var self_photo = [];
            $(".self_photo").each(function(){
                self_photo.push($(this).val());
            })
            if(self_photo_required && self_photo.length<=0){
                layer.alert('请添加店面形象照');
                return false;
            }
            form_field.self_photo = self_photo;
            form_field.self_promotion = layedit.getContent(editIndex);
            if(self_promotion_required && !form_field.self_promotion){
                layer.alert('请填写近期促销');
                return false;
            }

            ajax_post('{{url($url_prefix.'admin/seller/api/submit_app_info')}}',
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


            @if($sellerDetail->self_photo == '')
                var self_photo_tpl = $('#self-photo-block-tpl').html();
                $('#self-photo-list').append(self_photo_tpl);
            @endif

            init_upload_block();

        });

        //添加材料多选块
        function add_multiple_stuff_block(obj){
            var type = $(obj).attr('add_type');
            var num = $(obj).parents('.layui-input-block').find('.multiple-stuff-block').length;
            switch(type)
            {
                case 'self_photo':
                    if(num>="{{$limit['seller.self_photo.limit']}}"){
                        layer.msg("抱歉，店面形象照只能添加{{$limit['seller.self_photo.limit']}}个！");
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
                            console.log($(elem).prev().prev())
                            $(elem).prev().prev().val(res.data.access_path);
                        }
                    });
                    $(elem).attr('data-is-init','1');
                }


            });


        }

    </script>

    <script type="text/javascript">
        /*地图处理相关script*/

        var map = null;
        var point = null;
        var map_center_marker = null;
        var company_city_name = '';
        var final_city_name = '';

        var self_longitude = '{{$sellerDetail->self_longitude or 0}}';
        var self_latitude = '{{$sellerDetail->self_latitude or 0}}';

        //创建和初始化地图函数：
        function initMap(){
            createMap();//创建地图
            setMapEvent();//设置地图事件
            addMapControl();//向地图添加控件
        }

        //创建地图函数：
        function createMap(){
            map = new BMap.Map("baidu-map");
            if(self_longitude!=0 && self_latitude!=0){
                point = new BMap.Point(self_longitude,self_latitude);
                map.centerAndZoom(point,17);
                var marker = new BMap.Marker(new BMap.Point(self_longitude,self_latitude));
                map.addOverlay(marker);
                final_city_name = company_city_name;
            }else{
                if(company_city_name){
                    point = new BMap.Point(116.331398,39.897445);
                    map.centerAndZoom(point,12);
                    final_city_name = company_city_name;
                    set_map_city(company_city_name);
                }else{
                    //获取本地城市
                    point = new BMap.Point(116.331398,39.897445);
                    map.centerAndZoom(point,12);
                    var myCity = new BMap.LocalCity();
                    myCity.get(get_map_city);
                }
            }
        }

        //地图事件设置函数：
        function setMapEvent(){
            map.disableDragging();//启用地图拖拽事件，默认启用(可不写)
            map.disableScrollWheelZoom();//启用地图滚轮放大缩小
            map.disableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
            map.disableKeyboard();//禁用键盘上下左右键移动地图，默认禁用(可不写)
            map.disableContinuousZoom();    //启用地图惯性拖拽，默认禁用

        }

        //地图控件添加函数：
        function addMapControl(){
            var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件
            map.addControl(top_left_navigation);
        }

        function get_map_city(result){
            var cityName = result.name;
            final_city_name = cityName;
            set_map_city(cityName)
        }

        function set_map_city(cityName){
            final_city_name = cityName;
            map.setCenter(cityName);
        }

        //搜索地图结果
        function map_search(){
            var city_id = $('#self_city_id option:selected').val();
            var city_name = $('#self_city_id option:selected').text();
            if(!city_id){
                layer.alert('请选择省市！');return false;
            }
            var keyword = $('#self_address').val();
            if(!keyword){
                layer.alert('请输入地址！');return false;
            }
            set_map_city(city_name);
            map.centerAndZoom(city_name,12);
            var options = {
                onSearchComplete: function(results){
                    // 判断状态是否正确
                    if (local.getStatus() == BMAP_STATUS_SUCCESS){
                        map.clearOverlays();
                        var s = [];
                        for (var i = 0; i < results.getCurrentNumPois(); i++){
                            //添加搜索结果标注
                            var search_point = new BMap.Point(results.getPoi(i).point.lng,results.getPoi(i).point.lat);
                            var marker = new BMap.Marker(search_point);
                            map.addOverlay(marker);
                            //查询结果列表显示
                            var block_html =
                                    '<div class="block" onclick="click_map_result(this,\''+results.getPoi(i).title+'\',\''+results.getPoi(i).address+'\',\''+results.getPoi(i).point.lng+'\',\''+results.getPoi(i).point.lat+'\')">' +
                                    '<div class="selected"><img width="30" height="30" src="/v1/images/common/map_result_selected.png"/></div>' +
                                    '<div class="unselect"><img width="30" height="30" src="/v1/images/common/map_result_unselect.png"/></div>' +
                                    '<div class="title">'+results.getPoi(i).title+'</div>' +
                                    '<div class="address">'+results.getPoi(i).address+'</div>' +
                                    '</div>' +
                                    '';
                            s.push(block_html);

                        }
                        document.getElementById("map-result").innerHTML = s.join("");
                    }
                }
            };
            var local = new BMap.LocalSearch(city_name, options);
            local.search(keyword);
        }

        function click_map_result(obj,title,content,p_lng,p_lat){
            map.centerAndZoom(final_city_name,17);
            $('#map-result .block .selected').hide();
            $(obj).find('.selected').show();
            openInfo(title,content,p_lng,p_lat);
            //保存经纬度数据
            $('#self_longitude').val(p_lng);
            $('#self_latitude').val(p_lat);
        }

        function openInfo(title,content,p_lng,p_lat){
            var point = new BMap.Point(p_lng, p_lat);
            var opts = {
                width : 200,     // 信息窗口宽度
                height: 100,     // 信息窗口高度
                title : title , // 信息窗口标题
                enableMessage:true,//设置允许信息窗发送短息
            }
            var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象
            map.openInfoWindow(infoWindow,point); //开启信息窗口
        }

        $(document).ready(function() {
            coordinate_func();

        });

        function coordinate_func() {
            var script = document.createElement("script"), coordinate = self_longitude+','+self_latitude;
            script.src = "//api.map.baidu.com/api?v=2.0&ak=KUlxGA9v8yb3HnFVscpWyr5XgI0l5g9b&callback=map_func";
            document.body.appendChild(script);
            map_func = function () {
                initMap();
            }
        }

    </script>
@endsection
