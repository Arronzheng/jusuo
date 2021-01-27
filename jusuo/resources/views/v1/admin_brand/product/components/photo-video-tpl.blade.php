<div class="info-block" style="margin-bottom:15px;float:left;">
    <div class="layui-input-inline">
        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
            <i class="layui-icon layui-icon-close"></i>
        </button>
        <div class="layui-upload-drag video-upload-drag" data-is-init="0" data-name-class="n-photo-video" data-upload-url="{!! url('admin/brand/product/api/upload_product_video') !!}">
            <i class="layui-icon"></i>
            <p>仅支持MP4格式，每个视频大小限制50M以内</p>
            <input type="hidden" class="n-photo-video" lay-verify="photo_required" data-photo-type="产品视频" value="{{$data or ''}}"/>
            <div class="upload-video-preview" @if(!isset($data))style="display:none;" @endif ><a target="_blank" href="{{$data or ''}}">已上传（查看）</a></div>
        </div>

    </div>

    <div style="clear:both"></div>
</div>