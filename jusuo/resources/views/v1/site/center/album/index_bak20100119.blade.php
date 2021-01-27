@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/album/index.css')}}">
    <style>
        .main-title .title-block{}
    </style>
    <div class="module-title">
        <a href="{{url('/center/album')}}" class="title-block @if(!request()->input('sts'))active @endif">
            全部方案
            <span class="active-block"></span>
        </a>
        <a href="{{url('/center/album?sts=ok')}}" class="title-block @if(request()->input('sts')=='ok')active @endif">
            已上传
            <span class="active-block"></span>
        </a>
        <a href="{{url('/center/album?sts=edit')}}" class="title-block @if(request()->input('sts')=='edit')active @endif">
            编辑中
            <span class="active-block"></span>
        </a>
        <form id="filter-form" action="">
            <input type="hidden" name="sts" value="{{request()->input('sts')}}"/>
            <input type="hidden" name="page" value="1"/>
            <div class="search-block">
                <input type="text" name="kw" value="{{request()->input('kw')}}" placeholder="搜索方案名"/>
                <div class="search-btn" onclick="submit_search()">
                    <img src="{{asset('v1/images/site/center/album/index/search-btn.png')}}"/>
                </div>
            </div>
        </form>

    </div>

    <div class="module-content">
        <div class="album-list">
            @if(count($vdata['datas'])<=0)
                <div class="no-data-tips">暂无相关数据</div>
            @endif

            @foreach($vdata['datas'] as $album)
                <div class="album-block">
                    <div class="img-block">
                        <img src="{{get_img_route($album->photo_cover,0,281,211)}}"/>
                    </div>
                    <div class="info-block">
                        <div class="album-title">{{$album->title}}</div>
                        <div class="album-tag">
                            <span class="tag-block">{{$album->count_area}}㎡</span>
                            @foreach($album->house_types as $house_type)
                                <span class="tag-block">{{$house_type}}</span>
                            @endforeach
                            @foreach($album->styles as $style)
                                <span class="tag-block">{{$style}}</span>
                            @endforeach
                        </div>
                        <div class="album-stat">
                            <div class="stat-block">
                                <img class="stat-icon" src="{{asset('v1/images/site/center/album/index/read-count-icon.png')}}"/>
                                <div class="stat-num">{{$album->count_visit}}</div>
                            </div>
                            <div class="stat-block">
                                <img class="stat-icon" src="{{asset('v1/images/site/center/album/index/like-count-icon.png')}}"/>
                                <div class="stat-num">{{$album->count_praise}}</div>
                            </div>
                            <div class="stat-block">
                                <img class="stat-icon" src="{{asset('v1/images/site/center/album/index/collect-count-icon.png')}}"/>
                                <div class="stat-num">{{$album->count_fav}}</div>
                            </div>
                            <div class="stat-block">
                                <img class="stat-icon" src="{{asset('v1/images/site/center/album/index/download-count-icon.png')}}"/>
                                <div class="stat-num">{{$album->count_use}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="operation-block">
                        @if($album->period_status == \App\Models\Album::PERIOD_STATUS_EDIT)
                            <a href="{{url('/center/album/'.$album->id.'/edit')}}" class="op-btn op-primary">编辑</a>
                        @endif
                        <div class="op-btn">分享</div>
                        <div class="more-btn">
                            …
                            <div class="sub-menu">
                                <div class="menu-content">
                                    <div class="pointer">
                                        <span></span>
                                    </div>
                                    @if($album->period_status == \App\Models\Album::PERIOD_STATUS_FINISH)
                                        <div class="menu-block" onclick="copy_album('{{$album->id}}')">复制方案</div>
                                    @endif
                                    <div class="menu-block" onclick="delete_album('{{$album->id}}')">删除</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div style="clear:both;"></div>

        </div>

        <div id="pager" class="pager"></div>

    </div>



@endsection

@section('body')

@endsection

@section('script')
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        layui.element.init();
        var form = layui.form;

        var laypage = layui.laypage

        //调用分页 完整功能
        laypage.render({
            elem: 'pager'
            ,count: '{{$vdata['datas']->total()}}'
            ,limit: '{{$vdata['datas']->perPage()}}'
            ,curr: '{{$vdata['datas']->currentPage()}}'
            ,layout: ['count','prev', 'page', 'next']
            ,jump: function(obj,first){
                //首次不执行
                if(!first){
                    $('#filter-form input[name=page]').val(obj.curr);
                    $('#filter-form').submit();
                }
            }
        });

        function submit_search(){
            var keyword = $("#filter-form input[name='kw']").val();
            if(!keyword){
                return false;
            }
            $('#filter-form').submit();
        }

        function copy_album(album_id){
            layer.load(1);
            ajax_post("{{url('/center/album/api/copy')}}",{id:album_id}, function (res) {
                layer.closeAll('loading');
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    location.reload()
                } else {
                    layer.msg(res.msg);
                }
            });
        }

        function delete_album(album_id){
            layer.load(1);
            layer.confirm('确定删除吗?', {icon: 3, title:'提示'}, function(index){
                ajax_post("{{url('/center/album/api/delete')}}",{id:album_id}, function (res) {
                    layer.closeAll('loading');
                    if (res.status == 1) {
                        layer.msg('操作成功！');
                        location.reload()
                    } else {
                        layer.msg(res.msg);
                    }
                });
                layer.close(index);
            },function(){
                layer.closeAll('loading');
            });

        }
    </script>
@endsection


