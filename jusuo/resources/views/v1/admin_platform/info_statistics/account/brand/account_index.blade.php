@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>信息统计</cite></a><span lay-separator="">/</span>
            <a><cite>账号统计</cite></a><span lay-separator="">/</span>
            <a><cite>品牌</cite></a>
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
                        <label class="layui-form-label">经营品类：</label>
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
                        <label class="layui-form-label" style="width:130px">名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" autocomplete="off" value="{{request()->input('name')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">所在城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="company_province_id" lay-verify="required" lay-filter="areaBelongProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($vdata['provinces'] as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="company_city_id" lay-verify="required" lay-filter="areaBelongCityId">
                                    <option value="">请选择城市</option>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="company_district_id" name="abi" lay-verify="required" >
                                    <option value="">请选择区/县</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">开通时间</label>
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

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

@endsection

@section('body')
    <script type="text/html" id="tableToolbarTpl">

        @can('info_statistic.account.brand_detail')
        <a onclick="openDetail('@{{d.brand_id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @endcan

        @can('info_statistic.account.brand_set_top')
        @{{#     if( d.top_status ){       }}
        <a href="javascript:;" title="取消置顶" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-danger layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-download-circle"></i>
        </a>
        @{{#     }else{     }}
        <a href="javascript:;" title="置顶" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-upload-circle"></i>
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

        form.render();

        let base_api = '{{url('/admin/platform/info_statistics/account/brand/api')}}';
        let table_api = '{{url('/admin/platform/info_statistics/account/brand/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'brand_id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'login_account',width:120, title: '登录账号'}
                ,{field: 'brand_name',width:140, title: '品牌名称'}
                ,{field: 'company_name',width:160, title: '公司名称'}
                ,{field: 'product_category_name',width:120, title: '经营产品',}
                ,{field: 'count_product',width:100,sort: true, title: '产品数'}
                ,{field: 'count_album',width:100,sort: true, title: '方案数'}
                ,{field: 'count_designer',width:130,sort: true, title: '直属设计师数'}
                ,{field: 'count_dealer_lv1',width:130,sort: true, title: '一级销售商数'}
                ,{field: 'count_dealer_lv2',width:130, sort: true,title: '二级销售商数'}
                ,{field: 'count_designer_lv1',width:160,sort: true, title: '一级销售商设计师数'}
                ,{field: 'count_designer_lv2',width:160,sort: true, title: '二级销售商设计师数'}
                ,{field: 'start_at',width:160,sort: true,  title: '开通时间'}
                ,{field: 'brand_status_text',width:100, title: '账号状态'}
                ,{field: 'operation', title: '操作' ,width:200 ,fixed:'right',templet:'#tableToolbarTpl'}

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

        $(document).ready(function(){
            //监听省份变化
            form.on('select(areaBelongProvinceId)', function(data){
                var province_id = data.value;
                get_area_global(province_id,'company_city_id','城市');
            });

            //监听城市变化
            form.on('select(areaBelongCityId)', function(data){
                var city_id = data.value;
                get_area_global(city_id,'company_district_id','区/县');
            });
        });

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,pc: $('select[name="pc"]').val()
                    ,abi: $('select[name="abi"]').val()
                    ,name: $('input[name="name"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='pc']").val('')
            $("#company_province_id").val('')
            $("#company_city_id").val('')
            $("#company_district_id").val('')
            $('input[name="name"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情框
        function openDetail(id){
            let page = '{{url('/admin/platform/info_statistics/account/brand')}}/'+id;
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

        function ajax_member_status(url) {
            ajax_post(url,{}, function (res) {
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    layer.closeAll()
                    reloadTable()
                } else {
                    layer.msg(res.msg);
                }
            });
        }

    </script>
@endsection
