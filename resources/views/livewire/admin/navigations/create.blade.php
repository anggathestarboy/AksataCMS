<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Create Navigation</h1>
            <a href="{{ route('admin.navigations.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Back to list</a>
        </div>

        @include('livewire.admin.navigations.partials.form')
    </div>
</div>