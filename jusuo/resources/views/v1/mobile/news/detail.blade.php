@extends('v1.mobile.layout',[
  'css'=>[
        '/v1/static/iconfont/iconfont.css',
   ],
   'js'=>[
        '/v1/static/iconfont/iconfont.js',
   ]
])

@section('content')
    <div class="container">
        <div class="title">{{$news->title}}</div>
        <p style="margin: 16px 0;"><br></p>
        <div class="news-content">{!! $news->content !!}</div>
    </div>
@endsection

@section('script')


@endsection