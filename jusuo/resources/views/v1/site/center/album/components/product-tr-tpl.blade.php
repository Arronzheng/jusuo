<tr>
    <td >
        <input type="hidden" class="n-product-id" value="{{$data->id or 0}}"/>
        <span class="product-name">{{$data->name or ''}}</span>
    </td>
    <td class="product-type">{{$data->type_text or ''}}</td>
    <td class="product-code">{{$data->code or ''}}</td>
    <td class="product-spec">{{$data->spec_text or ''}}</td>
    <td class="product-img">
        <a target="_blank" href="#">
            <img height="40px" src="{{$data->photo_cover or ''}}"/>
        </a>
    </td>
    <td><button type="button" class="layui-btn layui-btn-sm layui-btn-danger" onclick="remove_product_tr(this);">删除</button></td>
</tr>