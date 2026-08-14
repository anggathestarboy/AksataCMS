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
                    @if (is_string($defaultSeoImage) && $defaultSeoImage !== '' && \Illuminate\Support\Facades\Storage::disk('public')->exists($defaultSeoImage))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($defaultSeoImage) }}" alt="Current default SEO image"
                            class="mt-3 h-32 w-auto rounded-lg border border-gray-200 object-cover">
                    @endif
                    <input type="file" wire:model="defaultSeoImage" accept="image/*"
                        class="mt-3 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('defaultSeoImage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="siteFooter" class="block text-sm font-medium text-gray-700">Site Footer</label>
                    <textarea id="siteFooter" wire:model="siteFooter" rows="4"
                        placeholder="Supports basic HTML (e.g. &lt;p&gt;© 2026 My Site&lt;/p&gt;)"
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
