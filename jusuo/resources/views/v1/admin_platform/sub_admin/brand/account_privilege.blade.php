@extends('v1.admin.components.layout.blank_body',[])
@section('content')

    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" >
        <form>
            <div id="LAY-auth-tree-index" style="padding:20px;">
                <span id="initBlock" style="line-height:36px;">正在加载...</span>
            </div>
        </form>

    </div>

@endsection

@section('script')

    <script>
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend')}}/'
            }).extend({
                authtree: 'authtree',
            });
            layui.use(['jquery', 'authtree', 'form', 'layer'], function () {
                var $ = layui.jquery;
                var authtree = layui.authtree;
                var form = layui.form;
                var layer = layui.layer;
                // 一般来说，权限数据是异步传递过来的
                $.ajax({
                    url: '{{url($url_prefix.'admin/platform/sub_admin/api/account/get_admin_privilege/'.$data->id)}}',
                    dataType: 'json',
                    data: {},
                    success: function(res){
                        if(res.data.list.length>0){
                            var trees = authtree.listConvert(res.data.list, {
                                primaryKey: 'id'
                                ,startPid: 0
                                ,parentKey: 'parent_id'
                                ,nameKey: 'display_name'
                                ,valueKey: 'id'
                                ,checkedKey: res.data.checkedId

                            });
                            // 如果后台返回的不是树结构，请使用 authtree.listConvert 转换
                            authtree.render('#LAY-auth-tree-index', trees, {
                                inputname: 'privileges[]',
                                layfilter: 'lay-check-auth',
                                autowidth: true,
                                openall  : true,
                                hidechoose:true
                            });
                        }else{
                            $('#initBlock').html('暂无权限');
                        }

                    }
                });
            });
        });
    </script>

@endsection