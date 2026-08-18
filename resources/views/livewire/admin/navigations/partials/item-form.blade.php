@if ($showForm)
    <div class="mb-6 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                @if ($editingItemId !== null)
                    Edit Item
                @elseif ($formParentId !== null)
                    Add Submenu Item
                @else
                    Add Item
                @endif
            </h3>
            <button type="button" wire:click="closeForm" title="Close"
                class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-4 py-4">
            {{-- Type --}}
            <div>
                <label class="block text-xs font-medium text-gray-500">Target Type</label>
                <select wire:model.live="type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="external">External Link</option>
                    <option value="internal">Internal Link</option>
                    <option value="page">Page</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL + new tab (external / internal) --}}
            <div class="mt-4" x-show="$wire.type === 'external' || $wire.type === 'internal'">
                <label class="block text-xs font-medium text-gray-500">
                    {{ $type === 'external' ? 'External URL' : 'Internal Path' }}
                </label>
                <input type="text" wire:model="url"
                    placeholder="{{ $type === 'external' ? 'https://example.com/...' : '/kontak-khusus' }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('url')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" wire:model="openInNewTab" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Open in new tab
                </label>
            </div>

            {{-- Page (type = page) --}}
            <div class="mt-4" x-show="$wire.type === 'page'">
                <label class="block text-xs font-medium text-gray-500">Page</label>
                <select wire:model="pageId"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select a page...</option>
                    @foreach ($pages as $page)
                        <option value="{{ $page->id }}">
                            {{ $page->translation($activeLocale)?->title ?? $page->translation()?->title ?? '(untitled page #' . $page->id . ')' }}
                        </option>
                    @endforeach
                </select>
                @error('pageId')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icon --}}
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-500">Icon (optional)</label>
                <input type="text" wire:model="icon" placeholder="e.g. home, star"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('icon')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Label (locale tabs) --}}
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-500 mb-2">Label</label>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1 w-fit">
                    @foreach (config('cms.locales') as $locale => $label)
                        <button type="button" wire:click="$set('activeLocale', '{{ $locale }}')"
                            class="px-3 py-1 text-xs font-medium rounded-md transition {{ $activeLocale === $locale ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="mt-2">
                    <input type="text" wire:model.live="labels.{{ $activeLocale }}"
                        placeholder="Label in {{ config('cms.locales')[$activeLocale] ?? $activeLocale }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('labels.' . $activeLocale)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Error summary --}}
            @if ($this->getErrorBag()->isNotEmpty())
                <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                    <h4 class="text-sm font-medium text-red-800">Please fix the following before saving:</h4>
                    <ul class="mt-1 list-disc list-inside text-xs text-red-700">
                        @foreach ($this->getErrorBag()->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 flex items-center gap-3">
                <button type="button" wire:click="closeForm"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </button>
                <button type="button" wire:click="saveItem"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $editingItemId !== null ? 'Update Item' : 'Add Item' }}
                </button>
            </div>
        </div>
    </div>
@endif