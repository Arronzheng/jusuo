
@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <style>
        .verify-img img{
            width:180px;
        }
        .layui-form-label{
            width:120px;
        }
    </style>
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 20px 0;">
        <form>

            <div class="layui-form-item">
                <label class="layui-form-label">头像</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        <img width="50" height="50" src="{{$data->detail->url_avatar or ''}}"/>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">昵称</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->detail->nickname}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真实姓名</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->detail->realname}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">性别</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{\App\Models\DesignerDetail::genderGroup($data->detail->gender)}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">出生年月</label>
                <div class="layui-input-block">
                    <div class="layui-form-mid layui-word-aux">{{$data->detail->self_birth_time}}</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所在城市</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->area_belong_text}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">联系电话</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->detail->contact_telephone}}
                    </div>
                </div>
            </div>

            <hr/>

            <div class="layui-form-item">
                <label class="layui-form-label">教育信息</label>
                @if($data->detail->self_education)
                    @foreach(unserialize($data->detail->self_education) as $edu)
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                {{isset($edu['graduation_year'])?$edu['graduation_year']:'?'}}年{{isset($edu['graduation_month'])?$edu['graduation_month']:'?'}}月 毕业于 {{isset($edu['school'])?$edu['school']:'?'}}（{{isset($edu['education'])?$edu['education']:'?'}}） {{isset($edu['profession'])?$edu['profession']:'?'}}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">工作经验</label>
                @if($data->detail->self_work)
                    @foreach(unserialize($data->detail->self_work) as $exp)
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                {{$exp['work_start_year'] or ''}}年{{$exp['work_start_month'] or ''}}月 至 {{$exp['work_end_year'] or ''}}年{{$exp['work_end_month'] or ''}}月 服务于 {{$exp['company_name'] or ''}} 职位{{$exp['take_position'] or ''}}
                            </div>
                        </div>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                工作描述：{{$exp['work_description'] or ''}}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">证书与奖项</label>
                @if($data->detail->self_award)
                    @foreach(unserialize($data->detail->self_award) as $awd)
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                {{$awd['profit_year'] or ''}}年{{$awd['profit_month'] or ''}}月 获 {{$awd['certificate_name'] or ''}}
                            </div>
                        </div>
                        <div class="layui-input-block">
                            <div class="verify-img"  id="verify-img-2">
                                <img src="{{$awd['certificate_pic'] or ''}}"/>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">工作地址</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->detail->self_working_address}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">自我介绍</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->detail->self_introduction}}
                    </div>
                </div>
            </div>

            <hr/>

            <div class="layui-form-item">
                <label class="layui-form-label">设计师类型</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{\App\Models\DesignerDetail::designerTypeGroup($data->detail->self_designer_type)}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">服务城市</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->area_serving_text}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">擅长风格</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->style_text}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">服务专长</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->detail->self_expert}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">账号代码</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->designer_account}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所属组织</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->organization_text}}
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">是否已实名认证</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{\App\Models\DesignerDetail::approveRealnameGroup($data->detail->approve_realname)}}
                        @if($data->detail->approve_realname==\App\Models\DesignerDetail::APPROVE_REALNAME_YES)
                            （{{$data->detail->approve_time}}）
                        @endif
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">注册时间</label>
                <div class="layui-input-inline">
                    <div class="layui-form-mid layui-word-aux">
                        {{$data->created_at}}
                    </div>
                </div>
            </div>


        </form>

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