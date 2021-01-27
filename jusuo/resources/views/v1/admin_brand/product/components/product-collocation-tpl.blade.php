<div class="info-block product-collocation-block" style="margin-bottom:15px;float:left;border:1px solid #dedede;padding:15px;">
    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary pull-right" onclick="remove_custom_info_block(this)" >
        <i class="layui-icon layui-icon-close"></i>
    </button>
    <div style="width:800px;overflow:hidden;margin-bottom:10px;">
        <div class="layui-form-mid layui-word-aux">
            @if($cf['collocation.note.required'])<span class="required">*</span>@endif 应用说明：
        </div>
        <div class="layui-input-inline" style="width:600px">
            <input type="text" name="collocation_note[]" value="{{$data->collocation_note or ''}}" @if($cf['collocation.note.required'])lay-verify="required"@endif placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <div class="layui-form-item">
            <div class="collocation-product-search">
                <div class="layui-form-mid layui-word-aux">
                    @if($cf['collocation.product.required'])<span class="required">*</span>@endif 搭配产品：
                </div>
                <input type="text" style="display:inline" autocomplete="off" class="layui-input"  value="" placeholder="搜索搭配产品">
                <div class="collocation-product-result">
                    <table class="layui-table" style="margin-top:0;">
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
            <div class="collocation-product-selected">
                @if(isset($data) && isset($data->collocation_id))
                    <div class="selected-block" data-id="{{$data->collocation_id}}">已选产品：{{$data->collocation_id?$data->name:'无'}}</div>
                @endif
            </div>
            <div class="info-list"></div>
        </div>

    </div>

    <div style="margin-bottom:10px;">
        <div class="layui-form-item">
            @if($cf['collocation.photo.required'])<span class="required">*</span>@endif
            <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="product-collocation-photo-tpl"> + 添加产品搭配图</button>
            <div class="info-list">
                @if(isset($data->photo) && $data->photo != null)
                    @foreach (unserialize($data->photo) as $item)
                        @include("v1.admin_brand.product.components.product-collocation-photo-tpl",['data'=>$item,'cf'=>$cf])
                    @endforeach
                @endif
            </div>
        </div>

    </div>
    <div style="clear:both"></div>
</div>