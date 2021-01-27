@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>积分商城</cite></a><span lay-separator="">/</span>
            <a><cite>品牌账号</cite></a><span lay-separator="">/</span>
            <a><cite>品牌积分</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div id="filterContainer" class="layui-form c-table-filter-container fold" lay-filter="app-content-list">
            <div id="fold-spread">
                <div class="fold hidden">收起 ▲</div>
                <div class="spread">展开 ▼</div>
            </div>
            <form id="filter-form" action="" target="_blank" method="get">
                <input type="hidden" name="page" value="0"/>
                <input type="hidden" name="limit" value="0"/>
                <input type="hidden" name="export" value="1"/>
                <div class="layui-form-item">
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >品牌名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="kw" autocomplete="off" value="{{request()->input('kw')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-inline filter-block">
                        <button type="button" onclick="reloadTable()" class="layui-btn  layui-btn-custom-blue layuiadmin-btn-list" >
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                        <button type="button" onclick="resetFilter()" class="layui-btn layui-btn-primary">重置</button>
                    </div>

                    <input type="hidden" name="log_type" value="{{request()->input('log_type')}}"/>

                    <hr/>
                    <div style="padding-top:13px;">


                    </div>


                </div>
            </form>

        </div>

        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-primary" onclick="export_table()">导出</button>
            </div>
        </div>

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <script type="text/html" id="tableToolbarTpl">

       

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

        laydate.render({
            elem: '#date_start',
            value:'{{request()->input('date_start') ? request()->input('date_start') :''}}'
        });

        laydate.render({
            elem: '#date_end',
            value:'{{request()->input('date_end') ? request()->input('date_end') :''}}'

        });

        form.render();
        let log_type = '{{request()->input('log_type')}}';
        let base_api = '{{url('/admin/platform/integral/brand_account/api/integral_list')}}';
        let table_api = '{{url('/admin/platform/integral/brand_account/api/integral_list')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,limit:100
            ,limits:[100,200,500,800,1000]
            ,cols: [[ //表头
                {field: 'id', title: 'ID'}
                ,{field: 'brand_name',title: '品牌名称'}
                ,{field: 'point_money', title: '当前积分'}
                ,{field: 'total_buy', title: '累计购买积分',templet: function(d){
                    return '<a style="color:#1582FF" href="javascript:;" onclick="show_recharge(\''+d.id+'\')">'+d.total_buy+'</a>'
                }}
                ,{field: 'last_buy_time', title: '最后一次购买时间'}
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


        function show_recharge(brand_id){
            let page = '';
            layer.open({
                type: 2,
                title:'预存记录',
                area:['800px', '550px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:'/admin/platform/integral/brand_account/recharge_list?w=1&b='+brand_id,
                success:function(){

                }
            });
        }

    </script>

    {{--页面方法专用script--}}
    <script>

        function export_table(){
            var current_page = tableInstance.config.page.curr;
            var limit = tableInstance.config.page.limit;
            var total_count = tableInstance.config.page.count;
            if(current_page == undefined){
                layer.msg("请等待数据加载");return false;
            }
            $('#filter-form').find('input[name="page"]').val(current_page);
            $('#filter-form').find('input[name="limit"]').val(limit);
            $('#filter-form').attr('action',"{{url('/admin/platform/integral/brand_account/api/integral_list')}}");
            $('#filter-form').submit();
        }

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
