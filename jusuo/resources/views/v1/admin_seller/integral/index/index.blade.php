@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
    'js'=>['/v1/js/admin/module/table.js']
    ])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a lay-href="">积分商城</a><span lay-separator="">/</span>
            <a><cite>积分变动列表</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>


    <script type="text/html" id="tableToolbarTpl">
        <a onclick="editData('edit','@{{d.id}}')" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-edit"></i>编辑
        </a>
        <a onclick="destroyData('@{{d.id}}')" href="javascript:;"  class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-close"></i>删除
        </a>
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

        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: '{{url('/admin/seller/integral/index/api')}}' //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: '序号', width:80}
                ,{field: 'created_at', title: '收入时间'}
                ,{field: 'integral', title: '收入积分金额'}
                ,{field: 'available_integral', title: '收入后积分余额'}
                ,{field: 'remark', title: '备注'}
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
                url: '{{url('/admin/test/api')}}'
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,status: $('select[name="status"]').val()
                    ,keyword: $('input[name="keyword"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='status']").val('')
            $('input[name="keyword"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
