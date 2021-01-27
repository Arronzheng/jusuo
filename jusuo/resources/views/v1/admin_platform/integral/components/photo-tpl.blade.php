<div class="info-block" style="margin-bottom:15px;float:left;">
    <div class="layui-input-inline">
        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
            <i class="layui-icon layui-icon-close"></i>
        </button>
        <div class="layui-upload-drag image-upload-drag" data-is-init="0" data-name-class="n-photo" data-upload-url="{!! url('admin/platform/integral/goods/api/upload_image') !!}">
            <i class="layui-icon"></i>
            <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
            <input type="hidden" class="n-photo" lay-verify="photo_required" data-photo-type="礼品图" value="{{$data or ''}}"/>
            <div class="upload-img-preview"  style="background-image:url('{{$data or ''}}')"></div>
        </div>

    </div>

    <div style="clear:both"></div>
</div>