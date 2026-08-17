@php $errorBag = $this->getErrorBag(); @endphp

<div class="space-y-5">
    {{-- Publish Toggle --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Publish</h3>
        </div>
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Status</span>
                <button type="button" wire:click="togglePublish"
                    class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-inner"
                    :class="$wire.status === 'published' ? 'bg-indigo-600' : 'bg-gray-300'">
                    <span class="pointer-events-none inline-block h-6 w-6 rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out transform"
                        :class="$wire.status === 'published' ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Language Selector --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Language</h3>
        </div>
        <div class="px-4 py-3">
            <select wire:model.live="activeLocale"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium">
                @foreach (config('cms.locales') as $locale => $label)
                    <option value="{{ $locale }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Title / Slug / Meta (driven by activeLocale) --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Content
                <span class="ml-1 text-indigo-500 normal-case tracking-normal">({{ config('cms.locales')[$activeLocale] ?? $activeLocale }})</span>
            </h3>
        </div>
        <div class="px-4 py-4 space-y-4" x-data="{ slugTouched: false }">
            <div>
                <label class="block text-xs font-medium text-gray-500">Title</label>
                <input type="text" wire:model.live="translations.{{ $activeLocale }}.title"
                    @input="if (! slugTouched) { const slug = $event.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); const el = document.querySelector('[data-slug-field]'); if (el) { el.value = slug; } $wire.set('translations.{{ $activeLocale }}.slug', slug, false); }"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('translations.' . $activeLocale . '.title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">Slug</label>
                <input type="text" data-slug-field wire:model.live="translations.{{ $activeLocale }}.slug"
                    @input="slugTouched = true"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono text-xs">
                @error('translations.' . $activeLocale . '.slug')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">Meta Title</label>
                <input type="text" wire:model="translations.{{ $activeLocale }}.meta.meta_title"
                    placeholder="Falls back to title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('translations.' . $activeLocale . '.meta.meta_title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">Meta Description</label>
                <textarea rows="2" wire:model="translations.{{ $activeLocale }}.meta.meta_description"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs"></textarea>
                @error('translations.' . $activeLocale . '.meta.meta_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">OG Image URL</label>
                <input type="text" wire:model="translations.{{ $activeLocale }}.meta.og_image"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('translations.' . $activeLocale . '.meta.og_image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    @if ($errorBag->isNotEmpty())
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3">
            <h4 class="text-sm font-medium text-red-800">Please fix the following before saving:</h4>
            <ul class="mt-1 list-disc list-inside text-xs text-red-700">
                @foreach ($errorBag->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
