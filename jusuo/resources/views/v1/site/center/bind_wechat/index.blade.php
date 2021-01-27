@extends('v1.site.center.layout')

@section('main-content')

    <div id="login-wx" class="login-cont-outer" style="height:170px;">
        <div style="background-size: contain;background-position: center;background-repeat: no-repeat;margin:auto;width:200px;height:200px;" class="" id="login-qrcode" onclick="refreshWechatLoginQrcode()"></div>
        <div class="hint" style="color:#999;font-size:16px;text-align: center;width:100%;margin-top:10px;">使用微信扫码，即可绑定微信</div>
    </div>

@endsection



@section('script')

    <script src="{{ asset('/v1/js/jquery.qrcode.min.js') }}"></script>
    <script>
        var wechatLogincount = 36;
        var wechatLoginToken = '';

        $(document).ready(function(){

            refreshWechatLoginQrcode();

        });

        //微信登录二维码
        function refreshWechatLoginQrcode() {
            if(wechatLogincount<=0){
                return false;
            }
            if ($('#login-qrcode').hasClass('disabled')){
                return false;
            }
            wechatLogincount--;
            ajax_get('{{url('/account/getRandomToken')}}',function (json) {
                wechatLoginToken=json.data;
                $('#login-qrcode').empty();
                $('#login-qrcode').qrcode({width:200,height:200,text:'{{url("/center/bindWechat")}}/'+wechatLoginToken});
                $('#login-qrcode').addClass('disabled');
                setTimeout('checkWechatLoginStatus()', 3000);
                setTimeout(function(){
                    $('#login-qrcode').removeClass('disabled');
                },'{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
            });
        }

        function checkWechatLoginStatus(){
            if(wechatLogincount<=0){
                return false;
            }
            ajax_post('{{url('/account/checkWechatLogin')}}',{'t':wechatLoginToken,'_token':'{{csrf_token()}}'},function (json) {
                if (json.status==1) {
                    //刷新页面
                    location.reload();
                }
                else {
                    if(wechatLogincount>0){
                        setTimeout('checkWechatLoginStatus()', 3000);
                    }

                }
            });

        }

    </script>

@endsection

