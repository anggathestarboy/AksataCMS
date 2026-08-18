@if ($navigation && $navigation->items->isNotEmpty())
    <nav>
        <ul {{ $attributes->merge(['class' => 'flex gap-6']) }}>
            @foreach ($navigation->items as $item)
                <x-navigation.item :item="$item" :locale="$locale" />
            @endforeach
        </ul>
    </nav>
@endif