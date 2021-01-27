@extends('v1.admin_brand.layout',[])

@section('style')
    <style>
        .config-submit-btn{margin-left:10px;margin-top:0;}
        .config-form .layui-form-mid.sub-title{width:160px;}
        .config-form .layui-form-mid.root-title{width:180px;}
    </style>
@endsection

@section('content')
    <div class="layui-card layadmin-header">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a><cite>积分商城</cite></a><span lay-separator="">/</span>
            <a><cite>积分规则</cite></a>
        </div>
    </div>
    <div class="layui-fluid">

        <div class="layui-card">
            <div class="layui-card-header">积分获取</div>
            <div class="layui-card-body">
                <div class="config-form"  action="">
                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'brand.integral_shop.rules.add_integral_per_space';?>
                                <div class="layui-form-mid">上传方案每空间获得积分</div>
                                <div class="layui-input-block input-value-block">
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                </div>
                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />

                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>



                    <div class="layui-form-item">
                        <form class="layui-form">
                            <div class="layui-form-mid">方案被下载/复制</div>
                            <div class="layui-inline">
                                <div class="layui-form-mid">（按作者设置增加积分）</div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-form-item">
                        <form class="layui-form">
                            <div class="layui-form-mid">每周一次，方案关注度排名前</div>
                            <?php $cname = 'brand.integral_shop.rules.add_integral_by_album_focus_rank';?>
                            <div class="layui-inline">
                                <div class="layui-input-inline short-input-inline" >
                                    <input type="text" name="rank_num" value="{{isset($param['configs'][$cname]['rank_num'])?$param['configs'][$cname]['rank_num']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid">&nbsp;名，增加 </div>
                                <div class="layui-input-inline short-input-inline" >
                                    <input type="text" name="add_integral" value="{{isset($param['configs'][$cname]['add_integral'])?$param['configs'][$cname]['add_integral']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid"> 积分</div>

                                <input type="hidden" name="param_name" value="{{$cname}}">
                                <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            </div>
                        </form>
                    </div>
                    <div class="layui-form-item">
                        <form class="layui-form">
                            <div class="layui-form-mid">每周一次，设计师关注度排名前</div>
                            <?php $cname = 'brand.integral_shop.rules.add_integral_by_designer_focus_rank';?>
                            <div class="layui-inline">
                                <div class="layui-input-inline short-input-inline" >
                                    <input type="text" name="rank_num" value="{{isset($param['configs'][$cname]['rank_num'])?$param['configs'][$cname]['rank_num']:''}}" placeholder="X" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid">&nbsp;名，增加 </div>
                                <div class="layui-input-inline short-input-inline" >
                                    <input type="text" name="add_integral" value="{{isset($param['configs'][$cname]['add_integral'])?$param['configs'][$cname]['add_integral']:''}}" placeholder="Y" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid"> 积分</div>

                                <input type="hidden" name="param_name" value="{{$cname}}">
                                <input class="layui-btn layui-btn-sm" onclick="submit_config_form(this,1)" type="button" value="提交" />
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">积分扣减</div>
            <div class="layui-card-body">
                <div class="config-form"  action="">
                    <div class="config-block">
                        <div class="layui-form-item">
                            <form action="" class="layui-form">
                                <?php $cname = 'brand.integral_shop.rules.reduce_integral_by_author_ratio';?>
                                <div class="layui-form-mid" style="width:260px;">下载/复制他人方案，按作者设置增加积分值乘以</div>
                                <div class="layui-input-block input-value-block">
                                    @include('v1.admin.components.param_config.single_text',[
                                     'cname'=>$cname,'config_set'=>isset($param['configs'][$cname])?$param['configs'][$cname]:null
                                     ])
                                </div>
                                <div class="layui-form-mid">扣减下载者积分</div>

                                <input class="layui-btn layui-btn-sm config-submit-btn" onclick="submit_config_form(this,1)" type="button" value="提交" />

                                <input type="hidden" name="param_name" value="{{$cname}}"/>
                            </form>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <form class="layui-form">
                            <div class="layui-form-mid">在积分商城兑换礼品</div>
                            <div class="layui-inline">
                                <div class="layui-form-mid">（按商品设置扣减相应积分）</div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


    </div>

@endsection

@section('script')

    @include('v1.admin.components.param_config.script.submit_config_form_script')

@endsection