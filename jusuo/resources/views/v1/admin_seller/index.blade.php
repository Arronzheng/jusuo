@extends('v1.admin_seller.layout',[
    'css'=>['/v1/static/admin-font/iconfont.css'],
    'js'=>['/v1/static/js/echarts.js','/v1/js/admin/chart.js'],
])

@section('style')

    <style>
        .title{margin-top:10px;margin-left:12px;font-size:16px;font-weight:bold;}
    </style>

@endsection

@section('content')
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
                <div class="title">产品统计</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02"><div class="data-title">产品总数</div><div class="data-num">{{$data['product_count_total']}}</div><span class="iconfont icon-node_end-copy"></span></div>
                    <div class="data-tag gradient-01"><div class="data-title">昨日新增</div><div class="data-num">{{$data['product_count_yesterday']}}</div><span class="iconfont icon-huoyue"></span></div>
                </div>
            </div>
            <div class="data-outer">
                <div class="title">积分相关</div>
                <div class="data-tag-outer">
                    <div class="data-tag gradient-02"><div class="data-title">积分账号余额</div><div class="data-num">{{$data['money']}}</div><span class="iconfont icon-BAI-qian"></span></div>
                    <div class="data-tag gradient-01"><div class="data-title">设计师奖励积分总额</div><div class="data-num">{{$data['designer_money']}}</div><span class="iconfont icon-BAI-qian"></span></div>
                </div>
            </div>
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