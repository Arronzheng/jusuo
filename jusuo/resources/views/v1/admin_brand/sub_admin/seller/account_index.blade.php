@extends('v1.admin_brand.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>销售商列表</cite></a>
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
                        <label class="layui-form-label">级别：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="lv" lay-verify="">
                                <option value="">全部</option>
                                <option value="1"  @if(request()->input('lv')==1) selected @endif>一级</option>
                                <option value="2"  @if(request()->input('lv')==2) selected @endif>二级</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">登录账号/用户名：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="ln" autocomplete="off" value="{{request()->input('login_name')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-bottom:10px;">
                        <label class="layui-form-label">可见城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="area_visible_province_id" name="avp" lay-verify="required" lay-filter="areaVisibleProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($provinces as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="area_visible_city_id" name="avc" lay-verify="required" lay-filter="areaVisibleCityId">
                                    <option value="">请选择城市</option>
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
                                    @foreach($provinces as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="serving_city_id" name="asc" lay-verify="required" lay-filter="areaServingCityId">
                                    <option value="">请选择城市</option>
                                </select>
                            </div>
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

    @can('account_manage.account_index.seller_admin_create')
        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-custom-blue" onclick="editData('add')">新增</button>
            </div>
        </div>
        @endcan

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>

@endsection

@section('body')
    <script type="text/html" id="tableToolbarTpl">
        @can('account_manage.account_index.seller_admin_detail')
        @{{#     if( d.dealer_status == '200' ){       }}
        <a  title="预览详情" target="_blank" href="/admin/brand/sub_admin/seller/preview_seller_detail/@{{d.web_id_code}}" class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-search"></i>
        </a>
        @{{#     }       }}
        @endcan

        @can('account_manage.account_index.designer.designer_integral_adjust')
        <a onclick="integralDistribute('@{{d.id}}')" title="积分发放" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-rmb"></i>
        </a>
        @endcan

        @can('account_manage.account_index.seller_admin_config')
        <a onclick="configData('@{{d.id}}')" title="配额管理" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-set-sm"></i>
        </a>
        @endcan

        @can('account_manage.account_index.seller_admin_edit')
        <a onclick="editData('edit','@{{d.id}}')" title="编辑" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-edit"></i>
        </a>
        @endcan


        @can('account_manage.account_index.seller_admin_modify_pwd')
        <a onclick="modifyPwd('@{{d.id}}')" title="修改密码" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-password"></i>
        </a>
        @endcan

        @can('account_manage.account_index.seller_admin_switch')
        @{{#     if( d.isOn ){       }}
        <a href="javascript:;" title="禁用" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-danger layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-close"></i>
        </a>
        @{{#     }else{     }}
        <a href="javascript:;" title="启用" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" >
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

        form.render();

        let base_api = '{{url('/admin/brand/sub_admin/seller/api/account')}}';
        let table_api = '{{url('/admin/brand/sub_admin/seller/api/account')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'login_account',width:160, title: '登录账号'}
                ,{field: 'login_username',width:100, title: '登录用户名'}
                ,{field: 'level', title: '级别'}
                ,{field: 'parent_seller_name',width:160, title: '上级销售商',}
                ,{field: 'dealer_name',width:160, title: '名称',}
                ,{field: 'legal_person_name',width:100, title: '法人代表',}
                /*,{field: 'privilege_area_serving_text',width:160, title: '服务地区范围',}*/
                ,{field: 'area_serving',width:160, title: '服务城市',}
                ,{field: 'area_visible',width:160, title: '可见城市',}
                ,{field: 'contact_name',width:100, title: '联系人'}
                ,{field: 'login_mobile',width:120, title: '联系电话'}
                ,{field: 'designer_count',width:160, title: '设计师账号(已授/可授)'}
                ,{field: 'created_at',width:150, title: '创建时间'}
                ,{field: 'expired_at',width:150, title: '账号有效期限'}
                ,{field: 'account_status_text',width:80, title: '账户状态'}
                ,{field: 'dealer_status_text',width:80,fixed:'right', title: '认证状态'}
                ,{field: 'operation', title: '操作' ,fixed:'right',width:240 ,templet:'#tableToolbarTpl'}

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
            form.on('select(areaVisibleProvinceId)', function(data){
                var province_id = data.value;
                get_area_global(province_id,'area_visible_city_id','城市');
            });


            //监听省份变化
            form.on('select(areaServingProvinceId)', function(data){
                var province_id = data.value;
                get_area_global(province_id,'serving_city_id','城市');
            });


        });

        //当前条件下重新加载table
        function reloadTable(sortObj){
            var area_serving_province = $('select[name="asp"]').val();
            var area_serving_city = $('select[name="asc"]').val();
            var area_visible_province = $('select[name="avp"]').val();
            var area_visible_city = $('select[name="avc"]').val();
            if(area_serving_province && !area_serving_city){
                layer.msg('请选择服务城市后再筛选');return false;
            }
            if(area_visible_province && !area_visible_city){
                layer.msg('请选择可见城市后再筛选');return false;
            }
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,lv: $('select[name="lv"]').val()
                    ,ln: $('input[name="ln"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                    ,asp: area_serving_province
                    ,asc: area_serving_city
                    ,avp: area_visible_province
                    ,avc: area_visible_city
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='asp']").val('')
            $("select[name='asc']").val('')
            $("select[name='avp']").val('')
            $("select[name='avc']").val('')
            $("select[name='lv']").val('')
            $('input[name="ln"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            $("#serving_province_id").val('')
            $("#serving_city_id").val('')
            $("#serving_district_id").val('')
            $("#area_visible_province_id").val('')
            $("#area_visible_city_id").val('')
            $("#area_visible_district_id").val('')
            form.render();
            reloadTable()
        }

        //打开编辑框
        function configData(id){
            let page = '{{url('/admin/brand/sub_admin/seller/account')}}/'+id+"/config";

            layer.open({
                type: 2,
                title:'配额管理',
                area:['100%', '100%'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){
                    layer.closeAll('loading');
                }
            });
        }

        //打开编辑框
        function editData(type,id){
            let page = '{{url('/admin/brand/sub_admin/seller/account/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/brand/sub_admin/seller/account')}}/'+id+"/edit";
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
                    layer.closeAll('loading');
                }
            });
        }

        //打开修改密码框
        function modifyPwd(id){
            let page = '{{url('/admin/brand/sub_admin/seller/account')}}/'+id+"/modify_pwd";
            layer.open({
                type: 2,
                title:'修改密码',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开积分发放框
        function integralDistribute(id){
            let page = '{{url('/admin/brand/sub_admin/seller/account')}}/'+id+"/integral_distribute";
            layer.open({
                type: 2,
                title:'积分发放',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开详情框
        function openDetail(id){
            let page = '{{url('/admin/brand/sub_admin/seller/account')}}/'+id;
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
