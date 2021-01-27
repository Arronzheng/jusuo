<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>微信绑定</title>
    <link rel="stylesheet" href="{{asset('v1/css/weui.css')}}"/>
    <style>
        .hide{
            display:none;
        }
    </style>
</head>
<body>

<div class="page" id="div-ask">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-info weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">微信绑定</h2>
            <p class="weui-msg__desc">是否绑定<a href="javascript:void(0);"></a>{{ $user->loginType }}账号？</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:go();" class="weui-btn weui-btn_primary">确认</a>
                <a href="javascript:cancel();" class="weui-btn weui-btn_default">取消</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    <a href="javascript:void(0);" class="weui-footer__link">{{--家装合作平台--}}</a>
                </p>
                <p class="weui-footer__text"></p>
            </div>
        </div>
    </div>
</div>

<div class="page hide" id="div-ok">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">操作成功</h2>
            <p class="weui-msg__desc">请返回<a href="javascript:void(0);">{{--家装合作平台--}}</a>网站继续其它操作 </p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:cancel();" class="weui-btn weui-btn_primary">确定</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    <a href="javascript:void(0);" class="weui-footer__link">{{--家装合作平台--}}</a>
                </p>
                <p class="weui-footer__text"></p>
            </div>
        </div>
    </div>
</div>

<div class="page hide" id="div-fail">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">操作失败</h2>
            <p class="weui-msg__desc" id="fail-hint">绑定失败，请稍后再试</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:cancel();" class="weui-btn weui-btn_primary">确定</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    <a href="javascript:void(0);" class="weui-footer__link">{{--家装合作平台--}}</a>
                </p>
                <p class="weui-footer__text"></p>
            </div>
        </div>
    </div>
</div>

<script src="{!! asset('v1/js/jquery.min.js') !!}"></script>
<script>
    function go(){
        $.post('{!! url('do_wechat_bind') !!}', {'t': '{{ $t }}','type':'{{$user->type}}', '_token':'{{csrf_token()}}'}, function (json) {
            if (json.status==1) {
                $('#div-ask').addClass('hide');
                $('#div-ok').removeClass('hide');
            }
            else{
                $('#div-ask').addClass('hide');
                $('#div-fail').removeClass('hide');
                if(json.msg)
                    $('#fail-hint').html(json.msg);
            }
        });
    }

    function cancel(){
        WeixinJSBridge.call('closeWindow');
    }

</script>
</body>
</html>