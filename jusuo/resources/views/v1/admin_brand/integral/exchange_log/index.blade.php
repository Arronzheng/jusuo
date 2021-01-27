<?php
$show_in_window = request()->input('w');
$layout_name = 'v1.admin_platform.layout';
$css = ['/v1/css/admin/module/table.css'];
$js = ['/v1/css/admin/module/table.js'];
if($show_in_window == 1){
    $layout_name = 'v1.admin.components.layout.blank_body';
}
?>
@extends($layout_name,[
   'css'=>$css,
   'js'=>$js
])

@section('content')
    @if(!$show_in_window)
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>积分商城</cite></a><span lay-separator="">/</span>
            <a><cite>兑换列表</cite></a>
        </div>
    </div>
    @endif
    <div class="layui-fluid">
        @if(!$show_in_window)
        <form id="filter-form" action="" target="_blank" method="get">
            <input type="hidden" name="page" value="0"/>
            <input type="hidden" name="limit" value="0"/>
            <input type="hidden" name="export" value="1"/>
        </form>

        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-primary" onclick="export_table()">导出</button>
            </div>
        </div>

        @endif

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <script type="text/html" id="tableToolbarTpl">

        {{--状态相关--}}
        @{{#     if( d.can_handle ){       }}
        @can('integral_shop.exchange_log_index.reject')
        <a href="javascript:;" title="拒绝" onclick="reject_exchange('@{{d.rejectApiUrl}}')" class="layui-btn-warm layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-close"></i>拒绝
        </a>
        @endcan
        @can('integral_shop.exchange_log_index.send')
        <a onclick="send_exchange('@{{d.id}}')" title="发货" href="javascript:;"  class="layui-btn-success layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-ok"></i>发货
        </a>
        @endcan
        @{{#     }     }}

    </script>

@endsection

@section('script')
    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        layui.element.init();
        var form = layui.form;
        var laydate = layui.laydate;


        form.render();

        let designer_id = '{{request()->input('d',0)}}';
        let base_api = '{{url('/admin/brand/integral/exchange_log/api')}}?d='+designer_id;
        let table_api = '{{url('/admin/brand/integral/exchange_log/api')}}?d='+designer_id;
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID',width:80, fixed: 'left'}
                ,{field: 'nickname',width:100, title: '昵称'}
                ,{field: 'realname',width:80, title: '真实姓名'}
                ,{field: 'good_name',width:120, title: '商品'}
                ,{field: 'count',width:80, title: '数量'}
                ,{field: 'total',width:80, title: '消耗积分'}
                ,{field: 'receiver_name',width:80, title: '收货人'}
                ,{field: 'receiver_tel',width:110, title: '收货电话'}
                ,{field: 'full_address',width:200, title: '地址'}
                ,{field: 'status_text',width:80, title: '状态'}
                ,{field: 'created_at',width:160, title: '兑换时间'}
                ,{field: 'sent_at',width:160, title: '发货时间'}
                @if(!$show_in_window)
                ,{field: 'operation', title: '操作' ,fixed:'right', width:150,templet:'#tableToolbarTpl'}
                @endif
            ]]
        });

        //监听排序事件
        table.on('sort(tableFilter)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            console.log(obj.field); //当前排序的字段名
            console.log(obj.type); //当前排序类型：desc（降序）、asc（升序）、null（空对象，默认排序）
            console.log(this); //当前排序的 th 对象

            //尽管我们的 table 自带排序功能，但并没有请求服务端。
            //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
            reloadTable(obj)

        });

    </script>

    {{--页面方法专用script--}}
    <script>

        //导出
        function export_table(){
            var current_page = tableInstance.config.page.curr;
            var limit = tableInstance.config.page.limit;
            var total_count = tableInstance.config.page.count;
            if(current_page == undefined){
                layer.msg("请等待数据加载");return false;
            }
            $('#filter-form').find('input[name="page"]').val(current_page);
            $('#filter-form').find('input[name="limit"]').val(limit);
            $('#filter-form').attr('action',"{{url('/admin/brand/integral/exchange_log/api')}}");
            $('#filter-form').submit();
        }

        //发货
        function send_exchange(id){
            let page = '{{url('/admin/brand/integral/exchange_log')}}/'+id+"/send";

            layer.open({
                type: 2,
                title:'填写发货信息',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //拒绝兑换
        function reject_exchange(url){

            layer.confirm('确定拒绝兑换？', {icon: 3, title:'提示'}, function(index){
                //do something
                layer.prompt({
                    formType: 2,
                    value: '',
                    title: '填写拒绝理由',
                    area: ['400px', '250px'] //自定义文本域宽高
                },function(val, index1){
                    ajax_post(url,{remark:val}, function (result ) {
                        if (result.status == 1) {
                            layer.msg('操作成功');
                            layer.close(index1);
                            layer.close(index);
                            reloadTable()
                        } else {
                            //将提交按钮恢复
                            $('.layui-btn').attr('disabled',false);
                            layer.msg(result.msg);
                        }
                    });

                });

            });
        }

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    /*,is_super_admin: $('select[name="is_super_admin"]').val()
                    ,keyword: $('input[name="keyword"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()*/
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            /*$("select[name='is_super_admin']").val('')
            $('input[name="keyword"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')*/
            form.render();
            reloadTable()
        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
