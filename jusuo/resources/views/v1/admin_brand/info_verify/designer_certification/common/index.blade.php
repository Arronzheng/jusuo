@extends('v1.admin_brand.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>账号审核</cite></a><span lay-separator="">/</span>
            <a><cite>设计师实名资料</cite></a>
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
                        <label class="layui-form-label">类型：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="dsntype" lay-verify="">
                                <option value="">全部</option>
                                <option value="1">品牌设计师</option>
                                <option value="2">销售商设计师</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">账号/真实姓名：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" autocomplete="off" value="{{request()->input('keyword')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">所在城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select name="province_id" lay-filter="areaBelongProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($provinces as $item)
                                        <option value="{{$item->id}}"  >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="area_belong_city_id" name="city_id"  lay-filter="areaBelongCityId">
                                    <option value="">请选择城市</option>
                                    @if(isset($cities))
                                        @foreach($cities as $item)
                                            <option value="{{$item->id}}" >{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">注册时间</label>
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
        @can('account_manage.info_verify.designer_realname_info.brand_designer_detail')
        @{{#    if( d.org_type=='brand' ){     }}
        <a onclick="openDetail('@{{d.id}}','@{{d.org_type}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @{{#    }     }}
        @endcan

        @can('account_manage.info_verify.designer_realname_info.seller_designer_detail')
        @{{#    if( d.org_type=='seller' ){     }}
        <a onclick="openDetail('@{{d.id}}','@{{d.org_type}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @{{#    }     }}
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

        laydate.render({
            elem: '#date_start',
            value:'{{request()->input('date_start') ? request()->input('date_start') :''}}'
        });

        laydate.render({
            elem: '#date_end',
            value:'{{request()->input('date_end') ? request()->input('date_end') :''}}'

        });

        //监听所在地区省份变化
        form.on('select(areaBelongProvinceId)', function(data){
            var province_id = data.value;
            get_area(province_id,'area_belong_city_id','城市');
        });

        //监听所在地区城市变化
        form.on('select(areaBelongCityId)', function(data){
            var city_id = data.value;
            get_area(city_id,'area_belong_district_id','区/县');
        });

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url($url_prefix.'admin/api/get_area_children?pi=')}}'+area_id,function(res){
                if(res.status && res.data.length>0){
                    $.each(res.data,function(k,v){
                        options+='<option value="'+ v.id+'">'+ v.name+'</option>';
                    });
                    $('#'+next_elem).html(options);
                    form.render('select');
                }
            });
        }

        let base_api = '{{url('/admin/brand/info_verify/designer_certification/api')}}';
        let table_api = '{{url('/admin/brand/info_verify/designer_certification/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'designer_type',width:120, title: '类型'}
                ,{field: 'designer_account',width:120, title: '账号'}
                ,{field: 'realname',width:100, title: '真实姓名'}
                ,{field: 'code_idcard',width:140, title: '身份证号'}
                ,{field: 'nickname',width:120, title: '昵称'}
                ,{field: 'login_mobile',width:120, title: '注册手机号'}
                ,{field: 'genderText',width:80, title: '性别'}
                ,{field: 'local',width:120, title: '所在城市'}
                ,{field: 'designer_type_text',width:120, title: '账号类型'}
                ,{field: 'created_at',width:140, title: '注册时间'}
                ,{field: 'approve_text',width:100, title: '审核结果',fixed:'right'}
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

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,keyword: $('input[name="keyword"]').val()
                    ,province_id: $('select[name="province_id"]').val()
                    ,city_id: $('select[name="city_id"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $('select[name="province_id"]').val('')
            $('select[name="city_id"]').val('')
            $('input[name="keyword"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情框
        function openDetail(id,org_type){
            let page = '{{url('/admin/brand/info_verify/designer_certification/brand')}}/'+id;
            if(org_type=='seller'){
                page = '{{url('/admin/brand/info_verify/designer_certification/seller')}}/'+id;
            }
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
