<?php
$layout_name = 'v1.admin.components.layout.blank_body';
$css = ['/v1/css/admin/module/table.css'];
$js = ['/v1/css/admin/module/table.js'];
?>
@extends($layout_name,[
   'css'=>$css,
   'js'=>$js
])

@section('content')

    <div class="layui-fluid">


        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>



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

        let designer_id = '{{$id}}';
        let base_api = '{{url('/admin/seller/seller_designer/api/exchange_log')}}/'+designer_id;
        let table_api = '{{url('/admin/seller/seller_designer/api/exchange_log')}}/'+designer_id;
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
