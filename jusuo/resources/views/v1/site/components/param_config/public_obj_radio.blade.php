<div class="layui-input-block input-value-block">
    @if(!isset($parent_set) || (isset($parent_set) && $parent_set['value']>=10) )
        <input type="radio" name="value" value="10" title="合作对象" @if(isset($config_set) && $config_set['value']==10) checked @endif>
    @endif
    @if(!isset($parent_set) || (isset($parent_set) && $parent_set['value']>=20) )
        <input type="radio" name="value" value="20" title="本地用户" @if(isset($config_set) && $config_set['value']==20) checked @endif>
    @endif
    @if(!isset($parent_set) || (isset($parent_set) && $parent_set['value']>=30) )
        <input type="radio" name="value" value="30" title="所有人"@if(isset($config_set) && $config_set['value']==30) checked @endif>
    @endif
</div>