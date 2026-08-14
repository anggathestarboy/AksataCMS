@php $errorBag = $this->getErrorBag(); @endphp

<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errorBag->isNotEmpty())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3">
                <h4 class="text-sm font-medium text-red-800">Please fix the following before saving:</h4>
                <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                    @foreach ($errorBag->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" wire:model.live="status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="publishedAt" class="block text-sm font-medium text-gray-700">Published At</label>
                <input id="publishedAt" type="datetime-local" wire:model="publishedAt"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-500"
                    @disabled($status !== 'published')>
                @error('publishedAt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8" x-data="{ tab: @js(array_key_first(config('cms.locales'))) }">
            <h3 class="text-lg font-medium text-gray-900">Translations</h3>

            <div class="border-b border-gray-200 mt-4">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    @foreach (config('cms.locales') as $locale => $label)
                        @php
                            $hasTabErrors = $errorBag->has('translations.' . $locale . '.title')
                                || $errorBag->has('translations.' . $locale . '.slug');
                        @endphp
                        <button type="button" @click="tab = @js($locale)"
                            :class="tab === @js($locale) ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center px-1 py-2 border-b-2 text-sm font-medium">
                            {{ $label }}
                            @if ($locale === config('cms.default_locale'))
                                <span class="ms-1 text-xs text-gray-400">(default)</span>
                            @endif
                            @if ($hasTabErrors)
                                <span class="ms-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>

            @foreach (config('cms.locales') as $locale => $label)
                <div x-show="tab === @js($locale)" class="mt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title ({{ $label }})</label>
                            <input type="text" wire:model.live="translations.{{ $locale }}.title"
                                @change="const el = document.querySelector('[data-slug-field=&quot;{{ $locale }}&quot;]'); if (el) { el.value = $event.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); el.dispatchEvent(new Event('input', { bubbles: true })); }"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('translations.' . $locale . '.title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug ({{ $label }})</label>
                            <input type="text" data-slug-field="{{ $locale }}" wire:model.live="translations.{{ $locale }}.slug"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('translations.' . $locale . '.slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <input type="text" wire:model="translations.{{ $locale }}.meta.meta_title"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('translations.' . $locale . '.meta.meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <textarea rows="2" wire:model="translations.{{ $locale }}.meta.meta_description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            @error('translations.' . $locale . '.meta.meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">OG Image URL</label>
                            <input type="text" wire:model="translations.{{ $locale }}.meta.og_image"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('translations.' . $locale . '.meta.og_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex items-center justify-between">
            <div>
                @if (isset($page))
                    <button type="button" x-data @click="if (confirm('Delete this page?')) $wire.delete()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Delete
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pages.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="button" wire:click="save"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
