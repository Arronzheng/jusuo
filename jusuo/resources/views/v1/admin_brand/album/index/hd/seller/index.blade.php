@extends('v1.admin_brand.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>方案管理</cite></a><span lay-separator="">/</span>
            <a><cite>方案列表</cite></a><span lay-separator="">/</span>
            <a><cite>高清图</cite></a><span lay-separator="">/</span>
            <a><cite>销售商方案</cite></a>
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
                        <label class="layui-form-label">来源：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="type" lay-verify="">
                                <option value="">全部</option>
                                @foreach(\App\Models\Album::typeGroup() as $key=>$item)
                                    <option value="{{$key}}"  @if(request()->input('type')===(string)$key) selected @endif>{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">风格：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="stl" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['styles'] as $key=>$item)
                                    <option value="{{$item->id}}"  @if(request()->input('stl')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">方案色系：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="clr" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['colors'] as $key=>$item)
                                    <option value="{{$item->id}}"  @if(request()->input('stl')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">户型类别：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="ht" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['house_types'] as $key=>$item)
                                    <option value="{{$item->id}}"  @if(request()->input('ht')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">面积区间</label>
                        <div class="layui-input-inline" style="width:100px">
                            <input type="text" name="area_start" placeholder="最小面积" value="{{request()->input('area_start')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline" style="width:100px">
                            <input type="text" name="area_end" placeholder="最大面积" value="{{request()->input('area_end')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">空间数</label>
                        <div class="layui-input-inline" style="width:100px">
                            <input type="text" name="sc" placeholder="请输入数量" value="{{request()->input('sc')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">涉及方案品类：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="pt" lay-verify="">
                                <option value="">瓷砖</option>
                            </select>
                        </div>
                    </div>
                    {{--<div class="layui-inline filter-block">
                        <label class="layui-form-label">品牌</label>
                        <div class="layui-input-inline">
                            <input type="text" autocomplete="off" id="brand-select"  value="" readonly placeholder="点击搜索并选择" class="layui-input">
                            <input type="hidden" name="bi" value="" id="h-belong-brand">
                        </div>
                    </div>--}}
                    <hr/>
                    <div style="padding-top:13px;">

                        <div class="layui-inline filter-block">
                            <label class="layui-form-label" >方案标题：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="tle" autocomplete="off" value="{{request()->input('tle')? : ''}}" placeholder="" class="layui-input">
                            </div>
                        </div>
                        {{--<div class="layui-inline filter-block">
                            <label class="layui-form-label" >所有方名称：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="rn" autocomplete="off" value="{{request()->input('rn')? : ''}}" placeholder="" class="layui-input">
                            </div>
                        </div>--}}
                        <div class="layui-inline filter-block">
                            <label class="layui-form-label" >作者昵称：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="nn" autocomplete="off" value="{{request()->input('nn')? : ''}}" placeholder="" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline filter-block">
                            <label class="layui-form-label" >方案名称：</label>
                            <div class="layui-input-inline">
                                <input type="text" name="pn" autocomplete="off" value="{{request()->input('nn')? : ''}}" placeholder="" class="layui-input">
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


                </div>
            </form>

        </div>

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <script type="text/html" id="tableToolbarTpl">

        @can('album_manage.album_index.hd_photo.seller_album_detail')
        <a onclick="openDetail('@{{d.id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @endcan

        @can('album_manage.album_index.hd_photo.seller_album_apply_platform')
        @{{#     if( d.can_switch ){       }}
        <a href="javascript:;" title="@{{ d.status_platform_title }}" onclick="ajax_status('@{{d.changeStatusPlatformApiUrl}}')" class="layui-btn-@{{ d.status_platform_style }} layui-btn  layui-btn-xs" lay-event="edit">
            @{{ d.status_platform_title }}
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
                        url:'{!! url($url_prefix.'admin/brand/album/index/hd/seller/api/get_brands') !!}',
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

        let base_api = '{{url('/admin/brand/album/index/hd/seller/api')}}';
        let table_api = '{{url('/admin/brand/album/index/hd/seller/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID', fixed: 'left'}
                ,{field: 'code',width:140, title: '编号'}
                ,{field: 'title', width:140,title: '标题'}
                ,{field: 'designer_account',width:140, title: '设计师账号编码'}
                ,{field: 'realname',width:140, title: '真实姓名'}
                ,{field: 'nickname',width:140, title: '昵称'}
                ,{field: 'type_text',width:120, title: '来源'}
                ,{field: 'style_text',width:120, title: '风格'}
                ,{field: 'house_type_text',width:120, title: '户型'}
                ,{field: 'count_area_text',width:120, title: '面积',sort: true}
                ,{field: 'space_count',width:120, title: '空间数量',sort: true}
                ,{field: 'status_text',width:100, title: '状态'}
                ,{field: 'updated_at',width:160, title: '最后编辑时间',sort: true}
                ,{field: 'operation',width:200, title: '操作' ,fixed:'right',templet:'#tableToolbarTpl'}
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
                    ,type: $('select[name="type"]').val()
                    ,stl: $('select[name="stl"]').val()
                    ,clr: $('select[name="clr"]').val()
                    ,ht: $('select[name="ht"]').val()
                    ,area_start: $('input[name="area_start"]').val()
                    ,area_end: $('input[name="area_end"]').val()
                    ,sc: $('input[name="sc"]').val()  //space_count
                    ,bi: $('input[name="bi"]').val()
                    ,tle: $('input[name="tle"]').val()
                    ,rn: $('input[name="rn"]').val()
                    ,nn: $('input[name="nn"]').val()
                    ,pn: $('input[name="pn"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='type']").val('')
            $("select[name='stl']").val('')
            $("select[name='clr']").val('')
            $("select[name='ht']").val('')
            $("input[name='area_start']").val('')
            $("input[name='area_end']").val('')
            $("input[name='sc']").val('')
            $("input[name='bi']").val('')
            $('input[name="tle"]').val('')
            $('input[name="rn"]').val('')
            $('input[name="nn"]').val('')
            $('input[name="pn"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情页
        function openDetail(id){
            let page = '{{url('/admin/brand/album/index/hd/seller')}}/'+id;
            layer.open({
                type: 2,
                title:'方案详情',
                area:['900px', '600px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }


        function ajax_status(url,operation) {
            if(url){
                ajax_post(url,{op:operation}, function (res) {
                    if (res.status == 1) {
                        layer.msg('操作成功！');
                        reloadTable()
                    } else {
                        layer.msg(res.msg);
                    }
                });
            }

        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
