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
            <a><cite>销售商应用信息</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.company_address.required';?>
                        <div class="layui-form-mid root-title">公司地址</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.seller_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <!--<div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.contact_address.required';?>
                        <div class="layui-form-mid root-title">联系地址</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.seller_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>-->

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.contact_zip_code.required';?>
                        <div class="layui-form-mid root-title">邮政编码</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.seller_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_introduction.required';?>
                        <div class="layui-form-mid root-title">商家介绍</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.seller_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_promise.required';?>
                        <div class="layui-form-mid root-title">服务承诺</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.seller_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_address.required';?>
                        <div class="layui-form-mid root-title">店面地址</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('info_manage.sub_account.app_info.seller_submit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
            </div>

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
                        <div class="layui-form-mid root-title">联系人</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">联系电话</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">LOGO</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="必填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">主页路径</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="选填" readonly checked/>
                        </div>
                    </form>
                </div>
            </div>

            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">近期促销</div>
                        <div class="layui-input-block input-value-block">
                            <input type="radio" title="选填" readonly checked/>
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