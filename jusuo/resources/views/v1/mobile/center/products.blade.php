@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/products.css'
   ],
   'js'=>[
   ]
])

@section('content')

    <div class="sub-container">
        <div class="sub-container-title">{{ $title }}</div>
        <div class="item-list">
            @foreach($products as $v)
            <div class="item-outer" data-attr="{{ $v->web_id_code }}">
                <div class="item-image"
                     style="background-image:url({{ url($v->photo_product) }})"></div>
                {{--<div class="action-data-outer">
                    <div class="action"><span class="iconfont icon-shoucang2"></span>12</div>
                    <div class="action"><span class="iconfont icon-yinyongziyuan"></span>2</div>
                    <div class="action"><span class="iconfont icon-mima-xianshi"></span>112</div>
                </div>--}}
                <div class="item-info">
                    <div class="title-outer single-line">{{ $v->name.' '.$v->code }}</div>
                    <div class="info-outer single-line">{{ $v->series.' '.$v->spec }}</div>
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
            window.location.href = '/mobile/product/s/'+code;
        });

    </script>

@endsection