@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/album.css'
   ],
   'js'=>[
       '/v1/js/mobile/product_comment.js'
   ]
])

@section('content')
    <style>
        body{background-color:#ffffff!important;}
        .sub-container{padding: 10px;margin-bottom:70px;}
        .comment-input-outer{z-index:11;position:fixed;bottom:0;display:flex;padding:10px;border-top:1px solid rgba(0,0,0,.006);background: white;}
        #b-list-more-comment{display:none;height:40px;width:100%;line-height:40px;text-align:center;font-size:12px;}
        .s-follow{
            color:#1582FF;
            overflow: hidden;
        }
        .list-empty{font-size:12px;text-align:center;color:#888888;padding:20px 0 40px 0;}
        .customer-service{color:rgba(234,107,107,1)}
    </style>
    <div class="sub-container">
        <div class="sub-container-title">全部问答(<span class="comment-count"></span>)</div>

        <div id="product-comments"></div>

        <div id="list-tips"></div>

        <div id="b-list-more-comment" onclick="list_more_comment()">
            查看更多...
        </div>
    </div>

    <div class="comment-input-outer">
        <div id="comment-input">
            <input placeholder="问点什么吧" />
        </div>
        <div id="comment-send" class="active" onclick="commit_comment()">发送</div>
    </div>

    <input type="hidden" id="i_comment_page" value="1"/>

    @verbatim
    <script id="product-comments-tpl" type="text/html">
        {{each datas.data comment comment_index}}
        <div class="comment-outer" {{if comment.answer}} style="margin-bottom:10px;" {{/if}}>
            <div class="sub-container-avatar"
                 style="background-image:url('{{comment.ask_avatar}}')"></div>
            <div class="comment-text-outer">
                <div class="sub-container-nickname">{{comment.ask_name}}<span class="sub-container-time">{{comment.ask_time}}</span></div>
                <div class="comment-praise active" style="display:none;"><span class="iconfont icon-dianzan2"></span>2</div>
                <div class="sub-container-content">{{@ comment.question}}</div>
            </div>
        </div>
        {{if comment.answer}}
        <div class="comment-outer" style="margin-top:0;">
            <div class="sub-container-avatar"></div>
            <div class="comment-text-outer">
                <div class="sub-container-nickname customer-service">客服<span class="sub-container-time">{{comment.answer_time}}</span></div>
                <div class="comment-praise active" style="display:none;"><span class="iconfont icon-dianzan2"></span>2</div>
                <div class="sub-container-content">{{@ comment.answer}}</div>
            </div>
        </div>
        {{/if}}

        {{/each}}
    </script>

    <script id="list-empty-tpl" type="text/html">
        <div class="list-empty">暂无相关数据</div>
    </script>

    @endverbatim

@endsection

@section('script')

    <script>

        var commit_comment_api_url = "{{url('/mobile/product/api/commit_comment/'.request()->route('web_id_code'))}}?__bs=" + __cache_brand;
        var product_comments_api_url = "{{url('/mobile/product/api/list_product_comments/'.request()->route('web_id_code'))}}?__bs=" + __cache_brand;
        var current_url = "{{url()}}";

    </script>

@endsection