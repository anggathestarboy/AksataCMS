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
        <label class="block text-xs font-medium text-gray-600">
            {{ $field['label'] }}
            @if ($field['required'])
                <span class="text-red-600">*</span>
            @endif
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
            @if (filled($value))
                <img src="{{ Storage::disk('public')->url($value) }}" alt="{{ $field['label'] }}"
                    class="mt-2 h-20 w-auto rounded-md border border-gray-200 object-cover">
            @endif
            <input type="file" wire:model="{{ $uploadPrefix }}{{ $fieldPath }}"
                class="mt-2 block w-full text-xs text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-[10px] file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100">
            @error($uploadPrefix . $fieldPath)
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
            <input type="text" wire:model="{{ $wirePrefix }}{{ $fieldPath }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @endif

        @if (! $isRepeater)
            @error($errorPrefix . $fieldPath)
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>
@endforeach
