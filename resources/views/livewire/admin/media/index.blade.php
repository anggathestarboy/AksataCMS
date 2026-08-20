<div class="py-10" x-data="{ showNewFolder: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Media Library</h1>
        </div>

        {{-- Breadcrumbs --}}
        <nav class="flex items-center text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <button wire:click="navigateToFolder(null)"
                class="hover:text-gray-700 font-medium {{ is_null($currentFolderId) ? 'text-indigo-600' : '' }}">
                Media Library
            </button>
            @if ($currentFolder)
                @foreach ($ancestors as $ancestor)
                    <svg class="mx-2 h-4 w-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <button wire:click="navigateToFolder({{ $ancestor->id }})"
                        class="hover:text-gray-700">
                        {{ $ancestor->name }}
                    </button>
                @endforeach
                <svg class="mx-2 h-4 w-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ $currentFolder->name }}</span>
            @endif
        </nav>

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 mb-4">
            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button type="button" x-on:click="showNewFolder = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    New Folder
                </button>
                <label
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md shadow-sm hover:bg-indigo-100 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    Upload
                    <input type="file" wire:model="upload" accept="image/*" class="hidden">
                </label>
                @error('upload')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex-1"></div>

            {{-- Search --}}
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search media..."
                class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        {{-- Action bar (multi-select / clipboard) --}}
        @if (count($selectedIds) > 0 || $clipboard)
            <div class="flex items-center gap-2 mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                @if (count($selectedIds) > 0)
                    <span class="text-xs text-indigo-700 font-medium mr-2">{{ count($selectedIds) }} selected</span>
                    <button type="button" wire:click="cutSelected"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L4.939 4.939"/></svg>
                        Cut
                    </button>
                    <button type="button" wire:click="copySelected"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                    <button type="button" wire:click="bulkDelete"
                        wire:confirm="Delete selected items?"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-red-700 bg-red-50 border border-red-200 rounded hover:bg-red-100">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                    <button type="button" wire:click="deselectAll"
                        class="text-[11px] text-gray-500 hover:text-gray-700 ml-1">
                        Deselect all
                    </button>
                @endif

                @if ($clipboard)
                    <div class="{{ count($selectedIds) > 0 ? 'border-l border-indigo-200 pl-2 ml-1' : '' }}">
                        <span class="text-xs text-indigo-700 font-medium mr-2">
                            {{ $clipboard['action'] === 'cut' ? count($clipboard['ids']) . ' cut' : count($clipboard['ids']) . ' copied' }}
                        </span>
                        <button type="button" wire:click="paste"
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-white bg-indigo-600 rounded hover:bg-indigo-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Paste
                        </button>
                        <button type="button" wire:click="clearClipboard"
                            class="text-[11px] text-gray-500 hover:text-gray-700 ml-1">
                            Clear
                        </button>
                    </div>
                @endif
            </div>
        @endif

        {{-- New Folder form --}}
        <div x-show="showNewFolder" x-cloak x-transition
            class="mb-4 bg-white border border-gray-200 rounded-lg p-4">
            <form wire:submit="createFolder" class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700">Folder Name</label>
                    <input type="text" wire:model="newFolderName" placeholder="New folder name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500">
                    Create
                </button>
                <button type="button" x-on:click="showNewFolder = false"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                    Cancel
                </button>
            </form>
        </div>

        {{-- Folders --}}
        @if ($folders->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
                @foreach ($folders as $folder)
                    <div class="group relative bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                        <div wire:click="navigateToFolder({{ $folder->id }})"
                            class="aspect-square bg-amber-50 flex items-center justify-center">
                            <svg class="w-12 h-12 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2 6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                            </svg>
                        </div>
                        <div class="p-2">
                            @if ($renamingFolderId === $folder->id)
                                <form wire:submit="saveRenameFolder" class="space-y-1.5">
                                    <input type="text" wire:model="renameFolderName" x-ref="renameInput"
                                        x-init="$nextTick(() => $refs.renameInput?.focus())"
                                        class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        @keydown.escape.window="$wire.cancelRenameFolder()">
                                    <div class="flex gap-1">
                                        <button type="submit"
                                            class="flex-1 px-2 py-1 text-[10px] font-medium text-white bg-indigo-600 rounded hover:bg-indigo-500">OK</button>
                                        <button type="button" wire:click="cancelRenameFolder"
                                            class="flex-1 px-2 py-1 text-[10px] font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                                    </div>
                                </form>
                            @else
                                <p class="text-[11px] font-medium text-gray-900 truncate" title="{{ $folder->name }}">{{ $folder->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $folder->media_count ?? $folder->media()->count() }} files</p>
                                <div class="mt-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <button type="button" wire:click="startRenameFolder({{ $folder->id }})"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-indigo-600 bg-indigo-50 rounded hover:bg-indigo-100">Rename</button>
                                    <button type="button"
                                        wire:confirm="Delete folder '{{ $folder->name }}'? Files will be moved to the parent folder."
                                        wire:click="deleteFolder({{ $folder->id }})"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">Delete</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Grid --}}
        @if ($media->isEmpty() && $folders->isEmpty())
            <div class="bg-white rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center">
                @if ($currentFolder)
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">This folder is empty.</p>
                @else
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No media files found.</p>
                    <p class="mt-1 text-xs text-gray-400">Upload an image above to get started.</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($media as $item)
                    <div class="group relative bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm"
                        x-data="{ editing: false }">

                        {{-- Checkbox --}}
                        <div class="absolute top-2 left-2 z-10">
                            <label class="inline-flex items-center">
                                <input type="checkbox"
                                    wire:click="toggleSelect({{ $item->id }})"
                                    {{ in_array($item->id, $selectedIds) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </label>
                        </div>

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
                                @if ($item->width && $item->height)
                                    <p class="text-[10px] text-gray-400">{{ $item->width }}&times;{{ $item->height }}</p>
                                @endif
                                <p class="text-[10px] text-gray-400">{{ number_format($item->size / 1024, 0) }} KB</p>

                                <div class="mt-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                    <a href="{{ route('admin.media.edit', $item->id) }}"
                                        class="flex-1 px-2 py-1 text-[10px] font-medium text-indigo-600 bg-indigo-50 rounded hover:bg-indigo-100 text-center">Edit</a>
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
