<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Navigation</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.navigations.items', $navigation) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Manage Items
                </a>
                <a href="{{ route('admin.navigations.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Back to list</a>
            </div>
        </div>

        @include('livewire.admin.navigations.partials.form')
    </div>
</div>