@extends('v1.admin_brand.layout',[])

@section('style')
    <style>
        .config-submit-btn{margin-left:10px;margin-top:0;}
        .config-form .layui-form-mid.sub-title{width:160px;}
    </style>
@endsection

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>产品管理</cite></a><span lay-separator="">/</span>
            <a><cite>产品信息设置</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="config-form"  action="">

            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.name.required';?>
                    <div class="layui-form-mid root-title">产品名称</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                               'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                               ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.code.required';?>
                    <div class="layui-form-mid root-title">产品编号</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                       ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.photo_product.required';?>
                    <div class="layui-form-mid root-title">产品图</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                             ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.photo_practicality.required';?>
                    <div class="layui-form-mid root-title">产品实物图</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                            'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                            ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.key_technology.required';?>
                    <div class="layui-form-mid root-title">核心工艺</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                             ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">产品配件 </div>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.code.required';?>
                        <div class="layui-form-mid sub-title">配件编号</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                                'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                                'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                            ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.spec.required';?>
                    <div class="layui-form-mid sub-title">配件规格</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                                'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                            ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.photo.required';?>
                    <div class="layui-form-mid sub-title">配件图</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                             ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.technology.required';?>
                    <div class="layui-form-mid sub-title">加工工艺</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                             ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">产品搭配</div>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.note.required';?>
                    <div class="layui-form-mid sub-title">搭配说明</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                              'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                              ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.product.required';?>
                    <div class="layui-form-mid sub-title">搭配产品</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                               'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                               ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <!--<div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.technology_desc.required';?>
                    <div class="layui-form-mid sub-title">工艺说明</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                        'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                       ])
                    @can('info_manage.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>-->
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.photo.required';?>
                    <div class="layui-form-mid sub-title">搭配图片</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                                 'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                                 ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.physical_chemical_property.required';?>
                    <div class="layui-form-mid root-title">理化性能</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                                 'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                                 ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.function_feature.required';?>
                    <div class="layui-form-mid root-title">功能特征</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                                 'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                                 ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.customer_value.required';?>
                    <div class="layui-form-mid root-title">顾客价值</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                              'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                              ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <div class="layui-form-mid root-title">空间应用</div>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.space.title.required';?>
                    <div class="layui-form-mid sub-title">标题</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                              'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                              ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.space.note.required';?>
                    <div class="layui-form-mid sub-title">说明</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                              'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                              ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>

            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.space.photo.required';?>
                    <div class="layui-form-mid sub-title">应用图</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                       ])
                    @can('info_manage.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.photo_video.required';?>
                    <div class="layui-form-mid root-title">产品视频</div>
                        @if($param['platform_configs'][$cname]['value'])
                            @include('v1.admin.components.param_config.required_radio',[
                            'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null,
                               'parent_set'=>isset($param['platform_configs'][$cname])?$param['platform_configs'][$cname]:null
                            ])
                            @can('info_manage.product.ceramic.app_info_edit')
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            @endcan
                        @else
                            <div class="layui-form-mid layui-word-aux">
                                必填
                            </div>
                        @endif

                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>

        </div>



    </div>

@endsection

@section('script')
    @include('v1.admin.components.param_config.script.submit_config_form_script')

@endsection