@extends('v1.site.center.layout',[
    'css' => [],
    'js'=>[
        '/v1/js/site/center/common/common.js',
        '/v1/js/site/center/integral/index.js',
        '/v1/static/js/xlPaging.js'
    ]
])

@section('main-content')
        <!-- 收藏关注-->
<style>
    .top-info{padding:30px 20px 40px 20px;}
    .top-info .title{color:#333333;font-size:14px;font-weight:bold;margin-bottom:15px;}
    .top-info .money-block{display:flex;align-items:center}
    .top-info .money-block .money{margin-right:10px;color:#3D82F7;font-weight:bold;font-size:35px;}
    .top-info .money-block .unit{margin-top:15px;}
    .top-info .money-block .i-icon{height:40px;margin-right:25px;}
    .top-info .money-block .exchange-btn{background-color:#3D82F7;width:150px;}
    .c-divider{margin-bottom:30px;height:3px;border-top:1px dashed #dfdfdf;border-bottom:1px dashed #dfdfdf;width:100%;}
    .disable-tab{
        cursor: pointer;display: inline-block;
        *display: inline;
        *zoom: 1;
        vertical-align: middle;
        font-size: 14px;
        transition: all .2s;
        -webkit-transition: all .2s;
        position: relative;
        line-height: 40px;
        min-width: 65px;
        padding: 0 15px;
        text-align: center;
        cursor: pointer;
    }
</style>
<div class="detailview" id="b3" >
    <div class="top-info">
        <div class="title">当前可用积分</div>
        <div class="money-block">
            <div class="money">{{$detail->point_money}}</div>
            <div class="unit">积分</div>
            <img class="i-icon" src="{{asset('v1/images/site/center/integral/jinbi.png')}}"/>

            <button onclick="location.href='/mall'" type="button" class="layui-btn exchange-btn layui-btn-sm" onclick="reloadTable()" >
                立即兑换
            </button>
        </div>
    </div>


    <div class="c-divider"></div>

    <div class="log-container">
        <div class="log-tabs">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    <li id="tab1" class="layui-this" onclick="init_integral_log()">变动明细</li>
                    <li id="tab2" onclick="init_exchange_log()">兑换记录</li>
                    @if($integral_rule_link)
                    <div class="disable-tab" onclick="event.preventDefault();window.open('{{$integral_rule_link}}')">积分规则</div>
                    @endif
                </ul>
                <div class="layui-tab-content">
                    <div id="tab1-content" class="layui-tab-item layui-show">
                        <table class="layui-table" id="tableInstance1" lay-filter="tableFilter" ></table>

                    </div>
                    <div id="tab2-content" class="layui-tab-item">
                        <table class="layui-table" id="tableInstance2" lay-filter="tableFilter"></table>

                    </div>
                    <div id="tab3-content" class="layui-tab-item">内容3</div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="tableToolbarTpl">
        @{{# if (d.show_cancel == 1) { }}
        <a onclick="cancel_order('@{{d.id}}')" title="取消兑换" href="javascript:;"  class="layui-btn-custom-blue layui-btn  layui-btn-xs" lay-event="edit">
             取消兑换
        </a>
        @{{# } }}
    </script>
</div>


@endsection


@section('script')

<script>
    var table = layui.table;

    var integral_log_init = false;
    var exchange_log_init = false;

    $(document).ready(function(){

        var hash = location.hash;
        console.log(hash)
        if(hash){
            $('.layui-tab-title li').removeClass('layui-this');
            $(hash).addClass('layui-this');
            $('.layui-tab-content div').removeClass('layui-show');
            $(hash+'-content').addClass('layui-show');
        }

        //初始化变动明细表
        init_integral_log();

    });

    function cancel_order(id){
        layer.confirm('确定取消兑换吗?', {icon: 3, title:'提示'}, function(index){
            ajax_post('/center/integral/api/exchange/cancel/'+id,{
                '_method':'PATCH',
                'id':id,
            },function(result){
                if(result.status){
                    layer.msg('取消兑换成功！')
                    location.reload();
                }else{
                    layer.msg(result.msg)
                }
            });

            layer.close(index);
        });
    }


    function init_integral_log(refresh){
        if(!integral_log_init  || refresh==true){
            var tableInstance1 = table.render({
                skin: 'line'
                ,elem: '#tableInstance1'
                ,url: "/center/integral/api/my_integral_log" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    /*{field: 'designer_id', title: 'ID', width:80, fixed: 'left'}*/
                    {field: 'created_at', title: '时间'}
                    ,{field: 'remark', title: '事件'}
                    ,{field: 'integral', title: '变动值'}
                    ,{field: 'available_integral', title: '变动后积分值'}
                ]]
            });
        }

        integral_log_init = true;
    }

    function init_exchange_log(refresh){
        if(!exchange_log_init || refresh==true){
            var tableInstance2 = table.render({
                skin: 'line'
                ,elem: '#tableInstance2'
                ,url: "/center/integral/api/my_exchange_log" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    /*{field: 'designer_id', title: 'ID', width:80, fixed: 'left'}*/
                    {field: 'good_name',width:120, title: '兑换商品'}
                    ,{field: 'count',width:60, title: '数量'}
                    ,{field: 'total',width:60, title: '积分'}
                    ,{field: 'receiver_name',width:80, title: '收货人'}
                    ,{field: 'receiver_tel',width:140, title: '电话'}
                    ,{field: 'full_address',width:200, title: '收货地址'}
                    ,{field: 'status_text',width:100, title: '状态'}
                    ,{field: 'created_at',width:180, title: '时间'}
                    ,{field: 'operation',width:100, title: '操作' ,fixed:'right',templet:'#tableToolbarTpl'}

                ]]
            });
        }

        exchange_log_init = true;
    }

</script>

@endsection


