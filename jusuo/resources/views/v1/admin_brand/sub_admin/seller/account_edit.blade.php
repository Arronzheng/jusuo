@extends('v1.admin.components.layout.form_layout',[])
@section('content')
    <style>
        .layui-form-item .layui-form-label{
            width:120px;
        }
    </style>
    <div class="layui-form" lay-filter="layuiadmin-app-form-list" id="layuiadmin-app-form-list" style="padding: 20px 30px 400px 0;">
        <form>
            {{--销售商管理员的新建暂时没有登录账号显示，因为销售商还没有建立--}}
            @if(!isset($data))
            <div class="layui-form-item">
                <label class="layui-form-label">登录用户名</label>
                <div class="layui-input-inline">
                    <input type="text" name="login_username" value="{{$data->login_username or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">公司名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="seller_name" value="{{$data->seller_name or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">联系人</label>
                <div class="layui-input-inline">
                    <input type="text" name="contact_name" value="{{$data->contact_name or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </div>
            @endif

            <div class="layui-form-item">
                <label class="layui-form-label">服务城市</label>
                <div class="layui-input-inline">
                    <div style="margin-bottom:10px;">
                        <button class="layui-btn layui-btn-sm" type="button" onclick="add_area_serving_block(this)" > + 添加服务城市</button>
                    </div>
                </div>
            </div>
            <div id="area-serving-list">
                @if(isset($data))
                    @foreach($data->area_serving_cities as $index=>$item)
                        @include("v1.admin_brand.sub_admin.seller.components.area_serving_tpl",['index_i'=>$index,'data'=>$item,'provinces'=>$provinces])
                    @endforeach
                @endif
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">可见城市</label>
                <div class="layui-input-inline">
                    <div style="margin-bottom:10px;">
                        <button class="layui-btn layui-btn-sm" type="button" onclick="add_area_visible_block(this)" > + 添加可见城市</button>
                    </div>
                </div>
            </div>
            <div id="area-visible-list">
                @if(isset($data))
                    @foreach($data->area_visible_cities as $index=>$item)
                        @include("v1.admin_brand.sub_admin.seller.components.area_visible_tpl",['index_i'=>$index,'data'=>$item,'provinces'=>$provinces])
                    @endforeach
                @endif
            </div>

            @if(!isset($data))
                <div class="layui-form-item">
                    <label class="layui-form-label">级别</label>
                    <div class="layui-input-block">
                        <input type="radio" name="level" value="1" lay-filter="levelRadio" title="一级" @if( (!isset($data)) || ( isset($data) && $data->level==1) ) checked @endif>
                        <input type="radio" name="level" value="2" lay-filter="levelRadio" title="二级" @if(isset($data) && $data->level==2) checked @endif>
                    </div>
                </div>

                {{--<div class="layui-form-item">
                    <label class="layui-form-label">服务范围</label>
                    <div class="layui-input-block">
                        @foreach(\App\Models\DetailDealer::privilegeAreaServingGroup() as $key=>$item)
                        <input type="radio" name="privilege_area_serving" value="{{$key}}" lay-filter="privilegeAreaServingRadio" title="{{$item}}" @if( (!isset($data)) || ( isset($data) && $data->privilege_area_serving==$key) ) checked @endif>
                        @endforeach
                    </div>
                </div>--}}
                <div class="layui-form-item" id="parent-seller-form-item" style="display:none;">
                    <label class="layui-form-label">所属上级销售商</label>
                    <div class="layui-input-inline">
                        <input type="text" autocomplete="off" id="parent-seller-select"  value="{{$data->parent_display_name or ''}}" readonly placeholder="请选择上级销售商" class="layui-input">
                        <input type="hidden" name="parent_seller_id" value="{{$data->parent_id or 0}}" id="h-parent-privilege">
                    </div>
                </div>
                {{--<div id="parent-seller-select" class="layui-form-item" style="display:none;">
                    <label class="layui-form-label">所属上级销售商</label>
                    <div class="layui-input-inline">
                        <select name="parent_seller_id" >
                            @foreach($seller_lv1s as $seller1)
                            <option value="{{$seller1->id}}">{{$seller1->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>--}}

                <div class="layui-form-item">
                    <label class="layui-form-label">手机号码</label>
                    <div class="layui-input-inline">
                        <input type="text" name="login_mobile" value="{{$data->login_mobile or ''}}" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">登录初始密码</label>
                    <div class="layui-input-inline">
                        <div class="layui-form-mid layui-word-aux">默认为上方填写的手机号码</div>
                    </div>
                </div>
            @endif

            <div class="layui-form-item submit-container">
                <div class="layui-input-block">
                    {{csrf_field()}}
                    @if(isset($data))
                        <input type="hidden" name="id" value="{{$admin_id}}" />
                    @endif
                    <button class="layui-btn layui-btn-custom-blue" id="submitBtn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                    {{--<button type="reset" class="layui-btn layui-btn-primary">重置</button>--}}
                </div>
            </div>
        </form>

    </div>

    <script type="text/html" id="area-serving-tpl">
        @include("v1.admin_brand.sub_admin.seller.components.area_serving_tpl",['provinces'=>$provinces])

    </script>

    <script type="text/html" id="area-visible-tpl">
        @include("v1.admin_brand.sub_admin.seller.components.area_visible_tpl",['provinces'=>$provinces])


    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{asset('plugins/layui-extend/tableSelect.js')}}"></script>

    {{--初始化专用script--}}
    <script>
        //JavaScript代码区域
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form;
        layui.element.init();

        /*日期控件*/
        var laydate = layui.laydate;
        laydate.render({
            elem: '#date_start',
            value:'{{request()->input('date_start') ? request()->input('date_start') :''}}'
        });
        laydate.render({
            elem: '#date_end',
            value:'{{request()->input('date_end') ? request()->input('date_end') :''}}'

        });
        /*日期控件*/


        /*自定义form验证*/
        form.verify({

        });
        /*自定义form验证*/

        /*表格选择控件*/
        $(function () {
            layui.config({
                base: '{{asset('plugins/layui-extend')}}/'
            });
            layui.use(['jquery','tableSelect'], function () {
                var tableSelect = layui.tableSelect;
                tableSelect.render({
                    elem: '#parent-seller-select',
                    checkedKey: 'id', //表格的唯一建值，非常重要，影响到选中状态 必填
                    searchKey: 'keyword',	//搜索输入框的name值 默认keyword
                    searchPlaceholder: '销售商关键词',	//搜索输入框的提示文字 默认关键词搜索
                    table: {
                        url:'{!! url($url_prefix.'admin/brand/sub_admin/seller/api/ajax_parent_seller') !!}',
                        cols: [[
                            { type: 'radio' },
                            { field: 'name', title: '公司名称' },
                            { field: 'short_name', title: '简称' },
                            { field: 'contact_name', title: '联系人' },
                            { field: 'contact_telephone', title: '联系电话' },

                        ]]
                    },
                    done: function (elem, data) {

                        var select_data = data.data;
                        if(select_data.length==0){
                            //取消选择
                            $('#h-parent-privilege').val(0);
                            $('#parent-seller-select').val('');

                        }
                        console.log(select_data);
                        for(var i=0;i<select_data.length;i++){
                            var privilege_id = select_data[i]['id'];
                            var display_name = select_data[i]['name'];
                            $('#h-parent-privilege').val(privilege_id);
                            $('#parent-seller-select').val(display_name);

                        }
                    }
                });

            });
        });
        /*表格选择控件*/

        form.render();

    </script>

    {{--页面方法专用script--}}
    <script>

        let submitAction = "{{url('/admin/brand/sub_admin/seller/api/account')}}";
        let submitMethod = "";
        @if(isset($data))
            submitAction = "{{url('/admin/brand/sub_admin/seller/api/account')}}/{{$admin_id}}";
        submitMethod = "PUT";
        @endif

        //记录当前级别是选择一级还是二级
        let current_level = 1;

        //提交Form信息
        form.on('submit(submitFormBtn)', function(form_info){
            //显示loading
            layer.load(1);
            //将提交按钮设置不可用
            $('#submitBtn').attr('disabled',true);

            var form_field = form_info.field;
            //处理开关控件：需要将其转换成数值
            if(!form_field.status) {
                form_field.status = "0";
            }
            //条件判断
            /*-------服务城市-------*/
           /* var serving_city = [];
            $(".area-serving-province").each(function(){
                serving_city.push($(this).val());
            })
            if(serving_city.length<=0){
                layer.msg('请至少添加一个服务城市');
                $('#submitBtn').attr('disabled',false);

                layer.closeAll('loading')

                return false;
            }*/
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
                            parent.reloadTable();
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

        //监听级别变化
        form.on('radio(levelRadio)', function(data){
            if(data.value==2){
                //显示所属上级销售商选择
                $('#parent-seller-form-item').show();
                current_level = 2;
            }else{
                $('#parent-seller-form-item').hide();
                current_level = 1;
            }
        });

        //监听省份变化
        form.on('select(areaServingProvinceId)', function(data){
            var province_id = data.value;
            var elem = data.elem;
            var city_id = $(elem).attr('data-city-id');
            get_area(province_id,city_id,'城市');
        });

        //监听城市变化
        form.on('select(areaServingCityId)', function(data){
            var city_id = data.value;
            //get_area(city_id,'area_serving_district_id','区/县');
        });

        function add_area_serving_block(){
            var tpl_name = 'area-serving-tpl';
            var block_tpl = $('#'+tpl_name).html();
            var block_obj = $(block_tpl)
            //当前已有多少个areablock
            var block_count = $('#area-serving-list').find('.layui-form-item').length;
            var city_id = parseInt(block_count)+1;
            console.log(block_tpl)
            block_obj.find('#area-serving-province').attr('data-city-id','area-serving-city-'+city_id)
            block_obj.find('#area-serving-province').attr('id','area-serving-province-'+city_id)
            block_obj.find('#area-serving-city').attr('id','area-serving-city-'+city_id)
            $('#area-serving-list').append(block_obj);
            //最后一定要进行form的render，不然控件用不了
            form.render();
        }

        function add_area_visible_block(){
            var tpl_name = 'area-visible-tpl';
            var block_tpl = $('#'+tpl_name).html();
            var block_obj = $(block_tpl)
            //当前已有多少个areablock
            var block_count = $('#area-visible-list').find('.layui-form-item').length;
            console.log(block_count)
            var city_id = parseInt(block_count)+1;
            block_obj.find('#area-visible-province').attr('data-city-id','area-visible-city-'+city_id)
            block_obj.find('#area-visible-province').attr('id','area-visible-province-'+city_id)
            block_obj.find('#area-visible-city').attr('id','area-visible-city-'+city_id)
            $('#area-visible-list').append(block_obj);
            //最后一定要进行form的render，不然控件用不了
            form.render();
        }

        function delete_area_block(obj){
            $(obj).parents('.layui-form-item').remove();
        }

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url($url_prefix.'admin/api/get_area_children?pi=')}}'+area_id,function(res){
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