<li class="dd-item" data-id="{{ $children->id }}">
    <div class="dd-handle">{{ $children->display_name }}</div>
    @if ($children->child)
        <ol class="dd-list">
            @foreach ($children->child as $child)
                @include('v1.admin_platform.privilege.brand.nestable-child', ['children' => $child])
            @endforeach
        </ol>
    @endif
</li>
