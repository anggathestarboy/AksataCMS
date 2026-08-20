<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Global Settings</h1>
        </div>

        @if (session()->has('status'))
            <div class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="save" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 space-y-6">
                <div>
                    <label for="siteTitle" class="block text-sm font-medium text-gray-700">Site Title</label>
                    <input id="siteTitle" type="text" wire:model="siteTitle"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('siteTitle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Default SEO Image</label>
                    <p class="mt-1 text-xs text-gray-500">Used as fallback og:image when a page has no image set.</p>
                    <div x-data="{
                        preview: @js((is_string($defaultSeoImage) && $defaultSeoImage !== '' && \Illuminate\Support\Facades\Storage::disk('public')->exists($defaultSeoImage)) ? \Illuminate\Support\Facades\Storage::disk('public')->url($defaultSeoImage) : ''),
                        fieldPath: 'defaultSeoImage',
                    }"
                    x-on:media-selected.window="
                        if ($event.detail.fieldPath === fieldPath) {
                            preview = $event.detail.url;
                            $wire.set(fieldPath, $event.detail.path);
                        }
                    ">
                        <template x-if="preview">
                            <img :src="preview" alt="Default SEO Image"
                                class="mt-3 h-32 w-auto rounded-lg border border-gray-200 object-cover">
                        </template>
                        <div class="flex items-center gap-2 mt-3">
                            <button type="button"
                                x-on:click="$dispatch('open-media-picker', { fieldPath: fieldPath })"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z"/></svg>
                                Browse Media
                            </button>
                        </div>
                    </div>
                    @error('defaultSeoImage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="siteFooter" class="block text-sm font-medium text-gray-700">Site Footer</label>
                    <textarea id="siteFooter" wire:model="siteFooter" rows="4"
                        placeholder="Enter your desc"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    @error('siteFooter')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="homePageId" class="block text-sm font-medium text-gray-700">Home Page</label>
                    <p class="mt-1 text-xs text-gray-500">The published page shown at "/" and its own slug is redirected to "/".</p>
                    <select id="homePageId" wire:model="homePageId"
                        class="mt-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">— First published page —</option>
                        @foreach ($pages as $page)
                            <option value="{{ $page->id }}">{{ $page->translation()?->title ?? "Page #{$page->id}" }}</option>
                        @endforeach
                    </select>
                    @error('homePageId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end px-6 py-4 bg-gray-50 text-right rounded-b-lg">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
