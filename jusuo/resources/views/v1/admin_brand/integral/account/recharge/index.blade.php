@extends('v1.admin_brand.layout',[
    'css' => ['/v1/css/admin/brand/bind_wechat.css'],
        'js'  => ['/v1/js/jquery.qrcode.min.js']
])

@section('style')
    <style>
        .layui-form-mid{padding:6px 0!important;}
        .pay-qrcode-content{text-align:center;padding-top:20px;}
    </style>
@endsection

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>积分商城</cite></a><span lay-separator="">/</span>
            <a><cite>积分账户</cite></a><span lay-separator="">/</span>
            <a><cite>积分预存</cite></a>
            <div class="right" style="margin-right:15px;">
                @can('integral_shop.goods_manage.goods_import')
                <button onclick="location.href='{{url('/admin/brand/integral/goods_import')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>商品引入
                </button>
                @endcan
                @can('integral_shop.goods_manage.goods_import')
                <button onclick="location.href='{{url('/admin/brand/integral/account/recharge/log')}}'" class="layui-btn layui-btn-sm layui-btn-custom-blue" >
                    <i class="layui-icon layui-icon-sm layui-icon-link" style="font-size:12px!important;"></i>预存记录
                </button>
                @endcan
            </div>
        </div>
    </div>
    <div class="layui-fluid">
        <div class="layui-form-item">
            <label class="layui-form-label">积分单价</label>
            <div class="layui-input-block">
                <div class="layui-form-mid layui-word-aux">
                    1积分=1分钱
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">购买积分</label>
            <div class="layui-input-inline">
                <input type="number" id="i-integral" oninput="onIntegralChange()" onkeyup="value=value.replace(/^(0+)|[^\d]+/g,'');" name="sort" value="{{$data->sort or 0}}" placeholder="请输入正整数" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">应付金额</label>
            <div class="layui-input-block">
                <div class="layui-form-mid layui-word-aux" id="need-money">￥0</div>
            </div>
        </div>
        <div class="layui-form-item submit-container">
            <div class="layui-input-block">
                {{csrf_field()}}
                <button class="layui-btn layui-btn-custom-blue" onclick="submitOrder()" id="submitBtn">确认支付</button>
            </div>
        </div>


        <div id="pay-qrcode-box" style="display:none;">
            <div class="pay-qrcode-content">
                <div class="qrcode-img" id="qrcode" ></div>
                <div class="tips">
                    请使用微信扫描支付
                </div>
            </div>


        </div>
    </div>



@endsection


@section('script')

    <script>

        function onIntegralChange(){
            var integral = $('#i-integral').val();
            if(!integral){
                integral = 0;
                console.log(integral)
            }else{
                integral = parseInt(integral)
            }
            var need_money = accDiv(integral,100);
            $('#need-money').html('￥'+need_money);

        }


        var payModal = null;
        var checkOrderInterval = null;
        var orderNo = '';

        //提交订单
        function submitOrder(){
            var integral = $('#i-integral').val();
            integral = parseInt(integral)
            if(!integral){
                layer.msg('请输入正确的积分数量！');return false;
            }
            layer.load(1)
            //提交给后台生成微信二维码
            ajax_post('{{url('/admin/brand/integral/account/recharge/api/submit')}}',{integral:integral}, function (res) {
                if (res.status == 1) {
                    var code_url = res.data.code_url
                    orderNo = res.data.order_no

                    //$('#pay-qrcode-box').show();

                    payModal = layer.open({
                        type: 1,
                        title:'积分充值支付',
                        area:['250px', '260px'],
                        resize:false,
                        maxmin:false,
                        content: $('#pay-qrcode-box').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                        success:function(){
                            $('.qrcode-img').html('').qrcode({width:150,height:150,text:code_url});
                        },
                        cancel: function(index, layero){
                            if(confirm('确定要取消支付吗？')){ //只有当点击confirm框的确定时，该层才会关闭
                                layer.close(index)
                                clearInterval(checkOrderInterval)

                            }
                            return false;
                        }
                    });

                    //轮询后台查询订单是否已支付
                    checkOrderInterval = setInterval(function(){
                        checkOrderPay();
                    },2000)

                    layer.closeAll('loading')
                    //展示二维码
                } else {
                    layer.msg(res.msg);
                }
            });
        }


        //定时查询订单是否已支付
        function checkOrderPay(){
            ajax_get('{{url('/admin/brand/integral/account/recharge/api/check_order')}}?on='+orderNo,function (res) {
                if (res.status == 1) {
                    if(res.data.isPaid){
                        layer.alert('支付成功！', {
                            title: '支付结果',
                            icon: 1,
                            skin: 'layer-ext-moon'
                        })
                        clearInterval(checkOrderInterval)
                        layer.close(payModal)
                    }

                } else {
                    layer.msg(res.msg);
                }
            });
        }


        function accDiv(arg1,arg2){
            var t1=0,t2=0,r1,r2;
            try{t1=arg1.toString().split(".")[1].length}catch(e){}   //--小数点后的长度
            try{t2=arg2.toString().split(".")[1].length}catch(e){}  //--小数点后的长度
            with(Math){
                r1=Number(arg1.toString().replace(".",""))  //--去除小数点变整数
                r2=Number(arg2.toString().replace(".",""))  //--去除小数点变整数
                return (r1/r2)*pow(10,t2-t1);   //---整数相除 在乘上10的平方  小数点的长度
            }
        }

    </script>

@endsection