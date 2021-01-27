@extends('v1.admin_platform.layout',[])

@section('style')

    <style>
        #data-section .title{margin-top:10px;margin-left:12px;font-size:16px;font-weight:bold;}
        #data-section .data-tag.width-40{width:40%}
    </style>

@endsection

@section('content')
    <div style="padding:15px;">
        <div class="section" id="data-section">
            <div class="data-outer">
                <div class="title">品牌账号统计</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02 width-40"><div class="data-title">已认证</div><div class="data-num">{{ $data['brand_count_verified'] }}</div><span class="iconfont icon-tupian"></span></div>
                    <div class="data-tag gradient-01 width-40"><div class="data-title">待审核</div><div class="data-num">{{$data['brand_count_to_be_verified']}}</div><span class="iconfont icon-huoyue"></span></div>
                </div>
            </div>
            <div class="data-outer">
                <div class="title">销售商统计</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02 width-40"><div class="data-title">已认证</div><div class="data-num">{{$data['dealer_count_verified']}}</div><span class="iconfont icon-user"></span></div>
                    <div class="data-tag gradient-01 width-40"><div class="data-title">昨日新增</div><div class="data-num">{{$data['dealer_count_verified_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                </div>
            </div>
            <div class="data-outer">
                <div class="title">设计师统计</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02 width-40"><div class="data-title">已认证</div><div class="data-num">{{$data['designer_count_verified']}}</div><span class="iconfont icon-user"></span></div>
                    <div class="data-tag gradient-01 width-40"><div class="data-title">昨日新增</div><div class="data-num">{{$data['designer_count_verified_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                </div>
            </div>
            <div class="data-outer">
                <div class="title">产品统计</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02 width-40"><div class="data-title">产品总数</div><div class="data-num">{{$data['product_count_verified']}}</div><span class="iconfont icon-user"></span></div>
                    <div class="data-tag gradient-01 width-40"><div class="data-title">昨日新增</div><div class="data-num">{{$data['product_count_verified_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                </div>
            </div>
            <div class="data-outer">
                <div class="title">方案统计</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02 width-40"><div class="data-title">方案总数</div><div class="data-num">{{$data['album_count_verified']}}</div><span class="iconfont icon-user"></span></div>
                    <div class="data-tag gradient-01 width-40"><div class="data-title">昨日新增</div><div class="data-num">{{$data['album_count_verified_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                </div>
            </div>
            <div class="data-outer">
                <div class="title">积分相关</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02"><div class="data-title">品牌预存积分余额</div><div class="data-num">{{$data['money_brand']}}</div><span class="iconfont icon-BAI-qian"></span></div>
                    <div class="data-tag gradient-01"><div class="data-title">平台待处理订单</div><div class="data-num">{{$data['count_order']}}</div><span class="iconfont icon-BAI-qian"></span></div>
                    <div class="data-tag gradient-03"><div class="data-title">设计师持有积分余额</div><div class="data-num">{{$data['money_designer']}}</div><span class="iconfont icon-user"></span></div>
                </div>
            </div>
        </div>
    </div>
@endsection
