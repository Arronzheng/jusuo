{{--<a href="{{url('/center/basic_info')}}" class="tab-block @if($active=='basic') active @endif"" >
基本信息
</a>

@if(auth()->user()->status==\App\Models\Designer::STATUS_ON)
    <a href="{{url('/center/realname_info')}}" class="tab-block @if($active=='realname') active @endif">
        实名信息
    </a>
    <a href="{{url('/center/app_info')}}" class="tab-block @if($active=='app') active @endif">
        应用信息
    </a>
@endif--}}

<div class="desigplan" id="persondaohang">
    <a class="@if($active=='basic') designtitle @else designtitle1 @endif " id="f0" href="{{url('/center/basic_info')}}">基本信息</a>
    @if(auth()->user()->status==\App\Models\Designer::STATUS_ON)
    <a class="@if($active=='realname') designtitle @else designtitle1 @endif" id="f1" href="{{url('/center/realname_info')}}">实名信息</a>
    <a class="@if($active=='app') designtitle @else designtitle1 @endif" id="f2" href="{{url('/center/app_info')}}">应用信息</a>
        @endif
</div>