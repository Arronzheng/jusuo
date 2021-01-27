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
                    <label class="layui-form-label" style="width:130px">服务城市：</label>

                    <div class="layui-inline filter-block">
                        <div class="layui-input-inline">
                            <select id="filter_province_id" name="a_p" lay-verify="required" lay-filter="filterProvinceId">
                                <option value="">请选择省</option>
                                @foreach($provinces as $item)
                                    <option value="{{$item->id}}" >{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select id="filter_city_id" name="a_c" lay-verify="required" lay-filter="filterCityId">
                                <option value="">请选择城市</option>
                                @if(isset($cities))
                                    @foreach($cities as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select id="filter_district_id" name="a_d" lay-verify="required"  lay-filter="filterDistrictId">
                                <option value="">请选择区/县</option>
                                @if(isset($param['districts']))
                                    @foreach($param['districts'] as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">公司名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="ln" autocomplete="off" value="{{request()->input('cn')? : ''}}" placeholder="" class="layui-input">
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
                <button  class="layui-btn layui-btn-custom-blue" onclick="setStructure('set')">设置产品结构</button>
                <button  class="layui-btn layui-btn-custom-blue" onclick="setStructure('cancel')">取消设置产品结构</button>
            </div>
        </div>

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

    <div style="display:none;" id="select-structure-form-tpl">

        <div id="select-structure-form">
            <form class="layui-form" >
                <div class="layui-form-item" style="margin-top:20px;">
                    <label class="layui-form-label" style="width:100px;">选择产品结构：</label>
                    <div class="layui-input-inline" >

                        @foreach($product_structures as $key=>$item)
                            <input type="radio" name="product_structure_id" value="{{$item->id}}" title="{{$item->name}}" @if( $key==0 ) checked @endif>
                        @endforeach

                    </div>
                </div>

            </form>

        </div>

    </div>

@endsection

@section('body')

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

        $(document).ready(function(){
            //监听省份变化
            form.on('select(filterProvinceId)', function(data){
                var province_id = data.value;
                get_area(province_id,'filter_city_id','城市');
            });

            //监听城市变化
            form.on('select(filterCityId)', function(data){
                var city_id = data.value;
                get_area(city_id,'filter_district_id','区/县');
            });
        });

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url('/common/get_area_children?pi=')}}'+area_id,function(res){
                if(res.status && res.data.length>0){
                    $.each(res.data,function(k,v){
                        options+='<option value="'+ v.id+'">'+ v.name+'</option>';
                    });
                    $('#'+next_elem).html(options);
                    form.render('select');
                }
            });
        }

        let base_api = '{{url('/admin/brand/product/authorize/structure/object/api')}}';
        let table_api = '{{url('/admin/brand/product/authorize/structure/object/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,limit:100
            ,limits:[100,200,500,800,1000]
            ,cols: [[ //表头
                {checkbox: true, fixed: 'left'}
                ,{field: 'id', title: 'ID', width:80}
                ,{field: 'area_belong',width:160, title: '所在城市',}
                ,{field: 'login_account',width:160, title: '登录账号'}
                ,{field: 'dealer_name',width:160, title: '公司名称',}
                ,{field: 'area_serving',width:160, title: '服务城市',}
                ,{field: 'level', title: '级别'}
                ,{field: 'contact_address',width:160, title: '详细地址',}
                ,{field: 'product_count',width:100, title: '合作产品数',}
                ,{field: 'designer_count',width:100, title: '直属设计师数'}
                ,{field: 'album_count',width:100, title: '方案数'}
                ,{field: 'point_focus',width:100, title: '关注度'}
                ,{field: 'star_level',width:100, title: '星级'}


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

        var set_operation = '';
        function setStructure(operation) {
            //获取销售商选中行
            var checkStatus = table.checkStatus('tableInstance');
            //checkStatus.data //获取选中行的数据
            var object_data = checkStatus.data;
            if(object_data.length<=0){
                layer.msg('请选择销售商！');
                layer.closeAll('loading');
                return false;
            }
            set_operation = operation;
            layer.open({
                type: 1,
                title:'选择对象并设置',
                skin: 'layui-layer-rim', //加上边框
                area: ['420px', '240px'], //宽高
                scrollbar: false,
                btn:[operation=='set'?'确认设置':'确认取消设置'],
                content: $('#select-structure-form-tpl').html(),
                success:function(){
                    form.render('radio');
                },
                yes:function(index, layero){
                    var product_structure_id = $(layero).find('input[name="product_structure_id"]:checked').val();
                    confirmSetStructure(product_structure_id,index);
                }
            });
        }

        function confirmSetStructure(product_structure_id,layer_index){
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
                layer.msg('请选择销售商！');
                layer.closeAll('loading');
                return false;
            }
            //获取已选择的产品
            var product_ids = [];
            var product_check_status = parent.get_table_check_status();
            var product_data = product_check_status.data;
            for(var i=0;i<product_data.length;i++){
                product_ids.push(product_data[i].id);
            }
            if(product_ids.length<=0){
                layer.msg('请选择产品！');
                layer.closeAll('loading');
                return false;
            }
            ajax_post('{{url('admin/brand/product/authorize/structure/object/api/set_structure')}}',
                {pids:product_ids,oids:object_ids,psid:product_structure_id,op:set_operation}, function (res) {
                    if (res.status == 1) {
                        layer.msg('操作成功！');
                        layer.closeAll('loading');
                        layer.close(layer_index);
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
                    ,a_d: $('select[name="a_d"]').val()
                    ,lv: $('select[name="lv"]').val()
                    ,ln: $('input[name="cn"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='a_d']").val('')
            $("select[name='lv']").val('')
            $('input[name="cn"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }


    </script>
@endsection
