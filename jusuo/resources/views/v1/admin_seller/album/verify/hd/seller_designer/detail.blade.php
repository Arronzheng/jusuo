@extends('v1.admin.components.layout.blank_body',[])

@section('style')
    <style>
        .verify-img{
            float:left;margin-right:15px;
        }
        .verify-img:hover{
            cursor:pointer
        }
        .verify-img img{
            width:120px;
        }
        .verify-img img:hover{
            cursor: pointer;
        }
        .layui-form-label{
            width:100px;
        }
        .multiple-show-block{
            float:none;border:1px solid #dedede;padding:10px 10px 0 10px;
        }
        .multiple-show-block .verify-img img{
            width:80px;
        }
        .multiple-show-block .row{
            margin-bottom:10px;
        }
        .layui-table td, .layui-table th{
            font-size:12px;
        }
        .layui-tab-title li{
            font-size:12px;
        }
    </style>
@endsection

@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <div class="layui-form-item">
                    <label class="layui-form-label">方案标题</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->title}}
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">封面图</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            <div class="verify-img" id="verify-img-1">
                                <img width="400px" onclick="click_img('{{$data['photo_cover'] or ''}}')" src="{{$data['photo_cover'] or ''}}" alt="封面图"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所在城市</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->area_text}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">街道</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->address_street}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">小区</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->address_residential_quarter}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">所在楼栋</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->address_building}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">所在户型号</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->address_layout_number}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">户型类别</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->house_type_text}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">总面积</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->count_area}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">风格</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->style_text}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">设计说明</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->description_design}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">户型图</label>
                    <div class="layui-input-block">
                        @if($data->photo_layout)
                            @foreach(unserialize($data->photo_layout) as $item)
                                <div class="layui-form-mid layui-word-aux">
                                    <div class="verify-img"  id="verify-img-4">
                                        <img onclick="click_img('{{$item or ''}}')"  src="{{$item or ''}}" alt="户型图"/>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">空间图</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            @if($data->album_sections)
                                @foreach($data->album_sections as $section)
                                    <div style="padding:10px;border:1px solid #dedede;">
                                        <div style="margin-bottom:8px;">空间类型：{{$section->space_type_text}}</div>
                                        <div style="margin-bottom:8px;">标题：{{$section->title}}</div>
                                        <div style="margin-bottom:8px;" >空间面积：{{$section->count_area}}平方米</div>
                                        <div style="margin-bottom:8px;" >风格：{{$section->style_text}}</div>
                                        <div class="layui-tab">
                                            <ul class="layui-tab-title">
                                                <li class="layui-this">空间设计</li>
                                                <li>产品应用</li>
                                                <li>施工</li>
                                            </ul>
                                            <div class="layui-tab-content">
                                                <div class="layui-tab-item layui-show">
                                                    @if(isset($section->content))
                                                        <div style="margin-bottom:10px;">
                                                            @foreach($section->content['design']['photos'] as $photo)
                                                                <img width="100px" onclick="click_img('{{$photo or ''}}')" src="{{$photo or ''}}" alt=""/>
                                                            @endforeach
                                                        </div>
                                                        <div >空间说明：{{$section->content['design']['description'] or ''}}</div>
                                                    @endif
                                                </div>
                                                <div class="layui-tab-item">
                                                    @if(isset($section->content))
                                                        <div style="margin-bottom:10px;">
                                                            @foreach($section->content['product']['photos'] as $photo)
                                                                <img width="100px" onclick="click_img('{{$photo or ''}}')" src="{{$photo or ''}}" alt=""/>
                                                            @endforeach
                                                        </div>
                                                        <div >空间说明：{{$section->content['product']['description'] or ''}}</div>
                                                    @endif
                                                </div>
                                                <div class="layui-tab-item">
                                                    @if(isset($section->content))
                                                        <div style="margin-bottom:10px;">
                                                            @foreach($section->content['build']['photos'] as $photo)
                                                                <img width="100px" onclick="click_img('{{$photo or ''}}')" src="{{$photo or ''}}" alt=""/>
                                                            @endforeach
                                                        </div>
                                                        <div >空间说明：{{$section->content['build']['description'] or ''}}</div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            @endif
                        </div>

                    </div>

                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">产品清单</label>
                    <div class="layui-input-inline" style="width:600px;">
                        <table class="layui-table" >
                            <thead>
                            <tr>
                                <th>品牌</th>
                                <th>产品名称</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data->product_ceramics as $product)
                            <tr>
                                <td>{{$product->brand_name}}</td>
                                <td>{{$product->name}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label">&nbsp;</label>
                    <div class="layui-input-inline" style="width: 300px">
                        @can('album_manage.verify.hd_photo_album_pass')
                            <a href="javascript:;" onclick="ajax_pass_album('{{url('admin/seller/album/verify/hd/seller_designer/api/'.$data->id.'/approval')}}')" class="layui-btn-success layui-btn" lay-event="edit">
                                <i class="layui-icon layui-icon-ok"></i>通过
                            </a>
                        @endcan
                        @can('album_manage.verify.hd_photo_album_reject')
                            <a href="javascript:;" onclick="ajax_reject_album('{{url('admin/seller/album/verify/hd/seller_designer/api/'.$data->id.'/reject')}}')" class="layui-btn-danger layui-btn" lay-event="del">
                                <i class="layui-icon layui-icon-close"></i>驳回
                            </a>
                        @endcan
                    </div>
                </div>



        </div>

    </div>
@endsection

@section('script')
    <script>

        function click_img(url){
            if(url){
                parent.window.open(url);
            }
        }

        function ajax_pass_album(url) {
            layer.confirm('你确定审核通过该方案吗？', {icon: 3, title: '提示！'}, function () {
                //显示loading
                layer.load(1);
                //将提交按钮设置不可用
                $('.layui-btn').attr('disabled',true);


                ajax_post(url,{}, function (result ) {
                    if (result.status == 1) {
                        layer.msg(result.msg, {
                            time: 1500
                        }, function(){
                            parent.reloadTable();
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭

                        });
                    } else {
                        //将提交按钮恢复
                        $('.layui-btn').attr('disabled',false);
                        layer.msg(result.msg);
                    }
                });
            })
        }

        function ajax_reject_album(url) {
            layer.prompt({title: '驳回理由', formType: 2}, function(text, index){

                if(!text){layer.msg('请填写驳回理由！');return false;}
                //显示loading
                layer.load(1);
                //将提交按钮设置不可用
                $('.layui-btn').attr('disabled',true);

                ajax_post(url,{reason:text}, function (result ) {
                    if (result.status == 1) {
                        layer.msg(result.msg, {
                            time: 1500
                        }, function(){
                            parent.reloadTable();
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭

                        });
                    } else {
                        //将提交按钮恢复
                        $('.layui-btn').attr('disabled',false);
                        layer.msg(result.msg);
                    }
                });

                layer.close(index);
                //layer.msg('演示完毕！驳回理由是：'+text);

            });
        }

    </script>
@endsection