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
            <a><cite>品牌</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="config-form"  action="">


            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">公司名称</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.basic_info.brand.name.character_limit';?>
                        <div class="layui-form-mid root-title">公司名称的限制字数 </div>
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
                        <div class="layui-form-mid root-title">品牌名称 </div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.basic_info.brand.brand_name.character_limit';?>
                        <div class="layui-form-mid root-title">品牌名称的限制字数 </div>
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
                        <div class="layui-form-mid root-title">营业执照</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">统一社会信用代码</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">法定代表人姓名</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">身份证号</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">身份证到期日期</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">法人代表身份证图</div>
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