@extends('v1.admin.components.layout.blank_body',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])


@section('content')
    <div class="layui-fluid">
        <div id="filterContainer" class="layui-form c-table-filter-container fold" lay-filter="app-content-list">
            <div id="fold-spread">
                <div class="fold hidden">收起 ▲</div>
                <div class="spread">展开 ▼</div>
            </div>
            <form action="" method="get">
                <div class="layui-form-item">

                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">品牌/公司名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" autocomplete="off" value="{{request()->input('keyword')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    {{--<div class="layui-inline filter-block">
                        <label class="layui-form-label">创建时间</label>
                        <div class="layui-input-inline">
                            <input type="text" name="date_start" id="date_start" placeholder="开始时间" value="{{request()->input('date_start')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline">
                            <input type="text" name="date_end" id="date_end" placeholder="结束时间" value="{{request()->input('date_end')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                    </div>--}}
                    <div class="layui-inline filter-block">
                        <button type="button" onclick="reloadTable()" class="layui-btn  layui-btn-custom-blue layuiadmin-btn-list" >
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                        <button type="button" onclick="resetFilter()" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>

        </div>

        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-custom-blue" onclick="authorize('authorize')">开放</button>
                <button  class="layui-btn layui-btn-custom-blue" onclick="authorize('cancel')">取消开放</button>
            </div>
        </div>

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

@endsection

@section('body')
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


        let base_api = '{{url('/admin/platform/integral/authorize/show/object/api')}}';
        let table_api = '{{url('/admin/platform/integral/authorize/show/object/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,limit:100
            ,limits:[100,200,500,800,1000]
            ,cols: [[ //表头
                {checkbox: true, fixed: 'left'},
                {field: 'id', title: 'ID', width:80},
                { field: 'name', title: '公司名称' },
                { field: 'brand_name', title: '品牌名称' },
                { field: 'organization_account', title: '组织账号' },
                { field: 'contact_name', title: '联系人' },
                { field: 'contact_telephone', title: '联系电话' },

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

        function authorize(operation) {
            //显示loading
            layer.load(1);
            //获取销售商选中行
            var checkStatus = table.checkStatus('tableInstance');
            //checkStatus.data //获取选中行的数据
            var object_data = checkStatus.data;
            var object_ids = [];
            for(var i=0;i<object_data.length;i++){
                object_ids.push(object_data[i].id);
            }
            if(object_ids.length<=0){
                layer.msg('请选择品牌！');
                layer.closeAll('loading');
                return false;
            }
            //获取已选择的产品
            var goods_ids = [];
            var product_check_status = parent.get_table_check_status();
            var product_data = product_check_status.data;
            for(var i=0;i<product_data.length;i++){
                goods_ids.push(product_data[i].id);
            }
            if(goods_ids.length<=0){
                layer.msg('请选择产品！');
                layer.closeAll('loading');
                return false;
            }
            ajax_post('{{url('admin/platform/integral/authorize/show/object/api/authorize_object')}}',
                {pids:goods_ids,oids:object_ids,op:operation}, function (res) {
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    reloadTable()
                } else {
                    layer.msg(res.msg);
                    layer.closeAll('loading');

                }
            });
        }

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
                    ,keyword: $('input[name="keyword"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $('input[name="keyword"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }


    </script>
@endsection
