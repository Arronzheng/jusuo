var count = 37;
var over = true;

function showQrcode() {
    count = 36;
    over = false;
    setTimeout('check_wechat_bind()', 5000);
    setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
    $('.qrcode').qrcode({width:200,height:200,text:"{{ url('bind',$remember_token }}"});
}

function timeOut() {
    qrcode_disable();
    over = true;
}

function refresh() {
    count--;
    qrcode_disable();
    setTimeout('check_wechat_bind()', 5000);
    setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
    over = false;
}

function qrcode_disable(){
    $('.qrcode').toggleClass('disabled');
}

function check_wechat_bind(){
    if (over){
        return false;
    }
    ajax_post('{{url('check_wechat_bind')}}',{'t':'{{ Auth::guard('brand')->user()->remember_token }}','openid':'{{ Auth::guard('brand')->user()->login_wx_openid }}','type':'{{\App\Http\Services\common\WechatService::BRAND}}'},function (json) {
        if (json.status) {
            layer.msg('绑定成功！');
            location.reload();
        }
        else{
            if(count){
                if(count<37){
                    count--;
                    setTimeout('check_wechat_bind()', 5000);
                }
                else{
                    count = 36;
                }
            }
        }
    })
}