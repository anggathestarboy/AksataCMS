<div class="py-6" x-data @keydown.window="if ((event.ctrlKey || event.metaKey) && event.key === 's') { event.preventDefault(); $wire.save(); }">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-900">Edit Page</h1>
            <div class="flex items-center gap-3">
                @if ($activeLocale === config('cms.default_locale'))
                    <button type="button" wire:click="copyFromDefault" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        Copy to All Languages
                    </button>
                @endif
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                    <svg wire:loading.remove class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading class="w-3.5 h-3.5 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Save
                    <span class="ml-1.5 text-[10px] text-indigo-300 font-normal normal-case tracking-normal">Ctrl+S</span>
                </button>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">

            {{-- LEFT COLUMN: Sections --}}
            <div class="space-y-4">
                {{-- Section Manager --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-4 py-3 border-b border-gray-50">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sections</h3>
                    </div>
                    <div class="px-4 py-3">
                        @error('newSectionTypeId')
                            <p class="mb-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="flex gap-2">
                            <select wire:model="newSectionTypeId"
                                class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs">
                                <option value="">Select section type...</option>
                                @foreach ($sectionTypes as $sectionType)
                                    <option value="{{ $sectionType->id }}">{{ $sectionType->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="addSection"
                                class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Section Cards --}}
                @if ($sections->isEmpty())
                    <div class="bg-white rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.75H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No sections yet</h3>
                        <p class="mt-1 text-xs text-gray-500">Select a section type above and click Add to get started.</p>
                    </div>
                @else
                    @foreach ($sections as $section)
                        @php
                            $isExpanded = $expandedSectionId === $section->id;
                            $sectionFields = $section->sectionType->fields ?? [];
                        @endphp
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden transition-all"
                            x-data="{ open: @js($isExpanded) }"
                            x-effect="$wire.expandedSectionId === {{ $section->id }} ? open = true : open = false">

                            {{-- Section Card Header --}}
                            <div class="flex items-center gap-2 px-4 py-3 {{ $isExpanded ? 'border-b border-gray-100 bg-gray-50/60' : '' }}">
                                {{-- Move buttons --}}
                                <div class="flex flex-col -space-y-0.5">
                                    <button type="button" wire:click="moveSection({{ $section->id }}, 'up')" title="Move up"
                                        class="text-gray-400 hover:text-gray-700 p-0.5 rounded hover:bg-gray-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button type="button" wire:click="moveSection({{ $section->id }}, 'down')" title="Move down"
                                        class="text-gray-400 hover:text-gray-700 p-0.5 rounded hover:bg-gray-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>

                                {{-- Section info --}}
                                <div class="flex-1 min-w-0 cursor-pointer select-none" wire:click="toggleSection({{ $section->id }})">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $section->sectionType->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $section->sectionType->slug }}</span>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-1">
                                    <button type="button" x-data @click="if (confirm('Remove this section?')) $wire.deleteSection({{ $section->id }})"
                                        class="text-gray-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-50 transition"
                                        title="Delete section">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>

                                    <button type="button" wire:click="toggleSection({{ $section->id }})"
                                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-md hover:bg-gray-100 transition">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Section Card Body (Collapsible) --}}
                            <div x-show="open" x-collapse x-cloak>
                                @if (empty($sectionFields))
                                    <div class="px-4 py-6 text-center text-xs text-gray-400">
                                        This section type has no editable fields.
                                    </div>
                                @else
                                    <div class="px-4 py-4">
                                        <div class="mb-3 flex items-center gap-2">
                                            <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                                {{ config('cms.locales')[$activeLocale] ?? $activeLocale }}
                                            </span>
                                        </div>

                                        @include('livewire.admin.pages.partials.dynamic-fields', [
                                            'fields' => $sectionFields,
                                            'locale' => $activeLocale,
                                            'path' => $activeLocale,
                                            'sectionId' => $section->id,
                                        ])
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- RIGHT COLUMN: Sidebar --}}
            <div class="lg:sticky lg:top-6">
                @include('livewire.admin.pages.partials.page-form')
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
        x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        x-cloak
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-white"
        :class="type === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
        <svg x-show="type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <svg x-show="type !== 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-text="message"></span>
    </div>
</div>
