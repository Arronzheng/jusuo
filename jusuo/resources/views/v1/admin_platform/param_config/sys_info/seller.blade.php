@extends('v1.admin_platform.layout',[])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>平台设置</cite></a><span lay-separator="">/</span>
            <a><cite>系统信息</cite></a><span lay-separator="">/</span>
            <a><cite>销售商</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="config-table">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>项目类型</th>
                    <th>设置</th>
                    <th>设置</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>销售商关注度 = </td>
                    <td>
                        <form>
                            <?php $cname = 'platform.sys_info.seller.focus.cal_rule';?>

                                <div class="layui-inline">
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="days" value="{{isset($param['configs'][$cname]['days'])?$param['configs'][$cname]['days']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp;天内的（ 直属设计师上传方案量 × </div>
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="designer_album_sum" value="{{isset($param['configs'][$cname]['designer_album_sum'])?$param['configs'][$cname]['designer_album_sum']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp;+ 设计师主页浏览量 × </div>
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="index_read_count" value="{{isset($param['configs'][$cname]['index_read_count'])?$param['configs'][$cname]['index_read_count']:''}}" placeholder="Z" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp;） </div>
                                    <input type="hidden" name="param_name" value="{{$cname}}">

                                </div>
                        </form>
                    </td>
                    <td>
                        @can('platform_setting.system_info.designer_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </td>
                </tr>

                </tbody>
            </table>

        </div>

    </div>
    <div class="layui-tab layui-tab-brief"  lay-filter="docDemoTabBrief">
        {{--@include('v1.admin.platform.param_config.formula.components.tab_header',
       ['index'=>'designer'])--}}



    </div>

@endsection

@section('script')
    <script>
        function submit_config_form(obj,submit_type){
            var form_no_name = $(obj).parents('tr').find('form :not("input[name=\'param_name\']")');
            var form = $(obj).parents('tr').find('form');
            var param_name = form.find('input[name="param_name"]').val();
            var data = form_no_name.serializeArray();
            ajax_post('{!! url($url_prefix.'admin/api/param_config/update') !!}', {
                param_value:data,
                param_name:param_name,
                submit_type:submit_type,
            }, function(result){
                layer.msg(result.msg);
            });
        }

    </script>
@endsection