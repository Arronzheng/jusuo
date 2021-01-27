@extends('v1.admin_brand.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <style>
        .top-info{color:#3D82F7;}
        .top-info .info-row{font-size:14px!important;margin-bottom:15px;}
        .top-info .info-row a{font-size:14px!important;color:#3D82F7;}
    </style>
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>在线课堂</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="top-info">
            <div class="info-row">在线课堂品牌账号：{{$class_brand?$class_brand->login_account:'未开通'}}</div>
            <div class="info-row">在线课堂后台管理链接：<a target="_blank" href="http://brand.ijusuo.cn">http://brand.ijusuo.cn</a></div>
        </div>

        @can('online_class.designer_create')
        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-custom-blue" onclick="editData('add')">新增</button>
            </div>
        </div>
        @endcan

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <script type="text/html" id="tableToolbarTpl">
        @can('online_class.designer_modify_pwd')
        <a onclick="editData('edit','@{{d.id}}')" title="修改密码" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-edit"></i>
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

        let base_api = '{{url('/admin/brand/online_class/api')}}';
        let table_api = '{{url('/admin/brand/online_class/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID'}
                ,{field: 'type_text', title: '账号类型'}
                ,{field: 'login_account', title: '登录手机号'}
                ,{field: 'operation', title: '操作' ,templet:'#tableToolbarTpl'}
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

        //打开编辑框
        function editData(type,id){
            let page = '{{url('/admin/brand/online_class/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/brand/online_class')}}/'+id+"/edit";
            }
            layer.open({
                type: 2,
                title:'编辑信息',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        function ajax_status(url) {
            ajax_post(url,{}, function (res) {
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    reloadTable()
                } else {
                    layer.msg(res.msg);
                }
            });
        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
