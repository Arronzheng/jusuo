@if(isset($config_set))
    @if(isset($config_set['value[]']) && count($config_set['value[]'])>0)
        @foreach($config_set['value[]'] as $item)
            @include('v1.admin.components.param_config.multiple_text_block',[
                'value'=>$item
            ])
        @endforeach
    @endif
@endif
