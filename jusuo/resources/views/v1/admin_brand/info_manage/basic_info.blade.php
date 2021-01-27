@extends('v1.admin_brand.layout',[
     'css' => ['/v1/css/admin/brand/brand_info.css'],
])

@section('content')
    <style>
        .layui-layout-admin .layui-form-label{
            width:160px;
        }
        .layui-layout-admin .show-form .layui-form-label{
            width:110px;
        }
        .pass-tips{
            margin-bottom:20px;color:#3e82f7;margin-left:20px;
        }
    </style>
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>品牌信息</cite></a>
            <div class="right" style="margin-right:15px;">

                @can('info_manage.account_info.app_info')
                {{--<button onclick="location.href='{{url('/admin/brand/app_info')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>应用信息
                </button>--}}
                @endcan

            </div>
        </div>
    </div>
    @include('v1.admin_brand.components.brand_info_tabs')
    <div class="layui-fluid">
        <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 15px;">
                    @if($log_status == 1)
                        您提交的申请正在审核中，请耐心等候。
                    @elseif($log_status==-1)
                        {{--审核已通过--}}
                        <div class="pass-tips">您提交的申请已通过。</div>
                        <form class="show-form">
                            <div class="layui-form-item">
                                <label class="layui-form-label">公司名称</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        {{$brand->name}}
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">品牌名称</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        {{$brand->brand_name}}
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">营业执照</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        <img width="50" height="50" src="{{$certification->url_license or ''}}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">统一社会信用代码</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        {{$certification->code_license or ''}}
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">法定代表人姓名</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        {{$certification->legal_person_name or ''}}
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">身份证号</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        {{$certification->code_idcard or ''}}
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">身份证到期日期</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                        {{$certification->expired_at_idcard  or ''}}
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">法人代表身份证图</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">
                                            <a href="{{$certification->url_idcard_front or ''}}" style="margin-right:10px;">
                                                <img width="50" height="50" src="{{$certification->url_idcard_front or ''}}"/>
                                            </a>
                                        <a href="{{$certification->url_idcard_back or ''}}" style="margin-right:10px;">
                                            <img width="50" height="50" src="{{$certification->url_idcard_back or ''}}"/>
                                        </a>
                                    </div>
                                </div>
                            </div>


                        </form>
                    @else
                        <form class="layui-form" action="" lay-filter="component-form-group">
                            <div class="layui-form-item">
                                <label class="layui-form-label">公司名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="name" value=""  lay-verify="required" @if($config['name']['platform.basic_info.brand.name.character_limit'])maxlength="{{$config['name']['platform.basic_info.brand.name.character_limit']}}" @endif autocomplete="off" placeholder="请输入公司名称" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">品牌名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="brand_name" value="{{isset($brand->brand_name)?$brand->brand_name:''}}"  lay-verify="required" @if($config['brand_name']['platform.basic_info.brand.brand_name.character_limit'])maxlength="{{$config['brand_name']['platform.basic_info.brand.brand_name.character_limit']}}" @endif autocomplete="off" placeholder="请输入品牌名称" class="layui-input layui-disabled" disabled>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">营业执照</label>
                                <div class="layui-input-inline">
                                    <div class="layui-upload-drag" id="b-upload-business-licence">
                                        <i class="layui-icon"></i>
                                        <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                                        <input type="hidden" name="url_license" lay-verify="license_photo" value=""/>
                                        <div class="upload-img-preview"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">统一社会信用代码</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="code_license" value=""  lay-verify="required" maxlength="20" autocomplete="off" placeholder="请输入统一社会信用代码" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">法定代表人姓名</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="legal_person_name" value=""  lay-verify="required" maxlength="10" autocomplete="off" placeholder="请输入法定代表人姓名" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">身份证号</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="code_idcard" value=""  lay-verify="required" maxlength="20" autocomplete="off" placeholder="请输入身份证号" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">身份证到期日期</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="expired_at_idcard" id="expired_at_idcard" lay-verify="required" readonly placeholder="请选择有效期过期时间" value="" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">法人代表身份证图（正反面）</label>
                                <div class="layui-input-block" >
                                    <div class="layui-upload-drag" id="b-upload-id-front">
                                        <i class="layui-icon"></i>
                                        <p>身份证正面<br/>大小控制在2M以内</p>
                                        <input type="hidden" name="url_idcard_front" lay-verify="id_card_photo" value=""/>
                                        <div class="upload-img-preview" ></div>
                                    </div>
                                    <div class="layui-upload-drag" id="b-upload-id-back">
                                        <i class="layui-icon"></i>
                                        <p>身份证反面<br/>大小控制在2M以内</p>
                                        <input type="hidden" name="url_idcard_back" lay-verify="id_card_photo" value=""/>
                                        <div class="upload-img-preview" ></div>
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
                    @endif
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

        laydate.render({
            elem: '#expired_at_idcard',
            value:''
        });

        var idCard_size = 2*1024;

        // console.log(idCard_size);
        //身份证正面图片上传
        var uploadIdFront = upload.render({
            elem: '#b-upload-id-front'
            ,url: '{{url($url_prefix.'admin/brand/api/upload_id_card')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size:idCard_size  //KB
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
                $('#b-upload-id-front .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-id-front input').val(res.data.access_path);

            }
        });

        var uploadIdBack = upload.render({
            elem: '#b-upload-id-back'
            ,url: '{{url($url_prefix.'admin/brand/api/upload_id_card')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size:idCard_size  //KB
            ,acceptMime: 'image/jpeg,image/jpg,image/png'
            ,before: function(obj){layer.load(1);}
            ,done: function(res) {
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){layer.msg(res.msg);console.log(res);}
                //上传成功
                $('#b-upload-id-back .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-id-back input').val(res.data.access_path);

            }
        });


        var licence_size = 2*1024;
        var uploadBusinessLicence = upload.render({
            elem: '#b-upload-business-licence'
            ,url: '{{url($url_prefix.'admin/brand/api/upload_business_licence')}}'
            ,data:{'_token':'{{csrf_token()}}'}
            ,size:licence_size  //KB
            ,acceptMime: 'image/jpeg,image/jpg,image/png'
            ,before: function(obj){layer.load(1);}
            ,done: function(res) {
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){return layer.msg(res.msg);}
                //上传成功
                $('#b-upload-business-licence .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-business-licence input').val(res.data.access_path);
            }
        });

        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({
            id_card_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传法人身份证图片';
                }
            },
            license_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传营业执照图片';
                }
            },
            brandPath: [/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{1,8}$/, '路径必须为字母+数字组合，最多8个字符']

        });

        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            ajax_post('{{url($url_prefix.'admin/brand/api/submit_basic_info')}}',
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

        @if($log_status == 2)
        {{--var msg = '您的申请以被驳回,驳回原因'+{{$$approving_log['notice']}}--}}
        layer.alert('您的申请已被驳回,驳回原因:'+'         '+'{{$approving_log->remark}}'+'  '+'(点击确认标记为已读)',function(){
            ajax_post('{{url($url_prefix.'admin/brand/api/update_log_status')}}', {id:'{{$approving_log->id}}'},
                function(result){
                    // console.log(result);
                    if(result.status){
                        // layer.msg(result.msg);
                        location.reload();
                    }else{
                        layer.msg(result.msg);
                    }
                });
        });
        @endif
    </script>
@endsection
