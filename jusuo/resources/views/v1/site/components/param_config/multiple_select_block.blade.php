<?php
$item_value = null;
if(isset($item)){
    if(is_array($item)){
        $item_value = $item;
    }else{
        $item_value = null;
    }
}else{
    $item_value = null;
}
?>

@if($item_value )
    <div class="layui-inline multiple-select-item" >

        <input class="layui-input text" name="option" type="text" value="{{$item_value?$item_value['name']:''}}"  >
        <input type="hidden" name="id" value="{{$item_value?$item_value['id']:0}}">
        <input type="hidden" name="type" value="{{isset($type)?$type:0}}">

        {{--<div class="close-btn" onclick="delete_multiple_select_item(this)" style="display:inline-block">删除</div>--}}
        <div class="close-btn" onclick="modify_multiple_select_item(this)" style="display:inline-block">提交修改</div>
    </div>
@endif