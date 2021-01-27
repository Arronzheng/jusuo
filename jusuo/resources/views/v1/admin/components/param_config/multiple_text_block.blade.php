<div class="multiple-text-block">
    <div class="layui-inline">
        <input type="text" name="value[]" value="{{isset($value)?$value:''}}" readonly autocomplete="off" placeholder="" class="layui-input">
        <div class="delete-btn" onclick="delete_multiple_block(this);" >删除</div>
    </div>
</div>
