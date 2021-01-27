@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <style>
        .verify-img{
            float:left;margin-right:15px;
        }
        .verify-img:hover{
            cursor:pointer
        }
        .verify-img img{
            width:180px;
        }
        .layui-form-item .layui-form-label{
            width:120px;
        }
        .layui-form-item .layui-input-inline{
            width:400px;
        }

    </style>
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 20px 0;">

        <div class="layui-card">
            <div class="layui-card-header">实名信息</div>
            <div class="layui-card-body">
                @if(!$certification)
                    未通过审核
                @else
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
                @endif
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">应用信息</div>
            <div class="layui-card-body">
                @if(!$certification)
                    未通过审核
                @else
                    <form class="show-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌LOGO</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    <img width="50" height="50" src="{{$brandDetail->url_avatar or ''}}"/>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">主页路径</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->brand_domain or ''}}
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">所在城市</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->area_belong_text or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系人</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brand->contact_name or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">联系电话</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brand->contact_telephone  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">邮政编码</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brand->contact_zip_code  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司地址</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->company_address  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司规模</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->self_introduction_scale  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌理念</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->self_introduction_brand  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">产品理念</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->self_introduction_product  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">产品理念</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->self_introduction_product  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务理念</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->self_introduction_service  or ''}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌荣誉</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">
                                    @foreach($brandDetail->self_award_array as $item)
                                        <div class="verify-img" id="verify-img-1">
                                            <p style="text-align: center">{{$item['title'] or ''}}</p>
                                            <a target="_blank" href="{{$item['photo'] or ''}}" style="margin-right:10px;">
                                                <img  src="{{$item['photo'] or ''}}"/>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">团队建设</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">
                                    @foreach($brandDetail->self_staff_array as $item)
                                        <div class="verify-img" id="verify-img-1">
                                            <p style="text-align: center">{{$item['title'] or ''}}</p>
                                            <a target="_blank" href="{{$item['photo'] or ''}}" style="margin-right:10px;">
                                                <img  src="{{$item['photo'] or ''}}"/>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务理念</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$brandDetail->self_introduction_plan  or ''}}
                                </div>
                            </div>
                        </div>


                    </form>
                @endif
            </div>
        </div>


    </div>
@endsection

@section('script')

    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form;
        layui.element.init();

        var laydate = layui.laydate;
        laydate.render({
            elem: '#expired_at',
            type:'datetime',
            value:'{{request()->input('expired_at') ? request()->input('expired_at') :''}}'
        });

        /*自定义form验证*/
        form.verify({

        });
        /*自定义form验证*/

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/platform/sub_admin/brand/api/account')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/platform/sub_admin/brand/api/account')}}/{{$data->id}}";
            submitMethod = "PUT";
        @endif

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;
            //处理开关控件：需要将其转换成数值
            if(!form_field.status) {
                form_field.status = "0";
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
@endsection