@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>产品策略</cite></a><span lay-separator="">/</span>
            <a><cite>产品授权记录({{\App\Models\LogProductAuthorization::logTypeGroup(request()->input('log_type'))}})</cite></a>
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
                    {{--<div class="layui-inline filter-block">
                        <label class="layui-form-label">记录类型：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="log_type" lay-verify="">
                                <option value="">全部</option>
                                @foreach(\App\Models\LogProductAuthorization::logTypeGroup() as $key=>$item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>--}}
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">操作类型：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="log_type_operation" lay-verify="">
                                <option value="">全部</option>
                                @foreach(\App\Models\LogProductAuthorization::logTypeOperationGroup() as $key=>$item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >销售商名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="sn" autocomplete="off" value="{{request()->input('sn')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >产品名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="pn" autocomplete="off" value="{{request()->input('pn')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >授权内容关键词：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="param" autocomplete="off" value="{{request()->input('param')? : ''}}" placeholder="如'明星产品'" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">操作时间</label>
                        <div class="layui-input-inline">
                            <input type="text" name="date_start" id="date_start" placeholder="开始时间" value="{{request()->input('date_start')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline">
                            <input type="text" name="date_end" id="date_end" placeholder="结束时间" value="{{request()->input('date_end')? : ''}}" autocomplete="off" class="layui-input">
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
        let base_api = '{{url('/admin/brand/product/authorize/log/api')}}';
        let table_api = '{{url('/admin/brand/product/authorize/log/api')}}?log_type='+log_type;
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,limit:100
            ,limits:[100,200,500,800,1000]
            ,cols: [[ //表头
                {field: 'id',width:80, title: 'ID'}
                ,{field: 'admin_name',width:160,title: '操作管理员'}
                ,{field: 'log_type_text',width:100, title: '记录类型'}
                ,{field: 'log_type_operation_text',width:120, title: '操作类型'}
                ,{field: 'products', title: '相关产品'}
                ,{field: 'objects', title: '相关销售商'}
                ,{field: 'content', title: '授权内容'}
                ,{field: 'created_at',width:160, title: '操作时间'}
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

        function export_table(){
            var current_page = tableInstance.config.page.curr;
            var limit = tableInstance.config.page.limit;
            var total_count = tableInstance.config.page.count;
            if(current_page == undefined){
                layer.msg("请等待数据加载");return false;
            }
            $('#filter-form').find('input[name="page"]').val(current_page);
            $('#filter-form').find('input[name="limit"]').val(limit);
            $('#filter-form').attr('action',"{{url('/admin/brand/product/authorize/log/api')}}");
            $('#filter-form').submit();
        }

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,log_type: $('input[name="log_type"]').val()
                    ,log_type_operation: $('select[name="log_type_operation"]').val()
                    ,pn: $('input[name="pn"]').val()
                    ,sn: $('input[name="sn"]').val()
                    ,param: $('input[name="param"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            //$("select[name='log_type']").val('')
            $("select[name='log_type_operation']").val('')
            $('input[name="pn"]').val('')
            $('input[name="sn"]').val('')
            $('input[name="param"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }
        
        //打开授权对象框
        function openAuthorizeObject(type,id){
            //获取选中行
            var checkStatus = get_table_check_status();
            //checkStatus.data //获取选中行的数据
            var checked_data = checkStatus.data;
            var checked_ids = [];
            for(var i=0;i<checked_data.length;i++){
                checked_ids.push(checked_data[i].id);
            }
            if(checked_ids.length<=0){
                layer.msg('请选择产品！');
                layer.closeAll('loading');

                return false;
            }

            let page = '{{url('/admin/brand/product/authorize/log/object')}}';
            layer.open({
                type: 2,
                title:'设置产品价格',
                area:['900px', '600px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
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
