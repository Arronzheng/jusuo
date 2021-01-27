<div class="info-block" style="margin-bottom:15px;">
    <div class="layui-form">
        <div class="layui-inline" style="width:400px">
            <input type="text" name="physical_chemical_property[]" lay-verify="required" @if($cf['physical_chemical_property.character_limit'])maxlength="{{$cf['physical_chemical_property.character_limit']}}" @endif value="{{$data or ''}}" autocomplete="off" placeholder="" class="layui-input">
        </div>
        <div class="layui-inline">
            <div class="layui-btn layui-btn-primary" style="font-size:12px;" onclick="remove_custom_info_block(this);" >删除</div>
        </div>
    </div>

</div>