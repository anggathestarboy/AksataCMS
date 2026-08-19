<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Media Library</h1>
        </div>

        {{-- Upload Area --}}
        <div class="mb-6 bg-white rounded-lg border border-dashed border-gray-300 p-6">
            <div class="text-center">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Upload images to your media library</p>
                <div class="mt-3">
                    <input type="file" wire:model="upload" accept="image/*"
                        class="block w-full max-w-xs mx-auto text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100">
                </div>
                <p class="mt-1 text-[10px] text-gray-400">JPEG, PNG, GIF, WebP, SVG — max 5 MB</p>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search media..."
                class="block w-full sm:w-72 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        {{-- Grid --}}
        @if ($media->isEmpty())
            <div class="bg-white rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center">
                <p class="text-sm text-gray-500">No media files found.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($media as $item)
                    <div class="group relative bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm"
                        x-data="{ editing: false }">

                        {{-- Thumbnail --}}
                        <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                            <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->name }}"
                                class="w-full h-full object-cover">
                        </div>

                        {{-- Info --}}
                        <div class="p-2">
                            @if ($editingId === (string) $item->id)
                                {{-- Edit mode --}}
                                <input type="text" wire:model="editName"
                                    class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-1">
                                <input type="text" wire:model="editAltText" placeholder="Alt text"
                                    class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-1">
                                <div class="flex gap-1">
                                    <button type="button" wire:click="saveEdit"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-white bg-indigo-600 rounded hover:bg-indigo-500">Save</button>
                                    <button type="button" wire:click="cancelEdit"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                                </div>
                            @else
                                {{-- Display mode --}}
                                <p class="text-[11px] font-medium text-gray-900 truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                                <p class="text-[10px] text-gray-400 truncate">{{ $item->file_name }}</p>
                                <p class="text-[10px] text-gray-400">{{ number_format($item->size / 1024, 0) }} KB</p>

                                <div class="mt-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <button type="button" wire:click="startEdit({{ $item->id }})"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-indigo-600 bg-indigo-50 rounded hover:bg-indigo-100">Edit</button>
                                    <button type="button"
                                        wire:confirm="Are you sure you want to delete '{{ $item->name }}'?"
                                        wire:click="delete({{ $item->id }})"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">Delete</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $media->links() }}
            </div>
        @endif
    </div>
</div>
