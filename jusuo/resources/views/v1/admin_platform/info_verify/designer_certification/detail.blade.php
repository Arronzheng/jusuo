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
                        <div class="layui-form-item">
                            <label class="layui-form-label">真实姓名</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">{{$verify_content['legal_person_name'] or ''}}</div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证号</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">{{$verify_content['code_idcard'] or ''}}</div>
                            </div>
                        </div>


                        {{--<div class="layui-form-item">
                            <label class="layui-form-label">身份证有效期</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">{{$verify_content['expired_at_idcard'] or ''}}</div>
                            </div>
                        </div>--}}

                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证图片</label>
                            <div class="layui-input-block">
                                <div class="verify-img"  id="verify-img-2">
                                    <img src="{{$verify_content['url_idcard_front'] or ''}}" alt="身份证正面"/>
                                </div>
                                <div class="verify-img"  id="verify-img-2">
                                    <img src="{{$verify_content['url_idcard_back'] or ''}}" alt="身份证反面"/>
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item" style="margin-top:20px;">
                            <label class="layui-form-label">&nbsp;</label>
                            <div class="layui-input-inline" style="width: 300px">
                                @if($verify_content['is_approved'] == \App\Models\LogDesignerCertification::IS_APROVE_VERIFYING)
                                    @can('account_manage.info_verify.designer_realname_info.free_designer_pass')
                                    <a href="javascript:;" onclick="ajax_pass_member('{{url('admin/platform/info_verify/designer_certification/api/'.$verify_content['id'].'/approval')}}')" class="layui-btn-success layui-btn" lay-event="edit">
                                        <i class="layui-icon layui-icon-ok"></i>通过
                                    </a>
                                    @endcan
                                    @can('account_manage.info_verify.designer_realname_info.free_designer_reject')
                                    <a href="javascript:;" onclick="ajax_reject_member('{{url('admin/platform/info_verify/designer_certification/api/'.$verify_content['id'].'/reject')}}')" class="layui-btn-danger layui-btn" lay-event="del">
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