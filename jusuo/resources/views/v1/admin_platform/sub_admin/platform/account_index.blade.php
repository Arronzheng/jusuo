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
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>账号列表</cite></a><span lay-separator="">/</span>
            <a><cite>平台管理员</cite></a>
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
                    {{--<div class="layui-inline filter-block">
                        <label class="layui-form-label">角色：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="r" lay-verify="">
                                <option value="">全部</option>
                                @foreach($roles as $item)
                                    <option value="{{$item->id}}"  @if(request()->input('role_id')===(string)$item->id) selected @endif>{{$item->display_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>--}}
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">登录账号/用户名：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="ln" autocomplete="off" value="{{request()->input('login_name')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">真实姓名：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="rn" autocomplete="off" value="{{request()->input('rn')? : ''}}" placeholder="" class="layui-input">
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

    @can('account_manage.account_index.platform_manager_create')
        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <button  class="layui-btn layui-btn-custom-blue" onclick="editData('add')">新增</button>
            </div>
        </div>
        @endcan

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

        @can('account_manage.account_index.platform_manager_detail')
        <a onclick="openDetail('@{{d.id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>
        @endcan

        @can('account_manage.account_index.platform_manager_edit')
        @{{#    if( d.canEdit ){     }}
            @{{#    if(! d.is_super_admin ){     }}
        <a onclick="editData('edit','@{{d.id}}')" title="编辑" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-edit"></i>
        </a>
            @{{# } }}
        @{{# } }}
        @endcan

        @can('account_manage.account_index.platform_manager_modify_pwd')
        @{{#    if( d.canModifyPwd ){     }}
        <a onclick="modifyPwd('@{{d.id}}')" title="修改密码" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-password"></i>
        </a>
        @{{#    }     }}
        @endcan

        @can('account_manage.account_index.platform_manager_switch')
        @{{#    if( d.canEdit ){     }}
            @{{#    if(! d.is_super_admin ){     }}
                @{{#     if( d.isOn ){       }}
                <a href="javascript:;" title="禁用" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
                    <i class="layui-icon layui-icon-close"></i>
                </a>
                @{{#     }else{     }}
                <a href="javascript:;" title="启用" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" lay-event="edit">
                    <i class="layui-icon layui-icon-ok"></i>
                </a>
                @{{#     }     }}

            @{{#     }     }}
        @{{#    }     }}
        @endcan

        @can('account_manage.account_index.platform_manager_delete')
        @{{#    if( d.canEdit ){     }}
            @{{#    if(! d.is_super_admin ){     }}
            <a onclick="destroyData('@{{d.id}}')" title="删除" href="javascript:;"  class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
                <i class="layui-icon layui-icon-delete"></i>
            </a>
            @{{#     }     }}
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

        let base_api = '{{url('/admin/platform/sub_admin/api/account')}}';
        let table_api = '{{url('/admin/platform/sub_admin/api/account')}}?type={{request()->input("type")}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,cols: [[ //表头
                {field: 'id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'login_account', title: '登录账号'}
                ,{field: 'login_username', title: '登录用户名'}
                ,{field: 'role_name', title: '管理员角色',templet:'#roleNameTpl'}
                ,{field: 'realname', title: '管理员姓名'}
                ,{field: 'self_department', title: '管理员部门'}
                ,{field: 'self_position', title: '管理员职位'}
                ,{field: 'operation', title: '操作' , width:200 ,templet:'#tableToolbarTpl'}

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
                    ,ln: $('input[name="ln"]').val()
                    ,rn: $('input[name="rn"]').val()
                    //,r: $('select[name="r"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("input[name='ln']").val('')
            $("input[name='rn']").val('')
            //$('select[name="r"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开编辑框
        function editData(type,id){
            let page = '{{url('/admin/platform/sub_admin/account/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/platform/sub_admin/account')}}/'+id+"/edit";
            }
            layer.open({
                type: 2,
                title:'编辑信息',
                area:['900px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                success:function(){

                }
            });
        }

        //打开修改密码框
        function modifyPwd(id){
            let page = '{{url('/admin/platform/sub_admin/account')}}/'+id+"/modify_pwd";
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

        //打开详情框
        function openDetail(id){
            let page = '{{url('/admin/platform/sub_admin/account')}}/'+id;
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

        //删除行数据
        function destroyData(id){
            let apiUrl = base_api+'/'+id;
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

        //打开权限框
        function checkPrivilege(id){
            let page = '{{url('/admin/platform/sub_admin/account/privilege')}}/'+id;
            layer.open({
                type: 2,
                title:'管理员权限',
                area:['700px', '500px'],
                resize:true,
                maxmin:true,
                //content: $('#edit-form').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                content:page,
                shadeClose:true,
                success:function(){

                }
            });
        }

    </script>
@endsection
