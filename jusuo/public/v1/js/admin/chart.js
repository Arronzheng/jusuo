window.onload = function(){
    showChart();
}


function showChart() {

    var myChart = echarts.init(document.getElementById('album-add'));

    option = {
        grid: {
            left: '7%',
            right: '5%',
            top: '25%',
            bottom: '19%',
        },
        title: {
            show: false
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
            data: ['新增方案数'],
            right: '3%',
            top: '10%',
            itemWidth: 11,
            itemHeight: 11,
            textStyle: {
                color: '#333',
                fontSize: 13
            }
        },
        toolbox: {
            show: false,
        },
        xAxis: [
            {
                type: 'category',
                boundaryGap: true,
                show: true,
                axisTick: {
                    show: false
                },
                axisLabel: {
                    fontSize: 12,
                    color: '#d0d0d0',
                    margin: 8,
                    interval: 0,
                },
                axisLine: {
                    lineStyle: {
                        type: 'solid',
                        color: '#4e608b',//左边线的颜色
                        width: '1'//坐标线的宽度
                    }
                },
                data: xData
            }
        ],
        yAxis: [
            {
                type: 'value',
                scale: true,
                name: '',
                axisLine: {
                    show: false
                },
                splitNumber: 4,
                axisTick: {
                    show: false
                },
                splitLine: {
                    lineStyle: {
                        // 使用深浅的间隔色
                        color: '#4e608b'
                    }
                },
                axisLabel: {
                    fontSize: 12,
                    color: '#d0d0d0',
                },
                min: 0,
                boundaryGap: [0.2, 0.2]
            }
        ],
        series: [
            {
                name: '新增方案数',
                type: 'bar',
                label: {
                    normal: {
                        show: true,
                        position: 'top',
                        textStyle: {
                            color: '#1dacfe'
                        }
                    }
                },
                itemStyle: {
                    normal: {
                        color: new echarts.graphic.LinearGradient(0, 1, 0, 0, [{
                            offset: 0,
                            color: "#4889fb" // 0% 处的颜色
                        }, {
                            offset: 1,
                            color: "#15b3ff" // 100% 处的颜色
                        }], false)
                    }
                },
                barWidth: '50%',
                data: y1Data
            }
        ]
    };

    myChart.setOption(option);
}