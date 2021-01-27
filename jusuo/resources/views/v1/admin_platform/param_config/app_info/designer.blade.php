@extends('v1.admin_platform.layout',[])

@section('style')
    <style>
        .config-submit-btn{margin-left:10px;margin-top:0;}
    </style>
@endsection

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>平台设置</cite></a><span lay-separator="">/</span>
            <a><cite>应用信息</cite></a><span lay-separator="">/</span>
            <a><cite>设计师</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.avatar.required';?>
                        <div class="layui-form-mid root-title">头像</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.nickname.required';?>
                        <div class="layui-form-mid root-title">昵称</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.gender.required';?>
                        <div class="layui-form-mid root-title">性别</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_birth_time.required';?>
                        <div class="layui-form-mid root-title">出生年月日</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.area_belong.required';?>
                        <div class="layui-form-mid root-title">所在城市</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_working_address.required';?>
                        <div class="layui-form-mid root-title">工作地址</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_education.required';?>
                        <div class="layui-form-mid root-title">教育经历</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_work.required';?>
                        <div class="layui-form-mid root-title">工作经历</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_award.required';?>
                        <div class="layui-form-mid root-title">证书奖项</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.self_introduction.required';?>
                        <div class="layui-form-mid root-title">自我介绍</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.designer.contact_telephone.required';?>
                        <div class="layui-form-mid root-title">联系手机</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.designer_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
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