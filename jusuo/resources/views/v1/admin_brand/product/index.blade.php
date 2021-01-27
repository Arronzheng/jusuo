@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css','/plugins/layui-extend/dropdown/dropdown.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>产品管理</cite></a><span lay-separator="">/</span>
            <a><cite>产品列表</cite></a>
            <div class="right" style="margin-right:15px;">
                @can('product_manage.product_verify')
                <div class="layui-dropdown" id="example1">
                    <!-- 触发器 -->
                    <button class="layui-btn layui-btn-sm layui-dropdown-toggle layui-btn-custom-blue">
                        <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>
                        产品审核
                        <i class="layui-icon layui-icon-triangle-d"></i>
                    </button>
                    <!-- 下拉框 -->
                    <div class="layui-dropdown-menu">
                        <div >
                            @can('product_manage.product_verify.wait_verify_index')
                            <a href="{{url('/admin/brand/product/verify/wait')}}" class="menu-item" >待审核</a>
                            @endcan
                            @can('product_manage.product_verify.reject_verify_index')
                            <a href="{{url('/admin/brand/product/verify/reject')}}" class="menu-item" >已驳回</a>
                            @endcan
                            @can('product_manage.product_verify.pass_verify_index')
                            <a href="{{url('/admin/brand/product/verify/pass')}}" class="menu-item" >已通过</a>
                            @endcan
                        </div>
                    </div>
                </div>
                @endcan
                @can('product_manage.series_index')
                <button onclick="location.href='{{url('/admin/brand/product/series')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>产品系列
                </button>
                @endcan

            </div>
        </div>
    </div>
    <div class="layui-fluid">
        <div id="filterContainer" class="layui-form c-table-filter-container fold" lay-filter="app-content-list">
            <div id="fold-spread">
                <div class="fold hidden">收起 ▲</div>
                <div class="spread">展开 ▼</div>
            </div>
            <form id="filter-form" action="" target="_blank" method="get">
                <input type="hidden" name="page" value="0"/>
                <input type="hidden" name="limit" value="0"/>
                <input type="hidden" name="export" value="1"/>
                <div class="layui-form-item">
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">品牌站可用状态：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="status" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['product_status'] as $key=>$item)
                                    <option value="{{$key}}"  @if(request()->input('status')===(string)$key) selected @endif>{{$item}}</option>
                                @endforeach
                            </select>
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
                        <label class="layui-form-label">系列：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="srs" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['series'] as $item)
                                    <option value="{{$item->id}}"  @if(request()->input('srs')===(string)$item->id) selected @endif>{{$item->name}}</option>
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
                        <label class="layui-form-label">规格：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="spec" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['specs'] as $item)
                                    <option value="{{$item->id}}"  @if(request()->input('spec')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">可见状态：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="vstatus" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['visible_status'] as $key=>$item)
                                    <option value="{{$key}}"  @if(request()->input('vstatus')===(string)$key) selected @endif>{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--<div class="layui-inline filter-block">
                        <label class="layui-form-label">产品结构：</label>
                        <div class="layui-input-inline" style="padding-right: 14px">
                            <select name="psid" lay-verify="">
                                <option value="">全部</option>
                                @foreach($vdata['product_structures'] as $key=>$item)
                                    <option value="{{$item->id}}"  @if(request()->input('psid')===(string)$item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>--}}
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="width:130px">产品名称/编号：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="pn" autocomplete="off" value="{{request()->input('pn')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">在售地区</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="sale_province_id" name="sp" lay-verify="required" lay-filter="saleProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($vdata['provinces'] as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="sale_city_id" name="sc" lay-verify="required" lay-filter="saleCityId">
                                    <option value="">请选择城市</option>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="sale_district_id" name="sd" lay-verify="required" >
                                    <option value="">请选择区/县</option>
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

    @can('product_manage.product_create')
        <div class="op-container">
            <div class="left"></div>
            <div class="right">
                <a  class="layui-btn layui-btn-custom-blue" href="{{url('/admin/brand/product/create')}}">新增</a>
                <button  class="layui-btn layui-btn-primary" onclick="export_table()">导出</button>
            </div>
        </div>
        @endcan

        <table class="layui-table" id="tableInstance" lay-filter="tableFilter" lay-skin="line"></table>

    </div>


    <script type="text/html" id="tableToolbarTpl">

        @can('product_manage.product_detail')
        {{--<a onclick="openDetail('@{{d.id}}')" title="查看详情" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs">
            <i class="layui-icon layui-icon-list"></i>
        </a>--}}
        @endcan
        @can('product_manage.product_edit')
        <a onclick="editData('edit','@{{d.id}}')" title="编辑" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-edit"></i>
        </a>
        @endcan
        @can('product_manage.product_switch')
        @{{#     if( d.can_switch ){       }}
        @{{#     if( d.visible ){       }}
        <a href="javascript:;" title="品牌站下架" onclick="ajax_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
            下架
        </a>
        @{{#     }else{     }}
        <a href="javascript:;" title="品牌站展示" onclick="ajax_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" lay-event="edit">
             展示
        </a>
        @{{#     }     }}
        @{{#     }     }}
        @endcan
        @can('info_statistic.product.ceramic_set_top')
        @{{#     if( d.can_switch ){       }}
        @{{#     if( d.top_status_brand ){       }}
        <a href="javascript:;" title="取消置顶" onclick="ajax_member_status('@{{d.changeTopStatusApiUrl}}')" class="layui-btn-danger layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-download-circle"></i>
        </a>
        @{{#     }else{     }}
        <a href="javascript:;" title="置顶" onclick="ajax_member_status('@{{d.changeTopStatusApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" >
            <i class="layui-icon layui-icon-upload-circle"></i>
        </a>
        @{{#     }     }}
        @{{#     }     }}
        @endcan
        {{--@can('product_manage.product_switch')
        @{{#     if( d.can_switch ){       }}
        <a href="javascript:;" title="@{{ d.status_platform_title }}" onclick="ajax_status('@{{d.changeStatusPlatformApiUrl}}')" class="layui-btn-@{{ d.status_platform_style }} layui-btn  layui-btn-xs" lay-event="edit">
            @{{ d.status_platform_title }}
        </a>
        @{{#     }     }}

        @endcan--}}

        @{{#     if( d.can_delete ){       }}
        <a href="javascript:;" title="删除" onclick="destroyData('@{{d.id}}')" class="layui-btn-danger layui-btn  layui-btn-xs" lay-event="edit">
            <i class="layui-icon layui-icon-delete"></i>
        </a>
        @{{#     }     }}


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

        /*下拉dropdown控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend/dropdown')}}/'
            }).extend({
                dropdown: 'dropdown'
            }).use(['dropdown'], function(){
                var dropdown = layui.dropdown;
                var config = {} // 参见说明文档参数配置

                //执行实例
                var instance = dropdown.render(config); // 返回实例，可用于后续处理（如绑定事件）

                // 模拟当弹窗出现调用的方法
                instance.on(instance.ON_SHOW, function(){
                    // your code
                })

            });
        });
        /*下拉dropdown控件*/

        laydate.render({
            elem: '#date_start',
            value:'{{request()->input('date_start') ? request()->input('date_start') :''}}'
        });

        laydate.render({
            elem: '#date_end',
            value:'{{request()->input('date_end') ? request()->input('date_end') :''}}'

        });

        form.render();

        let base_api = '{{url('/admin/brand/product/api')}}';
        let table_api = '{{url('/admin/brand/product/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,limit:20
            ,limits:[20,50,100,200,500,1000]
            ,cols: [[ //表头
                {field: 'id', title: 'ID', fixed: 'left'}
                /*,{field: 'name', width:140,title: '名称', fixed: 'left'}*/
                ,{field: 'name',width:140, title: '名称',templet: function(d){
                    if(d.type_text=='产品' && d.can_preview==1){
                        return '<a style="color:#4183C4" target="_blank" href="/admin/brand/product/preview_product_detail/'+d.web_id_code+'">'+ d.name+'</a>'
                    }else{
                        return d.name
                    }
                }}
                    //20200326甲方说去掉指导价和定价方式
                /*,{field: 'guide_price', width:140,title: '指导价',templet: function(d){
                    return '￥'+d.guide_price
                 }}
                ,{field: 'price_way_text',width:120, title: '定价方式'}*/
                //,{field: 'type_text', width:140,title: '类型'}
                /*,{field: 'code',width:140, title: '产品编号'}*/
                ,{field: 'code',width:140, title: '产品编号',templet: function(d){
                    if(d.type_text=='产品' && d.can_preview==1){
                        return '<a style="color:#4183C4" target="_blank" href="/admin/brand/product/preview_product_detail/'+d.web_id_code+'">'+ d.code+'</a>'
                    }else{
                        return d.code;
                    }
                }}
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

                ,{field: 'styles_text',width:140, title: '可应用空间风格'}
                //,{field: 'structure_text',width:140, title: '默认产品结构'}
                ,{field: 'album_counts',width:120, title: '关联方案数'}
                ,{field: 'count_visit',width:80, title: '浏览量'}
                ,{field: 'seller_usage',width:120, title: '销售商使用量'}
                ,{field: 'count_fav',width:80, title: '收藏量'}
                ,{field: 'point_focus',width:80, title: '关注度'}
                ,{field: 'created_at',width:160, title: '创建时间'}
                ,{field: 'visible_text',width:100, title: '显示状态'}
                ,{field: 'status_text',width:100, title: '品牌站可用状态',fixed:'right'}
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

        function export_table(){
            var current_page = tableInstance.config.page.curr;
            var limit = tableInstance.config.page.limit;
            var total_count = tableInstance.config.page.count;
            if(current_page == undefined){
                layer.msg("请等待数据加载");return false;
            }
            $('#filter-form').find('input[name="page"]').val(current_page);
            $('#filter-form').find('input[name="limit"]').val(limit);
            $('#filter-form').attr('action',"{{url('/admin/brand/product/api')}}");
            $('#filter-form').submit();
        }

    </script>

    {{--页面方法专用script--}}
    <script>

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

        $(document).ready(function(){
            //监听省份变化
            form.on('select(saleProvinceId)', function(data){
                var province_id = data.value;
                get_area_global(province_id,'sale_city_id','城市');
            });

            //监听城市变化
            form.on('select(saleCityId)', function(data){
                var city_id = data.value;
                get_area_global(city_id,'sale_district_id','区/县');
            });
        });

        //当前条件下重新加载table
        function reloadTable(sortObj){
            table.reload('tableInstance',{
                url: table_api
                ,where: {
                    sort: sortObj?sortObj.field:'' //排序字段
                    ,order: sortObj?sortObj.type:'' //排序方式
                    ,ac: $('select[name="ac"]').val()
                    ,tc: $('select[name="tc"]').val()
                    ,pn: $('input[name="pn"]').val()
                    ,srs: $('select[name="srs"]').val()
                    ,clr: $('select[name="clr"]').val()
                    ,spec: $('select[name="spec"]').val()
                    ,status: $('select[name="status"]').val()
                    ,vstatus: $('select[name="vstatus"]').val()
                    ,psid: $('select[name="psid"]').val()
                    ,keyword: $('input[name="keyword"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                    ,sp: $('select[name="sp"]').val()
                    ,sc: $('select[name="sc"]').val()
                    ,sd: $('select[name="sd"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("select[name='ac']").val('')
            $("select[name='tc']").val('')
            $("select[name='pn']").val('')
            $("select[name='srs']").val('')
            $("select[name='clr']").val('')
            $("select[name='spec']").val('')
            $("select[name='status']").val('')
            $("select[name='vstatus']").val('')
            $("select[name='psid']").val('')
            $('input[name="keyword"]').val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            $("#sale_province_id").val('')
            $("#sale_city_id").val('')
            $("#sale_district_id").val('')
            form.render();
            reloadTable()
        }

        //打开详情页
        function openDetail(id){
            let page = '{{url('/admin/brand/product')}}/'+id;
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
            let page = '{{url('/admin/brand/product/create')}}';
            if(type=='edit'){
                page = '{{url('/admin/brand/product')}}/'+id+"/edit";
            }
            var width = type=='edit'?'1000px':'700px';
            var height = type=='edit'?'600px':'500px';
            layer.open({
                type: 2,
                title:'编辑信息',
                area:[width, height],
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

        function ajax_status(url) {
            if(!url){
                return false;
            }
            ajax_post(url,{}, function (res) {
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
