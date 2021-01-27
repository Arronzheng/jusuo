@extends('v1.admin_platform.layout',[])

@section('style')
    <style>
        .config-submit-btn{margin-left:10px;margin-top:0;}
    </style>
@endsection

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>账号信息设置</cite></a><span lay-separator="">/</span>
            <a><cite>设计师应用信息</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.gender.required';?>
                        <div class="layui-form-mid root-title">性别</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.designer_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_birth_time.required';?>
                        <div class="layui-form-mid root-title">出生年月日</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.designer_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_working_address.required';?>
                        <div class="layui-form-mid root-title">工作地址</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.designer_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_education.required';?>
                        <div class="layui-form-mid root-title">教育经历</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.designer_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_work.required';?>
                        <div class="layui-form-mid root-title">工作经历</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.designer_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>


                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_introduction.required';?>
                        <div class="layui-form-mid root-title">自我介绍</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.designer_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="config-block">
                    <div class="layui-form-item">
                        <form action="" class="layui-form">
                            <div class="layui-form-mid root-title">昵称</div>
                            <div class="layui-input-block input-value-block">
                                <input type="radio" title="必填" readonly checked/>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="config-block">
                    <div class="layui-form-item">
                        <form action="" class="layui-form">
                            <div class="layui-form-mid root-title">省市区</div>
                            <div class="layui-input-block input-value-block">
                                <input type="radio" title="必填" readonly checked/>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="config-block">
                    <div class="layui-form-item">
                        <form action="" class="layui-form">
                            <div class="layui-form-mid root-title">联系手机</div>
                            <div class="layui-input-block input-value-block">
                                <input type="radio" title="必填" readonly checked/>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="config-block">
                    <div class="layui-form-item">
                        <form action="" class="layui-form">
                            <div class="layui-form-mid root-title">奖项证书</div>
                            <div class="layui-input-block input-value-block">
                                <input type="radio" title="选填" readonly checked/>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <script type="text/html" id="upload-desc-block-tpl">
            @include('v1.admin.components.param_config.multiple_text_block')
        </script>

        <script type="text/html" id="multiple-select-item-tpl">
            @include('v1.admin.components.param_config.multiple_select_block')

        </script>
    </div>



@endsection

@section('script')
    @include('v1.admin.components.param_config.script.multiple_text_script')
    @include('v1.admin.components.param_config.script.multiple_select_script')
    @include('v1.admin.components.param_config.script.submit_config_form_script')

@endsection