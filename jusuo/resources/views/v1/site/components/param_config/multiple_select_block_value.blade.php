@if(isset($config_set))
    @if(isset($config_set) && count($config_set)>0)
        @foreach($config_set as $item)
            @include('v1.admin.components.param_config.multiple_select_block',[
                'item'=>$item,'type'=>$type
            ])
        @endforeach
    @endif
@endif