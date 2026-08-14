<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Section Content</h1>
            <a href="{{ route('admin.pages.edit', $page) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Back to page</a>
        </div>

        <div class="mb-6">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $sectionType->name }}</span>
            <span class="ms-2 text-sm text-gray-500">{{ $sectionType->slug }}</span>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6" x-data="{ tab: @js(array_key_first(config('cms.locales'))) }">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                        @foreach (config('cms.locales') as $locale => $label)
                            <button type="button" @click="tab = @js($locale)"
                                :class="tab === @js($locale) ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="inline-flex items-center px-1 py-2 border-b-2 text-sm font-medium">
                                {{ $label }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                @foreach (config('cms.locales') as $locale => $label)
                    <div x-show="tab === @js($locale)" class="mt-6">
                        @if (count($this->fields) === 0)
                            <p class="text-sm text-gray-500">This section type has no editable fields.</p>
                        @else
                            @include('livewire.admin.pages.partials.dynamic-fields', [
                                'fields' => $this->fields,
                                'locale' => $locale,
                                'path' => $locale,
                            ])
                        @endif
                    </div>
                @endforeach

                <div class="mt-8 flex items-center justify-end">
                    <a href="{{ route('admin.pages.edit', $page) }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="button" wire:click="save"
                        class="ms-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
