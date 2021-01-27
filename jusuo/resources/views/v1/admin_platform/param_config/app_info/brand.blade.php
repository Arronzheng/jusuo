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
            <a><cite>全局设置</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-form"  action="">
            <div class="config-block">

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.avatar.required';?>
                        <div class="layui-form-mid root-title">LOGO</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.brand_domain.required';?>
                        <div class="layui-form-mid root-title">主页路径</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.area_belong.required';?>
                        <div class="layui-form-mid root-title">所在城市</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.contact_name.required';?>
                        <div class="layui-form-mid root-title">联系人</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.contact_telephone.required';?>
                        <div class="layui-form-mid root-title">联系电话</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <!--<div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.contact_address.required';?>
                        <div class="layui-form-mid root-title">联系地址</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>-->

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.contact_zip_code.required';?>
                        <div class="layui-form-mid root-title">邮政编码</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>



                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.company_address.required';?>
                        <div class="layui-form-mid root-title">公司地址</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_introduction_scale.required';?>
                        <div class="layui-form-mid root-title">公司规模</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_introduction_brand.required';?>
                        <div class="layui-form-mid root-title">品牌理念</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_introduction_product.required';?>
                        <div class="layui-form-mid root-title">产品理念</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_introduction_service.required';?>
                        <div class="layui-form-mid root-title">服务理念</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>

                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_award.required';?>
                        <div class="layui-form-mid root-title">品牌荣誉</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_staff.required';?>
                        <div class="layui-form-mid root-title">团队建设</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>

                    </form>
                </div>
                <div class="layui-form-item">
                    <form class="layui-form">
                        <?php $cname = 'platform.app_info.brand.self_introduction_plan.required';?>
                        <div class="layui-form-mid root-title">品牌规划</div>
                        <div class="layui-form-mid sub-title">是否必填</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                        @can('platform_setting.app_info.brand_edit')
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