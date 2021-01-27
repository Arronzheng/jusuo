<div class="info-block product-accessory-block" style="margin-bottom:15px;float:left;border:1px solid #dedede;padding:15px;">
    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
        <i class="layui-icon layui-icon-close"></i>
    </button>
    <div style="width:800px;overflow:hidden;margin-bottom:10px;">
        <div class="layui-form-mid layui-word-aux">
            @if($cf['accessory.code.required'])<span class="required">*</span>@endif 配件编号：
        </div>
        <div class="layui-input-inline">
            <input type="text" name="accessory_number[]" value="{{$data['code'] or ''}}" @if($cf['accessory.code.required'])lay-verify="required"@endif placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">
             配件规格：@if($cf['accessory.spec.required'])<span class="required">*</span>@endif 长
        </div>
        <div class="layui-input-inline" style="width:80px">
            <input type="text" name="accessory_length[]" value="{{$data['spec_length'] or ''}}" @if($cf['accessory.spec.required'])lay-verify="required"@endif placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">
            × @if($cf['accessory.spec.required'])<span class="required">*</span>@endif 宽
        </div>
        <div class="layui-input-inline" style="width:80px">
            <input type="text" name="accessory_width[]" value="{{$data['spec_width'] or ''}}" @if($cf['accessory.spec.required'])lay-verify="required"@endif placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <div class="layui-form-mid layui-word-aux">
            @if($cf['accessory.technology.required'])<span class="required">*</span>@endif 加工工艺：
        </div>
        <div class="layui-input-inline">
            <input type="text" name="accessory_technology[]" value="{{$data['technology'] or ''}}" @if($cf['accessory.technology.required'])lay-verify="required"@endif placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div style="clear:both;"></div>
    </div>
    <div style="margin-bottom:10px;">
        <div class="layui-form-item">
            @if($cf['accessory.photo.required'])<span class="required">*</span>@endif
            <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="product-accessory-photo-tpl"> + 添加配件图</button>
            <div class="info-list">
                @if(isset($data['photo']) && $data['photo'] != null)
                    @foreach (unserialize($data['photo']) as $item)
                        @include("v1.admin_brand.product.components.product-accessory-photo-tpl",['data'=>$item,'cf'=>$cf])
                    @endforeach
                @endif
            </div>
        </div>

    </div>
    <div style="clear:both"></div>
</div>