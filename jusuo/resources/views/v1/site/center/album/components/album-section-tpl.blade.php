<div class="section-info-row">
    <button type="button" class="layui-btn layui-btn-primary" style="position:absolute;right:20px;top:20px;" onclick="remove_section_block(this)" >
        <i class="layui-icon layui-icon-close"></i>
    </button>
    <div class="cst-form-row">
        <div class="cst-form-item pull-left" style="width:250px;margin-right:20px;">
            <div class="cst-form-label">
                <span class="required">*</span>
                <span class="label-text">空间类别</span>
            </div>
            <div class="cst-form-input">
                <select class="n-section-space-type" name="section_space_type[]" lay-verify="required">
                    @foreach($space_types as $key=> $item)
                        <option value="{{$item->id}}" @if(isset($section) && isset($section->space_type_id) && $section->space_type_id==$item->id)selected @endif >{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{--<div class="cst-form-item pull-left" style="width:250px;margin-right:20px;">
            <div class="cst-form-label">
                <span class="required">*</span>
                <span class="label-text">空间名称</span>
            </div>
            <div class="cst-form-input">
                <input class="layui-input n-section-title" lay-verify="required" type="text" name="section_title[]"  value="{{$section->title or ''}}" placeholder="空间名称" maxlength="20" >

            </div>
        </div>--}}
        <div class="cst-form-item pull-left" style="width:250px;margin-right:20px;">
            <div class="cst-form-label">
                @if($config['section_area_required'])<span class="required">*</span> @endif
                <span class="label-text">空间面积</span>
            </div>
            <div class="cst-form-input">
                <?php
                $count_area_verify = ['section_area_range'];
                if($config['section_area_required']){
                    array_push($count_area_verify,'required');
                }
                $count_area_verify = implode('|',$count_area_verify);
                ?>
                <input class="layui-input n-section-area" lay-verify="{{$count_area_verify}}" type="text"  value="{{$section->count_area or ''}}" placeholder="空间面积"  >
            </div>
        </div>

        <div style="clear:both"></div>

    </div>

    <div class="cst-form-row">
        <div class="cst-form-item" >
            <div class="cst-form-label">
                <span class="required">*</span>
                <span class="label-text">空间风格</span>
            </div>
            <div class="cst-form-input">
                @foreach($styles as $item)
                    <input type="checkbox" name="section_style_id[]" class="n-section-style-id" lay-verify="required"  lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($section) && in_array($item->id,$section->style_ids)) checked @endif>
                @endforeach
            </div>
        </div>
    </div>

    <div class="cst-form-title">空间设计</div>
    <div class="cst-form-row">
        <div class="cst-form-item">
            <div class="cst-form-label">
                @if($config['section_design_photo_min_limit']>0)<span class="required">*</span> @endif
                <span class="label-text">上传空间图</span>
                <span class="help-text">建议上传4:3比例图片，大小不超过2M</span>
            </div>
            <div class="cst-form-input">
                <div class="info-list">
                    @if(isset($section) && isset($section->content) && isset($section->content['design']))
                        @foreach ($section->content['design']['photos'] as $item)
                            @include('v1.site.center.album.components.section-design-photo-tpl',['data'=>$item])
                        @endforeach
                    @endif
                    @include('v1.site.center.album.components.section-design-photo-tpl',['data'=>null,'is_add_btn'=>true])

                </div>
            </div>
        </div>
        <div style="clear:both"></div>
        <div class="cst-form-item">
            <div class="cst-form-label">
                @if($config['each_space_description_required'])<span class="required">*</span> @endif
                <span class="label-text">设计说明</span>
            </div>
            <div class="cst-form-input">
                <textarea  class="layui-textarea n-section-design-description" name="each_space_description[]" placeholder="空间设计说明" @if($config['each_space_description_required'])lay-verify="required"@endif maxlength="{{$config['each_space_description_char_limit']}}" style="width: 100%; height: 130px;" autocomplete="off">{{$section->content['design']['description'] or ''}}</textarea>
            </div>
        </div>
    </div>


    <div class="cst-form-title">产品应用</div>
    <div class="cst-form-row">
        <div class="cst-form-item">
            <div class="cst-form-label">
                @if($config['section_product_photo_min_limit']>0)<span class="required">*</span> @endif
                <span class="label-text">上传产品应用图</span>
                <span class="help-text">建议上传4:3比例图片，大小不超过2M</span>
            </div>
            <div class="cst-form-input">
                <div class="info-list">
                    @if(isset($section) && isset($section->content) && isset($section->content['product']))
                        @foreach ($section->content['product']['photos'] as $item)
                            @include('v1.site.center.album.components.section-product-photo-tpl',['data'=>$item])
                        @endforeach
                    @endif
                    @include('v1.site.center.album.components.section-product-photo-tpl',['data'=>null,'is_add_btn'=>true])

                </div>
            </div>
        </div>
        <div style="clear:both"></div>
        <div class="cst-form-item">
            <div class="cst-form-label">
                @if($config['each_space_product_app_description_required'])<span class="required">*</span> @endif
                <span class="label-text">应用说明</span>
            </div>
            <div class="cst-form-input">
                <textarea class="layui-textarea n-section-product-description" name="each_space_build_description[]" placeholder="产品应用说明" @if($config['each_space_product_app_description_required'])lay-verify="required"@endif maxlength="{{$config['each_space_product_app_description_char_limit']}}" style="width: 100%; height: 130px;" autocomplete="off" >{{$section->content['product']['description'] or ''}}</textarea>
            </div>
        </div>
    </div>


    <div class="cst-form-title">施工</div>
    <div class="cst-form-row">
        <div class="cst-form-item">
            <div class="cst-form-label">
                @if($config['section_build_photo_min_limit']>0)<span class="required">*</span> @endif
                <span class="label-text">上传施工图</span>
                <span class="help-text">建议上传4:3比例图片，大小不超过2M</span>
            </div>
            <div class="cst-form-input">
                <div class="info-list">
                    @if(isset($section) && isset($section->content) && isset($section->content['build']))
                        @foreach ($section->content['build']['photos'] as $item)
                            @include('v1.site.center.album.components.section-build-photo-tpl',['data'=>$item])
                        @endforeach
                    @endif
                    @include('v1.site.center.album.components.section-build-photo-tpl',['data'=>null,'is_add_btn'=>true])
                </div>
            </div>
        </div>
        <div style="clear:both"></div>
        <div class="cst-form-item">
            <div class="cst-form-label">
                @if($config['each_space_build_description_required'])<span class="required">*</span> @endif
                <span class="label-text">施工说明</span>
            </div>
            <div class="cst-form-input">
                <textarea  class="layui-textarea n-section-build-description"  placeholder="施工说明" @if($config['each_space_build_description_required'])lay-verify="required"@endif maxlength="{{$config['each_space_build_description_char_limit']}}" style="width: 100%; height: 130px;" autocomplete="off">{{$section->content['build']['description'] or ''}}</textarea>
            </div>
        </div>
    </div>


    <div style="clear:both"></div>

</div>

