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
        .rich-text img{width:80%;}

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
                            <label class="layui-form-label">品牌名称</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$seller->brand_name}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">公司名称</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$seller->name}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">账号名称</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$administrator->login_account}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务城市</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$sellerDetail->area_serving_text}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">经营品类</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$seller->product_category_name}}
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
                                    {{$certification->code_license}}
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">法定代表人姓名</label>
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
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证到期日期</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$certification->expired_at_idcard}}
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
                    <form>

                        <div class="layui-form-item">
                            <label class="layui-form-label">LOGO</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    <img width="50" height="50" src="{{$sellerDetail->url_avatar or ''}}"/>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">账号子域名</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$sellerDetail->dealer_domain}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务城市</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$sellerDetail->area_serving_text}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务城市范围权限</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$sellerDetail->privilege_area_serving_text}}
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">商家介绍</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$sellerDetail->self_introduction}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">店面形象照</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    @foreach($sellerDetail->self_photo as $item)
                                        <a href="{{$item or ''}}" style="margin-right:10px;">
                                            <img width="50" height="50" src="{{$item or ''}}"/>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">服务承诺</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{$sellerDetail->self_promise}}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">最近促销</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux rich-text">
                                    {!! $sellerDetail->self_promotion !!}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">是否置顶</label>
                            <div class="layui-input-inline">
                                <div class="layui-form-mid layui-word-aux">
                                    {{\App\Models\DetailDealer::isTopGroup($sellerDetail->is_top)}}
                                    @if($sellerDetail->is_top)
                                        <span>（{{$sellerDetail->is_top_time}}）</span>
                                    @endif
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