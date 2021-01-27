<div class="layui-form-item">
    <label class="layui-form-label">&nbsp;</label>
    <div class="layui-input-block" >
        <div class="layui-input-inline">
            <select id="@if(isset($index_i))area-visible-province-{{$index_i+1}}@else{{'area-visible-province'}}@endif" @if(isset($index_i))data-city-id="area-visible-city-{{$index_i+1}}" @endif name="area_visible_province[]" lay-verify="required" lay-filter="areaServingProvinceId">
                <option value="">请选择省</option>
                @foreach($provinces as $item)
                    <option value="{{$item->id}}" @if(isset($data) && $data['province_id']==$item->id) selected @endif >{{$item->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="layui-input-inline">
            <select id="@if(isset($index_i))area-visible-city-{{$index_i+1}}@else{{'area-visible-city'}}@endif" name="area_visible_city[]" lay-verify="required" lay-filter="areaServingCityId">
                <option value="">请选择城市</option>
                @if(isset($data) && isset($data['city_data']))
                    @foreach($data['city_data'] as $item)
                        <option value="{{$item->id}}" @if(isset($data) && $data['city_id']==$item->id) selected @endif>{{$item->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="layui-input-inline">
            <button class="layui-btn layui-btn-sm layui-btn-primary" type="button" onclick="delete_area_block(this)">删除</button>
        </div>
        <div style="clear:both"></div>
    </div>
</div>