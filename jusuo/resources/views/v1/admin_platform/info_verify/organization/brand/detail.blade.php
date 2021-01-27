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
        .verify-img img:hover{
            cursor: pointer;
        }
        .layui-form-label{
            width:180px;
            text-align:left;
        }
    </style>
@endsection

@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-card-header">注册信息</div>
            <div class="layui-card-body" style="padding: 15px;">
                @if(isset($verify_content['name']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">公司名称</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">{{$verify_content['name'] or ''}}</div>
                        </div>
                    </div>
                @endif
                    @if(isset($verify_content['brand_name']))
                        <div class="layui-form-item">
                            <label class="layui-form-label">品牌名称</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">{{$verify_content['brand_name'] or ''}}</div>
                            </div>
                        </div>
                    @endif
                    @if(isset($verify_content['brand_domain']))
                        <div class="layui-form-item">
                            <label class="layui-form-label">账号子域名</label>
                            <div class="layui-input-block">
                                <div class="layui-form-mid layui-word-aux">{{$verify_content['brand_domain'] or ''}}</div>
                            </div>
                        </div>
                    @endif
                @if(isset($verify_content['url_license']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">营业执照</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                <div class="verify-img"  id="verify-img-4">
                                    <img onclick="click_img('{{$verify_content['url_license'] or ''}}')" width="400px" src="{{$verify_content['url_license'] or ''}}" alt="营业执照"/>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($verify_content['code_license']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">统一社会信用代码</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">{{$verify_content['code_license'] or ''}}</div>
                        </div>
                    </div>
                @endif
                @if(isset($verify_content['legal_person_name']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">法定代表人姓名</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">{{$verify_content['legal_person_name'] or ''}}</div>
                        </div>
                    </div>
                @endif
                @if(isset($verify_content['code_idcard']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证号</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">{{$verify_content['code_idcard'] or ''}}</div>
                        </div>
                    </div>
                @endif
                @if(isset($verify_content['expired_at_idcard']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证有效期</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">{{$verify_content['expired_at_idcard'] or ''}}</div>
                        </div>
                    </div>
                @endif

                @if(isset($verify_content['url_idcard_front']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">法定代表人身份证正面照片</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                <div class="verify-img" id="verify-img-1">
                                    <img width="400px" onclick="click_img('{{$verify_content['url_idcard_front'] or ''}}')" src="{{$verify_content['url_idcard_front'] or ''}}" alt="法定代表人身份证正面"/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($verify_content['url_idcard_back']))
                    <div class="layui-form-item">
                        <label class="layui-form-label">法定代表人身份证反面照片</label>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">
                                <div class="verify-img"  id="verify-img-2">
                                    <img width="400px" onclick="click_img('{{$verify_content['url_idcard_back'] or ''}}')" src="{{$verify_content['url_idcard_back'] or ''}}" alt="法定代表人身份证反面"/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <div class="layui-form-item">
                    <label class="layui-form-label">&nbsp;</label>
                    <div class="layui-input-inline" style="width: 300px">
                        @if($verify_content['is_approved'] == \App\Models\LogDealerCertification::IS_APROVE_VERIFYING)
                            @can('account_manage.info_verify.org_info.brand_pass')
                                <button href="javascript:;" onclick="ajax_pass_brand('{{url('admin/platform/info_verify/organization/api/brand/'.$verify_content['id'].'/approval')}}')" class="layui-btn-success layui-btn" lay-event="edit">
                                    <i class="layui-icon layui-icon-ok"></i>通过
                                </button>
                            @endcan
                            @can('account_manage.info_verify.org_info.brand_reject')
                                <button href="javascript:;" onclick="ajax_reject_brand('{{url('admin/platform/info_verify/organization/api/brand/'.$verify_content['id'].'/reject')}}')" class="layui-btn-danger layui-btn" lay-event="del">
                                    <i class="layui-icon layui-icon-close"></i>驳回
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>

        function click_img(url){
            if(url){
                parent.window.open(url);
            }
        }

        function ajax_pass_brand(url) {
            layer.confirm('你确定通过该品牌吗？', {icon: 3, title: '提示！'}, function () {
                //显示loading
                layer.load(1);
                //将提交按钮设置不可用
                $('.layui-btn').attr('disabled',true);


                ajax_post(url,{}, function (result ) {
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

        function ajax_reject_brand(url) {
            layer.prompt({title: '驳回理由', formType: 2}, function(text, index){

                if(!text){layer.msg('请填写驳回理由！');return false;}
                //显示loading
                layer.load(1);
                //将提交按钮设置不可用
                $('.layui-btn').attr('disabled',true);

                ajax_post(url,{reason:text}, function (result ) {
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

        function ajax_brand_status(url) {
            ajax_post(url,{}, function (res) {
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    window.location.reload()
                } else {
                    layer.msg(res.msg);
                }
            });
        }

    </script>
@endsection