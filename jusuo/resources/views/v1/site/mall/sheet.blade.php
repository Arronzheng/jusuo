@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/css/site/mall/sheet.css',
        '/v1/static/swiper/swiper.min.css',
   ],
   'js'=>[
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/site/mall/sheet.js',
        '/v1/js/ajax.js',
   ]
])

@section('content')

    <div class="designer-list-container">
        <div class="nav_lujin">
            <span class="navtext1">首页 / 积分商城 / </span>
            <span class="navtext2">确认和提交订单</span>
        </div>

        <div class="address-list-outer">
            <div class="detail-text">选择收货地址<span class="address-add" id="address-add-btn">+新增</span></div>
            <div class="address-list" id="address-list">加载中...</div>

            <div class="detail-text">确认商品信息</div>
            <div class="sale-outer">
                <table class="full-width">
                    <tr><th>商品名称</th><th>每件积分</th><th>兑换数量</th></tr>
                    <tr>
                        <td>
                            <img class="sale-img" id="sale-img" src="">
                            <div class="sale-name" id="sale-name"></div>
                        </td>
                        <td class="text-center" id="sale-integral"></td>
                        <td class="text-center" >&times;<span id="sale-count"></span></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="remark-row">
                            <div class="remark-hint">备注</div>
                            <div class="remark-input-outer">
                                <input name="remark" id="i-remark" value="" placeholder="选填" class="layui-input">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="confirm-outer">
                <div class="confirm-box">
                    <div>
                        <div class="money-outer">
                            <span class="bold">使用积分：</span>
                            <span class="money-num" id="total-amount">0</span>
                        </div>
                        <div class="confirm-address-outer" id="confirm-address" >
                            <div>
                                <span class="bold">寄送至：</span>
                                <span class="province"></span>
                                <span class="city"></span>
                                <span class="district"></span>
                                <span class="address"></span>
                            </div>
                            <div>
                                <span class="bold">收货人：</span>
                                <span class="receiver"></span>
                                <span class="telephone"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="confirm-btn-outer">
                    <div class="confirm-btn" onclick="submitOrder();">提交订单</div>
                </div>
            </div>

        </div>

    </div>

    {{--<div id="address-add-form" style="display:none;">

    </div>--}}

    <script type="text/html" id="address-list-tpl">
        @verbatim
        {{each data address i}}
        <div data-id="{{address.id}}" class="address-outer {{if address.is_default}}active{{/if}}">
            {{if address.is_default}}<div class="address-default-tag">默认地址</div>{{/if}}
            {{if !address.is_default}}<div class="address-default-tag" onclick="setDefaultAddress(event,'{{address.id}}')">设为默认</div>{{/if}}
            <div class="address-del" onclick="deleteAddress(event,'{{address.id}}')"><span class="iconfont icon-delete"></span></div>
            <div class="address-hd">{{address.province_name}}{{address.city_name}}（{{address.receiver_name}}收）</div>
            <div class="address-txt"><span>{{address.area_name}}</span><span>{{address.receiver_address}}</span><span>{{address.receiver_tel}}</span></div>
            <div class="address-modify" onclick="editAddress(event,'edit','{{address.id}}')">修改</div>
            <div class="address-selected"></div>
        </div>
        {{/each}}
        @endverbatim
    </script>

@endsection

@section('script')
    <script>



        var web_id_code = "{{request()->input('g')}}";
        var goodCount = "{{request()->input('c')}}"*1;


    </script>

@endsection