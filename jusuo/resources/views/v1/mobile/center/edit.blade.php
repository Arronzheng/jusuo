@extends('v1.mobile.layout',[
   'css'=>[
       //'/v1/css/mobile/center.css',
       '/v1/css/mobile/edit.css'
   ],
   'js'=>[
   ]
])

@section('content')
    <style>
        .web-img-upload-input{}
        #edit-row-tpl{position:relative;}
        .input-block {padding: 20px;}
        .input-block input{
            display: block;
            width: 230px;
            height: 36px;
            margin: 0 auto;
            line-height: 30px;
            padding-left: 10px;
            border: 1px solid #e6e6e6;
            color: #333;
        }
        .submit-btn{height:35px;line-height:35px;;text-align:center;background-color:#1582FF;color:#ffffff;}
    </style>
    @verbatim

    <div class="container" id="page-app">
        <div class="sub-container">
            <div class="menu menu-avatar">
                头像<span class="right iconfont icon-arrowdropdown"></span><div class="menu-avatar-image" :style="{'background-image':'url('+form.url_avatar+')'}"></div>
                <input type="file" name="" accept="image/jpeg,image/jpg,image/png" class="web-img-upload-input" @change="uploadIMG">

            </div>
            <div class="menu" @click="modify_nickname()">昵称<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{form.nickname}}</span></div>
            <div class="menu" @click="modify_year()">工作经验<span class="right iconfont icon-arrowdropdown"></span><span class="right">{{form.self_working_year}}</span></div>
        </div>

        <div id="edit-row-tpl" style="display:none;">
            <div class="layui-layer-title" style="cursor: move;">编辑信息</div>
            <span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>
            <div id="" class="input-block">
                <input type="text" class="edit-input" />
            </div>
            <div class="layui-layer-btn layui-layer-btn-">
                <div class="submit-btn" @click="submit_edit()">确定</a>
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
                    edit_type:'nickname',
                    edit_value:'',
                    form:{
                        nickname:'',
                        url_avatar:'',
                        self_working_year:0
                    },
                    max_select_num:1,
                    picavalue: "",
                    imgUrl: null,
                    isEnlargeImage: false
                }
            },
            computed:{

            },
            mounted(){
                var self = this;
                $(document).on('click','.submit-btn',function(){
                    self.submit_edit();
                });
            },
            created () {
                this.get_edit_info();
            },
            methods: {
                modify_nickname(){
                    this.edit_type = 'nickname';
                    layer.open({
                        type: 1,
                        title: false,
                        closeBtn: 0,
                        shadeClose: true,
                        content: $('#edit-row-tpl').html()
                    });
                    $('.edit-input').val(this.form.nickname)
                },
                modify_year(){
                    this.edit_type = 'self_working_year';
                    layer.open({
                        type: 1,
                        title: false,
                        closeBtn: 0,
                        shadeClose: true,
                        content: $('#edit-row-tpl').html()
                    });
                    $('.edit-input').val(this.form.self_working_year)
                },
                submit_edit(){
                    var self = this;
                    layer.load(1);
                    var request_data = {};
                    request_data.nickname = this.form.nickname;
                    request_data.self_working_year = this.form.self_working_year;
                    if(this.edit_type=='nickname'){
                        request_data.nickname = $('.layui-layer-content .edit-input').val();
                    }
                    if(this.edit_type=='self_working_year'){
                        var edit_value = $('.layui-layer-content .edit-input').val();
                        if(isNaN(edit_value)){
                            layer.closeAll('loading');
                            layer.msg('请输入正确的数字！');
                            return false;
                        }
                        request_data.self_working_year = edit_value;
                    }
                    ajax_post("/mobile/center/api/submit_edit",request_data, function (res) {
                        layer.closeAll();

                        if (res.status == 1) {
                            layer.msg('修改成功！');
                            self.get_edit_info();
                        } else {
                            layer.msg(res.msg);
                        }
                    });

                },
                get_edit_info(){
                    var self = this;
                    ajax_get('/mobile/center/api/get_edit_info', function (res) {
                        if (res.status) {

                            var data = res.data;

                            self.$set(self,'form',data);

                        }else{
                            if(res.code == 2001){
                                m_go_login()
                            }else{
                                layer.msg(res.msg)
                            }
                        }


                    }, function () {})
                },
                uploadIMG(e) {
                    let files = e.target.files || e.dataTransfer.files;
                    if (!files.length) return;
                    if(files.length>this.max_select_num){
                        layer.msg('最多只能选择'+this.max_select_num+"张图片")
                        return;
                    }
                    for(var i = 0;i<files.length;i++){
                        this.picavalue = files[i];
                        if (this.picavalue.size / 1024 > 5000) {
                            layer.msg('图片过大不支持上传')
                        } else {
                            this.imgPreview(this.picavalue);
                        }
                    }

                },
                //获取图片
                imgPreview(file, callback) {
                    let self = this;
                    //判断支不支持FileReader
                    if (!file || !window.FileReader) return;
                    if (/^image/.test(file.type)) {
                        //创建一个reader
                        let reader = new FileReader();

                        //将图片转成base64格式
                        reader.readAsDataURL(file);
                        //读取成功后的回调
                        reader.onloadend = function() {
                            let result = this.result;
                            let img = new Image();
                            img.src = result;
                            console.log("********未压缩前的图片大小********");
                            console.log(result.length);
                            img.onload = function() {
                                let data = null;
                                if(result.length>1024*1024){  //>1M
                                    data = self.compress(img,0.1);
                                }else if(result.length>800*1024){ //>800K
                                    data = self.compress(img,0.2);
                                }else if(result.length>600*1024){ //>600K
                                    data = self.compress(img,0.3);
                                }else if(result.length>400*1024){ //>400K
                                    data = self.compress(img,0.4);
                                }else if(result.length>200*1024){ //>200K
                                    data = self.compress(img,0.6);
                                }else{
                                    data = self.compress(img,0.8);
                                }

                                self.imgUrl = result;

                                let blob = self.dataURItoBlob(data);

                                /*console.log("*******base64转blob对象******");
                                 console.log(blob);*/

                                var formData = new FormData();
                                formData.append("file", blob);

                                /*console.log("********将blob对象转成formData对象********");
                                 console.log(formData.get("file"));*/
                                layer.load(1)
                                ajax_upload("/mobile/center/api/upload_avatar",formData, function (res) {
                                    layer.closeAll('loading');

                                    if (res.status == 1) {
                                        self.$set(self.form,'url_avatar',res.data.access_path);
                                        layer.msg('修改头像成功！');
                                    } else {
                                        layer.msg(res.msg);
                                    }
                                });


                            };
                        };
                    }
                },
                // 压缩图片
                compress(img,ratio) {
                    let canvas = document.createElement("canvas");
                    let ctx = canvas.getContext("2d");
                    let initSize = img.src.length;
                    let width = img.width;
                    let height = img.height;
                    canvas.width = width;
                    canvas.height = height;
                    // 铺底色
                    ctx.fillStyle = "#fff";
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0, width, height);

                    //进行最小压缩
                    let ndata = canvas.toDataURL("image/jpeg", ratio);
                    console.log("*******压缩后的图片大小*******");
                    //console.log(ndata)
                    console.log(ndata.length);
                    return ndata;
                },
                // base64转成bolb对象
                dataURItoBlob(base64Data) {
                    var byteString;
                    if (base64Data.split(",")[0].indexOf("base64") >= 0)
                        byteString = atob(base64Data.split(",")[1]);
                    else byteString = unescape(base64Data.split(",")[1]);
                    var mimeString = base64Data
                            .split(",")[0]
                            .split(":")[1]
                            .split(";")[0];
                    var ia = new Uint8Array(byteString.length);
                    for (var i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    return new Blob([ia], { type: mimeString });
                },
                //删除事件
                delImg() {
                    this.imgUrl = null;
                }
            },

        })

    </script>


@endsection