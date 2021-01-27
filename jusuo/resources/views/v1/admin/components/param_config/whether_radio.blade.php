<div class="layui-input-block input-value-block">
    <input type="radio" name="value" value="0" title="否" @if(isset($config_set) && $config_set && $config_set['value']==0) checked @endif>
    <input type="radio" name="value" value="1" title="是" @if(isset($config_set) && $config_set && $config_set['value']==1) checked @endif>
</div>