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
            <a><cite>基础信息</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="config-form"  action="">
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.album.basic_info.space.min_limit';?>
                        <div class="layui-form-mid root-title">空间最低数量</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不少于</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        @can('platform_setting.album.basic_info_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>
            <div class="config-block">
                <div class="layui-form-item">
                    <form action="" class="layui-form">
                        <?php $cname = 'platform.album.basic_info.related_product.min_limit';?>
                        <div class="layui-form-mid root-title">关联产品最低数量</div>
                        <div class="layui-input-block input-value-block">
                            <div class="layui-form-mid">不少于</div>
                            @include('v1.admin.components.param_config.single_text',[
                             'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                             ])
                            <div class="layui-form-mid">&nbsp;个</div>
                        </div>
                        @can('platform_setting.album.basic_info_edit')
                            <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                        <input type="hidden" name="param_name" value="{{$cname}}"/>
                    </form>
                </div>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.basic_info.total_area.required';?>
                    <div class="layui-form-mid root-title">总面积是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.basic_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title">总面积填写范围</div>
                    <?php $cname = 'platform.album.basic_info.total_area.number_range';?>
                    <div class="layui-inline">
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp; -  </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>

                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.album.basic_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <?php $cname = 'platform.album.basic_info.each_space_area.required';?>
                    <div class="layui-form-mid root-title">各空间面积是否必填</div>
                    @include('v1.admin.components.param_config.required_radio',[
                       'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                       ])
                    @can('platform_setting.album.basic_info_edit')
                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                    @endcan
                    <input type="hidden" name="param_name" value="{{$cname}}"/>

                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title">各空间面积填写范围</div>
                    <?php $cname = 'platform.album.basic_info.space_area.number_range';?>
                    <div class="layui-inline">
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;- </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.album.basic_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title">下载积分填写范围</div>
                    <?php $cname = 'platform.album.basic_info.download_integral.number_range';?>
                    <div class="layui-inline">
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;- </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.album.basic_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>
            <div class="layui-form-item">
                <form class="layui-form">
                    <div class="layui-form-mid root-title">复制积分填写范围</div>
                    <?php $cname = 'platform.album.basic_info.copy_integral.number_range';?>
                    <div class="layui-inline">
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="lower_limit" value="{{isset($param['configs'][$cname]['lower_limit'])?$param['configs'][$cname]['lower_limit']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">&nbsp;- </div>
                        <div class="layui-input-inline short-input-inline" >
                            <input type="text" name="upper_limit" value="{{isset($param['configs'][$cname]['upper_limit'])?$param['configs'][$cname]['upper_limit']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                        </div>
                        <input type="hidden" name="param_name" value="{{$cname}}">
                        @can('platform_setting.album.basic_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </div>
                </form>
            </div>

        </div>


    </div>

@endsection

@section('script')

    @include('v1.admin.components.param_config.script.submit_config_form_script')

@endsection