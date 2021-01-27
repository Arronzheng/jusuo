@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css','/plugins/layui-extend/dropdown/dropdown.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>产品管理</cite></a><span lay-separator="">/</span>
            <a><cite>产品审核</cite></a><span lay-separator="">/</span>
            <a><cite>已驳回</cite></a>
            <div class="right" style="margin-right:15px;">
                @can('product_manage.product_verify')
                <div class="layui-dropdown" id="example1">
                    <!-- 触发器 -->
                    <button class="layui-btn layui-btn-sm layui-dropdown-toggle layui-btn-custom-blue">
                        <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>
                        产品审核
                        <i class="layui-icon layui-icon-triangle-d"></i>
                    </button>
                    <!-- 下拉框 -->
                    <div class="layui-dropdown-menu">
                        <div >
                            @can('product_manage.product_verify.wait_verify_index')
                            <a href="{{url('/admin/brand/product/verify/wait')}}" class="menu-item" >待审核</a>
                            @endcan
                            @can('product_manage.product_verify.pass_verify_index')
                            <a href="{{url('/admin/brand/product/verify/pass')}}" class="menu-item" >已通过</a>
                            @endcan
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
    <div class="layui-fluid">


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

        /*下拉dropdown控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend/dropdown')}}/'
            }).extend({
                dropdown: 'dropdown'
            }).use(['dropdown'], function(){
                var dropdown = layui.dropdown;
                var config = {} // 参见说明文档参数配置

                //执行实例
                var instance = dropdown.render(config); // 返回实例，可用于后续处理（如绑定事件）

                // 模拟当弹窗出现调用的方法
                instance.on(instance.ON_SHOW, function(){
                    // your code
                })

            });
        });
        /*下拉dropdown控件*/

        form.render();

        let base_api = '{{url('/admin/brand/product/verify/reject/api')}}';
        let table_api = '{{url('/admin/brand/product/verify/reject/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: '产品id', fixed: 'left'}
                ,{field: 'name',title: '名称'}
                ,{field: 'created_at', title: '提交审核时间'}
                ,{field: 'created_by', title: '提交人'}
                ,{field: 'remark',title: '驳回理由'}
                ,{field: 'updated_at', title: '驳回时间'}
                ,{field: 'approved_by', title: '审核人'}
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
                    ,ac: $('select[name="ac"]').val()
                    ,tc: $('select[name="tc"]').val()
                    ,clr: $('select[name="clr"]').val()
                    ,spec: $('select[name="spec"]').val()
                    ,status: $('select[name="status"]').val()
                    ,vstatus: $('select[name="vstatus"]').val()
                    ,keyword: $('input[name="keyword"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='ac']").val('')
            $("select[name='tc']").val('')
            $("select[name='clr']").val('')
            $("select[name='spec']").val('')
            $("select[name='status']").val('')
            $("select[name='vstatus']").val('')
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
