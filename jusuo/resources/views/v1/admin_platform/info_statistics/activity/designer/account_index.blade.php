@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

<?php
$guardName = request()->input('type');
$guardCNName = \App\Services\common\GuardRBACService::getCNNameByGuard($guardName);
?>
@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>信息统计</cite></a><span lay-separator="">/</span>
            <a><cite>活跃度统计</cite></a><span lay-separator="">/</span>
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
                    <div class="layui-form-item" style="margin-bottom:10px;">
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


    <script type="text/html" id="isSuperAdminTpl">
        <span>@{{d.is_super_admin_text}}</span>
    </script>
    <script type="text/html" id="shownTpl">
        <span>@{{d.shown_text}}</span>
    </script>
    <script type="text/html" id="isMenuTpl">
        <span>@{{d.is_menu_text}}</span>
    </script>
    <script type="text/html" id="roleNameTpl">
        <a href="javascript:;" style="" onclick="checkPrivilege('@{{ d.id }}')">@{{d.role_name}}</a>
    </script>

    <script type="text/html" id="tableToolbarTpl">

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

        let base_api = '{{url('/admin/platform/info_statistics/activity/designer/api')}}';
        let table_api = '{{url('/admin/platform/info_statistics/activity/designer/api')}}?type={{request()->input("type")}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'designer_account',width:120, title: '账号'}
                ,{field: 'nickname',width:120, title: '昵称'}
                ,{field: 'realname',width:100, title: '真实姓名'}
                ,{field: 'login_mobile',width:120, title: '注册手机号'}
                ,{field: 'designer_type_text',width:120, title: '账号类型'}
                ,{field: 'genderText',width:80, title: '性别'}
                ,{field: 'area_belong',width:140, title: '所在城市'}
                ,{field: 'area_serving',width:140, title: '服务城市'}
                ,{field: 'count_upload_album',width:120,sort:true, title: '上传方案数'}
                ,{field: 'count_fav_album',width:120,sort:true, title: '收藏方案数'}
                ,{field: 'count_praise_album',width:120,sort:true, title: '点赞方案数'}
                ,{field: 'count_fav_designer',width:140,sort:true, title: '关注设计师数'}
                ,{field: 'count_download_album',width:140,sort:true, title: '下载方案数'}
                ,{field: 'count_copy_album',width:140,sort:true, title: '复制方案数'}
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

        });

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,abp: $('select[name="abp"]').val()
                    ,abc: $('select[name="abc"]').val()
                    ,abd: $('select[name="abd"]').val()
                    ,asp: $('select[name="asp"]').val()
                    ,asc: $('select[name="asc"]').val()
                    ,asd: $('select[name="asd"]').val()
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

            form.render();
            reloadTable()
        }

        function ajax_member_status(url) {
            ajax_post(url,{}, function (res) {
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    reloadTable()
                } else {
                    layer.msg(res.msg);
                }
            });
        }

    </script>
@endsection
