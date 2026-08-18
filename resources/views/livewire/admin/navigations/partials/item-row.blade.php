@props(['item', 'depth' => 0])

<div x-sort:item="{{ $item->id }}" data-item-id="{{ $item->id }}"
    class="group rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center gap-2 px-3 py-2.5">
        {{-- Drag handle --}}
        <span x-sort:handle title="Drag to reorder" class="cursor-grab text-gray-400 hover:text-gray-600 shrink-0">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 2a2 2 0 11.001 4.001A2 2 0 017 2zm0 6a2 2 0 11.001 4.001A2 2 0 017 8zm0 6a2 2 0 11.001 4.001A2 2 0 017 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 01.001 4.001A2 2 0 0113 8zm0 6a2 2 0 01.001 4.001A2 2 0 0113 14z"/>
            </svg>
        </span>

        {{-- Move arrows --}}
        <div class="flex flex-col -space-y-0.5 shrink-0">
            <button type="button" wire:click="moveItem({{ $item->id }}, 'up')" title="Move up"
                class="text-gray-400 hover:text-gray-700 p-0.5 rounded hover:bg-gray-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
            </button>
            <button type="button" wire:click="moveItem({{ $item->id }}, 'down')" title="Move down"
                class="text-gray-400 hover:text-gray-700 p-0.5 rounded hover:bg-gray-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        {{-- Label + resolved URL --}}
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-gray-900 truncate">
                {{ $item->label($activeLocale) ?: $item->label(config('cms.default_locale')) ?: '(no label)' }}
            </div>
            <div class="text-[11px] text-gray-400 font-mono truncate">{{ $item->resolvedUrl($activeLocale) }}</div>
        </div>

        {{-- Badges --}}
        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-indigo-100 text-indigo-800">{{ $item->type }}</span>
        @if ($item->open_in_new_tab)
            <span title="Opens in new tab" class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">↗</span>
        @endif
        @if ($item->icon)
            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">{{ $item->icon }}</span>
        @endif

        {{-- Actions --}}
        <div class="shrink-0 flex items-center gap-1">
            <button type="button" wire:click="openCreate({{ $item->id }})" title="Add submenu item"
                class="text-gray-400 hover:text-indigo-600 p-1.5 rounded-md hover:bg-indigo-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
            <button type="button" wire:click="openEdit({{ $item->id }})" title="Edit"
                class="text-gray-400 hover:text-indigo-600 p-1.5 rounded-md hover:bg-indigo-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button type="button" x-data @click="if (confirm('Delete this item? Its submenu items will be removed too.')) $wire.deleteItem({{ $item->id }})" title="Delete"
                class="text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>

    {{-- Submenu (sortable container, same group for nesting) --}}
    @if ($item->children->isNotEmpty())
        <div x-sort x-sort:group="nav-items" x-sort:config="{ animation: 150 }"
            x-sort="(item, position) => $wire.updateOrder(item, position, {{ $item->id }})"
            class="px-3 pb-2.5 pt-1 pl-8 space-y-2">
            @foreach ($item->children as $child)
                @include('livewire.admin.navigations.partials.item-row', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>