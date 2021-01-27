@extends('v1.site.center.layout',[
      'js'  => ['/v1/js/jquery.qrcode.min.js']
])

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/layout.css')}}">
    <link rel="stylesheet" href="{{asset('v1/css/site/center/form.css')}}">
    <link rel="stylesheet" href="{{asset('v1/css/site/center/album/index.css')}}">
    <style>
        .main-title .title-block{}
        .add-album-block{overflow:hidden;padding:15px;}
        .add-album-btn{float:right;margin:15px 15px 0 20px;}
        #share-box{text-align: center;}
        #share-box{text-align: center;}
        #qrcode{margin-top:30px;}
        #share-box .tips{margin-top:20px;color:#888888;}
    </style>
    <div class="detailview" >
        <div class="module-title">

            <a href="{{url('/center/album')}}" class="title-block @if(!request()->input('sts'))active @endif">
                设计方案
                <span class="active-block"></span>
            </a>

            <form class="layui-form">
                <a class="add-album-btn btn-fill" href="/center/album/create" target="_self">上传方案</a>

                <div class="status-change" >
                    <div class="layui-inline filter-block">
                        <label class="layui-form-label" style="font-size:16px;">状态</label>
                        <div class="layui-input-inline" style="width:246px;">
                            <select name="sts" lay-filter="statusSelect">
                                <option value="">全部</option>
                                @foreach($vdata['status_arr'] as $item)
                                    <option value="{{$item['id']}}" @if(request()->input('sts')==$item['id'])selected @endif>{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


            </form>





        </div>

        <div class="module-content">

            <form id="filter-form" class="layui-form"  action="" method="get">
                <input type="hidden" name="sts" value="{{request()->input('sts')}}"/>
                <input type="hidden" name="page" value="1"/>

                <div class="filter-form">

                    <div class="filter-row">
                        <div class="layui-inline filter-block">
                            <label class="layui-form-label" >方案名称</label>
                            <div class="layui-input-inline" >
                                <input type="text" name="kw" autocomplete="off" value="{{request()->input('kw')? : ''}}" placeholder="填写方案名称" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline filter-block">
                            <label class="layui-form-label">设计风格</label>
                            <div class="layui-input-inline" >
                                <select name="stl" lay-filter="">
                                    <option value="">全部</option>
                                    @foreach($vdata['styles'] as $item)
                                        <option value="{{$item->id}}" @if(request()->input('stl') == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline filter-block" style="margin-right:0;">
                            <label class="layui-form-label">方案户型</label>
                            <div class="layui-input-inline" >

                                <select name="htype" lay-filter="">
                                    <option value="">全部</option>
                                    @foreach($vdata['house_types'] as $item)
                                        <option value="{{$item->id}}" @if(request()->input('htype') == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="filter-row">
                        <div class="layui-inline filter-block">
                            <label class="layui-form-label">方案面积</label>
                            <div class="layui-input-inline" >

                                <select name="ca" lay-filter="">
                                    <option value="">全部</option>
                                    @foreach($vdata['count_area'] as $item)
                                        <option value="{{$item['id']}}" @if(request()->input('ca') == $item['id']) selected @endif>{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="layui-input-inline" style="width:163px;" >
                                <input type="text" name="area_start" placeholder="最小面积" value="{{request()->input('area_start')? : ''}}" autocomplete="off" class="layui-input">
                            </div>
                            <span>-</span>
                            <div class="layui-input-inline" style="width:163px;" >
                                <input type="text" name="area_end" placeholder="最大面积" value="{{request()->input('area_end')? : ''}}" autocomplete="off" class="layui-input">
                            </div>--}}
                        </div>

                        <div class="layui-inline filter-block" >
                            <label class="layui-form-label">上传时间</label>
                            <div class="layui-input-inline" >
                                <select name="time" lay-filter="">
                                    <option value="">全部</option>
                                    @foreach($vdata['upload_time'] as $item)
                                        <option value="{{$item['id']}}" @if(request()->input('time') == $item['id']) selected @endif>{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{--<div class="layui-input-inline" style="width:163px;">
                                <input type="text" name="date_start" id="date_start" placeholder="开始时间" value="{{request()->input('date_start')? : ''}}" autocomplete="off" class="layui-input">
                            </div>
                            <span >-</span>
                            <div class="layui-input-inline" style="width:163px;">
                                <input type="text" name="date_end" id="date_end" placeholder="结束时间" value="{{request()->input('date_end')? : ''}}" autocomplete="off" class="layui-input">
                            </div>--}}
                        </div>

                        <div class="layui-inline filter-block" style="margin-right:0;">
                            <label class="layui-form-label" >产品编号</label>
                            <div class="layui-input-inline" >
                                <input type="text" name="pc" autocomplete="off" value="{{request()->input('pc')? : ''}}" placeholder="方案中产品编号" class="layui-input">
                            </div>
                        </div>

                    </div>

                    <div class="filter-row" style="margin-bottom:0;">


                        <div class="layui-inline filter-block" style="margin-right:0;">
                            <div class="layui-input-inline" >
                                <div class="filter-submit-btn" style="margin-left:0;" onclick="$('#filter-form').submit()">筛选</div>
                            </div>
                        </div>
                    </div>


                </div>

            </form>
            <div class="album-list">
                @if(count($vdata['datas'])<=0)
                    <div class="no-data-tips">暂无相关数据</div>
                @endif

                @foreach($vdata['datas'] as $album)
                    <div class="album-block">
                        <div class="operation-block">
                            <div class="operation-main">
                                <div class="operation-entrance">
                                    <div class="entrance-main">
                                        <img src="{{asset('v1/images/site/center/album/index/operation-entrance.png')}}"/>
                                        <div class="menu-list">
                                            <div class="menu-main">
                                                <a target="_blank" href="{{url('/center/album/preview_album_detail/'.$album->web_id_code)}}"  class="menu-block ">预览</a>
                                                <a href="@if($album->period_status == \App\Models\Album::PERIOD_STATUS_EDIT){{url('/center/album/'.$album->web_id_code.'/edit')}}@else javascript:; @endif" class="menu-block @if($album->period_status != \App\Models\Album::PERIOD_STATUS_EDIT)menu-disable @endif" >编辑</a>
                                                <div @if($album->period_status == \App\Models\Album::PERIOD_STATUS_FINISH) onclick="copy_album('{{$album->web_id_code}}')" @endif class="menu-block @if($album->period_status != \App\Models\Album::PERIOD_STATUS_FINISH)menu-disable @endif">另存方案</div>
                                                <div @if($album->period_status == \App\Models\Album::PERIOD_STATUS_FINISH) onclick="ajax_status('{{$album->changeVisibleStatusApiUrl}}')" @endif class="menu-block @if($album->period_status != \App\Models\Album::PERIOD_STATUS_FINISH)menu-disable @endif">
                                                    @if($album->visible_status==\App\Models\Album::VISIBLE_STATUS_ON)下架方案 @else 上架方案 @endif
                                                </div>
                                                @if($album->status == \App\Models\Album::STATUS_PASS && $album->visible_status==\App\Models\Album::VISIBLE_STATUS_ON)
                                                    <div onclick="represent_album('{{$album->web_id_code}}')" class="menu-block">@if(!$album->is_representative_work)设为代表作 @else 取消代表作 @endif</div>

                                                @endif
                                                @if($album->status == \App\Models\Album::STATUS_PASS && $album->visible_status==\App\Models\Album::VISIBLE_STATUS_ON)
                                                    <div onclick="share_album('{{$album->web_id_code}}')" class="menu-block">分享</div>
                                                @endif
                                                @if($album->status != \App\Models\Album::STATUS_DELETE)
                                                    <div onclick="delete_album('{{$album->web_id_code}}')" class="menu-block">删除</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="img-block">
                            <img src="{{get_img_route($album->photo_cover,0,281,211)}}"/>
                        </div>
                        <div class="info-block">
                            <div class="album-title">{{$album->title}}</div>
                            <div class="album-tag">
                                <span class="tag-block">{{$album->count_area}}㎡</span>
                                @if(isset($album->house_types))
                                    @foreach($album->house_types as $house_type)
                                        <span class="tag-block">{{$house_type}}</span>
                                    @endforeach
                                @endif
                                @if($album->styles)
                                    @foreach($album->styles as $style)
                                        <span class="tag-block">{{$style}}</span>
                                    @endforeach
                                @endif

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
                                {{--<div class="stat-block">
                                    <img class="stat-icon" src="{{asset('v1/images/site/center/album/index/download-count-icon.png')}}"/>
                                    <div class="stat-num">{{$album->count_use}}</div>
                                </div>--}}
                            </div>
                        </div>
                        <div class="bottom-block">
                            <div class="left-block">{{time_ago($album->created_at)}}</div>
                            <div class="right-block" @if($album->status == \App\Models\Album::STATUS_REJECT)onmouseover="show_verify_tips(this,'{{$album->verify_note}}')" onmouseleave="hide_verify_tips()" @endif>
                                <?php
                                $status_style = '';
                                if(
                                        $album->period_status==\App\Models\Album::PERIOD_STATUS_EDIT &&
                                        $album->status == \App\Models\Album::STATUS_VERIFYING
                                ){
                                    $status_style = 'active';
                                }else if($album->status == \App\Models\Album::STATUS_REJECT){
                                    $status_style = 'danger';
                                }else if($album->status == \App\Models\Album::STATUS_PASS
                                        && $album->period_status == \App\Models\Album::PERIOD_STATUS_FINISH){
                                    $status_style = 'active';
                                }
                                if($album->visible_status == \App\Models\Album::VISIBLE_STATUS_OFF){
                                    $status_style = 'c-disable';
                                }

                                ?>
                                <span class="circle {{$status_style}}"></span><span class="status-name {{$status_style}}">{{$album->status_text}}</span>
                            </div>
                        </div>
                        {{--<div class="operation-block">
                            @if($album->period_status == \App\Models\Album::PERIOD_STATUS_EDIT)
                                <a href="{{url('/center/album/'.$album->web_id_code.'/edit')}}" class="op-btn op-primary">编辑</a>
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
                                            <div class="menu-block" onclick="copy_album('{{$album->web_id_code}}')">复制方案</div>
                                        @endif
                                        <div class="menu-block" onclick="delete_album('{{$album->web_id_code}}')">删除</div>
                                    </div>

                                </div>
                            </div>
                        </div>--}}
                    </div>
                @endforeach

                <div style="clear:both;"></div>

            </div>

            <div id="pager" class="pager"></div>

        </div>
    </div>



@endsection

@section('body')
    <div id="share-box" style="display:none;">
        <div class="qrcode-img" id="qrcode" onclick="refresh()"></div>
        <div class="tips">
            请使用微信扫描二维码
        </div>

    </div>

@endsection

@section('script')
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


        form.on('select(statusSelect)', function(data){
            $('#filter-form').find('input[name=sts]').val(data.value);
            $('#filter-form').submit();

        });

        form.render();

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

        var verify_tips = null;
        function show_verify_tips(obj,tips){
            verify_tips = layer.tips(tips,$(obj),{
                tips:[3,'#ff4040']
            })
        }

        function hide_verify_tips(){
            layer.close(verify_tips)
        }

        function ajax_status(url) {
            if(!url){
                return false;
            }
            //显示loading
            layer.load(1);
            ajax_post(url,{}, function (res) {
                layer.closeAll('loading')
                if (res.status == 1) {
                    layer.msg('操作成功！');
                    location.reload()
                } else {
                    layer.msg(res.msg);
                }
            });
        }

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

        function share_album(album_id){
            $('#qrcode').html('');
            $('#qrcode').qrcode({width:150,height:150,text:"{{url('/mobile/album/s')}}"+"/"+album_id});
            layer.open({
                type: 1,
                title :'分享到微信',
                skin: 'layui-layer-rim', //加上边框
                area: ['250px', '300px'], //宽高
                content: $('#share-box')
            });
        }

        //设为代表作
        function represent_album(album_id){
            layer.load(1);

            ajax_post("{{url('/center/album/api/change_represent')}}",{id:album_id}, function (res) {
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


