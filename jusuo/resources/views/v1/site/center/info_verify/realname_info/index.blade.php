@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/info_verify.css')}}">
    <style>
        .layui-form-label{
            width:100px;
        }
    </style>

    <div class="detailview show">
        @include('v1.site.center.components.info_verify.global_tab',[
      'log_status'=>$log_status,'active'=>'realname'
      ])

        <div id="verify-form">
            @if($log_status == 1)
                {{--审核中--}}
                您提交的申请正在审核中，请耐心等候。
            @elseif($log_status==-1)
                {{--审核已通过--}}
                <form>
                    <div class="layui-form-item">
                        <label class="layui-form-label">真实姓名</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$certification->legal_person_name}}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证号</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$certification->code_idcard}}
                            </div>
                        </div>
                    </div>
                    {{--<div class="layui-form-item">
                        <label class="layui-form-label">身份证到期日期</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$certification->expired_at_idcard}}
                            </div>
                        </div>
                    </div>--}}

                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证照片</label>
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
                        <label class="layui-form-label">真实姓名</label>
                        <div class="layui-input-inline">
                            <input type="text" name="legal_person_name" value=""  lay-verify="required" autocomplete="off" placeholder="请输入真实姓名" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="code_idcard" value=""  lay-verify="required" maxlength="18" autocomplete="off" placeholder="请输入身份证号" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证到期日期</label>
                        <div class="layui-input-inline">
                            <input type="text" name="expired_at_idcard" id="expired_at_idcard" lay-verify="required" readonly placeholder="请选择有效期过期时间" value="" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证照片（正反面）</label>
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
            ,url: '{{url($url_prefix.'center/upload_id_card')}}'
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
            ,url: '{{url($url_prefix.'center/upload_id_card')}}'
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



        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({
            id_card_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传身份证图片';
                }
            },
        });

        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            ajax_post('{{url($url_prefix.'center/submit_realname_info')}}',
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
        {{--var msg = '您的申请以被驳回,驳回原因'+{{$$log['notice']}}--}}
        layer.alert('您的申请已被驳回，请重新提交审核。驳回原因：'+'         '+'{{$log->remark}}'+'  ');
        @endif

        $(document).ready(function(){
            //监听省份变化
            form.on('select(companyProvinceId)', function(data){
                var province_id = data.value;
                get_area(province_id,'company_city_id','城市');
            });

            //监听城市变化
            form.on('select(companyCityId)', function(data){
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
@endsection
