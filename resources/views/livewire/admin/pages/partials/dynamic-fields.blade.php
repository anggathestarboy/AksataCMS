@php
    $inlineMode = isset($sectionId);
@endphp

@foreach ($fields as $field)
    @php
        $fieldPath = $path . '.' . $field['key'];
        $repeaterPath = str_starts_with($fieldPath, $locale . '.') ? substr($fieldPath, strlen($locale) + 1) : $fieldPath;
        $isRepeater = ($field['type'] ?? 'text') === 'repeater';

        if ($inlineMode) {
            $value = data_get($sectionContent[$sectionId] ?? [], $fieldPath, '');
            $wirePrefix = "sectionContent.{$sectionId}.";
            $errorPrefix = "sectionContent.{$sectionId}.";
            $uploadPrefix = "sectionUploads.{$sectionId}.";
        } else {
            $value = data_get($content ?? [], $fieldPath, '');
            $wirePrefix = 'content.';
            $errorPrefix = 'content.';
            $uploadPrefix = 'uploads.';
        }
    @endphp

    <div class="mt-4">
        <label class="block text-xs font-medium text-gray-600"
            x-data="{ show: false, timer: null }"
            @mouseenter="timer = setTimeout(() => show = true, 100)"
            @mouseleave="clearTimeout(timer); show = false">
            {{ $field['label'] }}
            @if ($field['required'])
                <span class="text-red-600">*</span>
            @endif
            <span x-show="show" x-transition
                class="ml-1 text-[10px] font-mono text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">
                {{ $field['key'] }}
            </span>
        </label>

        @if (($field['type'] ?? 'text') === 'textarea')
            <textarea wire:model="{{ $wirePrefix }}{{ $fieldPath }}" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        @elseif (($field['type'] ?? 'text') === 'rich-text')
            <textarea wire:model="{{ $wirePrefix }}{{ $fieldPath }}" rows="6"
                placeholder="enter your desc"
                class="mt-1 block w-full font-mono text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
            <p class="mt-1 text-[10px] text-gray-400">You can paste formatted HTML content here.</p>
        @elseif (($field['type'] ?? 'text') === 'image')
            <div x-data="{
                preview: @js(filled($value) ? \Illuminate\Support\Facades\Storage::disk('public')->url(is_array($value) ? ($value['path'] ?? '') : $value) : ''),
                fieldPath: @js($fieldPath),
                sectionId: @js($sectionId ?? null),
                wireKey: @js($wirePrefix . $fieldPath),
            }"
            x-on:media-selected.window="
                if ($event.detail.fieldPath === fieldPath && String($event.detail.sectionId ?? '') === String(sectionId ?? '')) {
                    preview = $event.detail.url;
                    $wire.handleImageSelection(wireKey, {
                        path: $event.detail.path,
                        width: $event.detail.width,
                        height: $event.detail.height,
                        alt: $event.detail.alt,
                        loading: $event.detail.loading,
                    });
                }
            ">
                <template x-if="preview">
                    <div class="relative mt-2 inline-block">
                        <img :src="preview" alt="{{ $field['label'] }}"
                            class="h-32 w-auto rounded-md border border-gray-200 object-cover">
                        <button type="button" x-on:click="preview = ''; $wire.handleImageSelection(wireKey, {path: '', width: null, height: null, alt: '', loading: 'lazy'})"
                            class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-[10px] hover:bg-red-600 shadow">&times;</button>
                    </div>
                </template>
                <div class="mt-2">
                    <button type="button"
                        x-on:click="$dispatch('open-media-picker', { fieldPath: fieldPath, sectionId: sectionId })"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v13.5A1.5 1.5 0 003.75 21z"/></svg>
                        Browse Media
                    </button>
                </div>
            </div>
            @error($uploadPrefix . $fieldPath)
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        @elseif (($field['type'] ?? 'text') === 'link')
            @php
                $linkValue = data_get($inlineMode ? ($sectionContent[$sectionId] ?? []) : ($content ?? []), $fieldPath, []);
                if (! is_array($linkValue)) {
                    $linkValue = [];
                }
                $linkType = $linkValue['link_type'] ?? 'internal';
            @endphp

            <div x-data="{ linkType: @js($linkType) }">
                {{-- Link label --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600">Link Label</label>
                    <input type="text" wire:model="{{ $wirePrefix }}{{ $fieldPath }}.label"
                        placeholder="Link text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Link type --}}
                <div class="mt-3">
                    <label class="block text-xs font-medium text-gray-600">Target Type</label>
                    <select wire:model.live="{{ $wirePrefix }}{{ $fieldPath }}.link_type"
                        x-model="linkType"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="internal">Internal Link</option>
                        <option value="external">External Link</option>
                        <option value="page">Page</option>
                    </select>
                </div>

                {{-- URL (external / internal) --}}
                <div class="mt-3" x-show="linkType !== 'page'">
                    <label class="block text-xs font-medium text-gray-600">
                        <span x-text="linkType === 'external' ? 'External URL' : 'Internal Path'"></span>
                    </label>
                    <input type="text" wire:model="{{ $wirePrefix }}{{ $fieldPath }}.url"
                        :placeholder="linkType === 'external' ? 'https://example.com/...' : '/kontak'"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Page selector --}}
                <div class="mt-3" x-show="linkType === 'page'">
                    <label class="block text-xs font-medium text-gray-600">Page</label>
                    <select wire:model="{{ $wirePrefix }}{{ $fieldPath }}.page_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select a page...</option>
                        @foreach ($pages as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->translation(config('cms.default_locale'))?->title ?? '(untitled #' . $p->id . ')' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Open in new tab --}}
                <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" wire:model="{{ $wirePrefix }}{{ $fieldPath }}.open_in_new_tab"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Open in new tab
                </label>
            </div>

        @elseif ($isRepeater)
            <div class="mt-2 space-y-3">
                @foreach ((array) data_get($inlineMode ? ($sectionContent[$sectionId] ?? []) : ($content ?? []), $fieldPath, []) as $index => $item)
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Item #{{ $index + 1 }}</span>
                            <div class="flex gap-1">
                                @if ($inlineMode)
                                    <button type="button" wire:click="moveRepeaterItem({{ $sectionId }}, @js($locale), @js($repeaterPath), {{ $index }}, 'up')" title="Move up"
                                        class="inline-flex items-center justify-center p-1 rounded bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 text-xs">↑</button>
                                    <button type="button" wire:click="moveRepeaterItem({{ $sectionId }}, @js($locale), @js($repeaterPath), {{ $index }}, 'down')" title="Move down"
                                        class="inline-flex items-center justify-center p-1 rounded bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 text-xs">↓</button>
                                    <button type="button" wire:click="removeRepeaterItem({{ $sectionId }}, @js($locale), @js($repeaterPath), {{ $index }})" title="Remove"
                                        class="inline-flex items-center justify-center p-1 rounded bg-red-600 text-white hover:bg-red-500 text-xs">✕</button>
                                @else
                                    <button type="button" wire:click="moveRepeaterItem(@js($locale), @js($repeaterPath), {{ $index }}, 'up')" title="Move up"
                                        class="inline-flex items-center justify-center p-1.5 rounded-md bg-white border border-gray-300 text-gray-600 hover:bg-gray-100">↑</button>
                                    <button type="button" wire:click="moveRepeaterItem(@js($locale), @js($repeaterPath), {{ $index }}, 'down')" title="Move down"
                                        class="inline-flex items-center justify-center p-1.5 rounded-md bg-white border border-gray-300 text-gray-600 hover:bg-gray-100">↓</button>
                                    <button type="button" wire:click="removeRepeaterItem(@js($locale), @js($repeaterPath), {{ $index }})" title="Remove"
                                        class="inline-flex items-center justify-center p-1.5 rounded-md bg-red-600 text-white hover:bg-red-500">✕</button>
                                @endif
                            </div>
                        </div>

                        @include('livewire.admin.pages.partials.dynamic-fields', [
                            'fields' => $field['fields'] ?? [],
                            'locale' => $locale,
                            'path' => $fieldPath . '.' . $index,
                            'sectionId' => $sectionId ?? null,
                        ])
                    </div>
                @endforeach

                @if ($inlineMode)
                    <button type="button" wire:click="addRepeaterItem({{ $sectionId }}, @js($locale), @js($repeaterPath))"
                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-wider shadow-sm hover:bg-gray-50">
                        + Add Item
                    </button>
                @else
                    <button type="button" wire:click="addRepeaterItem(@js($locale), @js($repeaterPath))"
                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-wider shadow-sm hover:bg-gray-50">
                        + Add Item
                    </button>
                @endif
            </div>
        @else
            @if (! is_array($value))
                <input type="text" wire:model="{{ $wirePrefix }}{{ $fieldPath }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @endif
        @endif

        @if (! $isRepeater)
            @error($errorPrefix . $fieldPath)
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>
@endforeach
