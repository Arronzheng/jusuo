@extends('v1.admin_platform.layout',[
   'css'=>['/v1/css/admin/module/table.css'],
   'js'=>['/v1/js/admin/module/table.js']
])

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>信息统计</cite></a><span lay-separator="">/</span>
            <a><cite>方案统计</cite></a>
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
                        <label class="layui-form-label">所属品牌</label>
                        <div class="layui-input-inline">
                            <input type="text" autocomplete="off" id="brand-select"  value="" readonly placeholder="点击搜索并选择" class="layui-input">
                            <input type="hidden" name="bi" value="" id="h-belong-brand">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label">空间类别</label>
                        <div class="layui-input-inline">
                            <select class="n-section-space-type" name="spt" lay-verify="required">
                                <option value="">全部</option>
                                @foreach($vdata['space_types'] as $key=> $item)
                                    <option value="{{$item->id}}" @if(isset($section) && isset($section->space_type_id) && $section->space_type_id==$item->id)selected @endif >{{$item->name}}</option>
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
                        <label class="layui-form-label">户型类别：</label>
                        <div class="layui-input-inline" >
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
                            <input type="text" name="area_start" placeholder="最小面积" value="<?php echo e(request()->input('area_start')? : ''); ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">-</div>
                        <div class="layui-input-inline" style="width:100px">
                            <input type="text" name="area_end" placeholder="最大面积" value="<?php echo e(request()->input('area_end')? : ''); ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" >标题：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="tle" autocomplete="off" value="{{request()->input('name')? : ''}}" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">所在城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="company_province_id" name="abp" lay-verify="required" lay-filter="areaBelongProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($vdata['provinces'] as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="company_city_id" name="abc" lay-verify="required" lay-filter="areaBelongCityId">
                                    <option value="">请选择城市</option>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="company_district_id" name="abd" lay-verify="required" >
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


        @can('info_statistic.album_set_top')
        @{{#     if( d.can_change_status ){       }}
            @{{#     if( d.top_status_platform ){       }}
            <a href="javascript:;" title="取消置顶" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-danger layui-btn  layui-btn-xs" >
                <i class="layui-icon layui-icon-download-circle"></i>
            </a>
            @{{#     }else{     }}
            <a href="javascript:;" title="置顶" onclick="ajax_member_status('@{{d.changeStatusApiUrl}}')" class="layui-btn-success layui-btn  layui-btn-xs" >
                <i class="layui-icon layui-icon-upload-circle"></i>
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
                        url:'{!! url($url_prefix.'admin/platform/info_statistics/album/api/get_brands') !!}',
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

        let base_api = '{{url('/admin/platform/info_statistics/album/api')}}';
        let table_api = '{{url('/admin/platform/info_statistics/album/api')}}';
        //转换静态表格
        var table = layui.table;
        let tableInstance = table.render({
            elem: '#tableInstance'
            ,url: table_api //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'album_id', title: 'ID', width:80, fixed: 'left'}
                ,{field: 'code',width:120, title: '方案编号'}
                ,{field: 'type_text',width:140, title: '来源'}
                ,{field: 'title',width:160, title: '标题'}
                ,{field: 'realname',width:140, title: '设计师真实姓名',}
                ,{field: 'area_text',width:160,title: '方案所在省市区'}
                ,{field: 'house_type_text',width:100,title: '户型'}
                ,{field: 'style_text',width:100,title: '风格'}
                ,{field: 'count_area',width:100,title: '面积'}
                ,{field: 'space_count',width:100, title: '空间数'}
                ,{field: 'album_section_space_type',width:120,title: '空间风格'}
                ,{field: 'product_count',width:120, title: '关联产品数'}
                ,{field: 'count_visit',width:100,sort: true, title: '浏览量'}
                ,{field: 'count_praise',width:100,sort: true, title: '点赞量'}
                ,{field: 'count_fav',width:100,sort: true, title: '收藏量'}
                /*,{field: 'count_use',width:140,sort: true, title: '下载或复制量'}
                ,{field: 'count_share',width:100,sort: true, title: '分享次数'}*/
                ,{field: 'point_focus',width:100,sort: true, title: '关注度'}
                ,{field: 'comment_count',width:100, title: '评论数'}
                ,{field: 'upload_time',width:160,sort: true,  title: '上传时间'}
                ,{field: 'operation', title: '操作' ,width:100 ,fixed:'right',templet:'#tableToolbarTpl'}

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
                    ,bi: $('input[name="bi"]').val()
                    ,spt: $('select[name="spt"]').val()
                    ,stl: $('select[name="stl"]').val()
                    ,ht: $('select[name="ht"]').val()
                    ,area_start: $('input[name="area_start"]').val()
                    ,area_end: $('input[name="area_end"]').val()
                    ,abp: $('select[name="abp"]').val()
                    ,abc: $('select[name="abc"]').val()
                    ,abd: $('select[name="abd"]').val()
                    ,tle: $('input[name="tle"]').val()
                    ,date_start: $('input[name="date_start"]').val()
                    ,date_end: $('input[name="date_end"]').val()
                }
            });
        }

        //重置筛选条件
        function resetFilter(){
            $("input[name='bi']").val('')
            $("select[name='spt']").val('')
            $("select[name='stl']").val('')
            $("select[name='ht']").val('')
            $("input[name='area_start']").val('')
            $("input[name='area_end']").val('')
            $("select[name='abp']").val('')
            $("select[name='abc']").val('')
            $("select[name='abd']").val('')
            $('input[name="tle"]').val('')
            $("#company_province_id").val('')
            $("#company_city_id").val('')
            $("#company_district_id").val('')
            $('input[name="date_start"]').val('')
            $('input[name="date_end"]').val('')
            form.render();
            reloadTable()
        }

        //打开详情框
        function openDetail(id){
            let page = '{{url('/admin/platform/info_statistics/album')}}/'+id;
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
