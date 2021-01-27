@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/albums.css'
   ],
   'js'=>[
   ]
])

@section('content')

    <div class="sub-container">
        <div class="sub-container-title">{{ $title }}</div>
        <div class="item-list">
            @foreach($albums as $v)
            <div class="item-outer" data-attr="{{$v->web_id_code}}">
                <div class="item-image"
                     style="background-image:url({{ url($v->photo_cover) }})"></div>
                {{--<div class="action-data-outer">
                    <div class="action"><span class="iconfont icon-shoucang2"></span>12</div>
                    <div class="action"><span class="iconfont icon-dianzan2"></span>2</div>
                    <div class="action"><span class="iconfont icon-mima-xianshi"></span>112</div>
                </div>--}}
                <div class="item-info">
                    <div class="title-outer single-line">{{ $v->title }}</div>
                    @if(isset($v->house_type))
                        <div class="info-outer single-line">{{ $v->house_type.' '.$v->count_area.'㎡ '.$v->style }}</div>
                    @elseif(isset($v->designer))
                        <div class="info-outer single-line">
                            <div class="avatar" style="background-image:url({{ url($v->avatar) }})"></div>
                            <div class="nickname">{{ $v->designer }}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach

        </div>
    </div>

@endsection

@section('script')

    <script>

        $('.item-outer').click(function(){
            var code = $(this).attr('data-attr');
            window.location.href = '/mobile/album/s/'+code;
        });

    </script>

@endsection