<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $navigation->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Menu items for
                    <code class="text-xs bg-gray-100 rounded px-1.5 py-0.5">{{ $navigation->slug }}</code>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.navigations.edit', $navigation) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Group Settings</a>
                <a href="{{ route('admin.navigations.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Back to list</a>
            </div>
        </div>

        {{-- Item Form --}}
        @include('livewire.admin.navigations.partials.item-form')

        {{-- Item Tree --}}
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Items</h3>
                <button type="button" wire:click="openCreate()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>

            <div class="p-4">
                @if ($items->isEmpty())
                    <div class="rounded-md bg-gray-50 border border-dashed border-gray-300 px-6 py-12 text-center">
                        <p class="text-sm text-gray-500">No menu items yet. Click "Add Item" to create the first one.</p>
                    </div>
                @else
                    <div x-sort x-sort:group="nav-items" x-sort:config="{ animation: 150 }"
                        x-sort="(item, position) => $wire.updateOrder(item, position, null)"
                        class="space-y-2">
                        @foreach ($items as $item)
                            @include('livewire.admin.navigations.partials.item-row', ['item' => $item, 'depth' => 0])
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Drag items by the grip handle to reorder, or drop an item onto a submenu list to nest it.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>