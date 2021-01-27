@extends('v1.admin_brand.product.components.edit_parent',[])

@section('edit_form')
    <form style="width:900px;">
        <div class="layui-form-item">
            <label class="layui-form-label">产品名称</label>
            <div class="layui-input-inline">
                <input type="text" name="name" @if($cf['name.character_limit'])maxlength="{{$cf['name.character_limit']}}" @endif  value="{{$data->name or ''}}" @if($cf['name.required'])lay-verify="required"@endif placeholder="请输入产品名称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">产品编号</label>
            <div class="layui-input-inline">
                <input type="text" name="code" value="{{$data->code or ''}}" @if($cf['code.character_limit'])maxlength="{{$cf['code.character_limit']}}" @endif @if($cf['code.required'])lay-verify="required"@endif placeholder="请输入产品编号" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否配件</label>
            <div class="layui-input-block">
                <input type="radio" lay-filter="typeRadio" name="type" value="0" title="否" @if(!isset($data) || (isset($data) && $data->type==\App\Models\ProductCeramic::TYPE_PRODUCT)) checked @endif >
                <input type="radio" lay-filter="typeRadio" name="type" value="1" title="是" @if(isset($data) && $data->type==\App\Models\ProductCeramic::TYPE_ACCESSORY) checked @endif  >
            </div>
        </div>
        <div class="layui-form-item" id="parent-id-form-item" style="@if(!isset($data) || $data->type==\App\Models\ProductCeramic::TYPE_PRODUCT) display:none;@endif">
            <label class="layui-form-label">所属父产品</label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" id="parent_product-select"  value="{{$data->parent_name or ''}}" readonly placeholder="点击搜索并选择" class="layui-input">
                <input type="hidden" name="parent_id" value="{{$data->parent_id or ''}}" id="h-parent-product">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">核心工艺</label>
            <div class="layui-input-inline">
                <textarea name="key_technology" @if($cf['key_technology.character_limit'])maxlength="{{$cf['key_technology.character_limit']}}" @endif @if($cf['key_technology.required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$data->key_technology or ''}}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">理化性能</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="physical-chemical-property-tpl" > + 添加理化性能</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->physical_chemical_property != NULL)
                        @foreach (unserialize($data->physical_chemical_property) as $item)
                            @include("v1.admin_brand.product.components.physical-chemical-property-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">功能特征</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="function-feature-tpl" > + 添加功能特征</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->function_feature != NULL)
                        @foreach (unserialize($data->function_feature) as $item)
                            @include("v1.admin_brand.product.components.function-feature-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">顾客价值</label>
            <div class="layui-input-inline">
                <textarea name="customer_value"  @if($cf['customer_value.character_limit'])maxlength="{{$cf['customer_value.character_limit']}}" @endif  @if($cf['customer_value.required'])lay-verify="required"@endif style="width: 400px; height: 150px;" autocomplete="off" class="layui-textarea">{{$data->customer_value or ''}}</textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">系列名称</label>
            <div class="layui-input-block">
                @foreach($vdata['ceramic_series'] as $item)
                    <input type="radio" name="series" value="{{$item->id}}" title="{{$item->name}}" @if(isset($data) && $data->series_id==$item->id) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">产品规格</label>
            <div class="layui-input-block">
                @foreach($vdata['ceramic_specs'] as $item)
                    <input type="radio" name="spec" value="{{$item->id}}" title="{{$item->name}}" @if(isset($data) && $data->spec_id==$item->id) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">应用类别</label>
            <div class="layui-input-block">
                @foreach($vdata['ceramic_apply_categories'] as $item)
                    <input type="checkbox" class="type" name="apply_categories[]" lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($data) && in_array($item->id,$data->apply_categories_ids)) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">工艺类别</label>
            <div class="layui-input-block">
                @foreach($vdata['ceramic_technology_categories'] as $item)
                    <input type="checkbox" class="type" name="technology_categories[]" lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($data) && in_array($item->id,$data->technology_categories_ids)) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">色系</label>
            <div class="layui-input-block">
                @foreach($vdata['ceramic_colors'] as $item)
                    <input type="checkbox" class="type" name="colors[]" lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($data) && in_array($item->id,$data->colors_ids)) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">表面特征</label>
            <div class="layui-input-block">
                @foreach($vdata['ceramic_surface_features'] as $item)
                    <input type="checkbox" class="type" name="surface_features[]" lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}" @if(isset($data) && in_array($item->id,$data->surface_features_ids)) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">可应用空间风格</label>
            <div class="layui-input-block">
                @foreach($vdata['styles'] as $item)
                    <input type="checkbox" class="type" name="styles[]" lay-skin="primary" title="{{$item->name}}" value="{{$item->id}}"  @if(isset($data) && in_array($item->id,$data->styles_ids)) checked @endif>
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">产品图</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="photo-product-tpl"> + 添加产品图</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->photo_product != NULL)
                        @foreach (unserialize($data->photo_product) as $item)
                            @include("v1.admin_brand.product.components.photo-product-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">实物图</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="photo-practicality-tpl"> + 添加实物图</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->photo_practicality != NULL)
                        @foreach (unserialize($data->photo_practicality) as $item)
                            @include("v1.admin_brand.product.components.photo-practicality-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">产品配件</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="product-accessory-tpl"> + 添加产品配件</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->accessories_data != NULL)
                        @foreach ($data->accessories_data as $item)
                            @include("v1.admin_brand.product.components.product-accessory-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">产品搭配</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="product-collocation-tpl"> + 添加产品搭配</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->collocations_product != NULL)
                        @foreach ($data->collocations_product as $item)
                            @include("v1.admin_brand.product.components.product-collocation-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">空间应用</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="space-application-tpl"> + 添加空间应用</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->spaces_data != NULL)
                        @foreach ($data->spaces_data as $item)
                            @include("v1.admin_brand.product.components.space-application-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">产品视频</label>
            <div class="layui-input-block" style="" >
                <div style="margin-bottom:10px;">
                    <button class="layui-btn layui-btn-sm" type="button" onclick="add_custom_info_block(this)" data-tpl="photo-video-tpl"> + 添加产品视频</button>
                </div>
                <div class="info-list">
                    @if(isset($data) && $data->photo_video != NULL)
                        @foreach (unserialize($data->photo_video) as $item)
                            @include("v1.admin_brand.product.components.photo-video-tpl",['data'=>$item,'cf'=>$cf])
                        @endforeach
                    @endif
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="layui-form-item submit-container">
            <div class="layui-input-block">
                {{csrf_field()}}
                @if(isset($data))
                    <input type="hidden" name="log_id" value="{{$data->log_id}}" />
                    <input type="hidden" name="id" value="{{$data->id}}" />
                @endif
                <button class="layui-btn layui-btn-custom-blue" id="submitBtn" lay-submit lay-filter="submitFormBtn">立即提交</button>
                {{--<button type="reset" class="layui-btn layui-btn-primary">重置</button>--}}
            </div>
        </div>
    </form>
@endsection