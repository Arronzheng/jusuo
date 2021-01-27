@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/designers.css'
   ],
   'js'=>[
   ]
])

@section('content')

    <div class="sub-container">
        <div class="sub-container-title">我的关注(<span id="num">{{ count($designers) }}</span>)</div>
        <div class="item-list">
            @foreach($designers as $v)
            <div class="item-outer" data-attr="{{ $v->web_id_code }}">
                <div class="item-image"
                     style="background-image:url({{ url($v->detail->url_avatar) }})"></div>
                <div class="item-info">
                    <div class="action active" id="fav-{{ $v->web_id_code }}">已关注</div>
                    <div class="title-outer single-line">{{ $v->detail->nickname }}</div>
                    <div class="info-outer single-line">{{ $v->detail->self_introduction }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

@endsection

@section('script')

    <script>

        $('.action').click(function(){
            var code = $(this).parent().parent().attr('data-attr');
            ajax_post('/index/post_fav_designer',{
                'code': code
            }, function(res){
                if (res.status == 1) {
                    $('#fav-'+code).removeClass('active');
                    if(res.data.faved){
                        $('#fav-'+code).addClass('active');
                        $('#fav-'+code).html('已关注');
                    }
                    else{
                        $('#fav-'+code).html('+关注');
                    }
                    $('#num').html(res.data.count);
                }
            });
            event.stopPropagation();
        });

        $('.item-outer').click(function(){
            var code = $(this).attr('data-attr');
            window.location.href = '/mobile/designer/s/'+code;
        });

    </script>

@endsection