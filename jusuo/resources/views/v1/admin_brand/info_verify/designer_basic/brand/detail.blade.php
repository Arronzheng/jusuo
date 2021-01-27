@extends('v1.admin.components.layout.blank_body',[])
@section('style')
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
        img.avatar{
            width: 34px;
            border-radius: 50%;
        }

        .layui-input-block:after{
            content: ".";
            display: block;
            height: 0;
            clear: both;
            visibility: hidden;
        }
    </style>
@endsection
@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-form " lay-filter="app-content-list">
                <form action="" method="get">
                    <div class="layui-card-body" style="padding: 15px;">

                        @if(isset($verify_content['self_designer_type']))
                            <div class="layui-form-item">
                                <label class="layui-form-label">设计师类型</label>
                                <div class="layui-input-block">
                                    {{--@if($verify_content['is_approved']!=\App\Models\LogDesignerDetail::IS_APROVE_APPROVAL)
                                        <div class="layui-form-mid layui-word-aux">
                                            {{\App\Models\DesignerDetail::designerTypeGroup($member->detail->self_designer_type)}}
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">修改为</div>
                                    @endif--}}
                                    <div class="layui-form-mid layui-word-aux">
                                        {{\App\Models\DesignerDetail::designerTypeGroup($verify_content['self_designer_type'])}}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($verify_content['area_serving_text']))
                            <div class="layui-form-item">
                                <label class="layui-form-label">服务城市</label>
                                <div class="layui-input-block">
                                    {{--@if($verify_content['is_approved']!=\App\Models\LogDesignerDetail::IS_APROVE_APPROVAL)
                                        <div class="layui-form-mid layui-word-aux">{{$member->area_serving_text or '空'}}</div>
                                        <div class="layui-form-mid layui-word-aux">修改为</div>
                                    @endif--}}
                                    <div class="layui-form-mid layui-word-aux">{{$verify_content['area_serving_text'] or ''}}</div>
                                </div>
                            </div>
                        @endif

                            @if(isset($verify_content['self_organization']))
                                <div class="layui-form-item">
                                    <label class="layui-form-label">工作单位</label>
                                    <div class="layui-input-block">
                                        {{--@if($verify_content['is_approved']!=\App\Models\LogDesignerDetail::IS_APROVE_APPROVAL)
                                            <div class="layui-form-mid layui-word-aux">{{$member->detail->self_organization or '空'}}</div>
                                            <div class="layui-form-mid layui-word-aux">修改为</div>
                                        @endif--}}
                                        <div class="layui-form-mid layui-word-aux">{{$verify_content['self_organization'] or ''}}</div>
                                    </div>
                                </div>
                            @endif

                               @if(isset($verify_content['style']))
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">擅长风格</label>
                                        <div class="layui-input-block">
                                            <div class="layui-form-mid layui-word-aux">
                                                {{$verify_content['style'] or ''}}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @if(isset($verify_content['space']))
                                <div class="layui-form-item">
                                    <label class="layui-form-label">擅长空间</label>
                                    <div class="layui-input-block">
                                        <div class="layui-form-mid layui-word-aux">
                                            {{$verify_content['space'] or ''}}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(isset($verify_content['self_expert']))
                                <div class="layui-form-item">
                                    <label class="layui-form-label">服务专长</label>
                                    <div class="layui-input-block">
                                        {{--@if($verify_content['is_approved']!=\App\Models\LogDesignerDetail::IS_APROVE_APPROVAL)
                                            <div class="layui-form-mid layui-word-aux">{{$member->detail->self_expert or '空'}}</div>
                                            <div class="layui-form-mid layui-word-aux">修改为</div>
                                        @endif--}}
                                        <div class="layui-form-mid layui-word-aux">{{$verify_content['self_expert'] or ''}}</div>
                                    </div>
                                </div>
                            @endif


                        <div class="layui-form-item">
                            <label class="layui-form-label">&nbsp;</label>
                            <div class="layui-input-inline" style="width: 300px">
                                @if($verify_content['is_approved'] == \App\Models\LogDesignerDetail::IS_APROVE_VERIFYING)
                                    @can('account_manage.info_verify.designer_basic_info.brand_designer_pass')
                                    <a href="javascript:;" onclick="ajax_pass_member('{{url('admin/brand/info_verify/designer_basic/api/brand/'.$verify_content['id'].'/approval')}}')" class="layui-btn-success layui-btn" lay-event="edit">
                                        <i class="layui-icon layui-icon-ok"></i>通过
                                    </a>
                                    @endcan
                                    @can('account_manage.info_verify.designer_basic_info.brand_designer_reject')
                                    <a href="javascript:;" onclick="ajax_reject_member('{{url('admin/brand/info_verify/designer_basic/api/brand/'.$verify_content['id'].'/reject')}}')" class="layui-btn-danger layui-btn" lay-event="del">
                                        <i class="layui-icon layui-icon-close"></i>驳回
                                    </a>
                                    @endcan
                                @endif
                            </div>
                        </div>

                    </div>
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>

    </div>

@endsection

@section('script')

    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        layui.element.init();
        var form = layui.form;

        var laypage = layui.laypage

        form.render();

        layer.photos({
            photos: '.verify-img'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

        function ajax_pass_member(url) {
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('.layui-btn').attr('disabled',true);

            layer.confirm('你确定通过该设计师资料修改？', {icon: 3, title: '提示！'}, function () {
                ajax_post(url,{}, function (result) {
                    if (result.status == 1) {
                        layer.msg(result.msg, {
                            time: 1500
                        }, function(){
                            parent.reloadTable();
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭

                        });
                    } else {
                        //将提交按钮恢复
                        $('.layui-btn').attr('disabled',false);
                        layer.msg(result.msg);
                    }
                });
            })
        }

        function ajax_reject_member(url) {
            layer.prompt({title: '驳回理由', formType: 2}, function(text, index){

                if(!text){layer.msg('请填写驳回理由！');return false;}

                //显示loading
                layer.load(1);
                //将提交按钮设置不可用
                $('.layui-btn').attr('disabled',true);

                ajax_post(url,{reason:text}, function (result) {
                    if (result.status == 1) {
                        layer.msg(result.msg, {
                            time: 1500
                        }, function(){
                            parent.reloadTable();
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭

                        });
                    } else {
                        //将提交按钮恢复
                        $('.layui-btn').attr('disabled',false);
                        layer.msg(result.msg);
                    }
                });

                layer.close(index);
                //layer.msg('演示完毕！驳回理由是：'+text);

            });
        }


    </script>
@endsection