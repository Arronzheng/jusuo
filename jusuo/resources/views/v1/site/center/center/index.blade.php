@extends('v1.site.layout',[
   'css'=>[
        '/v1/static/iconfont/iconfont.css',
        '/v1/static/swiper/swiper.min.css',
        '/v1/css/site/myplan.css',
        '/v1/css/site/user/bind_wechat.css',
   ],
   'js'=>[
        '/v1/static/js/xlPaging.js',
        '/v1/static/js/Chart.js',
        '/v1/static/js/province.js',
        '/v1/static/swiper/swiper.min.js',
        '/v1/js/ajax.js',
        '/v1/js/site/myplan.js',
   ]
])

@section('content')
    <div class="container">
        <div class="content">
            <div class="daohangview">
                <div id="daohangblock">

                </div>
            </div>
            <!--        我的方案html-->
            <div class="detailview" id="b0" style="display: none;">
                <div class="desigplan">
                    <div class="designtitle">设计方案</div>
                    <div class="titlesearch">状态</div>
                    <div class="select-menu" style="width:246px;position: absolute;right:20px;">
                        <div class="select-menu-div" style="width:246px;">
                            <input readonly class="select-menu-input" placeholder="请选择" id="albun_status_input"/>
                            <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
                        </div>
                        <ul class="select-menu-ul" style="width:246px;" id="album_status_ul">

                        </ul>
                    </div>
                </div>
                <div class="designsearch">
                    <div class="designsearchtitle">方案名称</div>
                    <input type="text" name="designname"  class="titleinput" placeholder="填写方案名称" id="album_title" />
                    <div class="designsearchtitle" style="margin-left:50px;">设计风格</div>
                    <div class="select-menu" style="width:184px;margin-left:10px;">
                        <div class="select-menu-div" style="width:184px;">
                            <input readonly class="select-menu-input" placeholder="筛选风格" id="album_stl_input" />
                            <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
                        </div>
                        <ul class="select-menu-ul" style="width:184px;" id="album_stl_ul">

                        </ul>
                    </div>

                    <div class="designsearchtitle" style="margin-left:50px;">方案户型</div>
                    <div class="select-menu" style="width:184px;margin-left:10px;">
                        <div class="select-menu-div" style="width:184px;">
                            <input readonly class="select-menu-input" placeholder="筛选户型" id="album_ht_input"/>
                            <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
                        </div>
                        <ul class="select-menu-ul" style="width:184px;" id="album_ht_ul">

                        </ul>
                    </div>

                </div>

                <div class="designsearch" style="margin-top:12px;">

                    <div class="designsearchtitle">方案面积</div>
                    <div class="select-menu" style="width:184px;margin-left:10px;">
                        <div class="select-menu-div" style="width:184px;">
                            <input readonly class="select-menu-input" placeholder="筛选面积" id="album_ca_input"/>
                            <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
                        </div>
                        <ul class="select-menu-ul" style="width:184px;" id="album_ca_ul">

                        </ul>
                    </div>

                    <div class="designsearchtitle" style="margin-left:50px;">上传时间</div>
                    <div class="select-menu" style="width:184px;margin-left:10px;">
                        <div class="select-menu-div" style="width:184px;">
                            <input readonly class="select-menu-input" placeholder="上传时间区间" />
                            <i class="fa fa-angle-down" style="font-size:24px;color:#B7B7B7;"></i>
                        </div>
                        <ul class="select-menu-ul" style="width:184px;" id="album_time_ul">

                        </ul>
                    </div>

                    <div class="designsearchtitle" style="margin-left:50px;">产品编号</div>
                    <input type="text" name="designproname"  class="titleinput" placeholder="方案中产品编号" id="album_product_no"/>
                </div>

                <div class="shuaibotton" id="album_filter" onclick="get_album_list()">筛选</div>
                <div id="produ">
                    <div class="fangan_container" id="fangan"></div>
                </div>
                <div id="page"></div>
                <input id="album_stl_value" value="" type="hidden">
                <input id="album_ht_value" value="" type="hidden">
                <input id="album_ca_value" value="" type="hidden">
                <input id="album_status_value" value="" type="hidden">
                <input id="album_time_value" value="" type="hidden">
            </div>

            <!--        产品列表html-->
            <div class="detailview" id="b1" style="display: none;">
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

            <!-- 收藏关注-->
            <div class="detailview" id="b2" style="display: none;">
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


            <!-- 收藏关注-->
            <div class="detailview" id="b3" style="display: none;">
                <div class="desigplan" id="shoucangdaohang"></div>
                <div id="d0content">
                    <div id="produ5" style="padding-top:17px;">
                        <div class="c0container" id="d0container"></div>
                    </div>
                    <div class="page4" id="page5"></div>
                </div>
                <div id="d1content" style="display:none;">
                    <div class="scprohead">
                        {{--<div class="scprobotton1"  id="fav_prodduct_org_1">所属组织</div>--}}
                        {{--<div class="scprobotton2"  id="fav_prodduct_org_0">其他产品</div>--}}
                    </div>
                    <div id="produ6">
                        <div class="c0container" id="d1container"></div>
                    </div>
                    <div class="page4" id="page6"></div>
                </div>
                <div id="d2content" style="display:none;">
                    <div id="produ7" style="padding-top:20px;">
                        <div class="c0container" id="d2container"></div>
                    </div>
                    <div class="page4" id="page7" style="margin-top:40px;"></div>
                </div>
            </div>

            <!--消息通知-->
            <div class="detailview" id="b4" style="display: none;">
                <div class="desigplan" id="noticedaohang"></div>
                <div id="c0content">
                    <div id="produ2">
                        <div class="c0container" id="c0container"></div>
                    </div>
                    <div id="page2"></div>
                </div>
                <div id="c1content" style="display:none;">
                    <div id="produ3">
                        <div class="c0container" id="c1container"></div>
                    </div>
                    <div id="page3"></div>
                </div>
                <div id="c2content" style="display:none;">
                    <div id="produ4">
                        <div class="c0container" id="c2container"></div>
                    </div>
                    <div class="page4" id="page4"></div>
                </div>
            </div>

            <!-- 个人中心-->
            <div class="detailview" id="b5" style="display: none;">
                <div class="desigplan" id="persondaohang"></div>
                <div id="f0content" style="margin-top:30px;margin-left:20px;"></div>
                <div id="f1content" style="margin-top:10px;display: none;">
                    <!--                <div class="safelabel">姓名</div>-->
                    <!--                <input type="text" name="name"  class="tc_input" placeholder="请输入您的姓名" />-->
                    <!--                <div class="safelabel">身份证号</div>-->
                    <!--                <input type="text" name="id"  class="tc_input" placeholder="请输入您的身份证号" />-->
                    <!--                <div class="safelabel">上传身份证</div>-->
                    <!--                <div class="idcardimage">-->
                    <!--                    <div class="idcard_front">-->
                    <!--                        <div class="uploadimage" id="preview" onmouseenter="showdel_idfront()"  onmouseleave="showdel_idfront1()">-->
                    <!--                            <div class="id_front"></div>-->
                    <!--                            <div class="del_fengmian" style="display: none;" onclick="del_idfront()">删除</div>-->
                    <!--                            <input id="upload-inputf" style=" width:252px;height:156px; cursor: pointer;position: absolute; top: 0; left: 0;opacity: 0;" type="file" accept="image/gif, image/jpg, image/png" onchange="showImgfront(this)"/>-->
                    <!--                        </div>-->
                    <!--                        <div class="id_tip">上传身份证正面图片</div>-->
                    <!--                    </div>-->
                    <!--                    <div class="idcard_front" style="margin-left:16px;">-->
                    <!--                        <div class="uploadimage" id="preview1" onmouseenter="showdel_idback()"  onmouseleave="showdel_idback1()">-->
                    <!--                            <div class="id_back"></div>-->
                    <!--                            <div class="del_fengmian1" style="display: none;" onclick="del_idback()">删除</div>-->
                    <!--                            <input id="upload-input1" style=" width:252px;height:156px; cursor: pointer;position: absolute; top: 0; left: 0;opacity: 0;" type="file" accept="image/gif, image/jpg, image/png" onchange="showImgback(this)"/>-->
                    <!--                        </div>-->
                    <!--                        <div class="id_tip">上传身份证背面图片</div>-->
                    <!--                    </div>-->
                    <!--                </div>-->
                    <!--                <div class="confirmidentity">提交</div>-->
                </div>
                <div id="f1content1" style="margin-top:30px;margin-left:20px;display: none;">
                    <div class="idenwait">
                        <div class="waitlogo"></div>
                        <div class="waittext">正在审核中，请耐心等待</div>
                    </div>
                </div>
                <div id="f1content2" style="margin-top:30px;margin-left:20px;display: none;">
                    <div class="idenwait">
                        <span class="iconfont icon-icon-test" style="color:#1582FF;font-size:26px;"></span>
                        <div class="waittext">已实名认证</div>
                    </div>
                    <div class="ficontext">姓名</div>
                    <div class="f1contname" id="f1contname"></div>
                    <div class="ficontext1">身份证号码</div>
                    <div class="f1contname" id="f1contid"></div>
                </div>
                <div id="f1content3" class="main1">
                    <div class="qrcode1">
                        <div class="mtitles">很抱歉，您的审核不通过，请再次修改</div>
                        <div class="mconfirm" onclick="closef1content3()">确定</div>
                    </div>
                </div>
                <div id="f2content" style="display: none;"></div>
                <div id="f2content1" style="margin-top:30px;margin-left:20px;display: none;">
                    <div class="idenwait">
                        <div class="waitlogo"></div>
                        <div class="waittext">正在审核中，请耐心等待</div>
                    </div>
                </div>
            </div>

            <!-- 安全中心-->
            <div class="detailview" id="b6" style="display: none;">
                <div class="desigplan" id="safedaohang"></div>
                <div id="e0content" style="padding-top:10px;">
                    <div id="bypsw">
                        <div class="safelabel">原始密码</div>
                        <input type="password" name="orignalpsw" id="by_pwd_original_pwd"  class="tc_input" placeholder="请输入原密码" />
                        <div class="safelabel">新密码</div>
                        <input type="password" name="newpsw"  class="tc_input" id="by_pwd_new_pwd" placeholder="请输入新密码" />
                        <div class="safelabel">确认密码</div>
                        <input type="password" name="confirmpsw"  class="tc_input" id="by_pwd_confirm_pwd" placeholder="请确认新密码" />
                        <div class="safebttom">
                            <div class="confirmbutton" onclick="bindSubmitChangePwdByPwd()">确认</div>
                            <div class="pswbyphone" onclick="changepsw()">使用手机验证码修改</div>
                        </div>
                    </div>
                    <div id="byphone" style="display: none;">
                        <div class="safelabel">手机号码</div>
                        <div id="tel"></div>
                        <div class="safelabel">验证码</div>
                        <div class="confirmcode">
                            <input type="password" name="code"  class="tc_input1" id="by_phone_smscode" placeholder="请输入验证码" />
                            <input type="hidden" id="by_phone_phone" value="">
                            <div class="sendcode" onclick="bindChangePwdSendCode()">发送验证码</div>
                        </div>
                        <div class="safelabel">新密码</div>
                        <input type="password" name="newpsw1" id="by_phone_newpassword"  class="tc_input" placeholder="请输入密码" />
                        <div class="safelabel">确认密码</div>
                        <input type="password" name="confirmpsw1" id="by_phone_confirmpassword"  class="tc_input" placeholder="请确认新密码" />
                        <div class="safebttom">
                            <div class="confirmbutton" onclick="bindSubmitChangePwdByPhone()">确认</div>
                            <div class="pswbyphone" onclick="changepsw()">使用原密码修改</div>
                        </div>
                    </div>
                </div>
                <div id="e1content" style="padding-top:10px;display: none;">
                    <div class="safelabel">密码</div>
                    <input type="password" name="psw"  class="tc_input" id="change_phone_pwd" placeholder="请输入密码" />
                    <div class="safelabel">新手机号</div>
                    <input type="text" name="phone"  class="tc_input" id="change_phone_new_phone" placeholder="请确认新手机号" />
                    <div class="safelabel">验证码</div>
                    <div class="confirmcode">
                        <input type="password" name="code"  class="tc_input1" id="change_phone_code" placeholder="请输入验证码" />
                        <div class="sendcode" onclick="bindChangePhoneSendCode()">发送验证码</div>
                    </div>
                    <div class="safebttom">
                        <div class="confirmbutton" onclick="bindSubmitChangePhone()">确认</div>
                    </div>
                </div>
                <div id="e2content" style="padding-top:10px;display: none;">
                    <div id="bind-wechat-box" style="margin-left:30px;@if(\Illuminate\Support\Facades\Auth::user()->login_wx_openid) display:none; @endif" >
                        <div class="qrcode-img" id="qrcode" onclick="refresh()"></div>
                        <div class="tips">
                            请使用微信扫描二维码，进行绑定
                        </div>

                    </div>
                    <div id="already-bind-box" style="@if(!\Illuminate\Support\Facades\Auth::user()->login_wx_openid) display:none; @endif">
                        <div class="tips" style="margin-left:30px;margin-top:30px;">
                            已绑定微信号
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!--在我的方案点击另存方案的弹窗-->
    <div class="main">
        <div class="qrcode">
            <div class="mtitle">另存方案</div>
            <input type="text" name="fanganname"  class="fanganinput" placeholder="填写新方案名称" />
            <div class="mbotton">
                <div class="confirm">确认</div>
                <div class="cancel" onclick="cancel()">取消</div>
            </div>
        </div>
    </div>

    <!--绑定微信弹窗-->
    <div id="qrcode1" class="qrcode" style="display:none;">
        <div class="ftext">
            <span>绑定微信</span>
            <a class="close" onclick="closefriend1()">×</a>
        </div>
        <div class="wx-bd">
            <img src="/v1/images/site/sjfa_xq/erweima.png" class="qrimage"/>
        </div>
        <div class="ftext" style="margin-top:-10px;"><p>使用“扫一扫”,即将可以绑定微信。</p></div>
    </div>



@endsection

@section('script')

    {{--绑定微信--}}
    <script>
        var count = 37;
        var over = true;

        function showQrcode() {
            count = 36;
            over = false;
            setTimeout('check_wechat_bind()', 5000);
            setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
            $('#qrcode').empty();
            $('#qrcode').qrcode({width:150,height:150,text:"{{ url('bind',Auth::user()->remember_token) }}?type={{\App\Http\Services\common\WechatService::DESIGNER}}"});
        }

        function timeOut() {
            qrcode_disable();
            over = true;
        }

        function refresh() {
            count--;
            qrcode_disable();
            setTimeout('check_wechat_bind()', 5000);
            setTimeout('timeOut()', '{{\App\Http\Services\common\WechatService::TIME_OUT_VALUE}}');
            over = false;
        }

        function qrcode_disable(){
            $('#qrcode').toggleClass('disabled');
        }

        function check_wechat_bind(){
            if (over){
                return false;
            }
            ajax_post('{{url('check_wechat_bind')}}',{'t':'{{ Auth::user()->remember_token }}','openid':'{{ Auth::user()->login_wx_openid }}','type':'{{\App\Http\Services\common\WechatService::DESIGNER}}'},function (json) {
                if (json.status) {
                    layer.msg('绑定成功！');
                    $('#already-bind-box').show();
                    $('#bind-wechat-box').hide();
                }
                else{
                    if(count){
                        if(count<37){
                            count--;
                            setTimeout('check_wechat_bind()', 5000);
                        }
                        else{
                            count = 36;
                        }
                    }
                }
            })
        }

    </script>


@endsection