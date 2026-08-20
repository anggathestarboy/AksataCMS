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
                <div class="flex items-center gap-3">
                    @if ($page && $page->status === 'published' && $page->url() !== null)
                        <a href="{{ $page->url() }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full hover:bg-emerald-100 transition">
                            View Live
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif
                    <button type="button" wire:click="togglePublish"
                        class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-inner"
                        :class="$wire.status === 'published' ? 'bg-indigo-600' : 'bg-gray-300'">
                        <span class="pointer-events-none inline-block h-6 w-6 rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out transform"
                            :class="$wire.status === 'published' ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                </div>
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
    <div wire:key="content-{{ $activeLocale }}" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
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
                <input type="text" wire:model.live="translations.{{ $activeLocale }}.meta.meta_title"
                    placeholder="Falls back to title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('translations.' . $activeLocale . '.meta.meta_title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">Meta Description</label>
                <textarea rows="2" wire:model.live="translations.{{ $activeLocale }}.meta.meta_description"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs"></textarea>
                @error('translations.' . $activeLocale . '.meta.meta_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">OG Image</label>
                <div x-data="{
                    preview: @js(!empty(data_get($translations[$activeLocale]['meta'] ?? [], 'og_image')) ? \Illuminate\Support\Facades\Storage::disk('public')->url(data_get($translations[$activeLocale]['meta'] ?? [], 'og_image')) : ''),
                    fieldPath: 'translations.{{ $activeLocale }}.meta.og_image',
                }"
                x-on:media-selected.window="
                    if ($event.detail.fieldPath === fieldPath) {
                        preview = $event.detail.url;
                        $wire.set(fieldPath, $event.detail.path);
                    }
                ">
                    <template x-if="preview">
                        <img :src="preview" alt="OG Image" class="mt-2 h-20 w-auto rounded-md border border-gray-200 object-cover">
                    </template>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button"
                            x-on:click="$dispatch('open-media-picker', { fieldPath: fieldPath })"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z"/></svg>
                            Browse Media
                        </button>
                        <input type="text" wire:model.live="translations.{{ $activeLocale }}.meta.og_image" placeholder="Or paste path..."
                            class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs">
                    </div>
                </div>
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
