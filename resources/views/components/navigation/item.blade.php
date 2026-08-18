@props(['item', 'locale'])

@php
    $label = $item->label($locale) ?: $item->label(config('cms.default_locale'));
    $url = $item->resolvedUrl($locale);
    $hasChildren = $item->children->isNotEmpty();
@endphp

<li @if ($hasChildren) x-data="{ open: false }" @endif class="{{ $hasChildren ? 'relative' : '' }}">
    <a href="{{ $url }}"
        @if ($hasChildren)
            @click.prevent="open = ! open"
        @endif
        @if ($item->open_in_new_tab) target="_blank" rel="noopener" @endif
        class="inline-flex items-center gap-1 transition-colors hover:text-indigo-600">
        @if ($item->icon)
            <span class="text-base leading-none">{!! $item->icon !!}</span>
        @endif
        {{ $label }}
        @if ($hasChildren)
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        @endif
    </a>

    @if ($hasChildren)
        <ul x-show="open" @click.outside="open = false" x-cloak
            class="absolute left-0 top-full mt-2 w-48 rounded-md border border-gray-200 bg-white py-2 shadow-lg">
            @foreach ($item->children as $child)
                <x-navigation.item :item="$child" :locale="$locale" />
            @endforeach
        </ul>
    @endif
</li>