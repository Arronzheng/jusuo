@extends('v1.admin.components.layout.blank_body',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
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
        let brand_id = '{{$id}}';
        let base_api = '{{url('/admin/seller/seller_designer/api/integral_log')}}';
        let table_api = '{{url('/admin/seller/seller_designer/api/integral_log')}}/'+brand_id;
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,limit:10
            ,limits:[10,20,50,80,100]
            ,cols: [[ //表头
                {field: 'id', title: 'ID'}
                ,{field: 'type_text',title: '类型'}
                ,{field: 'integral', title: '变动值'}
                ,{field: 'available_integral', title: '变动后余额'}
                ,{field: 'remark', title: '变动说明'}
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
                    ,kw: $('input[name="kw"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            //$("select[name='log_type']").val('')
            $('input[name="kw"]').val('')
            form.render();
            reloadTable()
        }


        function get_table_check_status(){
            var checkStatus = table.checkStatus('tableInstance');
            return checkStatus;
        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
