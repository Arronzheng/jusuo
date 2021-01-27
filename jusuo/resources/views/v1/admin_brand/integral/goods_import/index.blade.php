@extends('v1.admin_brand.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>积分商城</cite></a><span lay-separator="">/</span>
            <a><cite>商品管理</cite></a><span lay-separator="">/</span>
            <a><cite>商品引入</cite></a>
            <div class="right" style="margin-right:15px;">
                @can('integral_shop.goods_manage.goods_import')
                <button onclick="location.href='{{url('/admin/brand/integral/account/recharge')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>积分预存
                </button>
                @endcan
                @can('integral_shop.goods_manage.goods_import')
                <button onclick="location.href='{{url('/admin/brand/integral/account/recharge/log')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>预存记录
                </button>
                @endcan
            </div>
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
                        <label class="layui-form-label">所属品牌</label>
                        <div class="layui-input-inline">
                            <input type="text" autocomplete="off" id="brand-select"  value="" readonly placeholder="点击搜索并选择" class="layui-input">
                            <input type="hidden" name="bi" value="" id="h-belong-brand">
                        </div>
                    </div>--}}
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">分类</label>
                        <div class="layui-input-inline">
                            <select name="cid" >
                                <option value="0">无</option>
                                @foreach($categories as $item)
                                    <option value="{{$item->id}}" @if($item->pid==0) disabled @endif @if(isset($data) && $data->category_id_2 == $item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">所属品牌</label>
                        <div class="layui-input-inline">
                            <select name="bi" >
                                <option value="0">无</option>
                                @foreach($brands as $item)
                                    <option value="{{$item->id}}" @if(isset($data) && $data->brand_id == $item->id) @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">积分区间</label>
                        <div class="layui-input-inline" style="width:100px">
                            <input type="text" name="integral_start" placeholder="最小积分" value="<?php echo e(request()->input('area_start')? : ''); ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline" style="width:100px">
                            <input type="text" name="integral_end" placeholder="最大积分" value="<?php echo e(request()->input('area_end')? : ''); ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >标题：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" autocomplete="off" value="{{request()->input('keyword')? : ''}}" placeholder="" class="layui-input">
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

        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-primary" onclick="export_table()">导出</button>

            </div>
        </div>


        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <script type="text/html" id="tableToolbarTpl">

        {{--引入/取消引入相关--}}
        @can('integral_shop.goods_manage.goods_import')

        @{{#     if( d.is_import ){       }}
        <a href="javascript:;" title="取消引入" onclick="ajax_status('@{{d.changeImportApiUrl}}')" class="layui-btn-warm layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-close"></i>
        </a>
        @{{#     }else{     }}
        <a href="javascript:;" title="引入" onclick="ajax_status('@{{d.changeImportApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-ok"></i>
        </a>
        @{{#     }     }}
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


        /*表格选择控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend')}}/'
            });
            layui.use(['jquery','tableSelect'], function () {
                var tableSelect = layui.tableSelect;
                tableSelect.render({
                    elem: '#brand-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '品牌关键词搜索',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'admin/brand/info_statistics/album/api/get_brands') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'name', title: '公司名称' },
                            { field: 'brand_name', title: '品牌名称' },
                            { field: 'organization_account', title: '组织账号' },
                            { field: 'contact_name', title: '联系人' },
                            { field: 'contact_telephone', title: '联系电话' },
                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-belong-brand').val(0);
                            $('#brand-select').val('');

                        }
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['brand_name'];
                            $('#h-belong-brand').val(privilege_id);
                            $('#brand-select').val(display_name);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/


        form.render();

        let base_api = '{{url('/admin/brand/integral/goods_import/api')}}';
        let table_api = '{{url('/admin/brand/integral/goods_import/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {checkbox: true, fixed: 'left'}
                ,{field: 'id', title: 'ID', width:70,fixed: 'left'}
                ,{field: 'photo',width:100, title: '商品图',templet: function(d){
                    return '<a target="_blank" href="'+d.photo+'"><img height="30" src="'+d.photo+'"/></a>'
                }}
                ,{field: 'name',width:120, title: '名称',templet: function(d){
                    return '<a target="_blank" href="../../../mall/detail/'+d.web_id_code+'" style="color:cornflowerblue">'+d.name+'</a>'
                }}
                ,{field: 'market_price',width:80, title: '市场价'}
                ,{field: 'integral',width:80, title: '积分'}
                ,{field: 'brand_name', title: '品牌'}
                ,{field: 'category_text', title: '分类'}
                ,{field: 'status_text',width:80, title: '状态'}
                ,{field: 'sort',width:80, title: '排序'}
                //,{field: 'created_at', width:150, title: '添加时间'}
                ,{field: 'exchange_amount',width:80, title: '兑换量'}
                ,{field: 'operation', title: '操作' ,fixed:'right', width:170,templet:'#tableToolbarTpl'}
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
                    ,keyword: $('input[name="keyword"]').val()
                    ,bi: $('select[name="bi"]').val()
                    ,cid: $('select[name="cid"]').val()
                    ,integral_start: $('input[name="integral_start"]').val()
                    ,integral_end: $('input[name="integral_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $('input[name="keyword"]').val('')
            $('select[name="bi"]').val('')
            $('select[name="cid"]').val('')
            $('input[name="integral_start"]').val('')
            $('input[name="integral_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开编辑框
        function editData(type,id){
            let page = '{{url('/admin/brand/integral/goods_import/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/brand/integral/goods')}}/'+id+"/edit";
            }
            layer.open({
                type: 2,
                title:'编辑信息',
                area:['100%', '100%'],
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

        //删除数据
        function destroyData(id){
            let apiUrl = '{{url('/admin/brand/integral/goods_import/api')}}/'+id;
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                ajax_post(apiUrl,{
                    '_method':'DELETE',
                    'id':id,
                },function(result){
                    if(result.status){
                        layer.msg('删除成功！')
                        reloadTable()
                    }else{
                        layer.msg(result.msg)
                    }
                });

                layer.close(index);
            });

        }

        function export_table(){
            var current_page = tableInstance.config.page.curr;
            var limit = tableInstance.config.page.limit;
            var total_count = tableInstance.config.page.count;
            if(current_page == undefined){
                layer.msg("请等待数据加载");return false;
            }
            $('#filter-form').find('input[name="page"]').val(current_page);
            $('#filter-form').find('input[name="limit"]').val(limit);
            $('#filter-form').attr('action',"{{url('/admin/brand/integral/goods_import/api')}}");
            $('#filter-form').submit();
        }

    </script>

    {{--授权专用script--}}
    <script>
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
                layer.msg('请选择商品！');
                layer.closeAll('loading');

                return false;
            }

            let page = '{{url('/admin/brand/integral/authorize/show/object')}}';
            layer.open({
                type: 2,
                title:'开放/取消开放给品牌',
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

    </script>
@endsection
