@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
   ],
   'js'=>[
        '/v1/static/iconfont/iconfont.js',
   ]
])

<style>
    .container{
        display: block;
        position: relative;
        padding-top: 40px;
        width: 1100px;
        margin: 0 auto;
    }
    .title{
        font-size: 34px;
        font-weight: 700;
        line-height: 44px;
        color: #222;
    }
    .news-content{
        font-size: 16px;
        line-height: 28px;
        color: #222;
        word-wrap: break-word;
    }
    .news-content img{}

</style>

@section('content')

    <div class="container">
        <div class="title">{{$news->title}}</div>
        <p style="margin: 16px 0;"><br></p>
        <div class="news-content">{!! $news->content !!}</div>
    </div>

@endsection

@section('script')

@endsection