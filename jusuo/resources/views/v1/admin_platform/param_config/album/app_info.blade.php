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
            <a><cite>方案相关</cite></a><span lay-separator="">/</span>
            <a><cite>应用信息</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.album.app_info.layout_photo.min_limit';?>
                        <div class="layui-form-mid root-title">户型图</div>
                        <div class="layui-form-mid sub-title">最低数量</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不少于</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        @can('platform_setting.album.app_info_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.album.app_info.each_space_photo.min_limit';?>
                        <div class="layui-form-mid root-title">各空间高清图</div>
                            <div class="layui-form-mid sub-title">最低数量</div>
                            <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不少于</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        @can('platform_setting.album.app_info_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.album.app_info.each_space_product_app_photo.min_limit';?>
                        <div class="layui-form-mid root-title">各空间产品应用图</div>
                            <div class="layui-form-mid sub-title">最低数量</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不少于</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        @can('platform_setting.album.app_info_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.album.app_info.each_space_build_photo.min_limit';?>
                        <div class="layui-form-mid root-title">各空间施工图</div>
                            <div class="layui-form-mid sub-title">最低数量</div>
                            <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不少于</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        @can('platform_setting.album.app_info_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>


            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.title.required';?>
                    <div class="layui-form-mid root-title">标题</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.title.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>

            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_street.required';?>
                    <div class="layui-form-mid root-title">所在街道</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_street.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_residential_quarter.required';?>
                    <div class="layui-form-mid root-title">所在小区</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_residential_quarter.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_building.required';?>
                    <div class="layui-form-mid root-title">所在楼栋</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_layout_number.required';?>
                    <div class="layui-form-mid root-title">所在户型号</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.address_building.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>

            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.description_design.required';?>
                    <div class="layui-form-mid root-title">设计说明</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.description_design.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.description_layout.required';?>
                    <div class="layui-form-mid root-title">户型说明</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.description_layout.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.each_space_description.required';?>
                    <div class="layui-form-mid root-title">各空间说明</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.each_space_description.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.each_space_product_app_description.required';?>
                    <div class="layui-form-mid root-title">各空间产品应用说明</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.each_space_product_app_description.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.app_info.each_space_build_description.required';?>
                    <div class="layui-form-mid root-title">各空间施工说明</div>
                    <div class="layui-form-mid sub-title">是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>
            <div class="layui-form-item">
                <form action="" class="layui-form">
                    <?php $cname = 'platform.album.app_info.each_space_build_description.character_limit';?>
                    <div class="layui-form-mid root-title"></div>
                    <div class="layui-form-mid sub-title">限制字数</div>
                    <div class="layui-input-block input-value-block">
                        <div class="layui-form-mid">不超过</div>
                        @include('v1.admin.components.param_config.single_text',[
                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                         ])
                        <div class="layui-form-mid">&nbsp;个字符</div>
                    </div>
                    @can('platform_setting.album.app_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                </form>
            </div>

        </div>


    </div>

@endsection

@section('script')

    @include('v1.admin.components.param_config.script.submit_config_form_script')

@endsection