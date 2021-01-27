<div class="layui-input-block input-value-block">
    @if((isset($parent_set) && $parent_set['value']==0) )
        <span style="font-size:14px;line-height:35px;" class="pc-hide-submit-btn">（必填）</span>
    @endif
    @if(!isset($parent_set) || (isset($parent_set) && $parent_set['value']==1) )
    <input class="hide-submit-btn" type="radio" name="value" value="0" title="必填" @if(isset($config_set) && $config_set && $config_set['value']==0) checked @endif>
    @endif
    @if(!isset($parent_set) || (isset($parent_set) && $parent_set['value']==1) )
    <input type="radio" name="value" value="1" title="选填" @if(isset($config_set) && $config_set && $config_set['value']==1) checked @endif>
    @endif
</div>

