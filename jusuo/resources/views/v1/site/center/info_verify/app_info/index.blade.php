@extends('v1.site.center.layout')

@section('main-content')
    <link rel="stylesheet" href="{{asset('v1/css/site/center/info_verify.css')}}">
    <style>
        .pass-tips{
            margin-bottom:20px;color:#3e82f7;margin-left:20px;
        }
        .info-block{
            overflow:initial;
        }
        .required{color:red;}
    </style>

    <div class="detailview show">
        @include('v1.site.center.components.info_verify.global_tab',[
      'active'=>'app'
      ])

        <div id="verify-form">
            <form class="layui-form" action="" lay-filter="component-form-group">
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['avatar_required'])<span class="required">*</span> @endif头像</label>
                    <div class="layui-input-inline">
                        <div class="layui-upload-drag" id="b-upload-avatar">
                            <i class="layui-icon"></i>
                            <p>仅支持JPG/PNG格式，建议360*360像素，比例为1:1，大小限制200K以内</p>
                            <input type="hidden" name="url_avatar" @if($config['avatar_required'])lay-verify="avatar_photo"@endif value="{{$designerDetail->url_avatar or ''}}"/>
                            <div class="upload-img-preview" style="background-image:url('{{$designerDetail->url_avatar or ''}}')"></div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['nickname_required'])<span class="required">*</span> @endif昵称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="nickname" value="{{$designerDetail->nickname or ''}}"  @if($config['nickname_required'])lay-verify="required"@endif maxlength="30" autocomplete="off" placeholder="" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['gender_required'])<span class="required">*</span> @endif性别</label>
                    <div class="layui-input-inline">
                        <select name="gender" @if($config['gender_required'])lay-verify="required"@endif>
                            <option value="{{\App\Models\DesignerDetail::GENDER_MALE}}" @if($designerDetail->gender==\App\Models\DesignerDetail::GENDER_MALE) selected @endif>男</option>
                            <option value="{{\App\Models\DesignerDetail::GENDER_FEMALE}}" @if($designerDetail->gender==\App\Models\DesignerDetail::GENDER_FEMALE) selected @endif>女</option>

                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['self_birth_time_required'])<span class="required">*</span> @endif出生年月日</label>
                    <div class="layui-input-inline">
                        <input type="text" name="self_birth_time" id="self_birth_time" @if($config['self_birth_time_required'])lay-verify="required"@endif readonly placeholder="请选择出生年月日" value="{{date('Y-m-d',strtotime($designerDetail->self_birth_time))}}" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">工作经验（年）</label>
                    <div class="layui-input-inline">
                        <input type="number" name="self_working_year" value="{{$designerDetail->self_working_year or ''}}"  maxlength="2" autocomplete="off" placeholder="" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['area_belong_required'])<span class="required">*</span> @endif所在城市</label>
                    <div class="layui-input-block">
                        <div class="layui-input-inline">
                            <select id="company_province_id" name="area_belong_province" @if($config['area_belong_required'])lay-verify="required"@endif lay-filter="areaBelongProvinceId">
                                <option value="">请选择省</option>
                                @foreach($provinces as $item)
                                    <option value="{{$item->id}}" @if($designerDetail->area_belong_province && $item->id==$designerDetail->area_belong_province) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select id="company_city_id" name="area_belong_city" @if($config['area_belong_required'])lay-verify="required"@endif lay-filter="areaBelongCityId">
                                <option value="">请选择城市</option>
                                @if(isset($cities))
                                    @foreach($cities as $item)
                                        <option value="{{$item->id}}" @if($designerDetail->area_belong_city && $item->id==$designerDetail->area_belong_city) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="area_belong_district" @if($config['area_belong_required'])lay-verify="required"@endif id="company_district_id" lay-filter="companyDistrictId">
                                <option value="">请选择区/县</option>
                                @if(isset($districts))
                                    @foreach($districts as $item)
                                        <option value="{{$item->id}}" @if($designerDetail->area_belong_district && $item->id==$designerDetail->area_belong_district) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['self_working_address_required'])<span class="required">*</span> @endif工作地址</label>
                    <div class="layui-input-inline">
                        <input type="text" name="self_working_address" value="{{$designerDetail->self_working_address or ''}}"  @if($config['self_working_address_required'])lay-verify="required"@endif maxlength="20" autocomplete="off" placeholder="" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['self_education_required'])<span class="required">*</span> @endif教育信息</label>
                    <div class="layui-input-block" style="" >
                        <div style="margin-bottom:10px;">
                            <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="edu-info-block-tpl" data-add-type="self_education"> + 添加教育信息</button>
                        </div>
                        <div class="info-list">
                            @foreach($designerDetail->self_education_data as $edu)
                                <div class="info-block" style="margin-bottom:15px;overflow:initial">
                                    <div class="edu-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input n-school" value="{{$edu['school'] or ''}}" placeholder="就读学校" maxlength="20" >
                                        </div>
                                        <div class="layui-input-inline">
                                            <select class="n-education"  >
                                                <option value="小学" @if(isset($edu['education']) && $edu['education']=='小学') selected @endif>小学</option>
                                                <option value="初中" @if(isset($edu['education']) && $edu['education']=='初中') selected @endif>初中</option>
                                                <option value="高中" @if(isset($edu['education']) && $edu['education']=='高中') selected @endif>高中</option>
                                                <option value="本科" @if(isset($edu['education']) && $edu['education']=='本科') selected @endif>本科</option>
                                                <option value="大专" @if(isset($edu['education']) && $edu['education']=='大专"') selected @endif>大专</option>
                                                <option value="研究生" @if(isset($edu['education']) && $edu['education']=='研究生') selected @endif>研究生</option>
                                            </select>
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="edu-info-row">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input n-profession" value="{{$edu['profession'] or ''}}" placeholder="专业" maxlength="20" >
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <?php $now_year = intval(date('Y',time()));?>
                                            <select class="n-graduate_year"  >
                                                @for($i=1950;$i<=$now_year;$i++)
                                                    <option value="{{$i}}" @if(isset($edu['graduate_year']) && $edu['graduate_year']==$i)selected @endif>{{$i}}年</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <select class="n-graduate_month"  >
                                                <?php $now_month = intval(date('n',time()));?>
                                                @for($i=1;$i<=12;$i++)
                                                    <option value="{{$i}}" @if(isset($edu['graduate_month']) && $edu['graduate_month']==$i)selected @endif>{{$i}}月</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <button type="button" class="layui-btn layui-btn-primary" onclick="remove_custom_info_block(this)" >
                                            <i class="layui-icon layui-icon-close"></i>
                                        </button>
                                        <div style="clear:both"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['self_work_required'])<span class="required">*</span> @endif工作信息</label>
                    <div class="layui-input-block" style="" >
                        <div style="margin-bottom:10px;">
                            <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="work-info-block-tpl" data-add-type="self_work"> + 添加工作信息</button>
                        </div>
                        <div class="info-list">
                            @foreach($designerDetail->self_work_data as $work)
                                <div class="info-block" style="margin-bottom:15px;overflow:initial">
                                    <div class="work-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input n-company" value="{{$work['company'] or ''}}" placeholder="公司名称" maxlength="20" >
                                        </div>
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input n-position" value="{{$work['position'] or ''}}" placeholder="担任职位" maxlength="20" >
                                        </div>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="work-info-row">
                                        <div class="layui-input-inline" style="width:100px">
                                            <select class="n-start_year"  >
                                                @for($i=1950;$i<=$now_year;$i++)
                                                    <option value="{{$i}}" @if(isset($work['start_year']) && $i==$work['start_year'])selected @endif>{{$i}}年</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <select class="n-start_month"  >
                                                @for($i=1;$i<=12;$i++)
                                                    <option value="{{$i}}" @if(isset($work['start_month']) && $i==$work['start_month'])selected @endif>{{$i}}月</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="text-align:center;width:50px">
                                            <div class="layui-form-mid layui-word-aux" style="text-align:center;width:50px">
                                                至
                                            </div>
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <select class="n-end_year"  >
                                                @for($i=1950;$i<=$now_year;$i++)
                                                    <option value="{{$i}}" @if(isset($work['end_year']) && $i==$work['end_year'])selected @endif>{{$i}}年</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <select class="n-end_month"  >
                                                @for($i=1;$i<=12;$i++)
                                                    <option value="{{$i}}" @if(isset($work['end_month']) && $i==$work['end_month'])selected @endif>{{$i}}月</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <button type="button" class="layui-btn layui-btn-primary" onclick="remove_custom_info_block(this)" >
                                            <i class="layui-icon layui-icon-close"></i>
                                        </button>
                                        <div style="clear:both"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">@if($config['self_award_required'])<span class="required">*</span> @endif证书与奖项</label>
                    <div class="layui-input-block" style="" >
                        <div style="margin-bottom:10px;">
                            <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="award-info-block-tpl" data-add-type="self_award"> + 添加证书与奖项</button>
                        </div>
                        <div class="info-list">
                            @foreach($designerDetail->self_award_data as $award)
                                <div class="info-block" style="margin-bottom:15px;overflow:initial">
                                    <div class="award-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input n-award_name" value="{{$award['award_name'] or ''}}" placeholder="证书名称" maxlength="200" >
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <?php $now_year = intval(date('Y',time()));?>
                                            <select class="n-award_year"  >
                                                @for($i=1950;$i<=$now_year;$i++)
                                                    <option value="{{$i}}" @if(isset($award['award_year']) && $i==$award['award_year'])selected @endif>{{$i}}年</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="width:100px">
                                            <select class="n-award_month"  >
                                                <?php $now_month = intval(date('n',time()));?>
                                                @for($i=1;$i<=12;$i++)
                                                    <option value="{{$i}}" @if(isset($award['award_month']) && $i==$award['award_month'])selected @endif>{{$i}}月</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <button type="button" class="layui-btn layui-btn-primary" onclick="remove_custom_info_block(this)" >
                                            <i class="layui-icon layui-icon-close"></i>
                                        </button>
                                        <div style="clear:both"></div>
                                    </div>
                                    <div class="award-info-row" style="margin-bottom:10px;">
                                        <div class="layui-input-inline">
                                            <div class="layui-upload-drag" data-is-init="0" data-name-class="n-award_photo" data-upload-url="{!! url('center/app_info/upload_photo') !!}">
                                                <i class="layui-icon"></i>
                                                <p>仅支持JPG/PNG格式，每张图片大小限制200K以内</p>
                                                <input type="hidden" class="n-award_photo"  value="{{$award['award_photo'] or ''}}"/>
                                                <div class="upload-img-preview" style="background-image:url('{{$award['award_photo'] or ''}}');"></div>
                                            </div>
                                        </div>

                                        <div style="clear:both"></div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">自我介绍</label>
                    <div class="layui-input-inline">
                        <textarea name="self_introduction" @if($config['self_introduction_required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$designerDetail->self_introduction or ''}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">业务联系手机</label>
                    <div class="layui-input-inline">
                        <input type="text" name="self_working_telephone" value="{{$designerDetail->self_working_telephone or ''}}"  @if($config['contact_telephone_required'])lay-verify="required"@endif maxlength="20" autocomplete="off" placeholder="" class="layui-input">
                    </div>
                </div>

                <hr style="margin:50px 0;"/>

                <div class="layui-form-item">
                    <label class="layui-form-label">&nbsp;</label>
                    <div class="layui-input-inline">
                        <button type="button" class="layui-btn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>

                {{csrf_field()}}
                <input type="hidden" name="id" value="1"/>

            </form>

        </div>
    </div>




    <script type="text/html" id="edu-info-block-tpl">
        <div class="info-block" style="margin-bottom:15px;">
            <div class="edu-info-row" style="margin-bottom:10px;">
                <div class="layui-input-inline">
                    <input type="text" class="layui-input n-school" value="" placeholder="就读学校" maxlength="20" >
                </div>
                <div class="layui-input-inline">
                    <select class="n-education"  >
                        <option value="小学">小学</option>
                        <option value="初中">初中</option>
                        <option value="高中">高中</option>
                        <option value="本科">本科</option>
                        <option value="大专">大专</option>
                        <option value="研究生">研究生</option>
                    </select>
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="edu-info-row">
                <div class="layui-input-inline">
                    <input type="text" class="layui-input n-profession" value="" placeholder="专业" maxlength="20" >
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <?php $now_year = intval(date('Y',time()));?>
                    <select class="n-graduate_year"  >
                        @for($i=1950;$i<=$now_year;$i++)
                        <option value="{{$i}}" @if($i==$now_year)selected @endif>{{$i}}年</option>
                        @endfor
                    </select>
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <select class="n-graduate_month"  >
                        <?php $now_month = intval(date('n',time()));?>
                        @for($i=1;$i<=12;$i++)
                        <option value="{{$i}}" @if($i==$now_month)selected @endif>{{$i}}月</option>
                        @endfor
                    </select>
                </div>
                <button type="button" class="layui-btn layui-btn-primary" onclick="remove_custom_info_block(this)" >
                    <i class="layui-icon layui-icon-close"></i>
                </button>
                <div style="clear:both"></div>
            </div>
        </div>
    </script>

    <script type="text/html" id="work-info-block-tpl">
        <div class="info-block" style="margin-bottom:15px;">
            <div class="work-info-row" style="margin-bottom:10px;">
                <div class="layui-input-inline">
                    <input type="text" class="layui-input n-company" value="" placeholder="公司名称" maxlength="20" >
                </div>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input n-position" value="" placeholder="担任职位" maxlength="20" >
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="work-info-row">
                <div class="layui-input-inline" style="width:100px">
                    <select class="n-start_year"  >
                        @for($i=1950;$i<=$now_year;$i++)
                            <option value="{{$i}}" @if($i==$now_year)selected @endif>{{$i}}年</option>
                        @endfor
                    </select>
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <select class="n-start_month"  >
                        @for($i=1;$i<=12;$i++)
                            <option value="{{$i}}" @if($i==$now_month)selected @endif>{{$i}}月</option>
                        @endfor
                    </select>
                </div>
                <div class="layui-input-inline" style="text-align:center;width:50px">
                    <div class="layui-form-mid layui-word-aux" style="text-align:center;width:50px">
                        至
                    </div>
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <select class="n-end_year"  >
                        @for($i=1950;$i<=$now_year;$i++)
                            <option value="{{$i}}" @if($i==$now_year)selected @endif>{{$i}}年</option>
                        @endfor
                    </select>
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <select class="n-end_month"  >
                        @for($i=1;$i<=12;$i++)
                            <option value="{{$i}}" @if($i==$now_month)selected @endif>{{$i}}月</option>
                        @endfor
                    </select>
                </div>
                <button type="button" class="layui-btn layui-btn-primary" onclick="remove_custom_info_block(this)" >
                    <i class="layui-icon layui-icon-close"></i>
                </button>
                <div style="clear:both"></div>
            </div>
        </div>
    </script>


    <script type="text/html" id="award-info-block-tpl">
        <div class="info-block" style="margin-bottom:15px;">
            <div class="award-info-row" style="margin-bottom:10px;">
                <div class="layui-input-inline">
                    <input type="text" class="layui-input n-award_name" value="" placeholder="证书名称" maxlength="20" >
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <?php $now_year = intval(date('Y',time()));?>
                    <select class="n-award_year"  >
                        @for($i=1950;$i<=$now_year;$i++)
                            <option value="{{$i}}" @if($i==$now_year)selected @endif>{{$i}}年</option>
                        @endfor
                    </select>
                </div>
                <div class="layui-input-inline" style="width:100px">
                    <select class="n-award_month"  >
                        <?php $now_month = intval(date('n',time()));?>
                        @for($i=1;$i<=12;$i++)
                            <option value="{{$i}}" @if($i==$now_month)selected @endif>{{$i}}月</option>
                        @endfor
                    </select>
                </div>
                <button type="button" class="layui-btn layui-btn-primary" onclick="remove_custom_info_block(this)" >
                    <i class="layui-icon layui-icon-close"></i>
                </button>
                <div style="clear:both"></div>
            </div>
            <div class="award-info-row" style="margin-bottom:10px;">
                <div class="layui-input-inline">
                    <div class="layui-upload-drag" data-is-init="0" data-name-class="n-award_photo" data-upload-url="{!! url('center/app_info/upload_photo') !!}">
                        <i class="layui-icon"></i>
                        <p>仅支持JPG/PNG格式，每张图片大小限制200K以内</p>
                        <input type="hidden" class="n-award_photo"  value=""/>
                        <div class="upload-img-preview"></div>
                    </div>
                </div>
            </div>

        </div>
    </script>


@endsection

@section('script')
    <script>
        //layui后台模板依赖element模块，如果以非模块化方式加载js，则需要对依赖模块进行init。
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;

        var laydate = layui.laydate;

        laydate.render({
            elem: '#self_birth_time',
            value:''
        });

        layui.element.init();

        var avatar_size = 2*1024;
        //LOGO图片上传
        var uploadAvatar = upload.render({
            elem: '#b-upload-avatar'
            ,url: '{{url($url_prefix.'center/app_info/upload_avatar')}}'
            ,data: {'_token':'{{csrf_token()}}'}
            ,size:avatar_size  //KB
            ,acceptMime: 'image/jpeg,image/jpg,image/png'
            ,before: function(obj){layer.load(1);}
            ,done: function(res){
                layer.closeAll('loading');
                //如果上传失败
                if(!res.status){
                    layer.msg(res.msg);
                    console.log(res);
                }
                //上传成功
                $('#b-upload-avatar .upload-img-preview').css('background-image','url('+res.data.access_path+')');
                $('#b-upload-avatar input').val(res.data.access_path);
            }
        });



        //最后一定要进行form的render，不然控件用不了
        form.render();

        //表单验证
        form.verify({
            avatar_photo: function(value){ //value：表单的值、item：表单的DOM对象
                if(value == ''){
                    return '请上传头像';
                }
            },
        });

        var education_required = "{{$config['self_education_required']}}";
        var work_required = "{{$config['self_work_required']}}";
        var award_required = "{{$config['self_award_required']}}";
        //用form监听submit，可以用到validate的功能
        form.on('submit(submitFormBtn)', function(form_info){
            layer.load(1);
            var form_field = form_info.field;
            //处理教育信息字段
            var education_column = handle_education_column();
            if(education_required && education_column.school.length<=0){
                layer.alert('请至少添加一个教育信息');
                layer.closeAll('loading')
                return false;
            }
            Object.assign(form_field,education_column)
            //处理工作信息字段
            var work_column = handle_work_column();
            if(work_required && work_column.work_company.length<=0){
                layer.alert('请至少添加一个工作信息');
                layer.closeAll('loading')
                return false;
            }
            Object.assign(form_field,work_column)
            //处理证书与奖项信息字段
            var award_column = handle_award_column();
            if(award_required && award_column.award_name.length<=0){
                layer.alert('请至少添加一个证书与奖项信息');
                layer.closeAll('loading')
                return false;
            }
            Object.assign(form_field,award_column)
            ajax_post('{{url($url_prefix.'center/submit_app_info')}}',
                form_field,
                function(result){
                    if(result.status){
                        layer.alert(result.msg,{closeBtn :0},function(){
                            window.location.reload();
                        });
                    }else{
                        layer.closeAll('loading');
                        layer.msg(result.msg);
                    }
                });
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });

        //处理教育信息字段
        function handle_education_column(){
            var columns =[];
            var school = [];
            var education = [];
            var profession = [];
            var graduate_year = [];
            var graduate_month = [];
            //就读学校
            $('.n-school').each(function(){
                school.push($(this).val());
            });
            columns.school = school;
            //专业
            $('.n-education').each(function(){
                education.push($(this).val());
            });
            columns.education = education;
            //专业
            $('.n-profession').each(function(){
                profession.push($(this).val());
            });
            columns.profession = profession;
            //毕业年份
            $('.n-graduate_year').each(function(){
                graduate_year.push($(this).val());
            });
            columns.graduate_year = graduate_year;
            //毕业年份
            $('.n-graduate_month').each(function(){
                graduate_month.push($(this).val());
            });
            columns.graduate_month = graduate_month;
            return columns;
        }

        //处理工作信息字段
        function handle_work_column(){
            var columns =[];
            var company = [];
            var position = [];
            var start_year = [];
            var start_month = [];
            var end_year = [];
            var end_month = [];
            //公司名称
            $('.work-info-row .n-company').each(function(){
                company.push($(this).val());
            });
            columns.work_company = company;
            //担任职位
            $('.work-info-row .n-position').each(function(){
                position.push($(this).val());
            });
            columns.work_position = position;
            //开始年份
            $('.work-info-row .n-start_year').each(function(){
                start_year.push($(this).val());
            });
            columns.work_start_year = start_year;
            //开始月份
            $('.work-info-row .n-start_month').each(function(){
                start_month.push($(this).val());
            });
            columns.work_start_month = start_month;
            //结束年份
            $('.work-info-row .n-end_year').each(function(){
                end_year.push($(this).val());
            });
            columns.work_end_year = end_year;
            //结束月份
            $('.work-info-row .n-end_month').each(function(){
                end_month.push($(this).val());
            });
            columns.work_end_month = end_month;
            return columns;
        }

        //处理证书与奖项字段
        function handle_award_column(){
            var columns =[];
            var award_name =[];
            var award_year = [];
            var award_month = [];
            var award_photo = [];
            //证书名称
            $('.award-info-row .n-award_name').each(function(){
                award_name.push($(this).val());
            });
            columns.award_name = award_name;
            //证书年份
            $('.award-info-row .n-award_year').each(function(){
                award_year.push($(this).val());
            });
            columns.award_year = award_year;
            //证书月份
            $('.award-info-row .n-award_month').each(function(){
                award_month.push($(this).val());
            });
            columns.award_month = award_month;
            //证书照片
            $('.award-info-row .n-award_photo').each(function(){
                award_photo.push($(this).val());
            });
            columns.award_photo = award_photo;
            return columns;
        }


        $(document).ready(function(){
            //初始化上传按钮
            init_upload_block();

            //监听省份变化
            form.on('select(areaBelongProvinceId)', function(data){
                var province_id = data.value;
                get_area(province_id,'company_city_id','城市');
            });

            //监听城市变化
            form.on('select(areaBelongCityId)', function(data){
                var city_id = data.value;
                get_area(city_id,'company_district_id','区/县');
            });
        });

        //获取地区
        function get_area(area_id,next_elem,next_text){
            var options='<option value="">请选择'+next_text+'</option>';
            ajax_get('{{url('/common/get_area_children?pi=')}}'+area_id,function(res){
                if(res.status && res.data.length>0){
                    $.each(res.data,function(k,v){
                        options+='<option value="'+ v.id+'">'+ v.name+'</option>';
                    });
                    $('#'+next_elem).html(options);
                    form.render('select');
                }
            });
        }
    </script>

    {{--教育信息等逻辑--}}
    <script>
        //添加自定义信息块
        function add_custom_info_block(obj){
            var form_item = $(obj).parents('.layui-form-item');
            var info_list = form_item.find('.info-list');
            var type = $(obj).attr('data-add-type');
            var tpl_name = $(obj).attr('data-tpl');
            var block_tpl = $('#'+tpl_name).html();
            info_list.append(block_tpl);
            //最后一定要进行form的render，不然控件用不了
            form.render();
            init_upload_block();

        }

        //移除自定义信息块
        function remove_custom_info_block(obj){
            var info_block = $(obj).parents('.info-block');
            info_block.remove();
        }

        //自定义信息块初始化上传图片
        function init_upload_block(){
            layui.each($(".award-info-row .layui-upload-drag"),function(index, elem){
                var is_init = $(elem).attr('data-is-init');
                var name_class = $(elem).attr('data-name-class');
                var upload_url = $(elem).attr('data-upload-url');
                var upload_size = 200;
                if(is_init==0){
                    upload.render({
                        elem: elem
                        ,url: upload_url
                        ,data: {'_token':'{{csrf_token()}}'}
                        ,size:upload_size  //KB
                        ,acceptMime: 'image/jpeg,image/jpg,image/png'
                        ,before: function(obj){
                            layer.load(1);
                        }
                        ,done: function(res){
                            layer.closeAll('loading');
                            //如果上传失败
                            if(!res.status){
                                // console.log(res);
                                return layer.msg(res.msg);
                            }
                            //上传成功
                            $(elem).find('.upload-img-preview').css('background-image','url('+res.data.access_path+')');
                            $(elem).find('.'+name_class).val(res.data.access_path);
                        }
                    });
                    $(elem).attr('data-is-init','1');
                }


            });


        }

    </script>
@endsection
