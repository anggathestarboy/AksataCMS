@foreach ($fields as $field)
    @php
        $fieldPath = $path . '.' . $field['key'];
        $value = data_get($content, $fieldPath, '');
        $isRepeater = ($field['type'] ?? 'text') === 'repeater';
    @endphp

    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">
            {{ $field['label'] }}
            @if ($field['required'])
                <span class="text-red-600">*</span>
            @endif
        </label>

        @if (($field['type'] ?? 'text') === 'textarea')
            <textarea wire:model="content.{{ $fieldPath }}" rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
        @elseif (($field['type'] ?? 'text') === 'rich-text')
            <textarea wire:model="content.{{ $fieldPath }}" rows="8"
                placeholder="Supports basic HTML (h2, p, strong, a, ul, ...)"
                class="mt-1 block w-full font-mono text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
            <p class="mt-1 text-xs text-gray-400">You can paste formatted HTML content here.</p>
        @elseif (($field['type'] ?? 'text') === 'image')
            @if (filled($value))
                <img src="{{ Storage::disk('public')->url($value) }}" alt="{{ $field['label'] }}"
                    class="mt-2 h-24 w-auto rounded-md border border-gray-200">
            @endif
            <input type="file" wire:model="uploads.{{ $fieldPath }}"
                class="mt-2 block w-full text-sm text-gray-500 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100">
            @error('uploads.' . $fieldPath)
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @elseif ($isRepeater)
            <div class="mt-2 space-y-3">
                @foreach ((array) data_get($content, $fieldPath, []) as $index => $item)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-500">Item #{{ $index + 1 }}</span>
                            <div class="flex gap-1">
                                <button type="button" wire:click="moveRepeaterItem(@js($locale), @js($path . '.' . $field['key']), {{ $index }}, 'up')" title="Move up"
                                    class="inline-flex items-center justify-center p-1.5 rounded-md bg-white border border-gray-300 text-gray-600 hover:bg-gray-100">↑</button>
                                <button type="button" wire:click="moveRepeaterItem(@js($locale), @js($path . '.' . $field['key']), {{ $index }}, 'down')" title="Move down"
                                    class="inline-flex items-center justify-center p-1.5 rounded-md bg-white border border-gray-300 text-gray-600 hover:bg-gray-100">↓</button>
                                <button type="button" wire:click="removeRepeaterItem(@js($locale), @js($path . '.' . $field['key']), {{ $index }})" title="Remove"
                                    class="inline-flex items-center justify-center p-1.5 rounded-md bg-red-600 text-white hover:bg-red-500">✕</button>
                            </div>
                        </div>

                        @include('livewire.admin.pages.partials.dynamic-fields', [
                            'fields' => $field['fields'] ?? [],
                            'locale' => $locale,
                            'path' => $fieldPath . '.' . $index,
                        ])
                    </div>
                @endforeach

                <button type="button" wire:click="addRepeaterItem(@js($locale), @js($path . '.' . $field['key']))"
                    class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-wider shadow-sm hover:bg-gray-50">
                    + Add Item
                </button>
            </div>
        @else
            <input type="text" wire:model="content.{{ $fieldPath }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        @endif

        @if (! $isRepeater)
            @error('content.' . $fieldPath)
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>
@endforeach
