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
    </style>
@endsection

@section('content')
    <div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-card-header">产品信息</div>
            <div class="layui-card-body" style="padding: 15px;">
                <div class="layui-form-item">
                    <label class="layui-form-label">产品名称</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->name or ''}}
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">指导价</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->guide_price or ''}}
                        </div>
                    </div>
                </div>

                {{--<div class="layui-form-item">
                    <label class="layui-form-label">系统编号</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->sys_code}}
                        </div>
                    </div>
                </div>--}}

                <div class="layui-form-item">
                    <label class="layui-form-label">类型</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{\App\Models\ProductCeramic::typeGroup($data->type)}}
                            @if($data->type==\App\Models\ProductCeramic::TYPE_ACCESSORY)
                                （父产品：{{$data->parent_product or ''}}）
                            @endif
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">产品编号</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->code or ''}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">核心工艺</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->key_technology or ''}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">理化性能</label>
                    <div class="layui-input-inline">
                        @if($data->physical_chemical_property)
                            @foreach(unserialize($data->physical_chemical_property) as $item)
                                <div class="layui-form-mid layui-word-aux" style="float:none">
                                    <div>{{$item}}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">功能特征</label>
                    <div class="layui-input-inline">
                        @if($data->function_feature)
                            @foreach(unserialize($data->function_feature) as $item)
                                <div class="layui-form-mid layui-word-aux" style="float:none">
                                    <div>{{$item}}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">顾客价值</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->customer_value or ''}}
                        </div>
                    </div>
                </div>

                {{--<div class="layui-form-item">
                    <label class="layui-form-label">默认产品结构</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->structure_text or ''}}
                        </div>
                    </div>
                </div>--}}

                <div class="layui-form-item">
                    <label class="layui-form-label">系列</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->series_text or ''}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">规格</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->spec_text or ''}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">应用类别</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->apply_categories_text or ''}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">工艺类别</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->technology_categories_text or ''}}
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">色系</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->colors_text or ''}}
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">表面特征</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->surface_features_text or ''}}
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">可应用空间风格</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                            {{$data->styles_text or ''}}
                        </div>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label">产品图</label>
                    <div class="layui-input-block">
                        @if($data->photo_product)
                            @foreach(unserialize($data->photo_product) as $item)
                                <div class="layui-form-mid layui-word-aux">
                                    <div class="verify-img"  id="verify-img-4">
                                        <img onclick="click_img('{{$item or ''}}')"  src="{{$item or ''}}" alt="产品图"/>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">实物图</label>
                    <div class="layui-input-block">
                        @if($data->photo_practicality)
                            @foreach(unserialize($data->photo_practicality) as $item)
                                <div class="layui-form-mid layui-word-aux">
                                    <div class="verify-img"  id="verify-img-4">
                                        <img onclick="click_img('{{$item or ''}}')"  src="{{$item or ''}}" alt="实物图"/>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">产品配件</label>
                    <div class="layui-input-block">
                        @if($data->accessories)
                        @foreach($data->accessories as $item)
                            <div class="layui-form-mid layui-word-aux" >
                                <div class="multiple-show-block" >
                                    <div class="row">配件编号：{{$item['code'] or '无信息'}}，配件规格：长 [{{$item['spec_length'] or '无信息'}}] / 宽 [{{$item['spec_width'] or '无信息'}}]</div>
                                    <div class="row">加工工艺：{{$item['technology'] or '无信息'}}</div>
                                    <div class="row" style="overflow:hidden;">
                                        @if(isset($item['photo']))
                                            @foreach(unserialize($item['photo']) as $photo)
                                                <div class="verify-img"  id="verify-img-4">
                                                    <img onclick="click_img('{{$photo or ''}}')"  src="{{$photo or ''}}" alt="配件图"/>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">产品搭配</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux" >

                        @if($data->collocations)
                            @foreach($data->collocations as $item)
                                    <div class="multiple-show-block" >
                                        @if(isset($item['note']))
                                        <div class="row">应用说明：{{$item['note'] or ''}}</div>
                                        @else
                                            <div class="row">应用说明：无</div>
                                        @endif
                                        @if(isset($item['collocation_id']) && $item['collocation_id']>0)
                                            <div class="row">关联产品：产品名称：{{$item['name'] or ''}}，产品编号：{{$item['code'] or ''}}</div>
                                        @else
                                            <div class="row">关联产品：无</div>
                                        @endif
                                        <div class="row">搭配图：</div>
                                        <div class="row" style="overflow:hidden;">
                                            @if(isset($item['photo']))
                                                @foreach(unserialize($item['photo']) as $photo)
                                                    <div class="verify-img"  id="verify-img-4">
                                                        <img onclick="click_img('{{$photo or ''}}')"  src="{{$photo or ''}}" alt="搭配图"/>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">空间应用</label>
                    <div class="layui-input-block">
                        @if($data->spaces)
                            @foreach($data->spaces as $item)
                                <div class="layui-form-mid layui-word-aux" >
                                    <div class="multiple-show-block" >
                                        <div class="row">应用摘要：{{$item['title'] or ''}}</div>
                                        <div class="row">详细说明文字：{{$item['note'] or ''}}</div>
                                        <div class="row">应用图：</div>
                                        <div class="row" style="overflow:hidden;">
                                            <div class="verify-img"  id="verify-img-4">
                                                <img onclick="click_img('{{$item['photo'] or ''}}')"  src="{{$item['photo'] or ''}}" alt="应用图"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">产品视频</label>
                    <div class="layui-input-block">
                        @if($data->photo_video)
                            @foreach(unserialize($data->photo_video) as $key=>$item)
                                <div class="layui-form-mid layui-word-aux" style="float:none;margin-bottom:10px;">
                                    <a target="_blank" href="{{$item or ''}}" >产品视频{{$key+1}}[查看]</a>
                                </div>

                            @endforeach
                        @endif
                    </div>

                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label">&nbsp;</label>
                    <div class="layui-input-inline" style="width: 300px">
                        @can('product_manage.product_verify.wait_verify_pass')
                            <a href="javascript:;" onclick="ajax_pass_product('{{url('admin/brand/product/verify/wait/api/'.$data->id.'/approval')}}')" class="layui-btn-success layui-btn" lay-event="edit">
                                <i class="layui-icon layui-icon-ok"></i>通过
                            </a>
                        @endcan
                        @can('product_manage.product_verify.wait_verify_reject')
                            <a href="javascript:;" onclick="ajax_reject_product('{{url('admin/brand/product/verify/wait/api/'.$data->id.'/reject')}}')" class="layui-btn-danger layui-btn" lay-event="del">
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

        function ajax_pass_product(url) {
            layer.confirm('你确定审核通过该产品吗？', {icon: 3, title: '提示！'}, function () {
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

        function ajax_reject_product(url) {
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