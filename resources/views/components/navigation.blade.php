@if ($navigation && $navigation->items->isNotEmpty())
    <nav {{ $attributes }}>
        <ul class="flex items-center gap-6">
            @foreach ($navigation->items as $item)
                <x-navigation.item :item="$item" :locale="$locale" />
            @endforeach
        </ul>
    </nav>
@endif