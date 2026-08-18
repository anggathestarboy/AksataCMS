@props(['item', 'locale'])

@php
    $label = $item->label($locale) ?: $item->label(config('cms.default_locale'));
    $url = $item->resolvedUrl($locale);
    $hasChildren = $item->children->isNotEmpty();
@endphp

<li @if ($hasChildren) x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @endif class="{{ $hasChildren ? 'relative' : '' }}">
    <a href="{{ $url }}"
        @if ($item->open_in_new_tab) target="_blank" rel="noopener" @endif
        class="flex items-center gap-1 px-3 py-2 rounded-md text-sm transition-colors hover:bg-gray-100 hover:text-indigo-600">
        @if ($item->icon)
            <span class="text-base leading-none">{!! $item->icon !!}</span>
        @endif
        {{ $label }}
        @if ($hasChildren)
            <span @click.prevent.stop="open = ! open" class="cursor-pointer ml-0.5">
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        @endif
    </a>

    @if ($hasChildren)
        <ul x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
            @click.outside="open = false" x-cloak
            class="absolute left-0 top-full  w-52 rounded-lg border border-gray-200 bg-white py-3 px-2 shadow-lg">
            @foreach ($item->children as $child)
                <x-navigation.item :item="$child" :locale="$locale" />
            @endforeach
        </ul>
    @endif
</li>