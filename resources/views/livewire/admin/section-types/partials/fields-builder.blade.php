@foreach ($fields as $index => $field)
    @php
        $path = $prefix === '' ? (string) $index : $prefix . '.' . $index;
    @endphp

    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
        <div class="grid grid-cols-12 gap-3 items-end">
            <div class="col-span-4 sm:col-span-3">
                <label class="block text-xs font-medium text-gray-700">Key</label>
                <input type="text" wire:model="fields.{{ $path }}.key" placeholder="heading"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('fields.' . $path . '.key')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-4 sm:col-span-3">
                <label class="block text-xs font-medium text-gray-700">Label</label>
                <input type="text" wire:model="fields.{{ $path }}.label"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="col-span-4 sm:col-span-3">
                <label class="block text-xs font-medium text-gray-700">Type</label>
                <select wire:model.live="fields.{{ $path }}.type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="rich-text">Rich Text</option>
                    <option value="image">Image</option>
                    <option value="repeater">Repeater</option>
                    <option value="link">Link</option>
                </select>
            </div>
            <div class="col-span-4 sm:col-span-1">
                <label class="flex items-center gap-2 text-sm text-gray-700 pt-1">
                    <input type="checkbox" wire:model="fields.{{ $path }}.required"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-medium">Required</span>
                </label>
            </div>
            <div class="col-span-8 sm:col-span-2 flex items-end justify-end gap-1">
                <button type="button" wire:click="moveField('{{ $path }}', 'up')" title="Move up"
                    class="inline-flex items-center justify-center p-1.5 rounded-md bg-white border border-gray-300 text-gray-600 hover:bg-gray-100">↑</button>
                <button type="button" wire:click="moveField('{{ $path }}', 'down')" title="Move down"
                    class="inline-flex items-center justify-center p-1.5 rounded-md bg-white border border-gray-300 text-gray-600 hover:bg-gray-100">↓</button>
                <button type="button" wire:click="removeField('{{ $path }}')" title="Remove"
                    class="inline-flex items-center justify-center p-1.5 rounded-md bg-red-600 text-white hover:bg-red-500">✕</button>
            </div>
        </div>

        @if (($field['type'] ?? '') === 'repeater')
            <div class="mt-4 pl-4 border-l-4 border-indigo-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Repeater item fields</span>
                    <button type="button" wire:click="addField('{{ $path }}.fields')"
                        class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-wider shadow-sm hover:bg-gray-50">
                        + Add Item Field
                    </button>
                </div>

                <div class="space-y-3">
                    @include('livewire.admin.section-types.partials.fields-builder', [
                        'fields' => $field['fields'] ?? [],
                        'prefix' => $path . '.fields',
                    ])

                    @if (count($field['fields'] ?? []) === 0)
                        <p class="text-xs text-gray-400">No item fields defined.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endforeach
