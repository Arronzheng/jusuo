@extends('v1.admin_platform.layout',[])

@section('style')
    <style>
        .config-submit-btn{margin-left:10px;margin-top:0;}
        .config-form .layui-form-mid.sub-title{width:160px;}
    </style>
@endsection

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>平台设置</cite></a><span lay-separator="">/</span>
            <a><cite>产品相关</cite></a><span lay-separator="">/</span>
            <a><cite>瓷砖</cite></a><span lay-separator="">/</span>
            <a><cite>应用信息</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="config-form"  action="">

            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.name.required';?>
                    <div class="layui-form-mid root-title">产品名称</div>
                        @include('v1.admin.components.param_config.required_radio',[
                           'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                           ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.name.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.code.required';?>
                    <div class="layui-form-mid root-title">产品编号</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.code.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.photo_product.required';?>
                    <div class="layui-form-mid root-title">产品图</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                <?php $cname = 'platform.product.ceramic.app_info.photo_product.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.photo_practicality.required';?>
                    <div class="layui-form-mid root-title">产品实物图</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                    <?php $cname = 'platform.product.ceramic.app_info.photo_practicality.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.key_technology.required';?>
                    <div class="layui-form-mid root-title">核心工艺</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.key_technology.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
                        <div class="layui-form-mid root-title">产品配件 </div>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid sub-title">产品配件项目数量</div>
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.code.required';?>
                        <div class="layui-form-mid sub-title">配件编号</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.accessory.code.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.spec.required';?>
                    <div class="layui-form-mid sub-title">配件规格</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.accessory.spec.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.photo.required';?>
                    <div class="layui-form-mid sub-title">配件图</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.photo.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.accessory.technology.required';?>
                    <div class="layui-form-mid root-title">加工工艺</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.accessory.technology.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
                        <div class="layui-form-mid root-title">产品搭配</div>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid sub-title">产品搭配项目数量</div>
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.note.required';?>
                    <div class="layui-form-mid sub-title">应用说明</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.collocation.note.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.product.required';?>
                    <div class="layui-form-mid sub-title">搭配产品</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
        <!--<div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.product.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>-->
            <!--<div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.technology_desc.required';?>
                    <div class="layui-form-mid sub-title">工艺说明</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.collocation.technology_desc.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            </div>-->
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.photo.required';?>
                    <div class="layui-form-mid sub-title">搭配图片</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                    <?php $cname = 'platform.product.ceramic.app_info.collocation.photo.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.physical_chemical_property.required';?>
                    <div class="layui-form-mid root-title">理化性能</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.physical_chemical_property.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.function_feature.required';?>
                    <div class="layui-form-mid root-title">功能特征</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.function_feature.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.customer_value.required';?>
                    <div class="layui-form-mid root-title">顾客价值</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.customer_value.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
                        <div class="layui-form-mid root-title">空间应用</div>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid sub-title">空间应用项目数量</div>
                    <?php $cname = 'platform.product.ceramic.app_info.space.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.space.title.required';?>
                    <div class="layui-form-mid sub-title">标题</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.space.title.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.space.note.required';?>
                    <div class="layui-form-mid sub-title">说明</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.product.ceramic.app_info.space.note.character_limit';?>
                        <div class="layui-form-mid root-title"></div>
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
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.space.photo.required';?>
                    <div class="layui-form-mid sub-title">应用图</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <!--<div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                    <?php $cname = 'platform.product.ceramic.app_info.space.photo.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>-->
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.product.ceramic.app_info.photo_video.required';?>
                    <div class="layui-form-mid root-title">产品视频</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.product.ceramic.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title"></div>
                    <?php $cname = 'platform.product.ceramic.app_info.photo_video.limit';?>
                    <div class="layui-inline">
                        <div class="layui-form-mid">&nbsp;项目数量上限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;项目数量下限： </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.product.ceramic.app_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
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