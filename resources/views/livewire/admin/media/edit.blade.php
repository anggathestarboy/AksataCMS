<div class="py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.media.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; Media Library
                </a>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Media</h1>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Preview --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="aspect-video bg-gray-100 flex items-center justify-center">
                    <img src="{{ $media->url }}" alt="{{ $media->alt_text ?? $media->name }}"
                        class="max-w-full max-h-full object-contain">
                </div>
            </div>

            {{-- Details --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Filename</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $media->file_name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500">Slug</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $media->slug ?? '—' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Width</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if ($media->width)
                                {{ $media->width }} px
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Height</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if ($media->height)
                                {{ $media->height }} px
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if (! $media->width || ! $media->height)
                    <button type="button" wire:click="reReadDimensions"
                        class="text-xs text-indigo-600 hover:text-indigo-500">
                        Re-read dimensions from file
                    </button>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-500">MIME Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $media->mime_type }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500">Size</label>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($media->size / 1024, 0) }} KB</p>
                </div>

                <form wire:submit="save" class="space-y-5">
                    <div>
                        <label for="editAltText" class="block text-xs font-medium text-gray-700">Alt Text</label>
                        <input type="text" wire:model="editAltText" id="editAltText"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('editAltText')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="editLoading" class="block text-xs font-medium text-gray-700">Loading</label>
                        <select wire:model="editLoading" id="editLoading"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="lazy">lazy</option>
                            <option value="eager">eager</option>
                        </select>
                        @error('editLoading')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="editFolderId" class="block text-xs font-medium text-gray-700">Folder</label>
                        <select wire:model="editFolderId" id="editFolderId"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Root (no folder)</option>
                            @foreach ($folders as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-wirde uppercase hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Save
                        </button>
                        <a href="{{ route('admin.media.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 tracking-wirde uppercase shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
