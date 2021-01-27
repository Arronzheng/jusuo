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
            <a><cite>销售商</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.company_name.required';?>
                        <div class="layui-form-mid root-title">公司名称</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.area_belong.required';?>
                        <div class="layui-form-mid root-title">所在城市</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.contact_name.required';?>
                        <div class="layui-form-mid root-title">联系人</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.contact_telephone.required';?>
                        <div class="layui-form-mid root-title">联系电话</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.contact_address.required';?>
                        <div class="layui-form-mid root-title">联系地址</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.contact_zip_code.required';?>
                        <div class="layui-form-mid root-title">邮政编码</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.avatar.required';?>
                        <div class="layui-form-mid root-title">LOGO</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.company_address.required';?>
                        <div class="layui-form-mid root-title">公司地址</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_introduction.required';?>
                        <div class="layui-form-mid root-title">商家介绍</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_promise.required';?>
                        <div class="layui-form-mid root-title">服务承诺</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_address.required';?>
                        <div class="layui-form-mid root-title">店面地址</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_photo.required';?>
                        <div class="layui-form-mid root-title">店面形象照</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.dealer_domain.required';?>
                        <div class="layui-form-mid root-title">主页路径</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.seller.self_promotion.required';?>
                        <div class="layui-form-mid root-title">近期促销</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.seller_edit')
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