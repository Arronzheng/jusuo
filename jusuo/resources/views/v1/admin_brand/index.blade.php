@extends('v1.admin_brand.layout',[
    'css'=>['/v1/static/admin-font/iconfont.css'],
    'js'=>['/v1/static/js/echarts.js','/v1/js/admin/chart.js'],
])

@section('style')

    <style>
        .data-outer .title{margin-top:10px;margin-left:12px;font-size:16px;font-weight:bold;}
    </style>

@endsection

@section('content')
    <div style="padding:15px;">
        <div class="section" id="data-section">
            <div class="data-outer" style="width:100%;">
                <div class="title">配额使用情况</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02" style="width:12%;"><div class="data-title">一级经销商配额</div><div class="data-num">{{ $data['quota_dealer_lv1_used'].'/'.$data['quota_dealer_lv1'] }}</div><span class="iconfont icon-tupian"></span></div>
                    <div class="data-tag gradient-01" style="width:12%;"><div class="data-title">二级经销商配额</div><div class="data-num">{{ $data['quota_dealer_lv2_used'].'/'.$data['quota_dealer_lv2'] }}</div><span class="iconfont icon-huoyue"></span></div>
                    <div class="data-tag gradient-03" style="width:12%;"><div class="data-title">品牌设计师配额</div><div class="data-num">{{ $data['quota_designer_brand_used'].'/'.$data['quota_designer_brand'] }}</div><span class="iconfont icon-tupian"></span></div>
                    <div class="data-tag gradient-02" style="width:12%;"><div class="data-title">经销商设计师配额</div><div class="data-num">{{ $data['quota_designer_dealer_used'].'/'.$data['quota_designer_dealer'] }}</div><span class="iconfont icon-tupian"></span></div>
                    <div class="data-tag gradient-03" style="width:12%;"><div class="data-title">账号有效期</div><div class="data-num">{{$data['account_expired_at']}}</div><span class="iconfont icon-tupian"></span></div>
                </div>
            </div>
        </div>
    </div>
    <div style="padding:15px;">
        <div class="section" id="data-section">
            <div class="data-outer">
            <div class="title">设计方案统计</div>
            <div class="data-tag-outer">
                <div class="data-tag gradient-02"><div class="data-title">方案总数</div><div class="data-num">{{ $data['album_count_total'] }}</div><span class="iconfont icon-tupian"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">昨日新增</div><div class="data-num">{{$data['album_count_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">待审核</div><div class="data-num">{{$data['album_count_to_do']}}</div><span class="iconfont icon-tupian"></span></div>
            </div>
            </div>
            <div class="data-outer">
            <div class="title">设计师统计</div>
            <div class="data-tag-outer">
                <div class="data-tag gradient-02"><div class="data-title">设计师总数</div><div class="data-num">{{$data['designer_count_total']}}</div><span class="iconfont icon-user"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">昨日新增</div><div class="data-num">{{$data['designer_count_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">待审核</div><div class="data-num">{{$data['designer_count_to_do']}}</div><span class="iconfont icon-user"></span></div>
            </div>
            </div>
            <div class="data-outer">
            <div class="title">销售商统计</div>
            <div class="data-tag-outer">
                <div class="data-tag gradient-02"><div class="data-title">销售商总数</div><div class="data-num">{{$data['dealer_count_total']}}</div><span class="iconfont icon-user"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">昨日新增</div><div class="data-num">{{$data['dealer_count_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">待审核</div><div class="data-num">{{$data['dealer_count_to_do']}}</div><span class="iconfont icon-user"></span></div>
            </div>
            </div>
            <div class="data-outer">
            <div class="title">产品统计</div>
            <div class="data-tag-outer">
                <div class="data-tag gradient-02"><div class="data-title">产品总数</div><div class="data-num">{{$data['product_count_total']}}</div><span class="iconfont icon-node_end-copy"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">昨日新增</div><div class="data-num">{{$data['product_count_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">待审核</div><div class="data-num">{{ $data['product_count_to_do'] }}</div><span class="iconfont icon-node_end-copy"></span></div>
            </div>
            </div>
            <div class="data-outer">
            <div class="title">积分相关</div>
            <div class="data-tag-outer">
                {{--<div class="data-tag gradient-02"><div class="data-title">销售商积分余额</div><div class="data-num">待开发</div><span class="iconfont icon-BAI-qian"></span></div>--}}
                <div class="data-tag gradient-02"><div class="data-title">设计师持有积分总额</div><div class="data-num">{{$data['designer_money']}}</div><span class="iconfont icon-BAI-qian"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">待处理商城订单</div><div class="data-num">{{$data['sheet_to_do']}}</div><span class="iconfont icon-user"></span></div>
            </div>
            </div>
            {{--<div class="data-tag-outer">
                <div class="data-tag gradient-01"><div class="data-title">方案总数</div><div class="data-num">{{ $data['album_count_total'] }}</div><span class="iconfont icon-tupian"></span></div>
                <div class="data-tag gradient-02"><div class="data-title">设计师总数</div><div class="data-num">{{$data['designer_count_total']}}</div><span class="iconfont icon-user"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">产品总数</div><div class="data-num">{{$data['product_count_total']}}</div><span class="iconfont icon-node_end-copy"></span></div>
                <div class="data-tag gradient-04"><div class="data-title">积分账号余额</div><div class="data-num">{{$data['money']}}</div><span class="iconfont icon-BAI-qian"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">昨日新增方案</div><div class="data-num">{{$data['album_count_yesterday']}}</div><span class="iconfont icon-tupian"></span></div>
                <div class="data-tag gradient-02"><div class="data-title">昨日新增设计师</div><div class="data-num">{{$data['designer_count_yesterday']}}</div><span class="iconfont icon-user"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">昨日新增产品</div><div class="data-num">{{$data['product_count_yesterday']}}</div><span class="iconfont icon-node_end-copy"></span></div>
                <div class="data-tag gradient-04"><div class="data-title">昨日活跃设计师数</div><div class="data-num">{{$data['active_count_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                <div class="data-tag gradient-01"><div class="data-title">一级销售商总数</div><div class="data-num">{{ $data['dealer_lv1_count_total'] }}</div><span class="iconfont icon-user"></span></div>
                <div class="data-tag gradient-02"><div class="data-title">二级销售商总数</div><div class="data-num">{{ $data['dealer_lv2_count_total'] }}</div><span class="iconfont icon-user"></span></div>
                <div class="data-tag gradient-03"><div class="data-title">今日活跃一级销售商数</div><div class="data-num">{{ $data['dealer_lv1_count_active'] }}</div><span class="iconfont icon-huoyue"></span></div>
                <div class="data-tag gradient-04"><div class="data-title">今日活跃二级销售商数</div><div class="data-num">{{ $data['dealer_lv2_count_active'] }}</div><span class="iconfont icon-huoyue"></span></div>
            </div>--}}
            <div id="album-add" style="width:100%;height:400px;"></div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var xData = <?php echo $chartDataX ?>;
        var y1Data = <?php echo $chartDataY ?>;
    </script>

@endsection
