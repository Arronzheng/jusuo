<div class="info-block space-application-block" style="margin-bottom:15px;float:left;border:1px solid #dedede;padding:15px;">
    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
        <i class="layui-icon layui-icon-close"></i>
    </button>
    <div style="float:left;margin-right:15px;">
        <div class="layui-input-inline">
            <div class="layui-upload-drag image-upload-drag" data-is-init="0" data-name-class="n-space-application-photo" data-upload-url="{!! url('admin/brand/product/api/upload_product_image') !!}">
                <i class="layui-icon"></i>
                <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
                <input type="hidden" class="n-space-application-photo" @if($cf['space.photo.required'])lay-verify="photo_required"@endif data-photo-type="空间应用配图" value="{{$data['photo'] or ''}}"/>
                <div class="upload-img-preview" style="background-image:url('{{$data['photo'] or ''}}')"></div>
            </div>

        </div>
    </div>
    <div style="float:left;">
        <div style="width:500px;overflow:hidden;margin-bottom:10px;">
            <div class="layui-input-inline" style="width:400px;">
                @if($cf['space.title.required'])<span class="required">*</span>@endif
                <input type="text" style="display:inline-block;width:95%;" name="space_application_title[]" @if($cf['space.title.character_limit'])maxlength="{{$cf['space.title.character_limit']}}" @endif value="{{$data['title'] or ''}}" @if($cf['space.title.required'])lay-verify="required"@endif placeholder="空间应用主题" autocomplete="off" class="layui-input">
            </div>

        </div>
        <div style="width:500px;overflow:hidden;margin-bottom:10px;">
            <div class="layui-input-inline" style="width:500px">
                @if($cf['space.note.required'])<span class="required" style="float:left;margin-right:5px;">*</span>@endif
                <textarea type="text" style="float:left;width:380px;height:150px;"  name="space_application_note[]" @if($cf['space.note.character_limit'])maxlength="{{$cf['space.note.character_limit']}}" @endif @if($cf['space.note.required'])lay-verify="required"@endif  placeholder="详细说明" autocomplete="off" class="layui-input">{{$data['note'] or ''}}</textarea>
            </div>
        </div>
    </div>

    <div style="clear:both"></div>
</div>