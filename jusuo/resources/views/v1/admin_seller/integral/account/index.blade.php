@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
    'js'=>['/v1/js/admin/module/table.js']
    ])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a lay-href="">积分商城</a><span lay-separator="">/</span>
            <a><cite>设计师积分账户</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div id="filterContainer" class="layui-form c-table-filter-container fold" lay-filter="app-content-list">
            <div id="fold-spread">
                <div class="fold hidden">收起 ▲</div>
                <div class="spread">展开 ▼</div>
            </div>
            <form action="" method="get">
                <div class="layui-form-item">
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:150px">昵称/手机号：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="ln" autocomplete="off" value="{{request()->input('ln')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">真实姓名：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="rn" autocomplete="off" value="{{request()->input('rn')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">创建时间</label>
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
                </div>
            </form>

        </div>
    </div>
    <div class="layui-fluid">

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line" style="margin-top:-30px;"></table>

    </div>


    <script type="text/html" id="tableToolbarTpl">
        @can('account_manage.account_index.designer_integral_adjust')
        <a onclick="modifyIntegral('@{{d.id}}')" title="发放积分" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-rmb"></i>
        </a>
        @endcan
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

        let table_api = '{{url('/admin/seller/integral/account/api')}}?type={{request()->input("type")}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,cols: [[ //表头
                {field: 'id', title: 'ID', fixed: 'left'}
                ,{field: 'nickname',width:120, title: '昵称'}
                ,{field: 'realname',width:100, title: '真实姓名'}
                ,{field: 'login_mobile',width:120, title: '注册手机号'}
                ,{field: 'area_belong',width:160,title: '所在城市'}
                ,{field: 'point_money',width:80,title: '积分',templet: function(d){
                    return '<a style="color:#1582FF" href="javascript:;" onclick="show_integral_log('+d.id+')">'+d.point_money+'</a>'
                }}
                ,{field: 'exchange_count',width:100,title: '兑换礼品数' ,templet: function(d){
                    return '<a style="color:#1582FF" href="javascript:;" onclick="show_exchange_log('+d.id+')">'+d.exchange_count+'</a>'
                 }}
                ,{field: 'charge_count',width:100,title: '充值次数' ,templet: function(d){
                    return '<a style="color:#1582FF" href="javascript:;" onclick="show_charge_log('+d.id+')">'+d.charge_count+'</a>'
                }}
                ,{field: 'created_at',width:160, title: '注册时间'}
                ,{field: 'approve_info',width:160, title: '认证时间'}
                ,{field: 'status_text',width:120, fixed:'right', title: '账号状态'}
                ,{field: 'operation',title: '操作' ,width:200,fixed:'right',templet:'#tableToolbarTpl'}
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
                    ,ln: $('input[name="ln"]').val()
                    ,rn: $('input[name="rn"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("input[name='ln']").val('')
            $('input[name="rn"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开修改积分框
        function modifyIntegral(id){
            let page = '{{url('/admin/seller/seller_designer/account')}}/'+id+"/modify_integral";
            layer.open({
                type: 2,
                title:'发放积分',
                area:['700px', '300px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开积分变动明细表
        function show_integral_log(id){
            let page = '{{url('/admin/seller/seller_designer/integral_log')}}/'+id;
            layer.open({
                type: 2,
                title:'积分变动明细',
                area:['900px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开兑换记录表
        function show_exchange_log(id){
            let page = '{{url('/admin/seller/seller_designer/exchange_log')}}/'+id;
            layer.open({
                type: 2,
                title:'兑换记录',
                area:['900px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开充值记录表
        function show_charge_log(id){
            let page = '{{url('/admin/seller/seller_designer/charge_log')}}/'+id;
            layer.open({
                type: 2,
                title:'充值记录',
                area:['900px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
