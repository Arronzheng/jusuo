@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>资料审核</cite></a><span lay-separator="">/</span>
            <a><cite>设计师实名资料</cite></a><span lay-separator="">/</span>
            <a><cite>自由设计师</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

@endsection

@section('body')
    <script type="text/html" id="tableToolbarTpl">
        @can('account_manage.info_verify.designer_realname_info.free_designer_detail')
        <a onclick="openDetail('@{{d.id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
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

        form.render();

        let base_api = '{{url('/admin/platform/info_verify/designer_certification/api')}}';
        let table_api = '{{url('/admin/platform/info_verify/designer_certification/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'designer_account',width:120, title: '账号'}
                ,{field: 'realname',width:100, title: '真实姓名'}
                ,{field: 'code_idcard',width:160, title: '身份证号'}
                ,{field: 'nickname',width:120, title: '昵称'}
                ,{field: 'login_mobile',width:120, title: '注册手机号'}
                ,{field: 'genderText', title: '性别'}
                ,{field: 'local',width:160, title: '所在城市'}
                ,{field: 'designer_type_text',width:120, title: '账号类型'}
                ,{field: 'created_at',width:160, title: '注册时间'}
                ,{field: 'approve_text',width:120, title: '审核结果',fixed:'right',}
                ,{field: 'operation', title: '操作' ,width:100 ,fixed:'right',templet:'#tableToolbarTpl'}

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
                    ,is_super_admin: $('select[name="is_super_admin"]').val()
                    ,keyword: $('input[name="keyword"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='is_super_admin']").val('')
            $('input[name="keyword"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情框
        function openDetail(id){
            let page = '{{url('/admin/platform/info_verify/designer_certification')}}/'+id;
            layer.open({
                type: 2,
                title:'查看详情',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }



    </script>
@endsection
