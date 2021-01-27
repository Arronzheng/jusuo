@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/info_verify.css')}}">
    <style>
        .pass-tips{
            margin-bottom:20px;color:#3e82f7;margin-left:20px;
        }
    </style>

    <div class="detailview show">
        @include('v1.site.center.components.info_verify.global_tab',[
             'log_status'=>$log_status,'active'=>'basic'
             ])


        <div id="verify-form">
            @if($log_status == 1)
                {{--审核中--}}
                您提交的申请正在审核中，请耐心等候。
            @elseif($log_status==-1)
                {{--审核已通过--}}
                {{--<div class="pass-tips">您提交的申请已通过。</div>--}}

                <form>
                    <div class="layui-form-item">
                        <label class="layui-form-label">设计师类型</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{\App\Models\DesignerDetail::designerTypeGroup($designerDetail->self_designer_type)}}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">服务城市</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$designerDetail->area_serving_text}}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">工作单位</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$designerDetail->self_organization}}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">擅长风格</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$designerDetail->style_text}}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">擅长空间</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$designerDetail->space_text}}
                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">服务专长</label>
                        <div class="layui-input-inline">
                            <div class="layui-form-mid layui-word-aux">
                                {{$designerDetail->self_expert}}
                            </div>
                        </div>
                    </div>


                </form>
            @else
                <form class="layui-form" action="" lay-filter="component-form-group">
                    <div class="layui-form-item">
                        <label class="layui-form-label">设计师类型</label>
                        <div class="layui-input-inline">
                            <select name="self_designer_type" lay-verify="">
                                @foreach(\App\Models\DesignerDetail::designerTypeGroup() as $key=> $item)
                                    <option value="{{$key}}" >{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">服务城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="company_province_id" name="area_serving_province" lay-verify="required" lay-filter="companyProvinceId">
                                    <option value="">请选择省</option>
                                    @foreach($provinces as $item)
                                        <option value="{{$item->id}}" >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="company_city_id" name="area_serving_city" lay-verify="required" lay-filter="companyCityId">
                                    <option value="">请选择城市</option>
                                    @if(isset($cities))
                                        @foreach($cities as $item)
                                            <option value="{{$item->id}}" >{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select name="area_serving_district" lay-verify="required" id="company_district_id" lay-filter="companyDistrictId">
                                    <option value="">请选择区/县</option>
                                    @if(isset($param['districts']))
                                        @foreach($param['districts'] as $item)
                                            <option value="{{$item->id}}" >{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">工作单位</label>
                        <div class="layui-input-inline">
                            <input type="text" name="self_organization" value=""  lay-verify="required" {{--maxlength="20"--}} autocomplete="off" placeholder="请输入工作单位" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">擅长风格</label>
                        <div class="layui-input-block">
                            @foreach($styles as $item)
                                <input type="checkbox" class="type" name="style[]" lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}">
                            @endforeach
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">擅长空间</label>
                        <div class="layui-input-block">
                            @foreach($spaces as $item)
                                <input type="checkbox" class="type" name="space[]" lay-skin="primary" title="{{$item->name}}" lay-filter="type" value="{{$item->id}}">
                            @endforeach
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">服务专长</label>
                        <div class="layui-input-inline">
                            <input type="text" name="self_expert" value=""  lay-verify="required" {{--@if($config['self_expert_character_limit'])maxlength="{{$config['self_expert_character_limit']}}" @endif--}} autocomplete="off" placeholder="请输入服务专长" class="layui-input">
                        </div>
                    </div>

                    <hr style="margin:50px 0;"/>

                    <div class="layui-form-item">
                        <label class="layui-form-label">&nbsp;</label>
                        <div class="layui-input-inline">
                            <button type="button" class="layui-btn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>

                    {{csrf_field()}}
                    <input type="hidden" name="id" value="1"/>

                </form>
            @endif
        </div>
    </div>


@endsection

@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        layui.element.init();

        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({

        });

        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            ajax_post('{{url($url_prefix.'center/submit_basic_info')}}',
                form_field,
                function(result){
                    // console.log(result);
                    if(result.status){
                        layer.alert(result.msg,{closeBtn :0},function(){
                            window.location.reload();
                        });
                        // layer.msg(result.msg);

                    }else{
                        layer.msg(result.msg);
                    }
                });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

        @if($log_status == 2)
        {{--var msg = '您的申请以被驳回,驳回原因'+{{$$log['notice']}}--}}
        layer.alert('您的申请已被驳回，请重新提交审核。驳回原因：'+'         '+'{{$log->remark}}'+'  ');
        @endif

        $(document).ready(function(){
            //监听省份变化
            form.on('select(companyProvinceId)', function(data){
                var province_id = data.value;
                get_area(province_id,'company_city_id','城市');
            });

            //监听城市变化
            form.on('select(companyCityId)', function(data){
                var city_id = data.value;
                get_area(city_id,'company_district_id','区/县');
            });
        });

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url('/common/get_area_children?pi=')}}'+area_id,function(res){
                if(res.status && res.data.length>0){
                    $.each(res.data,function(k,v){
                        options+='<option value="'+ v.id+'">'+ v.name+'</option>';
                    });
                    $('#'+next_elem).html(options);
                    form.render('select');
                }
            });
        }
    </script>
@endsection
