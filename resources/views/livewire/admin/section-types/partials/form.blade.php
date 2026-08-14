<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" x-data="{ slugTouched: false }">
    <div class="p-6">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" type="text" wire:model.live="name" placeholder="Hero Banner"
                    @input="if (! slugTouched) { const slug = $event.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); $refs.slugField.value = slug; $wire.set('slug', slug, false); }"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                <input id="slug" type="text" wire:model.live="slug" placeholder="hero-banner" x-ref="slugField"
                    @input="slugTouched = true"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700">Icon</label>
                <select id="icon" wire:model="icon"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach ($this->availableIcons() as $icon)
                        <option value="{{ $icon }}">{{ $icon }}</option>
                    @endforeach
                </select>
                @error('icon')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900">Content Fields</h3>
            <p class="mt-1 text-sm text-gray-500">Define the editable fields this section type exposes for every locale.</p>

            <div class="mt-4 space-y-4">
                @include('livewire.admin.section-types.partials.fields-builder', [
                    'fields' => $this->fields,
                    'prefix' => '',
                ])

                @if (count($this->fields) === 0)
                    <div class="rounded-md bg-gray-50 border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                        No fields yet. Add your first field below.
                    </div>
                @endif

                <button type="button" wire:click="addField()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + Add Field
                </button>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-between">
            <div>
                @isset($sectionType)
                    <button type="button" x-data @click="if (confirm('Delete this section type?')) $wire.delete()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Delete
                    </button>
                @endisset
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.section-types.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="button" wire:click="save"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
