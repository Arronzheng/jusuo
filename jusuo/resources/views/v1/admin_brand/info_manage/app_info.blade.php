@extends('v1.admin_brand.layout',[
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
            <a><cite>品牌信息</cite></a>

            {{--<div class="right" style="margin-right:15px;">
                @can('info_manage.account_info.basic_info')
                <button onclick="location.href='{{url('/admin/brand/basic_info')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>实名信息
                </button>
                @endcan
            </div>--}}

        </div>
    </div>
    @include('v1.admin_brand.components.brand_info_tabs')

    <div class="layui-fluid">

        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 15px;">
                    <form class="layui-form" action="" lay-filter="component-form-group">
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司名称</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux" style="padding-top: 5px!important;">
                                    {{$brand->name or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌LOGO</label>
                            <div class="layui-input-inline">
                                <div class="layui-upload-drag" id="b-upload-avatar">
                                    <i class="layui-icon"></i>
                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                    <input type="hidden" name="url_avatar" @if($required_config['avatar_required'])lay-verify="avatar_photo"@endif value="{{$brandDetail->url_avatar or ''}}"/>
                                    <div class="upload-img-preview" style="background-image:url('{{$brandDetail->url_avatar or ''}}')"></div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌形象图</label>
                            <div class="layui-input-inline">
                                <div class="layui-upload-drag" id="b-upload-brand-image">
                                    <i class="layui-icon"></i>
                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                    <input type="hidden" name="brand_image" @if($required_config['avatar_required'])lay-verify="avatar_photo"@endif value="{{$brandDetail->brand_image or ''}}"/>
                                    <div class="upload-img-preview" style="background-image:url('{{$brandDetail->brand_image or ''}}')"></div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">主页路径</label>
                            @if($brandDetail->brand_domain)
                                <div class="layui-form-mid layui-word-aux" style="padding-top: 5px!important;">
                                    {{$brandDetail->brand_domain or ''}}
                                </div>
                            @else
                                <div class="layui-input-inline">
                                    <input type="text" id="brand-domain-input" name="brand_domain" maxlength="8" @if($required_config['brand_domain_required'])lay-verify="brandPath|required"@endif autocomplete="off" value="" placeholder="请输入主页路径" class="layui-input">
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
                                            <option value="{{$item->id}}" @if(isset($brandDetail) && $brandDetail->area_belong_province_id==$item->id) selected @endif >{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select id="area_belong_city_id"  @if($required_config['area_belong_required'])lay-verify="required"@endif lay-filter="areaBelongCityId">
                                        <option value="">请选择城市</option>
                                        @if(isset($cities))
                                            @foreach($cities as $item)
                                                <option value="{{$item->id}}" @if(isset($brandDetail) && $brandDetail->area_belong_city_id==$item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select name="area_belong_id" @if($required_config['area_belong_required'])lay-verify="required"@endif id="area_belong_district_id" lay-filter="areaBelongDistrictId">
                                        <option value="">请选择区/县</option>
                                        @if(isset($districts))
                                            @foreach($districts as $item)
                                                <option value="{{$item->id}}" @if(isset($brandDetail) && $brandDetail->area_belong_district_id==$item->id) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系人</label>
                            <div class="layui-input-inline">
                                <input type="text" name="contact_name" value="{{$brand->contact_name or ''}}"  @if($required_config['contact_name_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系电话</label>
                            <div class="layui-input-inline">
                                <input type="text" name="contact_telephone" value="{{$brand->contact_telephone or ''}}"  @if($required_config['contact_telephone_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        {{--<div class="layui-form-item">
                            <label class="layui-form-label">联系地址</label>
                            <div class="layui-input-inline">
                                <input type="text" name="contact_address" value="{{$brand->contact_address or ''}}"  lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>--}}
                        <div class="layui-form-item">
                            <label class="layui-form-label">邮政编码</label>
                            <div class="layui-input-inline">
                                <input type="text" name="contact_zip_code" value="{{$brand->contact_zip_code or ''}}"  @if($required_config['contact_zip_code_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>


                        <div class="layui-form-item">
                            <label class="layui-form-label">公司地址</label>
                            <div class="layui-input-inline">
                                <input type="text" name="company_address" value="{{$brandDetail->company_address or ''}}"  @if($required_config['company_address_required'])lay-verify="required"@endif autocomplete="off" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">企业简介</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction" lay-verify="required" style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$brandDetail->self_introduction or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司规模</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction_scale" @if($required_config['self_introduction_scale_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$brandDetail->self_introduction_scale or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌理念</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction_brand"  @if($required_config['self_introduction_brand_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$brandDetail->self_introduction_brand or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">产品理念</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction_product" @if($required_config['self_introduction_product_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$brandDetail->self_introduction_product or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务理念</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction_service" @if($required_config['self_introduction_service_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$brandDetail->self_introduction_service or ''}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌荣誉</label>

                            <div class="layui-input-block" style="width:900px" >
                                <div style="margin-bottom:10px;">
                                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_multiple_stuff_block(this)" add_type="honor"> + 添加品牌荣誉</button>
                                </div>
                                <div class="multiple-stuff-list" id="brand-honor-list" data-tpl="brand-honor-block-tpl">
                                    @if($brandDetail->self_award != NULL)
                                        @foreach (unserialize($brandDetail->self_award) as $award)
                                            <div class="multiple-stuff-block">
                                                <div class="top">
                                                    <div class="close-btn" onclick="close_multiple_stuff_block(this)">
                                                        ×
                                                    </div>
                                                </div>
                                                <input type="text" lay-verify="required" autocomplete="off" placeholder="请填写品牌荣誉" class="layui-input brand_glory_title" value="{{$award['title'] or ''}}">
                                                <input type="text" lay-verify="brand_glory_photo" class="brand_glory_photo"  value="{{$award['photo'] or ''}}" hidden>
                                                <input type="text" name="uoload_type" value="{!! url('admin/brand/api/upload_brandGloryPhoto') !!}" hidden>
                                                <div class="layui-upload-drag" style="width:168px">
                                                    <i class="layui-icon"></i>
                                                    <p>仅支持JPG/PNG格式，每张图片大小限制0.2M以内</p>
                                                    <div class="upload-img-preview" style="background-image: url({{$award['photo'] or ''}});"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">团队建设</label>

                            <div class="layui-input-block" style="width:900px" >
                                <div style="margin-bottom:10px;">
                                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_multiple_stuff_block(this)" add_type="team_building"> + 添加团队建设</button>
                                </div>
                                <div class="multiple-stuff-list" id="team-build-list" data-tpl="team-build-block-tpl">
                                    @if($brandDetail->self_staff != NULL)
                                        @foreach (unserialize($brandDetail->self_staff) as $staff)
                                            <div class="multiple-stuff-block">
                                                <div class="top">
                                                    <div class="close-btn" onclick="close_multiple_stuff_block(this)">
                                                        ×
                                                    </div>
                                                </div>
                                                <input type="text" lay-verify="required" autocomplete="off" placeholder="请填写团队建设" class="layui-input team_building_title" value="{{$staff['title'] or ''}}">
                                                <input type="text" lay-verify="team_building_photo" class="team_building_photo"  value="{{$staff['photo'] or ''}}" hidden>
                                                <input type="text" name="uoload_type" value="{!! url('admin/brand/api/upload_teamBuildingPhoto') !!}" hidden>
                                                <div class="layui-upload-drag" style="width:168px" type="team_building">
                                                    <i class="layui-icon"></i>
                                                    <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                                    <div class="upload-img-preview" style="background-image: url({{$staff['photo'] or ''}});"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌规划</label>
                            <div class="layui-input-inline">
                                <textarea name="self_introduction_plan" @if($required_config['self_introduction_plan_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$brandDetail->self_introduction_plan or ''}}</textarea>
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

    <script type="text/html" id="brand-honor-block-tpl">
        <div class="multiple-stuff-block">
            <div class="top">
                <div class="close-btn" onclick="close_multiple_stuff_block(this)">
                    ×
                </div>
            </div>
            <input type="text" lay-verify="required" autocomplete="off" placeholder="请填写品牌荣誉" class="layui-input brand_glory_title">
            <input type="text" lay-verify="brand_glory_photo" class="brand_glory_photo" hidden>
            <input type="text" name="uoload_type" value="{!! url('admin/brand/api/upload_brandGloryPhoto') !!}" hidden>
            <div class="layui-upload-drag" style="width:168px">
                <i class="layui-icon"></i>
                <p>仅支持JPG/PNG格式，每张图片大小限制0.2M以内</p>
                <div class="upload-img-preview" style=""></div>
            </div>
        </div>
    </script>
    <script type="text/html" id="team-build-block-tpl">
        <div class="multiple-stuff-block">
            <div class="top">
                <div class="close-btn" onclick="close_multiple_stuff_block(this)">
                    ×
                </div>
            </div>
            <input type="text" lay-verify="required" autocomplete="off" placeholder="请填写团队建设" class="layui-input team_building_title">
            <input type="text" lay-verify="team_building_photo" class="team_building_photo" hidden>
            <input type="text" name="uoload_type" value="{!! url('admin/brand/api/upload_teamBuildingPhoto') !!}" hidden>
            <div class="layui-upload-drag" style="width:168px" type="team_building">
                <i class="layui-icon"></i>
                <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
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
        //品牌LOGO图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-avatar'
            ,url: '{{url($url_prefix.'admin/brand/api/upload_avatar')}}'
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

        //品牌形象图片上传
        var uploadBrandImage = upload.render({
            elem: '#b-upload-brand-image'
            ,url: '{{url($url_prefix.'admin/brand/api/upload_avatar')}}'
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
                $('#b-upload-brand-image .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-brand-image input').val(res.data.access_path);
            }
        });


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
                    return '请上传品牌LOGO、形象图片';
                }
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


        var self_award_required = "{{$required_config['self_award_required'] or ''}}";
        var self_staff_required = "{{$required_config['self_staff_required'] or ''}}";
        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            var brand_glory_title = [];
            var brand_glory_photo = [];
            var team_building_title = [];
            var team_building_photo = [];
            $(".brand_glory_title").each(function(){
                brand_glory_title.push($(this).val());
            })
            $(".brand_glory_photo").each(function(){
                brand_glory_photo.push($(this).val());
            })
            $(".team_building_title").each(function(){
                team_building_title.push($(this).val());
            })
            $(".team_building_photo").each(function(){
                team_building_photo.push($(this).val());
            })
            if(self_award_required && brand_glory_photo.length<=0){
                layer.alert("请添加品牌荣誉");
                return false;
            }
            if(self_staff_required && team_building_title.length<=0){
                layer.alert("请添加团队建设");
                return false;
            }
            form_field.brand_glory_title = brand_glory_title;
            form_field.brand_glory_photo = brand_glory_photo;
            form_field.team_building_title = team_building_title;
            form_field.team_building_photo = team_building_photo;
            ajax_post('{{url($url_prefix.'admin/brand/api/submit_app_info')}}',
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
                    @if($brandDetail->self_certification == '')
            var company_auth_tpl = $('#company-auth-block-tpl').html();
            $('#company-auth-list').append(company_auth_tpl);
                    @endif
                    @if($brandDetail->self_award == '')
            var brand_honor_tpl = $('#brand-honor-block-tpl').html();
            $('#brand-honor-list').append(brand_honor_tpl);
                    @endif
                    @if($brandDetail->self_staff == '')
            var team_build_tpl = $('#team-build-block-tpl').html();
            $('#team-build-list').append(team_build_tpl);
            @endif
            init_upload_block();

        });

        //添加材料多选块
        function add_multiple_stuff_block(obj){
            var type = $(obj).attr('add_type');
            var num = $(obj).parents('.layui-input-block').find('.multiple-stuff-block').length;
            switch(type)
            {
                case 'honor':
                    if(num>="{{$limit['brand.self_award.limit']}}"){
                        layer.msg("抱歉，品牌荣誉只能添加{{$limit['brand.self_award.limit']}}个！");
                        return false;
                    }
                    break;
                case 'team_building':
                    if(num>="{{$limit['brand.self_staff.limit']}}"){
                        layer.msg("抱歉，团队建设只能添加{{$limit['brand.self_staff.limit']}}个！");
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
                            $(elem).prev().prev().val(res.data.access_path);
                        }
                    });
                    $(elem).attr('data-is-init','1');
                }


            });


        }

    </script>
@endsection
