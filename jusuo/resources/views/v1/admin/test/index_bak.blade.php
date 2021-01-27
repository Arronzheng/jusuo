@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
    'js'=>['/v1/js/admin/module/table.js']
    ])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a lay-href="">主页</a><span lay-separator="">/</span>
            <a><cite>列表模板</cite></a>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="layui-form c-table-filter-container fold" lay-filter="app-content-list">
            <div id="fold-spread">
                <div class="fold hidden">收起 ∧</div>
                <div class="spread">展开 ∨</div>
            </div>
            <form action="" method="get">
                <div class="layui-form-item">
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">状态：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="status" lay-verify="">
                                <option value="">全部</option>
                                @foreach(\App\Models\TestData::$statusGroup as $key=>$item)
                                <option value="{{$key}}"  @if(request()->input('status')===(string)$key) selected @endif>{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="keyword" autocomplete="off" value="{{request()->input('keyword')? : ''}}" placeholder="" class="layui-input">
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
                        <button class="layui-btn  layui-btn-custom-blue layuiadmin-btn-list" lay-submit>
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                        <a href="{{url('admin/test')}}" class="layui-btn layui-btn-primary">重置</a>
                    </div>
                </div>
            </form>

        </div>
        <div class="op-container">
            <div class="left">搜索结果：{{$datas->total()}}</div>
            <div class="right">
                <button  class="layui-btn layui-btn-custom-blue" onclick="editData('add')">新增</button>
            </div>
        </div>
        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line">
            <thead>
            <tr>
                <th lay-data="{field:'name',sort:true}">名称</th>
                <th lay-data="{field:'avatar'}">头像</th>
                <th lay-data="{field:'intro'}">个人简介</th>
                <th lay-data="{field:'hobby'}">爱好</th>
                <th lay-data="{field:'type'}">类型</th>
                <th lay-data="{field:'status'}">状态</th>
                <th lay-data="{field:'operation'}">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr>
                    <td>{{$data->name}}</td>
                    <td>
                        <a target="_blank" href="{{$data->avatar}}"><img src="{{$data->avatar}}" height="30px"/></a>
                    </td>
                    <td>{{$data->intro}}</td>
                    <td>{{$data->hobby}}</td>
                    <td>{{\App\Models\TestData::$typeGroup[$data->type]}}</td>
                    <td>{{\App\Models\TestData::$statusGroup[$data->status]}}</td>
                    <td>
                        <div class="layui-table-cell laytable-cell-3-0-7">
                            {{--<a  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
                                <i class="layui-icon layui-icon-read"></i>查看
                            </a>--}}
                            <a onclick="editData('edit','{{$data->id}}')" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
                                <i class="layui-icon layui-icon-edit"></i>编辑
                            </a>
                            <a onclick="destroyData('{{$data->id}}')" href="javascript:;"  class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
                                <i class="layui-icon layui-icon-close"></i>删除
                            </a>
                            {{--<a href="javascript:;"  class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
                                <i class="layui-icon layui-icon-close"></i>禁用
                            </a>
                            <a href="javascript:;"  class="layui-btn-success layui-btn  layui-btn-xs" lay-event="edit">
                                <i class="layui-icon layui-icon-ok"></i>启用
                            </a>--}}

                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div id="pager" class="pager"></div>


    </div>

@endsection

@section('script')
    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        layui.element.init();
        var form = layui.form;

        var laypage = layui.laypage
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

        //调用分页 完整功能
        laypage.render({
            elem: 'pager'
            ,count: '{{$datas->total()}}'
            ,limit: '{{$datas->perPage()}}'
            ,curr: '{{$datas->currentPage()}}'
            ,layout: ['count','prev', 'page', 'next']
            ,jump: function(obj,first){
                //首次不执行
                var keyword_type = '{{request()->input('keyword_type') ? request()->input('keyword_type') : '1'}}';
                var keyword = '{{request()->input('keyword') ? request()->input('keyword') : ''}}';
                var date_start = '{{request()->input('date_start') ? request()->input('date_start') : ''}}';
                var date_end = '{{request()->input('date_end') ? request()->input('date_end') : ''}}';
                var status = '{{request()->input('status') ? request()->input('status') : ''}}';
                if(!first){
                    location.href='{{url('/admin/test')}}?page='+obj.curr+'&keyword_type='+keyword_type+'&keyword='+keyword+
                        '&date_start='+date_start+'&date_end='+date_end+'&status='+status;
                }
            }
        });


        //转换静态表格
        var table = layui.table;
        let tableInstance = table.init('tableFilter', {
            id:'tableInstance'
            ,limit: 10 //注意：请务必确保 limit 参数（默认：10）是与你服务端限定的数据条数一致
            ,autoSort: false
            //支持所有基础参数
        });

        //监听排序事件
        table.on('sort(tableFilter)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            console.log(obj.field); //当前排序的字段名
            console.log(obj.type); //当前排序类型：desc（降序）、asc（升序）、null（空对象，默认排序）
            console.log(this); //当前排序的 th 对象

            //尽管我们的 table 自带排序功能，但并没有请求服务端。
            //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
            table.reload('tableInstance',{
                url: '{{url('/admin/test/api/index')}}'
                ,initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                ,where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                    field: obj.field //排序字段
                    ,order: obj.type //排序方式
                }
            });

        });

    </script>

    {{--页面方法专用script--}}
    <script>

        //打开编辑框
        function editData(type,id){
            let page = '{{url('/admin/test/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/test')}}/'+id+"/edit";
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

        //删除行数据
        function destroyData(id){
            let apiUrl = '{{url('/admin/test/api')}}/'+id;
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                ajax_post(apiUrl,{
                    '_method':'DELETE',
                    'id':id,
                },function(result){
                    if(result.status){
                        layer.msg('删除成功！')
                        location.reload();
                    }else{
                        layer.msg(result.msg)
                    }
                });

                layer.close(index);
            });

        }

        function reloadPage(){
            location.reload();
        }

    </script>
@endsection
