<a href="{{url('/center/reset_password')}}" class="tab-block @if($active=='oldpwd') active @endif" >
    原密码重置
</a>
<a href="{{ url('/center/reset_password/phone/index') }}" class="tab-block @if($active=='phone') active @endif" >
    手机密码重置
</a>