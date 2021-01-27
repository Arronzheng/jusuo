@extends('v1.mobile.layout',[
   'css'=>[],
   'js'=>[
       'https://api.map.baidu.com/api?v=2.0&ak=67jMQ5DmYTe1TLMBKFUTcZAR'
   ]
])

@section('content')

    <style>
        #tips{padding-top:120px;text-align:center;font-size:14px;}
        #re-location{line-height:35px;text-align:center;margin:0 auto;margin-top:120px;width:40%;height:35px;color:#07c160;border:1px solid #07c160;}
    </style>

    <div class="container">

        <div id="tips">定位中 ...</div>
        <div id="re-location" onclick="get_location()">重新定位</div>

    </div>

@endsection

@section('script')
    <script>
        var session_get_location_redirect = '{{session()->get('get_location_redirect')}}';

        //微信JSSDK初始化
        wx.config(<?php echo $jssdkConfig ?>);

        get_location();

        function get_location(){
            wx.ready(function () {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        //console.log(res)

                        //alert(res.latitude);
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                        var speed = res.speed; // 速度，以米/每秒计
                        var accuracy = res.accuracy; // 位置精度
                        var gc = new BMap.Geocoder();
                        var pointAdd = new BMap.Point(res.longitude, res.latitude);
                        //console.log(pointAdd)

                        gc.getLocation(pointAdd, function(rs) {
                            var city = rs.addressComponents.city;
                            //console.log(city)
                            //city输出：佛山市  等
                            sessionStorage.setItem('location_city',city);
                            ajax_get('/mobile/common/set_location?city='+city, function (res) {

                                if(res.status){
                                    //跳转到redirect
                                    if(session_get_location_redirect){
                                        location.href = session_get_location_redirect;
                                    }
                                }else{
                                    layer.msg(res.msg)
                                }



                            }, function () {
                            })


                        });
                    },
                    //用户取消授权位置回调函数
                    cancel:function(res){
                        //下面执行定位后的函数
                        layer.msg('请进行定位~');
                    },
                    fail: function () {
                        // 用户shibai分享后执行的回调函数
                        layer.msg('请进行定位~');
                    }
                });

            });
        }




    </script>
@endsection