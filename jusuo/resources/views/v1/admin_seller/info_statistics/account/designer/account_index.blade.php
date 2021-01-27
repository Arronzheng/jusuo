@extends('v1.admin_seller.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>账号统计</cite></a><span lay-separator="">/</span>
            <a><cite>设计师</cite></a>
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
                        <label class="layui-form-label">擅长风格：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="stl" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['styles'] as $item)
                                    <option value="{{$item->id}}"  >{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">擅长空间：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="spc" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['spaces'] as $item)
                                    <option value="{{$item->id}}"  >{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" autocomplete="off" value="{{request()->input('name')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">等级：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="lv" lay-verify="">
                                <option value="">全部</option>
                                <option value="-1">临时账号</option>
                                <option value="0">0级</option>
                                <option value="1">1级</option>
                                <option value="2">2级</option>
                                <option value="3">3级</option>
                                <option value="4">4级</option>
                                <option value="5">5级</option>
                                <option value="6">6级</option>
                                <option value="7">7级</option>
                                <option value="8">8级</option>
                                <option value="9">9级</option>
                                <option value="10">10级</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">所在城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="belong_province_id" name="abp" lay-verify="required" lay-filter="areaBelongProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($vdata['provinces'] as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="belong_city_id" name="abc" lay-verify="required" lay-filter="areaBelongCityId">
                                    <option value="">请选择城市</option>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="belong_district_id" name="abd" lay-verify="required" >
                                    <option value="">请选择区/县</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">服务城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="serving_province_id" name="asp" lay-verify="required" lay-filter="areaServingProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($vdata['provinces'] as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="serving_city_id" name="asc" lay-verify="required" lay-filter="areaServingCityId">
                                    <option value="">请选择城市</option>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="serving_district_id" name="asd" lay-verify="required" >
                                    <option value="">请选择区/县</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">注册时间</label>
                        <div class="layui-input-inline">
                            <input type="text" name="reg_start" id="reg_start" placeholder="开始时间" value="{{request()->input('reg_start')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline">
                            <input type="text" name="reg_end" id="reg_end" placeholder="结束时间" value="{{request()->input('reg_end')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">认证时间</label>
                        <div class="layui-input-inline">
                            <input type="text" name="cert_start" id="cert_start" placeholder="开始时间" value="{{request()->input('cert_start')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline">
                            <input type="text" name="cert_end" id="cert_end" placeholder="结束时间" value="{{request()->input('cert_end')? : ''}}" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <button type="button" onclick="reloadTable()" class="layui-btn  layui-btn-custom-blue layuiadmin-btn-list" >
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                        <button type="button" onclick="resetFilter()" class="layui-btn layui-btn-primary pull-right">重置</button>
                    </div>
                </div>
            </form>

        </div>

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

@endsection

@section('body')
    <script type="text/html" id="tableToolbarTpl">

        @can('info_statistic.account.designer_detail')
        <a onclick="openDetail('@{{d.designer_id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @endcan

        @can('info_statistic.account.designer_set_top')
        @{{#     if( d.top_dealer_status ){       }}
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
            elem: '#reg_start',
            value:'{{request()->input('reg_start') ? request()->input('reg_start') :''}}'
        });

        laydate.render({
            elem: '#reg_end',
            value:'{{request()->input('reg_end') ? request()->input('reg_end') :''}}'

        });

        laydate.render({
            elem: '#cert_start',
            value:'{{request()->input('cert_start') ? request()->input('cert_start') :''}}'
        });

        laydate.render({
            elem: '#cert_end',
            value:'{{request()->input('cert_end') ? request()->input('cert_end') :''}}'

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
                        url:'{!! url($url_prefix.'admin/seller/info_statistics/account/designer/api/get_brands') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'name', title: '组织名称' },
                            { field: 'organization_account', title: '组织账号' },
                            { field: 'contact_name', title: '联系人' },
                            { field: 'contact_telephone', title: '联系电话' },
                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-belong-organization').val(0);
                            $('#brand-select').val('');

                        }
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['name'];
                            $('#h-belong-organization').val(privilege_id);
                            $('#brand-select').val(display_name);

                        }
                    }
                });

                tableSelect.render({
                    elem: '#seller-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '销售商关键词搜索',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'admin/seller/info_statistics/account/designer/api/get_sellers') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'name', title: '组织名称' },
                            { field: 'organization_account', title: '组织账号' },
                            { field: 'contact_name', title: '联系人' },
                            { field: 'contact_telephone', title: '联系电话' },
                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-belong-organization').val(0);
                            $('#seller-select').val('');
                        }
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['name'];
                            $('#h-belong-organization').val(privilege_id);
                            $('#seller-select').val(display_name);

                        }
                    }
                });
            });
        });
        /*表格选择控件*/
        
        form.render();

        let base_api = '{{url('/admin/seller/info_statistics/account/designer/api')}}';
        let table_api = '{{url('/admin/seller/info_statistics/account/designer/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'designer_id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'nickname',width:100, title: '昵称'}
                ,{field: 'realname',width:100, title: '真实姓名'}
                ,{field: 'login_mobile',width:100, title: '注册手机号'}
                ,{field: 'self_designer_type_text',width:100, title: '类型'}
                ,{field: 'gender_text',width:80, title: '性别'}
                ,{field: 'area_belong',width:140, title: '所在城市'}
                ,{field: 'area_serving',width:140, title: '服务城市'}
                ,{field: 'style_text',width:120, title: '擅长风格'}
                ,{field: 'space_text',width:120, title: '擅长空间'}
                ,{field: 'self_expert',width:140, title: '服务专长'}
                ,{field: 'self_designer_level_text',width:80, title: '级别'}
                ,{field: 'point_focus',width:100, title: '关注度',sort:true}
                //,{field: 'point_experience',width:100,sort:true, title: '经验值'}
                ,{field: 'count_upload_album',width:100,sort:true, title: '方案数'}
                ,{field: 'count_top_album',width:130,sort:true, title: '方案置顶次数'}
                ,{field: 'count_visit',width:120,sort:true, title: '主页浏览量'}
                ,{field: 'count_praise',width:120,sort:true, title: '账号点赞数'}
                ,{field: 'account_status_text',width:100, title: '账号状态'}
                ,{field: 'created_at',width:160, title: '注册时间',sort:true}
                ,{field: 'cert_status_text',width:120, title: '认证状态'}
                ,{field: 'cert_time',width:160, title: '认证时间',sort:true}
                //,{field: 'created_at',width:160, title: '账号创建时间',sort:true}
                ,{field: 'operation', title: '操作' ,width:120 ,fixed:'right',templet:'#tableToolbarTpl'}

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
                get_area_global(province_id,'belong_city_id','城市');
            });

            //监听城市变化
            form.on('select(areaBelongCityId)', function(data){
                var city_id = data.value;
                get_area_global(city_id,'belong_district_id','区/县');
            });

            //监听省份变化
            form.on('select(areaServingProvinceId)', function(data){
                var province_id = data.value;
                get_area_global(province_id,'serving_city_id','城市');
            });

            //监听城市变化
            form.on('select(areaServingCityId)', function(data){
                var city_id = data.value;
                get_area_global(city_id,'serving_district_id','区/县');
            });

            //监听所属组织类型

            form.on('select(organizationType)', function(data){
                var organization_type = data.value;
                if(organization_type==1){
                    //品牌
                    $('.organization-select').hide();
                    $('.organization-select').val('');
                    $('#brand-select').show();
                }else if(organization_type==2){
                    //销售商
                    $('.organization-select').hide();
                    $('.organization-select').val('');
                    $('#seller-select').show();

                }else{
                    $('.organization-select').hide();
                    $('.organization-select').val('');

                    $('#no-organization-select').show();
                }
            });
        });

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,org: $('select[name="org"]').val()
                    ,spc: $('select[name="spc"]').val()
                    ,stl: $('select[name="stl"]').val()
                    ,lv: $('select[name="lv"]').val()
                    ,abp: $('select[name="abp"]').val()
                    ,abc: $('select[name="abc"]').val()
                    ,abd: $('select[name="abd"]').val()
                    ,asp: $('select[name="asp"]').val()
                    ,asc: $('select[name="asc"]').val()
                    ,asd: $('select[name="asd"]').val()
                    ,name: $('input[name="name"]').val()
                    ,orgid: $('input[name="orgid"]').val()
                    ,reg_start: $('input[name="reg_start"]').val()
                    ,reg_end: $('input[name="reg_end"]').val()
                    ,cert_start: $('input[name="cert_start"]').val()
                    ,cert_end: $('input[name="cert_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("#belong_province_id").val('')
            $("#belong_city_id").val('')
            $("#belong_district_id").val('')
            $("#serving_province_id").val('')
            $("#serving_city_id").val('')
            $("#serving_district_id").val('')
            $('input[name="name"]').val('')
            $('select[name="stl"]').val('')
            $('select[name="spc"]').val('')
            $('select[name="lv"]').val('')
            $('select[name="abp"]').val('')
            $('select[name="abc"]').val('')
            $('select[name="apd"]').val('')
            $('select[name="asp"]').val('')
            $('select[name="asc"]').val('')
            $('select[name="asd"]').val('')
            $('select[name="org"]').val('')
            $('input[name="orgid"]').val('')
            $('input[name="reg_start"]').val('')
            $('input[name="reg_end"]').val('')
            $('input[name="cert_start"]').val('')
            $('input[name="cert_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情框
        function openDetail(id){
            let page = '{{url('/admin/seller/info_statistics/account/designer')}}/'+id;
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
