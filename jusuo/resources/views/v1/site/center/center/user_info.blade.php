@extends('v1.site.center.layout',[
    'css' => [
        'v1/static/iconfont/iconfont.css',
        'v1/css/site/myplan.css',
    ],
    'js'=>[
        'v1/static/swiper/swiper.min.js',
        'v1/js/ajax.js',
    ]
])

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/info_verify.css')}}">


    <div class="module-tab">
        @include('v1.site.center.components.user_info.global_tab',[
        'active'=>'user_info'
        ])

    </div>

    <div id="verify-form">

        <form class="layui-form" action="" lay-filter="component-form-group">

        </form>
    </div>


@endsection

@section('script')

    <script>
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        layui.element.init();

        //最后一定要进行form的render，不然控件用不了
        form.render();

        form.verify({

        });


        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            var form_field = form_info.field;
            ajax_post('{{url($url_prefix.'center/reset_password/reset')}}',
                form_field,
                function(result){
                    // console.log(result);
                    if(result.status){
                        layer.alert(result.msg,{closeBtn :0},function(){
                            window.location.reload();
                        });
                        // layer.msg(result.msg);

                    }else{
                        layer.msg(result.msg);
                    }
                });

            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

    </script>

@endsection