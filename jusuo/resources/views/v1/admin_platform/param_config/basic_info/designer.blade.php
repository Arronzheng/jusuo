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
            <a><cite>设计师</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">账号类型 </div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">服务城市</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">工作单位</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">擅长风格</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">擅长空间</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">服务专长</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">账号代码</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">注册时自动填入</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">注册时所在城市</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">注册时自动填入</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">所属组织</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">注册时自动填入</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">认证标识</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">注册时自动填入</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">注册时间</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">注册时自动填入</div>
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