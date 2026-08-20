{{-- Media Picker Modal --}}
<div x-data="{
        open: false,
        search: '',
        wireTarget: null,
    }"
    x-on:open-media-picker.window="
        open = true;
        search = '';
        $nextTick(() => {
            $refs.pickerSearch?.focus();
            $wire.openPicker($event.detail.fieldPath || null, $event.detail.sectionId || null);
        })
    "
    x-on:media-selected.window="
        open = false;
    "
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    style="display: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/50" x-on:click="open = false; $wire.closePicker()"></div>

    {{-- Modal --}}
    <div class="relative w-full max-w-3xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[80vh]"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
            <h3 class="text-lg font-semibold text-gray-900">Select Media</h3>
            <button type="button" x-on:click="open = false; $wire.closePicker()"
                class="text-gray-400 hover:text-gray-600 p-1 rounded-md hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Breadcrumbs --}}
        <nav class="px-6 pt-3 flex items-center text-xs text-gray-500 shrink-0" aria-label="Picker Breadcrumb">
            <button type="button" wire:click="navigateToFolder(null)"
                class="hover:text-gray-700 font-medium {{ is_null($currentFolderId) ? 'text-indigo-600' : '' }}">
                All Media
            </button>
            @foreach ($ancestors as $ancestor)
                <svg class="mx-1.5 h-3 w-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <button type="button" wire:click="navigateToFolder({{ $ancestor->id }})"
                    class="hover:text-gray-700">
                    {{ $ancestor->name }}
                </button>
            @endforeach
            @if ($currentFolder)
                <svg class="mx-1.5 h-3 w-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ $currentFolder->name }}</span>
            @endif
        </nav>

        {{-- Upload + Search --}}
        <div class="px-6 py-3 border-b border-gray-100 flex items-center gap-3 shrink-0">
            <input type="file" wire:model="upload" accept="image/*"
                class="block text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-[10px] file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100">
            <input type="search" x-ref="pickerSearch" wire:model.live.debounce.300ms="search" placeholder="Search media..."
                class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs">
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">
            {{-- Folders --}}
            @if ($folders->isNotEmpty())
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mb-4">
                    @foreach ($folders as $folder)
                        <button type="button" wire:click="navigateToFolder({{ $folder->id }})"
                            class="flex flex-col items-center gap-1 p-2 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition text-center">
                            <svg class="w-8 h-8 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2 6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                            </svg>
                            <span class="text-[10px] text-gray-700 truncate w-full">{{ $folder->name }}</span>
                        </button>
                    @endforeach
                </div>
            @endif

            @if ($items->isEmpty() && $folders->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 16.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v.75a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75v-.75zM3 3.75A.75.75 0 013.75 3h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 3.75zM3 8.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No media found. Upload an image above.</p>
                </div>
            @else
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach ($items as $item)
                        <button type="button"
                            x-on:click="
                                $dispatch('media-selected', {
                                    path: @js($item->path),
                                    fieldPath: @js($targetFieldPath),
                                    sectionId: @js($targetSectionId),
                                    url: @js($item->url),
                                    width: @js($item->width),
                                    height: @js($item->height),
                                    alt: @js($item->alt_text ?? $item->name),
                                    loading: @js($item->loading ?? 'lazy'),
                                });
                                $wire.select({{ $item->id }});
                            "
                            class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-transparent hover:border-indigo-500 focus:border-indigo-500 focus:outline-none transition">
                            <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->name }}"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-2 opacity-0 group-hover:opacity-100 transition">
                                <p class="text-[10px] font-medium text-white truncate">{{ $item->name }}</p>
                                <p class="text-[9px] text-white/70">{{ $item->file_name }}</p>
                                @if ($item->width && $item->height)
                                    <p class="text-[9px] text-white/50">{{ $item->width }}&times;{{ $item->height }}</p>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
