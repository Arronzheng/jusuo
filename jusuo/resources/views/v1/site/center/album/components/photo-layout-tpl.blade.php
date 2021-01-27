<div class="cst-upload-box">
    <div class="i-upload-block cst-upload-block-sm" data-is-edit="@if($data) 1 @else 0 @endif" data-is-init="0" data-name-class="n-photo-layout" data-tpl="photo-layout-tpl" data-upload-url="{!! url('/center/album/api/upload_img') !!}">
        <div class="upload-icon">
            <div class="icon-horizontal"></div>
            <div class="icon-vertical"></div>
        </div>
        @if(!isset($is_add_btn) || !$is_add_btn)
        <input type="hidden" class="n-photo-layout"  lay-verify="photo_required"  data-photo-type="户型图" value="{{$data or ''}}"/>
        @endif
        <div class="upload-img-preview"  style="background-image:url('{{$data or ''}}')"></div>
    </div>
    @if(!isset($is_add_btn) || !$is_add_btn)
        <div class="delete-block">
            <div class="delete-main">
                <div class="delete-text">删除</div>
                <div class="delete-mask"></div>
            </div>
        </div>
    @endif
</div>


{{--
<div class="info-block" style="margin-bottom:15px;float:left;">
    <div class="layui-input-inline">
        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
            <i class="layui-icon layui-icon-close"></i>
        </button>
        <div class="layui-upload-drag image-upload-drag"  >
            <i class="layui-icon"></i>
            <p>仅支持JPG/PNG格式，每张图片大小限制2M以内</p>
        </div>

    </div>

    <div style="clear:both"></div>
</div>--}}
