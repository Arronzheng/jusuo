@extends('v1.site.center.layout',[
    'css' => [],
    'js'=>[
        '/v1/js/site/center/common/common.js',
        '/v1/static/js/xlPaging.js'
    ]
])

@section('main-content')
        <!--        产品列表html-->
<div class="detailview" id="b1" >
    <div class="desigplan">
        <div class="designtitle" id="product_brand_id"></div>
        <input type="text" name="productname"  class="productinput" placeholder="搜索产品名名称" id="product_name_value"/>
        <div class="searchlogo"></div>
    </div>
    <div class="designsearch">
        <div class="designsearchtitle" style="margin-left:20px;">应用类别</div>
        <div class="select-menu" style="width:184px;margin-left:10px;">
            <div class="select-menu-div" style="width:184px;">
                <input readonly class="select-menu-input" placeholder="请选择" id="product_ac_input"/>
                <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
            </div>
            <ul class="select-menu-ul" style="width:184px;" id="product_ac_ul">

            </ul>
        </div>
        <div class="designsearchtitle" style="margin-left:50px;">工艺类别</div>
        <div class="select-menu" style="width:184px;margin-left:10px;">
            <div class="select-menu-div" style="width:184px;">
                <input readonly class="select-menu-input" placeholder="请选择" id="product_tc_input"/>
                <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
            </div>
            <ul class="select-menu-ul" style="width:184px;" id="product_tc_ul">

            </ul>
        </div>
        <div class="designsearchtitle" style="margin-left:50px;">产品色系</div>
        <div class="select-menu" style="width:184px;margin-left:10px;">
            <div class="select-menu-div" style="width:184px;">
                <input readonly class="select-menu-input" placeholder="请选择" id="product_clr_input"/>
                <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
            </div>
            <ul class="select-menu-ul" style="width:184px;" id="product_clr_ul">

            </ul>
        </div>
    </div>
    <div class="designsearch">
        <div class="designsearchtitle" style="margin-left:20px;">产品规格</div>
        <div class="select-menu" style="width:184px;margin-left:10px;">
            <div class="select-menu-div" style="width:184px;">
                <input readonly class="select-menu-input" placeholder="请选择" id="product_spec_input"/>
                <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
            </div>
            <ul class="select-menu-ul" style="width:184px;" id="product_spec_ul">

            </ul>
        </div>
        <div class="designsearchtitle" style="margin-left:50px;">产品状态</div>
        <div class="select-menu" style="width:184px;margin-left:10px;">
            <div class="select-menu-div" style="width:184px;">
                <input readonly class="select-menu-input" placeholder="请选择" id="product_status_input"/>
                <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
            </div>
            <ul class="select-menu-ul" style="width:184px;" id="product_status_ul">

            </ul>
        </div>
        <div class="designsearchtitle" style="margin-left:50px;">产品结构</div>
        <div class="select-menu" style="width:184px;margin-left:10px;">
            <div class="select-menu-div" style="width:184px;">
                <input readonly class="select-menu-input" placeholder="请选择" id="product_str_input"/>
                <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
            </div>
            <ul class="select-menu-ul" style="width:184px;" id="product_str_ul">

            </ul>
        </div>
    </div>
    <div class="shuaibotton" onclick="get_product_list()">组合筛选</div>
    <div id="produ1" style="margin-top:52px;margin-left:20px;">
        <table border="0" id="tc_table"></table>
    </div>
    <div id="page1"></div>
    <input id="product_ac_value" value="" type="hidden">
    <input id="product_tc_value" value="" type="hidden">
    <input id="product_clr_value" value="" type="hidden">
    <input id="product_spec_value" value="" type="hidden">
    <input id="product_status_value" value="" type="hidden">
    <input id="product_str_value" value="" type="hidden">
</div>

@endsection


@section('script')

    <script>
        var show_structure = "{{auth()->user()->organization_type == \App\Models\Designer::ORGANIZATION_TYPE_SELLER?1:0}}";
    </script>
    <script type="text/javascript" src="{{asset('/v1/js/site/center/product/index.js')}}"></script>

@endsection


