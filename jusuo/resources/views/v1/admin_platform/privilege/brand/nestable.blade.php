@extends('v1.admin_platform.layout',[
   'css'=>[],
   'js'=>['/v1/static/js/jquery.nestable.js']
])

@section('content')
    <style>
        .dd { position: relative; display: block; margin: 10px; padding: 0; list-style: none; font-size: 13px; line-height: 20px; }

        .dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
        .dd-list .dd-list { padding-left: 30px; }
        .dd-collapsed .dd-list { display: none; }

        .dd-item,
        .dd-empty,
        .dd-placeholder { display: block; position: relative; margin: 0; padding: 0;}

        .dd-handle {
            display: block;

            margin: 1px 0;
            padding: 8px 10px;
            color: #333;
            text-decoration: none;
            border: 1px solid #ddd;
            background: #fff;
        }
        .dd-handle:hover { color: #2ea8e5; background: #fff; }

        .dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 20px; margin: 5px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 12px; line-height: 1; text-align: center; font-weight: bold; }
        .dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
        .dd-item > button[data-action="collapse"]:before { content: '-'; }

        .dd-placeholder { margin: 5px 0; padding: 0; min-height: 30px; background: #f2fbff; border: 1px dashed #b6bcbf; box-sizing: border-box; -moz-box-sizing: border-box; }

        .dd-dragel { position: absolute; pointer-events: none; z-index: 9999; }
        .dd-dragel > .dd-item .dd-handle { margin-top: 0; }
        .dd-dragel .dd-handle {
            -webkit-box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
            box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
        }
    </style>
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>账号管理</cite></a><span lay-separator="">/</span>
            <a><cite>品牌权限树</cite></a>
            <div class="right" style="margin-right:15px;">
                <button onclick="updateSort()" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-ok" style="font-size:12px!important;"></i>提交更新排序
                </button>
            </div>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="dd">
            <ol class="dd-list">
                @foreach($privileges_tree as $tree)
                    <li class="dd-item" data-id="{{ $tree->id }}">
                        <div class="dd-handle">{{ $tree->display_name }}</div>
                        @if ($tree->child)
                            <ol class="dd-list">
                                @foreach ($tree->child as $child)
                                    @include('v1.admin_platform.privilege.brand.nestable-child', ['children' => $child])
                                @endforeach
                            </ol>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>

    </div>


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

        $('.dd').nestable({ /* config options */ });
        $('.dd').nestable().on('change', function(){});

        function updateSort(){
            var r = $('.dd').nestable('serialize');
            ajax_post("{{url('/admin/platform/privilege_brand/api/nestable_sort')}}",
                {data:JSON.stringify(r)}, function (result ) {
                if (result.status == 1) {
                    layer.msg('更新排序成功！')
                } else {
                    //将提交按钮恢复
                    $('.layui-btn').attr('disabled',false);
                    layer.msg(result.msg);
                }
            });
        }

    </script>

    {{--页面方法专用script--}}
    <script>


    </script>
@endsection
