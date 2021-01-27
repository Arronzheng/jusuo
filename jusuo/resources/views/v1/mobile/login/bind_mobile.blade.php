@extends('v1.mobile.layout',[
   'css'=>[

   ],
   'js'=>[
   ]
])

@section('content')
<style>
    .form-content{padding-top:100px;}
    .pageWrapper{
        background-repeat: no-repeat;
        background-position: 0 0;
        -webkit-background-size:100% auto;
        background-size:100% auto;
    }
    .input-row{
        height:40px;;line-height:40px;;padding-left:42px;border-bottom:1px solid #ffffff;position:relative;
        margin:0 12px;
        background-repeat:no-repeat;
        background-size:.45rem .45rem;
        background-position: 0 .2rem;
    }
    .input-row.phone{
        background-image: url(../../assets/image/login/phone.png);
        margin-bottom:12px;
    }
    .input-row.password{
        background-image: url(../../assets/image/login/password.png);
    }
    .input-row input{
        font-size:14px;
        width:80%;
        background:transparent;position:absolute;top:0;left:42px;border:0;height:40px;;line-height:40px;;
        color:#555555;
    }
    .input-row.password input{
        width:40%;
    }
    ::-moz-placeholder { color: #ffffff; }
    ::-webkit-input-placeholder { color:#555555; }
    :-ms-input-placeholder { color:#555555; }
    .btn-code{
        height:25px;line-height:25px;
        position:absolute;right:0;border:1px solid #ffffff;color:#555555;
        -webkit-border-radius:12px;
        -moz-border-radius:12px;
        border-radius:12px;
        font-size:12px;text-align:center;
        padding:0 10px;top:10px;
    }
    .submit-btn{
        margin:45px 12px;background-color:#555555;height:40px;line-height:40px;
        -webkit-border-radius:25px;
        -moz-border-radius:25px;
        border-radius:25px;;
        color:#ffffff;text-align:center;
    }
    .submit-btn.disabled{color:#888888}
    #bind-title{text-align:center;color:#555555;font-size:20px;margin-bottom:25px;}
</style>

@verbatim
    <div id="page-app">
        <div class="pageWrapper" :style="{'min-height':window_height+'px'}" >

            <div class="form-content">
                <div id="bind-title">绑定手机</div>

                <div class="input-row phone">
                    <input placeholder="请输入手机号" v-model.trim="form.phone"/>
                </div>
                <div class="input-row password">
                    <input placeholder="请输入验证码" v-model.trim="form.code"/>
                    <div :class="{'btn-code':true}"  @click="getCode">{{btncode.text}}</div>

                </div>

            </div>
            <div @click="submit" :class="{'submit-btn':true,'disabled':submitBtnType}">
                提交绑定
            </div>
        </div>
    </div>
@endverbatim


@endsection

@section('script')

    <script>

        var app = new Vue({
            el: '#page-app',
            data () {
                return {
                    form:{
                        phone:'',
                        code:'',
                        wxUserInfo:'',
                        alipayUserInfo:''
                    },
                    btncode:{
                        text:'获取验证码',
                        disable:false,
                        isGetCode:false
                    },
                    count:60,
                    window_height:0
                }
            },
            computed:{
                submitBtnType(){
                    if(!this.form.phone || !this.form.code){
                        return true;
                    }
                    if(!this.isPhone(this.form.phone)){
                        return true;
                    }
                    if(!this.btncode.isGetCode){
                        return true;
                    }
                    return false;
                }
            },
            methods: {
                submit(){
                    if(this.submitBtnType){
                        return false;
                    }
                    var self = this;
                    if(this.form.phone&&this.form.code){

                        ajax_post('/mobile/login/api/submit_bind_mobile',this.form, function (res) {
                            if (res.status == 1) {
                                var login_redirect = res.data.url;
                                if(login_redirect){
                                    location.href=login_redirect;
                                }else{
                                    layer.msg('无法跳转，系统错误');
                                }
                            } else {
                                layer.msg(res.msg);
                            }
                        });

                    }else{

                        layer.msg('手机号码和验证码不能为空')

                    }
                },
                getCode(){
                    if(this.btncode.disable==true){
                        return true;
                    }
                    if(this.form.phone){
                        //判断手机格式
                        if(!this.isPhone(this.form.phone)){

                            layer.msg('手机号码格式不正确')
                            return false;
                        }


                        var self = this;
                        ajax_post('/mobile/login/api/get_sms',{
                            mobile_phone:self.form.phone
                        }, function (res) {
                            if (res.status == 1) {
                                self.btncode.isGetCode = true;
                                var timestamp=new Date().getTime();
                                sessionStorage.setItem('getCodeTime',timestamp);

                                layer.msg('验证码已发送')

                                self.countdown();
                            } else {
                                layer.msg(res.msg);
                            }
                        });


                    }else{
                        layer.msg('请填写手机号码')

                    }

                },
                isPhone(inputString){
                    var partten = /^1[3,5,8,7]\d{9}$/;
                    var fl=false;
                    if(partten.test(inputString))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                },
                countdown(){
                    this.btncode.disable=true;
                    this.btncode.text=this.count+'s';
                    var smsInterval=setInterval(()=>{
                                if(this.count>0){
                        this.btncode.text=this.count--+'s';
                    }else{
                        this.btncode.disable=false;
                        this.btncode.text='获取验证码';
                        this.count=60;
                        clearTimeout(smsInterval);
                    }

                },1000);
                }
            },
            mounted(){

            },
            created () {
                /*var timestamp=new Date().getTime();
                 sessionStorage.setItem('getCodeTime',timestamp);*/
                this.window_height = window.screen.availHeight;

                //如果有上一次获取验证码的时间
                var lastGetCodeTime = sessionStorage.getItem('getCodeTime');
                var nowTimestamp=new Date().getTime();

                if(lastGetCodeTime){
                    var diff_seconds=Math.round((nowTimestamp-lastGetCodeTime)/1000);
                    if(diff_seconds<60){
                        //距离上一次获取时间在60内，继续计时
                        this.count = parseInt(60-diff_seconds);
                        this.countdown();
                    }else{
                        sessionStorage.removeItem('getCodeTime');
                    }
                }


            }
        })

    </script>



@endsection