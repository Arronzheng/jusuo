@extends('v1.site.center.components.blank_body',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <style>
        .main-content{background-color:#ffffff}
        .layui-table, .layui-table-view{margin:0;}
        .cst-form-label{width:auto;font-size:16px;padding-left:0}
        .layui-laypage{float:left;}
        .layui-table-page{
            position: relative;
            width: 100%;
            padding: 27px 7px 0;
            height: 80px;
            font-size: 12px;
            white-space: nowrap;
        }
        .table-container{position:relative;}
        .pager{width:50%;float:left;}
        .bottom-container {overflow:hidden;}
        .bottom-container .op-btn:hover{cursor:pointer;}
        .bottom-container .op-btn{
            text-align:center;margin-top:22px;
            width:176px;
            height:44px;
            border-radius:4px;
            border:1px solid rgba(21,130,255,1);
            font-size:16px;color:#1582FF;line-height:44px;
            float:right;
        }
        .bottom-container .op-btn.highlight{
            color:#ffffff;background-color:#1582FF;
        }
        .search-btn{
            width:138px;
            height:38px;
            background:rgba(21,130,255,1);
            border-radius:4px;font-size:16px;color:#ffffff;text-align:center;line-height:38px;
        }
    </style>

    <div class="main-content" style="padding:20px;">
        <div class="filter-content">

            <form class="layui-form" id="filter-form" action="">
                <div class="layui-form-item" style="padding:10px 0 16px 0;margin:0;">

                    <div class="layui-inline">
                        <label class="layui-form-label cst-form-label" >名称</label>
                        <div class="layui-input-inline" style="width: 200px;">
                            <input type="text" name="name" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label cst-form-label" >类型</label>
                        <div class="layui-input-inline" style="width: 200px;">
                            <select name="type">
                                <option value="">全部</option>
                                @foreach(\App\Models\ProductCeramic::typeGroup() as $key=>$item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label cst-form-label" >产品编号</label>
                        <div class="layui-input-inline" style="width: 200px;">
                            <input type="text" name="code" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">
                        <button type="button" onclick="search()" class="layui-btn search-btn" >搜索</button>
                    </div>

                    <input type="hidden" name="page"/>

                </div>
            </form>

        </div>

        <div class="table-container">
            <table class="layui-table" onclick="close_window()" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

        </div>

        <div class="bottom-container">
            <div id="pager" class="pager"></div>
            <div class="op-btn highlight" onclick="choose_product()">选择产品</div>
            <div class="op-btn" style="margin-right:30px;">取消</div>
        </div>

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

        var init_pager = false
        let base_api = '{{url('/center/album/api/ajax_get_product')}}';
        let table_api = '{{url('/center/album/api/ajax_get_product')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: false //开启分页
            ,cols: [[ //表头
                {checkbox: true}
                ,{field: 'name', title: '名称'}
                ,{field: 'type_text', title: '类型'}
                ,{field: 'code', title: '产品编号'}
                ,{field: 'spec_text', title: '规格'}
                ,{field: 'photo_cover', title: '缩略图'}

            ]]
            ,done: function(res, curr, count){
                //如果是异步请求数据方式，res即为你接口返回的信息。
                //如果是直接赋值的方式，res即为：{data: [], count: 99} data为当前页数据、count为数据总长度
                console.log(res);
                //得到当前页码
                console.log(curr);
                //得到数据总量
                console.log(count);

                //每页显示数
                var limit = this.limit;
                var laypage = layui.laypage

                //调用分页 完整功能
                laypage.render({
                    elem: 'pager'
                    ,count: res['count']
                    ,limit: limit
                    ,curr: res['curr']
                    ,layout: ['count','prev', 'page', 'next']
                    ,jump: function(obj,first){
                        //首次不执行
                        if(!first){
                            $('#filter-form input[name=page]').val(obj.curr);
                            reloadTable()
                        }
                    }
                });

                init_pager = true;

            }
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

        function close_window(){
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭
        }

        function choose_product(){
            var checkStatus = table.checkStatus('tableInstance');
            var check_length = checkStatus.data.length
            if(check_length<=0){
                layer.msg('请选择产品！')
                return false;
            }
            parent.choose_product(checkStatus.data);
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭
        }

        //提交搜索
        function search(){
            $('#filter-form input[name=page]').val(1);
            reloadTable()
        }

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,name: $('input[name="name"]').val()
                    ,type: $('select[name="type"]').val()
                    ,code: $('input[name="code"]').val()
                    ,page: $('input[name="page"]').val()
                }
            });
        }

    </script>
@endsection
