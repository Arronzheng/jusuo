@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/static/iconfont/iconfont.css',
       '/v1/css/mobile/mall/sheet.css?v='.str_random(random_int(20,30)),
   ],
   'js'=>[
       '/v1/static/js/xlPaging.js',
       '/v1/js/ajax.js',
       '/v1/js/mobile/mall/sheet.js?v='.str_random(random_int(20,30))
   ],
   'title'=>'确认兑换订单'
])

@section('content')
    <style>
        html{background-color:#fff;}
        body{/*padding-bottom:50px;*/}

        #operation-outer{display:flex;justify-content:flex-end;width:100%;border-top:1px solid #f2f2f2;align-items:center;position:fixed;height:50px;left:0;bottom:0;background-color:#ffffff;z-index:1;}
        #choose-amount{}
        #choose-amount button{border:1px solid #dedede;width:25px;height:25px;}
        #choose-amount input{border:1px solid #dedede;height:25px;width:40px;padding:0;text-align:center;}
        #submit-btn{border:0;margin:0 15px;background-color:#FF8800;height:25px;line-height:25px;text-align:center;color:#ffffff;padding:0 10px;}

        #address-entrance{
            background-color:#ffffff;
            margin: 10px 0;
            /*-webkit-border-radius:10px;
            -moz-border-radius:10px;
            border-radius:10px;*/
            display:flex;
            align-items: center;
            padding: 15px 10px;
        }
        #address-entrance .icon{flex:0 0 30px;height:30px;width:30px;margin-right:8px;}
        #address-entrance .main-info{flex:1;}
        #address-entrance .main-info .row1{}
        #address-entrance .main-info .row1 .name{font-size:16px;margin-right:3px;}
        #address-entrance .main-info .row1 .phone{font-size:14px;color:#999999;}
        #address-entrance .main-info .row2{font-size:12px;margin-top:5px;}
        #address-entrance .pointer{flex:0 0 20px;height:20px;width:20px;margin-left:10px;}

        #detail-container{
            margin: 10px 0;
            padding:15px 10px;
            background-color: #fff;
            /*-webkit-border-radius:10px;
            -moz-border-radius:10px;
            border-radius:10px;;*/
        }
        #detail-container .detail-title{margin-bottom:10px;font-weight:bold;/*border-left:3px solid #ff8800;padding-left:10px;*/}
        #detail-container .sale-info{display:flex;}
        #detail-container .sale-info .sale-img{height:50px;width:50px;margin-right:8px;}
        #detail-container .sale-info .sale-name{padding:10px 5px;}

        #detail-container table th, table td{
            border:0;
        }
        #detail-container .remark-row{display:flex;align-items:center;padding-top:0px;font-size:12px;}
        #detail-container .remark-row .remark-hint{margin-right:15px;}
        #detail-container .remark-row .remark-input-outer{flex:1;}
        #detail-container .remark-row .remark-input-outer input{height:30px;line-height:30px;border:1px solid #f0f0f0;padding-left:5%;width:95%;}

        #confirm-outer{
            border-top:1px dotted #eee;
            margin: 10px 0;
            padding:15px 10px;
            background-color:#ffffff;
            text-align:right;
            line-height:24px;
            /*-webkit-border-radius:10px;
            -moz-border-radius:10px;
            border-radius:10px;;*/
        }

        #address-list-container{
            display:none;
            background-color:transparent;z-index:10;position:fixed;
            top:0;left:0;height:100%;width:100%;
        }

        #address-list-box{height:100%;width:100%;display:flex;justify-content:center;align-items:center;}
        #address-list-container .add-address-btn{
            position:fixed;bottom:10%;z-index:11;width:32%;left:34%;
            height:28px;line-height:28px;background-color:#FF8800;color:#ffffff;text-align:center;
            -webkit-border-radius:5px;
            -moz-border-radius:5px;
            border-radius:5px;font-size:12px;
        }
        #address-list-container .mask{background-color:#000000;top:0;left:0;position:fixed;height:100%;width:100%;opacity:.5;z-index:2;}
        #address-list{
            position:relative;border-radius:10px;background-color:#ffffff;
            padding:15px 15px;height:60%;width:85%;z-index:3;
        }
        #address-list .close-btn{
            background-color:#ffffff;position:absolute;display:flex;align-items: center;justify-content: center;
            z-index:11;height:30px;width:30px;top:-20px;right:-10px;
            -webkit-border-radius:30px;
            -moz-border-radius:30px;
            border-radius:30px;
            /*-webkit-box-shadow: inset hoff voff blur color;
            -moz-box-shadow: inset hoff voff blur color;*/
            box-shadow: -2px 2px 5px #555;
        }
        #address-list .close-btn img{height:60%;}
        #address-list .list-content{overflow-y: scroll;}
        #address-list .address-outer{
            padding:15px 0;display:flex;align-items: center;border-bottom:1px solid #f2f2f2;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        #address-list .address-outer:first-child{padding-top:0;}
        #address-list .address-outer .address-selected{flex:0 0 5%;height:auto;margin-right:3%;}
        #address-list .address-outer.active .address-selected img{display:block}
        #address-list .address-outer .address-selected img{display:none;width:100%;}
        #address-list .main-info{flex: 0 0 67%;margin-right:3%;}
        #address-list .address-outer .op-box{flex:0 0 22%;}
        #address-list .address-outer .op-box .op-btn{float:left;margin-right:10px;color:#FF8800;height:25px;line-height:25px;font-size:12px;}
        #address-list .address-outer .op-box .op-btn:last-child{margin-right:0;}
        #address-list .main-info .row1{}
        #address-list .main-info .row1 .name{font-size:14px;margin-right:3px;}
        #address-list .main-info .row1 .phone{font-size:12px;color:#999999;}
        #address-list .main-info .row2{font-size:12px;margin-top:0;/*display:flex;align-items:center;*/}
        #address-list .main-info .row2 .address-default-tag{
            /*flex:0 0 25px;*/text-align:center;font-size:12px;
            margin-right:5px;padding:1px 2px;;color:#ffffff;background-color:#FF8800;
        }
        #total-amount{color:#FF8800;font-size:16px;}
        .layui-layer-btn .layui-layer-btn0{background-color:rgb(255,136,0);border-color:rgb(255,136,0)}
    </style>

    <div>
        <div id="header" style="position:relative;height:20vw;background:linear-gradient(to right, rgb(245, 0, 48), rgb(245, 0, 48)) center center / cover no-repeat">
            <div id="text" style="line-height:20vw;color:white;position:absolute;left:10vw;top:0;">订单等待确认</div>
            <img id="image" src="/v1/images/site/center/integral/jinbi.png" style="position:absolute;width:14vw;height:14vw;top:3vw;right:10vw;"/>
        </div>

        <div id="address-entrance" onclick="openAddressListModal()">
            <img class="icon" src="{{asset('v1/images/mobile/mall/location.png')}}"/>
            <div class="main-info">
                <div class="row1">
                    <span class="name">-</span>
                    <span class="phone">-</span>
                </div>
                <div class="row2">-</div>
            </div>
            <img class="pointer"  src="{{asset('v1/images/mobile/mall/point-right.png')}}"/>
        </div>

        <div id="detail-container">
            <div class="detail-title">确认商品信息</div>
            <div class="sale-outer">
                <table class="full-width">
                    <tr><th>商品名称</th><th>积分</th><th>兑换数量</th></tr>
                    <tr>
                        <td width="70%">
                            <div class="sale-info" >
                                <img class="sale-img" id="sale-img" src="">
                                <div class="sale-name" id="sale-name"></div>
                            </div>

                        </td>
                        <td width="10%" class="text-center" id="sale-integral"></td>
                        <td width="20%" class="text-center" >&times;<span id="sale-count"></span></td>
                    </tr>
                    <tr>
                        <td colspan="3" >
                            <div class="remark-row">
                                <div class="remark-hint">备注</div>
                                <div class="remark-input-outer">
                                    <input name="remark" id="i-remark" value="" placeholder="选填" class="layui-input">
                                </div>
                            </div>

                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <div id="confirm-outer">
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
        </div>


        <div id="address-list-container">
            <div class="add-address-btn" id="address-add-btn">添加收货地址</div>

            <div id="address-list-box">
                <div class="address-list" id="address-list">
                    <div class="close-btn" onclick="closeAddressListModal()">
                        <img src="/v1/images/mobile/mall/close-btn2.png"/>
                    </div>
                    <div class="list-content">加载中...</div>
                </div>

            </div>
            <div class="mask"></div>
        </div>


    </div>


    <div id="operation-outer">

        <div id="submit-btn" onclick="submitOrder();">
            提交订单
        </div>
    </div>



    <script type="text/html" id="address-list-tpl">
        @verbatim
        {{each data address i}}
        <div data-id="{{address.id}}" class="address-outer {{if address.is_default}}active{{/if}}">
            <div class="icon address-selected">
                <img  src="/v1/images/mobile/mall/selected.png"/>
            </div>

            <div class="main-info">
                <div class="row1">
                    <span class="name">{{address.receiver_name}}</span>
                    <span class="phone">{{address.receiver_tel}}</span>
                </div>
                <div class="row2">
                    {{if address.is_default}}<span class="address-default-tag">默认</span>{{/if}}
                    {{address.province_name}}{{address.city_name}}{{address.area_name}}{{address.receiver_address}}
                </div>
            </div>
            <div class="op-box">

                {{if !address.is_default}}<div class="op-btn address-default-tag" onclick="setDefaultAddress(event,'{{address.id}}')">设默认</div>{{/if}}
                <div class="op-btn address-modify" onclick="editAddress(event,'edit','{{address.id}}')">修改</div>

                <div class="op-btn address-del" onclick="deleteAddress(event,'{{address.id}}')">删除</div>


            </div>

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