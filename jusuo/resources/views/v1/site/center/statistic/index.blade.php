@extends('v1.site.center.layout',[
    'css' => [],
    'js'=>[
        '/v1/js/site/center/common/common.js',
        '/v1/js/site/center/statistic/index.js',
        '/v1/static/js/Chart.js',
    ]
])

@section('main-content')
        <!-- 我的统计-->
<div class="detailview" id="b2">
    <div class="desigplan" id="tongjidaohang"></div>
    <div id="g0content">
        <div class="chart">
            <div class="chart1container">
                <div class="chart1title">
                    <div class="viewlogo"></div>
                    <div class="viewlabel">浏览量</div>
                </div>
                <div class="dataview">
                    <div class="dataview1">
                        <div class="oldview" id="chart_album_yes_visit_num">0</div>
                        <div class="oldview1">昨天新增浏览量</div>
                    </div>
                    <div class="dataview1" style="margin-left:60px;">
                        <div class="oldview" id="chart_album_month_visit_num">0</div>
                        <div class="oldview1">本月新增浏览量</div>
                    </div>
                </div>
                <div class="chartt">
                    <label class="ctitle">新增浏览量趋势</label>
                    <label class="ctitle1">近7天</label>
                </div>
                <canvas id="lines-graph"></canvas>
            </div>
            <div class="chart1container">
                <div class="chart1title">
                    <div class="viewlogo"></div>
                    <div class="viewlabel">收藏量</div>
                </div>
                <div class="dataview">
                    <div class="dataview1">
                        <div class="oldview" id="chart_album_yes_collect_num">0</div>
                        <div class="oldview1">昨天新增收藏量</div>
                    </div>
                    <div class="dataview1" style="margin-left:60px;">
                        <div class="oldview" id="chart_album_month_collect_num">0</div>
                        <div class="oldview1">本月新增收藏量</div>
                    </div>
                </div>
                <div class="chartt">
                    <label class="ctitle">新增收藏量趋势</label>
                    <label class="ctitle1">近7天</label>
                </div>
                <canvas id="lines-graph1"></canvas>
            </div>
        </div>
        <div class="topcontainer">
            <div class="chart1title" style="padding-top:20px;">
                <div class="viewlogo"></div>
                <div class="viewlabel">近30天浏览量TOP5方案</div>
            </div>
            <div id="top5view" style="margin-left:7px;"></div>
        </div>
        <div class="topcontainer">
            <div class="chart1title" style="padding-top:20px;">
                <div class="viewlogo1"></div>
                <div class="viewlabel">近30天收藏量TOP5方案</div>
            </div>
            <div id="top5view1" style="margin-left:7px;"></div>
        </div>
    </div>
    <!--            产品统计-->
    <div id="g1content" style="display: none;">
        <div class="protjcontainer">
            <div class="chart1title" style="padding-top:20px;">
                <div class="viewlogo2"></div>
                <div class="viewlabel">近7天使用的产品次数及占比</div>
            </div>
            <canvas id="myChart" width="800" height="280"></canvas>
        </div>
        <div class="topcontainer1">
            <div class="chart1title" style="padding-top:20px;">
                <div class="viewlogo2"></div>
                <div class="viewlabel">近30天浏览量TOP5产品</div>
            </div>
            <div id="top5viewpro" style="margin-left:7px;"></div>
        </div>
        <div class="topcontainer1">
            <div class="chart1title" style="padding-top:20px;">
                <div class="viewlogo2"></div>
                <div class="viewlabel">近30天收藏量TOP5产品</div>
            </div>
            <div id="top5viewpro1" style="margin-left:7px;"></div>
        </div>
    </div>
</div>


@endsection


@section('script')



@endsection


