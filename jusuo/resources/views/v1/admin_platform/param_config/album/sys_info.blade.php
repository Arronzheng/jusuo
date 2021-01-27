@extends('v1.admin_platform.layout',[])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>平台设置</cite></a><span lay-separator="">/</span>
            <a><cite>方案相关</cite></a><span lay-separator="">/</span>
            <a><cite>系统信息</cite></a>
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
                    <td>关注度 = </td>
                    <td>
                        <form>
                            <?php $cname = 'platform.album.sys_info.focus.cal_rule';?>

                            {{--<div class="layui-inline">
                                <div class="layui-form-mid">（直属+旗下销售商）的设计师的方案关注度 × </div>
                                <div class="layui-input-inline short-input-inline" >
                                    <input type="text" name="ratio" value="{{isset($param['configs'][$cname])?$param['configs'][$cname]['ratio']:''}}" placeholder="N" autocomplete="off" class="layui-input">
                                </div>
                                <input type="hidden" name="param_name" value="{{$cname}}">

                            </div>--}}

                                <div class="layui-inline">
                                    <div class="layui-form-mid">&nbsp;浏览量 × </div>
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="read_ratio" value="{{isset($param['configs'][$cname])?$param['configs'][$cname]['read_ratio']:''}}" placeholder="A" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp;+ 点赞量 × </div>
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="like_ratio" value="{{isset($param['configs'][$cname])?$param['configs'][$cname]['like_ratio']:''}}" placeholder="B" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp;+ 收藏量 × </div>
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="collection_ratio" value="{{isset($param['configs'][$cname])?$param['configs'][$cname]['collection_ratio']:''}}" placeholder="C" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp;+ 分享次数 × </div>
                                    <div class="layui-input-inline short-input-inline" >
                                        <input type="text" name="share_ratio" value="{{isset($param['configs'][$cname])?$param['configs'][$cname]['share_ratio']:''}}" placeholder="D" autocomplete="off" class="layui-input">
                                    </div>
                                    <div class="layui-form-mid">&nbsp; </div>
                                    <input type="hidden" name="param_name" value="{{$cname}}">

                                </div>
                        </form>
                    </td>
                    <td>
                        @can('platform_setting.album.sys_info_edit')
                            <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                        @endcan
                    </td>
                </tr>

                </tbody>
            </table>

        </div>

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