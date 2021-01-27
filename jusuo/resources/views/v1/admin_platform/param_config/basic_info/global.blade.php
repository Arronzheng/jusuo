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
            <a><cite>基本信息</cite></a><span lay-separator="">/</span>
            <a><cite>全局设置</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">擅长风格选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.basic_info.global_setting_create_option')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','style_options');" style="margin-bottom:15px;">添加风格选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['style_options'],'type'=>'style_options'
                                 ])

                            </div>
                        </div>

                    </form>
                </div>
            </div>


            <div class="config-block">
                <div class="layui-form-item">
                    <div class="layui-form-mid root-title">擅长空间选项</div>
                    <div class="layui-input-block input-value-block" style="width:800px;">
                        @can('platform_setting.basic_info.global_setting_create_option')
                            <div class="layui-btn {{--layui-btn-warm--}} layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-space-select-list','space_options');" style="margin-bottom:15px;">添加空间选项</div>
                        @endcan
                        <div id="favour-space-select-list">
                            @include('v1.admin.components.param_config.multiple_select_block_value',[
                                    'config_set'=>$param['space_options'],'type'=>'space_options'
                                ])
                        </div>
                    </div>
                </div>

            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.basic_info.global.style.limit';?>
                        <div class="layui-form-mid root-title">设计师擅长风格最大项目数量 </div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不超过</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.basic_info.global.space.limit';?>
                        <div class="layui-form-mid root-title">设计师擅长空间最大项目数量 </div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不超过</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.basic_info.global.self_organization.limit';?>
                        <div class="layui-form-mid root-title">设计师工作单位的限制字数 </div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不超过</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个字符</div>
                        </div>
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.basic_info.global.self_expert.limit';?>
                        <div class="layui-form-mid root-title">设计师服务专长的限制字数 </div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不超过</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个字符</div>
                        </div>
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <div class="layui-form-mid root-title">设计师账号类型选项</div>
                    <div class="layui-input-block input-value-block" style="width:800px;">
                        @can('platform_setting.basic_info.global_setting_create_option')
                            <div class="layui-btn {{--layui-btn-warm--}} layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','member-type-select-list','member_type');" style="margin-bottom:15px;">添加设计师账号类型选项</div>
                        @endcan
                        <div id="member-type-select-list">
                            @include('v1.admin.components.param_config.multiple_select_block_value',[
                                    'config_set'=>$param['member_types'],'type'=>'member_type'
                                ])
                        </div>
                    </div>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">设计图户型类别选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.basic_info.global_setting_create_option')
                                <div class="layui-btn {{--layui-btn-warm--}} layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','house_types');" style="margin-bottom:15px;">添加设计图户型类别</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['house_types'],'type'=>'house_types'
                                 ])

                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">设计图空间类别选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.basic_info.global_setting_create_option')
                                <div class="layui-btn {{--layui-btn-warm--}} layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','space_types');" style="margin-bottom:15px;">添加设计图空间类别</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['space_types'],'type'=>'space_types'
                                 ])

                            </div>
                        </div>

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