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
        <div class="layui-card">
            <div class="layui-card-header">设计师</div>
            <div class="layui-card-body">
                <div class="config-form"  action="">
                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.designer.self_education.limit';?>
                                <div class="layui-form-mid root-title">教育经历最大项目数量</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_work.limit';?>
                                    <div class="layui-form-mid root-title">工作经历最大项目数量</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_award.limit';?>
                                    <div class="layui-form-mid root-title">奖项证书最大项目数量</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.nickname.character_limit';?>
                                    <div class="layui-form-mid root-title">昵称限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_working_address.character_limit';?>
                                    <div class="layui-form-mid root-title">工作地址限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_education_school.character_limit';?>
                                    <div class="layui-form-mid root-title">教育经历学校限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>
                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_education_major.character_limit';?>
                                    <div class="layui-form-mid root-title">教育经历专业限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_work_company.character_limit';?>
                                    <div class="layui-form-mid root-title">工作经历公司名称限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>
                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_work_position.character_limit';?>
                                    <div class="layui-form-mid root-title">工作经历担任职位限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_award.character_limit';?>
                                    <div class="layui-form-mid root-title">奖项证书名称限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                        <div class="config-block">
                            <div class="layui-form-item">
                                <form action="" class="layui-form">
                                    <?php $cname = 'platform.app_info.global.designer.self_introduction.character_limit';?>
                                    <div class="layui-form-mid root-title">自我介绍限制字数</div>
                                    <div class="layui-input-block input-value-block">
                                        <div class="layui-form-mid">不超过</div>
                                        @include('v1.admin.components.param_config.single_text',[
                                         'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                         ])
                                        <div class="layui-form-mid">&nbsp;个字符</div>
                                    </div>
                                    @can('platform_setting.app_info.global_setting_edit')
                                        <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                    @endcan
                                    <input type="hidden" name="param_name" value="{{$cname}}"/>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">销售商</div>
            <div class="layui-card-body">
                <div class="config-form"  action="">

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.self_photo.limit';?>
                                <div class="layui-form-mid root-title">店面形象照最大项目数量</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.name.character_limit';?>
                                <div class="layui-form-mid root-title">公司名称限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.contact_name.character_limit';?>
                                <div class="layui-form-mid root-title">联系人限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.contact_address.character_limit';?>
                                <div class="layui-form-mid root-title">联系地址限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.company_address.character_limit';?>
                                <div class="layui-form-mid root-title">公司地址限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.self_introduction.character_limit';?>
                                <div class="layui-form-mid root-title">商家介绍限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.self_promise.character_limit';?>
                                <div class="layui-form-mid root-title">服务承诺限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.self_address.character_limit';?>
                                <div class="layui-form-mid root-title">店面地址限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.seller.dealer_domain.character_limit';?>
                                <div class="layui-form-mid root-title">主页路径限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">品牌</div>
            <div class="layui-card-body">
                <div class="config-form"  action="">

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_award.limit';?>
                                <div class="layui-form-mid root-title">品牌荣誉最大项目数量</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_staff.limit';?>
                                <div class="layui-form-mid root-title">团队建设最大项目数量</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.contact_name.character_limit';?>
                                <div class="layui-form-mid root-title">联系人限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    {{--《20191102测试反馈截图汇总》第16点要求取消联系地址限制字数--}}


                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.company_address.character_limit';?>
                                <div class="layui-form-mid root-title">公司地址限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_introduction_scale.character_limit';?>
                                <div class="layui-form-mid root-title">公司规模限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_introduction_brand.character_limit';?>
                                <div class="layui-form-mid root-title">品牌理念限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_award.character_limit';?>
                                <div class="layui-form-mid root-title">品牌荣誉限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_introduction_product.character_limit';?>
                                <div class="layui-form-mid root-title">产品理念限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_introduction_service.character_limit';?>
                                <div class="layui-form-mid root-title">服务理念限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_staff.character_limit';?>
                                <div class="layui-form-mid root-title">团队建设限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.self_introduction_plan.character_limit';?>
                                <div class="layui-form-mid root-title">品牌规划限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'platform.app_info.global.brand.brand_domain.character_limit';?>
                                <div class="layui-form-mid root-title">主页路径限制字数</div>
                                <div class="layui-input-block input-value-block">
                                    <div class="layui-form-mid">不超过</div>
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                    <div class="layui-form-mid">&nbsp;个字符</div>
                                </div>
                                @can('platform_setting.app_info.global_setting_edit')
                                    <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />
                                @endcan
                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

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