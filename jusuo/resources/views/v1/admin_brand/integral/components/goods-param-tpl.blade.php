<div class="info-block goods-param-block" style="width:700px;margin-bottom:10px;float:left;border:1px solid #dedede;padding: 10px;px;">
    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
        <i class="layui-icon layui-icon-close"></i>
    </button>
    <div style="overflow:hidden;">
        <div class="layui-form-mid layui-word-aux" style="width:70px;">
            参数项：
        </div>
        <div class="layui-input-inline">
            <input type="text" name="param_key[]" value="{{$data['key'] or ''}}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux" style="width:70px;">
            参数内容：
        </div>
        <div class="layui-input-inline">
            <input type="text" name="param_value[]" value="{{$data['value'] or ''}}" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div style="overflow:hidden;">

        <div style="clear:both;"></div>
    </div>
    <div style="clear:both"></div>
</div>