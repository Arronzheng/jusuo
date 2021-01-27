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
            <a><cite>产品相关</cite></a><span lay-separator="">/</span>
            <a><cite>瓷砖</cite></a><span lay-separator="">/</span>
            <a><cite>基本信息</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">规格选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_spec_options');" style="margin-bottom:15px;">添加规格选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_spec_options'],'type'=>'ceramic_spec_options'
                                 ])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">色系选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_color_options');" style="margin-bottom:15px;">添加色系选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_color_options'],'type'=>'ceramic_color_options'
                                 ])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">应用类别选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_apply_category_options');" style="margin-bottom:15px;">添加应用类别选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_apply_category_options'],'type'=>'ceramic_apply_category_options'
                                 ])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">工艺类别选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_technology_category_options');" style="margin-bottom:15px;">添加工艺类别选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_technology_category_options'],'type'=>'ceramic_technology_category_options'
                                 ])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">表面特征选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_surface_feature_options');" style="margin-bottom:15px;">添加表面特征选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_surface_feature_options'],'type'=>'ceramic_surface_feature_options'
                                 ])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">生命周期选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_life_phase_options');" style="margin-bottom:15px;">添加生命周期选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_life_phase_options'],'type'=>'ceramic_life_phase_options'
                                 ])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <div class="layui-form-mid root-title">产品状态选项</div>
                        <div class="layui-input-block input-value-block" style="width:800px;">
                            @can('platform_setting.product.ceramic.basic_info_edit')
                                <div class="layui-btn {{--layui-btn-warm --}}layui-btn-normal" onclick="add_multiple_select_item('multiple-select-item-tpl','favour-style-select-list','ceramic_product_status_options');" style="margin-bottom:15px;">添加生命周期选项</div>
                            @endcan
                            <div id="favour-style-select-list">
                                @include('v1.admin.components.param_config.multiple_select_block_value',[
                                     'config_set'=>$param['ceramic_product_status_options'],'type'=>'ceramic_product_status_options'
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