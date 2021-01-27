@extends('v1.site.components.layout.form_layout',[
   'css'=>[
        '/v1/css/site/mall/sheet.css',
   ],
   'js'=>[
   ]
])

@section('content')
    <style>
        .layui-form-item .layui-input-inline{
            width: 170px !important;;
        }
    </style>
    <div style="padding:20px 0;">
        <div id="verify-form">
            <form class="layui-form" action="" lay-filter="component-form-group">

                <form class="layui-form" action="" lay-filter="component-form-group">
                    {{csrf_field()}}
                    <div class="layui-form-item">
                        <label class="layui-form-label">收货人</label>
                        <div class="layui-input-inline">
                            <input id="i-receiver-name" name="receiver_name" value="{{$data->receiver_name or ''}}"  lay-verify="required" autocomplete="off" placeholder="请填写收货人姓名" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">联系电话</label>
                        <div class="layui-input-inline">
                            <input id="i-receiver-tel" name="receiver_tel" value="{{$data->receiver_tel or ''}}"  lay-verify="required" autocomplete="off" placeholder="请填写收货人联系电话" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">收货城市</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">
                                <select id="province_id" name="province_id" lay-verify="required" lay-filter="provinceId">
                                    <option value="">请选择省</option>
                                    @foreach($provinces as $item)
                                        <option value="{{$item->id}}" @if(isset($data) && $data->province_id == $item->id) selected @endif >{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select id="city_id" name="city_id" lay-verify="required" lay-filter="cityId">
                                    <option value="">请选择城市</option>
                                    @if(isset($cities))
                                        @foreach($cities as $item)
                                            <option value="{{$item->id}}" @if(isset($data) && $data->city_id == $item->id) selected @endif >{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select name="area_id" id="area_id" lay-verify="required"  lay-filter="districtId">
                                    <option value="">请选择区/县</option>
                                    @if(isset($districts))
                                        @foreach($districts as $item)
                                            <option value="{{$item->id}}" @if(isset($data) && $data->area_id == $item->id) selected @endif >{{$item->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">详细地址</label>
                        <div class="layui-input-inline">
                            <input id="i-receiver-address" name="receiver_address" value="{{$data->receiver_address or ''}}"  lay-verify="required" autocomplete="off" placeholder="请填写详细的收货地址" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item submit-container">
                        <div class="layui-input-block">
                            {{csrf_field()}}
                            @if(isset($data))
                                <input type="hidden" name="id" value="{{$data->id}}" />
                            @endif
                            <button class="layui-btn layui-btn-custom-blue" id="submitBtn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                            {{--<button type="reset" class="layui-btn layui-btn-primary">重置</button>--}}
                        </div>
                    </div>
                </form>

            </form>

        </div>


    </div>






@endsection

@section('body')

@endsection

@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form
                ,layer = layui.layer
                ,layedit = layui.layedit
                ,upload = layui.upload;

        var laydate = layui.laydate;


        layui.element.init();

        form.render();

        $(function(){
            //监听省份变化
            form.on('select(provinceId)', function(data){
                var province_id = data.value;
                get_area_global(province_id,'city_id','城市');
            });

            //监听城市变化
            form.on('select(cityId)', function(data){
                var city_id = data.value;
                get_area_global(city_id,'area_id','区/县');
            });
        })

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/mall/api/address')}}";
        let submitMethod = "";
        @if(isset($data))
                submitAction = "{{url('/mall/api/address')}}/{{$data->id}}";
        submitMethod = "PUT";
        @endif

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;

            //提交方式
            if(submitMethod){
                form_field._method = submitMethod;
            }
            ajax_post(submitAction,
                    form_field,
                    function(result){
                        if(result.status){
                            layer.msg(result.msg, {
                                time: 1500
                            }, function(){
                                parent.getAddressList();
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.layer.close(index); //再执行关闭

                            });
                        }else{
                            //将提交按钮恢复
                            $('#submitBtn').attr('disabled',false);
                            layer.msg(result.msg);
                        }
                    },function(result){
                        //将提交按钮恢复
                        $('#submitBtn').attr('disabled',false);
                        layer.msg('操作失败');
                    });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
    </script>

@endsection


