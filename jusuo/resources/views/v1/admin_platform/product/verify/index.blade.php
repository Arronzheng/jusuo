@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>产品管理</cite></a><span lay-separator="">/</span>
            <a><cite>产品审核</cite></a>
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
                        <label class="layui-form-label">品类：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="pc" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['product_categories'] as $item)
                                    <option value="{{$item->id}}"  @if(request()->input('pc')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">品牌</label>
                        <div class="layui-input-inline">
                            <input type="text" autocomplete="off" id="brand-select"  value="" readonly placeholder="点击搜索并选择" class="layui-input">
                            <input type="hidden" name="bi" value="" id="h-belong-brand">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                         <label class="layui-form-label">应用类别：</label>
                         <div class="layui-input-inline" style="padding-right: 14px">
                             <select name="ac" lay-verify="">
                                 <option value="">全部</option>
                                 @foreach($vdata['apply_categories'] as $item)
                                     <option value="{{$item->id}}"  @if(request()->input('ac')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">工艺类别：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="tc" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['technology_categories'] as $item)
                                    <option value="{{$item->id}}"  @if(request()->input('tc')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">色系：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="clr" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['colors'] as $item)
                                    <option value="{{$item->id}}"  @if(request()->input('clr')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">产品名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="pn" autocomplete="off" value="{{request()->input('pn')? : ''}}" placeholder="" class="layui-input">
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

    @can('product_manage.product_create')
        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <a  class="layui-btn layui-btn-custom-blue" href="{{url('/admin/platform/product/verify/create')}}">新增</a>
            </div>
        </div>
        @endcan

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <script type="text/html" id="tableToolbarTpl">

        @can('product_manage.product_verify_detail')
        <a onclick="openDetail('@{{d.id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @endcan

        @can('product_manage.product_verify_switch')
        @{{#     if( d.status_platform == 1 ){       }}
        {{--品牌申请平台展示，可上架或下架--}}
        <a href="javascript:;" title="平台展示下架" onclick="ajax_status('@{{d.changeStatusApiUrl}}','off')" class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-close"></i>
        </a>
        <a href="javascript:;" title="平台展示上架" onclick="ajax_status('@{{d.changeStatusApiUrl}}','on')" class="layui-btn-success layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-ok"></i>
        </a>
        @{{#     }else if(d.status_platform==2){     }}
        {{--平台展示中，可强制下架--}}
        <a href="javascript:;" title="下架" onclick="ajax_status('@{{d.changeStatusApiUrl}}','off')" class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-close"></i>
        </a>
        @{{#     }     }}

        @endcan

    </script>

@endsection

@section('script')
    <script type="text/javascript" src="{{asset('plugins/layui-extend/tableSelect.js')}}"></script>

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
                        url:'{!! url($url_prefix.'admin/platform/product/verify/api/get_brands') !!}',
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
                            var display_name = select_data[i]['name'];
                            $('#h-belong-brand').val(privilege_id);
                            $('#brand-select').val(display_name);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/

        form.render();

        let base_api = '{{url('/admin/platform/product/verify/api')}}';
        let table_api = '{{url('/admin/platform/product/verify/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID', fixed: 'left'}
                ,{field: 'brand_name', width:140,title: '品牌名称'}
                ,{field: 'name', width:140,title: '产品名称'}
                ,{field: 'code',width:140, title: '产品编号'}
                ,{field: 'sys_code',width:140, title: '系统编号'}
                ,{field: 'spec',width:120, title: '规格'}
                ,{field: 'series',width:120, title: '系列'}
                ,{field: 'image',width:100, title: '缩略图',templet: function(d){
                    return '<a target="_blank" href="'+d.image+'"><img height="30" src="'+d.image+'"/></a>'
                }}
                ,{field: 'apply_categories_text',width:140, title: '应用类别'}
                ,{field: 'technology_categories_text',width:140, title: '工艺类别'}
                ,{field: 'surface_features_text',width:140, title: '表面特征'}
                ,{field: 'colors_text', width:140,title: '色系'}
                /*,{field: 'price_way_text',width:120, title: '定价方式'}
                ,{field: 'styles_text',width:140, title: '可应用空间风格'}
                ,{field: 'count_visit',width:80, title: '浏览量'}
                ,{field: 'usage',width:80, title: '使用量'}
                ,{field: 'count_fav',width:80, title: '收藏量'}
                ,{field: 'point_focus',width:80, title: '关注度'}
                ,{field: 'created_at',width:160, title: '创建时间'}*/
                ,{field: 'visible_text',width:100, title: '可见状态'}
                ,{field: 'status_text',width:100, title: '品牌站可用状态'}
                ,{field: 'operation',width:140, title: '操作' ,fixed:'right',templet:'#tableToolbarTpl'}
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
                    ,pc: $('select[name="pc"]').val()
                    ,bi: $('input[name="bi"]').val()
                    ,ac: $('select[name="ac"]').val()
                    ,tc: $('select[name="tc"]').val()
                    ,clr: $('select[name="clr"]').val()
                    ,pn: $('input[name="pn"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='pc']").val('')
            $("input[name='bi']").val('')
            $("select[name='ac']").val('')
            $("select[name='tc']").val('')
            $("select[name='clr']").val('')
            $('input[name="pn"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情页
        function openDetail(id){
            let page = '{{url('/admin/platform/product/verify')}}/'+id;
            layer.open({
                type: 2,
                title:'产品详情',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开编辑框
        function editData(type,id){
            let page = '{{url('/admin/platform/product/verify/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/platform/product/verify')}}/'+id+"/edit";
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

        function ajax_status(url,operation) {
            ajax_post(url,{op:operation}, function (res) {
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
