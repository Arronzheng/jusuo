<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微信注册</title>

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
            <h2 class="weui-msg__title">微信注册</h2>
            <p class="weui-msg__desc">是否确定通过微信注册？</p>
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
                    <a href="javascript:void(0);" class="weui-footer__link">{{--商家合作平台--}}</a>
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
            <p class="weui-msg__desc">登录成功，请返回<a href="javascript:void(0);">{{--商家合作平台--}}</a>网站操作</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:cancel();" class="weui-btn weui-btn_primary">确定</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    <a href="javascript:void(0);" class="weui-footer__link">{{--商家合作平台--}}</a>
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
            <p class="weui-msg__desc" id="fail-hint">登录失败，请稍后再试</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:cancel();" class="weui-btn weui-btn_primary">确定</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    <a href="javascript:void(0);" class="weui-footer__link">{{--商家合作平台--}}</a>
                </p>
                <p class="weui-footer__text"></p>
            </div>
        </div>
    </div>
</div>

<script src="{!! asset('v1/js/jquery.min.js') !!}"></script>
<script type="text/javascript" src="https://cdn.bootcss.com/layer/2.3/layer.js"></script>
<script src="{!! asset('v1/js/ajax.js') !!}"></script>

<script>
    function go(){
        ajax_post('{!! url('/account/doWechatRegister') !!}',
            {'t':'{{ $t }}','_token':'{{csrf_token()}}'},
            function(result){
                if (result.status) {
                    $('#div-ask').addClass('hide');
                    $('#div-ok').removeClass('hide');
                }
                else{
                    $('#div-ask').addClass('hide');
                    $('#div-fail').removeClass('hide');
                    if(result.msg)
                        $('#fail-hint').html(result.msg);
                }
            });
    }

    function cancel(){
        WeixinJSBridge.call('closeWindow');
    }

</script>
</body>
</html>