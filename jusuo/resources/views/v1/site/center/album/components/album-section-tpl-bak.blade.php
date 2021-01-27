<div class="info-block" style="margin-bottom:15px;border:1px solid #dedede;padding:10px;position:relative;">
    <button type="button" class="layui-btn layui-btn-primary" style="position:absolute;right:10px;top:10px;" onclick="remove_custom_info_block(this)" >
        <i class="layui-icon layui-icon-close"></i>
    </button>
    <div class="section-info-row">
        <div class="layui-form-item">
            <label class="layui-form-label">空间类别</label>
            <div class="layui-input-inline">
                <select class="n-section-space-type" name="section_space_type[]" lay-verify="required">
                    @foreach($space_types as $key=> $item)
                        <option value="{{$item->id}}" @if(isset($section) && isset($section->space_type_id) && $section->space_type_id==$item->id)selected @endif >{{$item->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">空间名称</label>
            <div class="layui-input-inline">
                <input class="layui-input n-section-title" type="text" name="section_title[]"  value="{{$section->title or ''}}" placeholder="空间名称" maxlength="20" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">空间面积</label>
            <div class="layui-input-inline">
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

        <div class="layui-form-item">
            <label class="layui-form-label">风格</label>
            <div class="layui-input-block">
                @foreach($styles as $item)
                    <input type="checkbox" name="section_style_id[]" class="n-section-style-id" lay-verify="required"  lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($section) && in_array($item->id,$section->style_ids)) checked @endif>
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">空间设计</label>

            <div class="layui-input-block" style="width:900px" >
                <textarea  class="layui-textarea n-section-design-description" name="each_space_description[]" placeholder="空间设计说明" @if($config['each_space_description_required'])lay-verify="required"@endif maxlength="{{$config['each_space_description_char_limit']}}" style="width: 400px; height: 100px;" autocomplete="off">{{$section->content['design']['description'] or ''}}</textarea>

                <div style="margin-bottom:10px;margin-top:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="section-design-photo-tpl" > + 添加空间设计配图</button>
                </div>
                <div class="info-list">
                    @if(isset($section) && isset($section->content))
                        @foreach ($section->content['design']['photos'] as $item)
                            @include('v1.site.center.album.components.section-design-photo-tpl',['data'=>$item])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">产品应用</label>

            <div class="layui-input-block" style="width:900px" >
                <textarea class="layui-textarea n-section-product-description" name="each_space_build_description[]" placeholder="产品应用说明" @if($config['each_space_product_app_description_required'])lay-verify="required"@endif maxlength="{{$config['each_space_product_app_description_char_limit']}}" style="width: 400px; height: 100px;" autocomplete="off" >{{$section->content['product']['description'] or ''}}</textarea>

                <div style="margin-bottom:10px;margin-top:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="section-product-photo-tpl" > + 添加产品应用配图</button>
                </div>
                <div class="info-list">
                    @if(isset($section) && isset($section->content))
                        @foreach ($section->content['build']['photos'] as $item)
                            @include('v1.site.center.album.components.section-product-photo-tpl',['data'=>$item])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">施工</label>

            <div class="layui-input-block" style="width:900px" >
                <textarea  class="layui-textarea n-section-build-description"  placeholder="施工说明" @if($config['each_space_build_description_required'])lay-verify="required"@endif maxlength="{{$config['each_space_build_description_char_limit']}}" style="width: 400px; height: 100px;" autocomplete="off">{{$section->content['build']['description'] or ''}}</textarea>

                <div style="margin-bottom:10px;margin-top:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="section-build-photo-tpl" > + 添加施工配图</button>
                </div>
                <div class="info-list">
                    @if(isset($section) && isset($section->content))
                        @foreach ($section->content['build']['photos'] as $item)
                            @include('v1.site.center.album.components.section-build-photo-tpl',['data'=>$item])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>


        <div style="clear:both"></div>
    </div>


</div>
