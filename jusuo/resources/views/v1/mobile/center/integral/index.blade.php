@extends('v1.mobile.layout',[
   'css'=>[
       '/v1/css/mobile/center/integral.css'
   ],
   'js'=>[
   ]
])

@section('content')
    <style>
        html,body{background-color:#F0F0F0!important;}
        #top-container{height:25vw;display:flex;align-items: center;background-color:#ffffff;}
        #top-container img{width:50px;height:50px;margin-left:20px;margin-right:10px;}
        #top-container .integral-info{height:50px;}
        #top-container .integral-info .info-top{display:flex;align-items: flex-end}
        #top-container .integral-info .info-top .integral-value{margin-right:5px;font-size:20px;color:#1582FF;font-weight:bold;}
        #top-container .integral-info .info-top .integral-unit{font-size:12px;margin-bottom:4px;color:#1582FF;}
        #top-container .integral-info .info-bottom{font-size:12px;color:#666666;}

        #log-container{height:12vw;width:100%;}
        #log-container .tab-list{width:100%;height:100%;display:flex;color:#555555;font-size:12px;}
        #log-container .tab-list .tab-item{position:relative;width:20%;display:flex;justify-content:center;align-items:center;height:100%;text-align:center;}
        #log-container .tab-list .tab-item .color-block{display:none;height:2px;width:60%;background-color:#1582FF;position:absolute;left:20%;top:72%;}
        #log-container .tab-list .tab-item.active{color:#1582FF}
        #log-container .tab-list .tab-item.active .color-block{display:block}
        #log-container .tab-content{
            width:100%;overflow:hidden;
        }
        #log-container .tab-content .log-list{
            padding:5px;background-color:#ffffff;margin:7px;display:none;
            -webkit-border-radius:10px;
            -moz-border-radius:10px;
            border-radius:10px;
        }
        #log-container .tab-content .log-list .table-content{

        }
        #log-container .tab-content .log-list.show{display:block}
        .border-box { -webkit-box-sizing:border-box; -moz-box-sizing:border-box; -ms-box-sizing:border-box; box-sizing:border-box }
        table td{border:0;white-space: nowrap;}
        table thead td{border:0;white-space: nowrap;font-weight:bold;}
        .get-more-btn{
            display:none;
            height:25px;line-height:25px;margin:0 auto;text-align:center;
            color:#888888;border:1px solid #888888;font-size:12px;width:30%;
            margin-top:15px;
        }
        .table-content{overflow:hidden;}
        #exchange-table-1-box{float:left;width:80%;overflow-x:scroll}
        #exchange-table-1-box #exchange-table-1{}
        #exchange-table-2{float:left;width:20%;}
        #exchange-table-2 .op-btn{color:#1582FF}
    </style>

    <div class="container" id="container">
        <div id="top-container">
            <img src="/v1/images/mobile/center/integral/jinbi.png" id="integral-icon"/>
            <div class="integral-info">
                <div class="info-top">
                    <span class="integral-value">{{\Illuminate\Support\Facades\Auth::user()->detail->point_money}}</span>
                    <span class="integral-unit">积分</span>
                </div>
                <div class="info-bottom">发布赚积分 积分享兑换</div>
            </div>
        </div>

        <div id="log-container">
            <div class="tab-list">
                <div class="tab-item active">变动明细<span class="color-block"></span></div>
                <div class="tab-item">兑换记录<span class="color-block"></span></div>
                @if($integral_rule_link)
                <div class="tab-item" onclick="event.preventDefault();location.href='{{$integral_rule_link}}'">积分规则<span class="color-block"></span></div>
                @endif
            </div>
            <div class="tab-content ">
                <div class="log-list  show">
                    <div class="table-content">
                        <table style="border:0" border="0" cellpadding="0" cellspacing="0">
                            <thead>
                            <tr style="">
                                <td>时间</td>
                                <td>事件</td>
                                <td>变动值</td>
                                <td>变动后积分</td>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                    <div class="get-more-btn">加载更多</div>
                </div>
                <div class="log-list" style="position:relative;">
                    <div  class="table-content">
                        <div id="exchange-table-1-box">
                            <table id="exchange-table-1" border="0" cellpadding="0" cellspacing="0">
                                <thead>
                                <tr style="">
                                    <td>兑换商品</td>
                                    <td>数量</td>
                                    <td>积分</td>
                                    <td>收货人</td>
                                    <td>电话</td>
                                    <td>收货地址</td>
                                    <td>状态</td>
                                    <td>时间</td>
                                </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>

                        <table id="exchange-table-2">
                            <thead>
                            <tr style="">
                                <td width="80">操作</td>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>


                    <div class="get-more-btn">加载更多</div>
                </div>

            </div>
        </div>

    </div>

    @verbatim
    <script type="text/html" id="table-0-tpl">
        {{ each data log i }}

        <tr>
            <td>{{log.created_at}}</td>
            <td>{{log.remark}}</td>
            <td>{{log.integral}}</td>
            <td>{{log.available_integral}}</td>
        </tr>

        {{ /each }}
    </script>

    <script type="text/html" id="table-1-left-tpl">

        {{ each data log i }}

        <tr>
            <td>{{log.good_name}}</td>
            <td>{{log.count}}</td>
            <td>{{log.total}}</td>
            <td>{{log.receiver_name}}</td>
            <td>{{log.receiver_tel}}</td>
            <td>{{log.full_address}}</td>
            <td>{{log.status_text}}</td>
            <td>{{log.created_at}}</td>
            <td>{{log.operation}}</td>
        </tr>

        {{ /each }}
    </script>

    <script type="text/html" id="table-1-right-tpl">

        {{ each data log i }}

        <tr>
            <td>
                {{ if log.show_cancel == 1 }}
                <a class="op-btn" onclick="cancel_order('{{log.id}}')" title="取消兑换" href="javascript:;" >
                    取消兑换
                </a>
                {{ /if }}
            </td>
        </tr>

        {{ /each }}
    </script>

    @endverbatim

@endsection

@section('script')

    <script>

        var center_index_api_url = "{{url('/mobile/center/api/index')}}";
        var integral_log_api_url = "{{url('/mobile/center/integral/api/my_integral_log')}}";
        var exchange_log_api_url = "{{url('/mobile/center/integral/api/my_exchange_log')}}";
        var current_url = "{{url()}}";
        var table_page = [];
        var table_page_max = [];
        var table_init = [];
        table_page[0] = 0
        table_page[1] = 0
        table_page_max[0] = 999
        table_page_max[1] = 999
        table_init[0] = false
        table_init[1] = false

        $('.tab-list .tab-item').on('click',function(){
            var index = $(this).index()
            $('.tab-item').removeClass('active')
            $(this).addClass('active')
            $('.log-list').hide();
            $('.log-list').eq(index).show();
            if(!table_init[index]){
                get_table_data(index)
            }

        });

        //获取兑换明细列表
        get_table_data(0)

        //加载更多列表
        $('.get-more-btn').on('click',function(){
            var index = $(".get-more-btn").index(this);
            console.log(index)

            get_table_data(index)
        });

        //获取table数据
        function get_table_data(index){
            //如果列表已经拿完数据，则不再请求
            if(table_page[index] >= table_page_max[index]){
                return false;
            }
            layer.load();
            var new_page = parseInt(table_page[index]) + 1
            var api_url = index == 0?integral_log_api_url:exchange_log_api_url;
            ajax_get(api_url+"?page="+new_page,function(res){

                layer.closeAll("loading");

                if(res.status){
                    if(index == 0){
                        var html = template('table-'+index+'-tpl', {data:res.data.data});
                        $('.log-list').eq(index).find('tbody').append(html);
                    }else{
                        var table_left = template('table-'+index+'-left-tpl', {data:res.data.data});
                        var table_right = template('table-'+index+'-right-tpl', {data:res.data.data});
                        $('#exchange-table-1').find('tbody').append(table_left);
                        $('#exchange-table-2').find('tbody').append(table_right);
                    }

                    //设置table已初始化
                    table_init[index] = true;

                    //设置table的当前页码
                    table_page[index] =res.data.current_page
                    table_page_max[index] =res.data.last_page
                    if(res.data.current_page == res.data.last_page){
                        $('.log-list').eq(index).find('.get-more-btn').hide();
                    }else{
                        $('.log-list').eq(index).find('.get-more-btn').show();
                    }
                }
                else if(res.status==0){
                    m_go_login();
                }

            },function(){})
        }

        function cancel_order(id){
            layer.confirm('确定取消兑换吗?', {icon: 3, title:'提示'}, function(index){
                ajax_post('/mobile/center/integral/api/exchange/cancel/'+id,{
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


    </script>

    <script src="{{asset('/v1/js/mobile/center.js')}}"></script>

@endsection