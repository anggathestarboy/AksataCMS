<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Create Section Type</h1>
            <a href="{{ route('admin.section-types.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Back to list</a>
        </div>

        @include('livewire.admin.section-types.partials.form')

        <p class="mt-4 text-sm text-gray-500">
            Setelah disimpan, file template Blade akan dibuat otomatis di
            <code class="font-mono text-xs text-gray-700">resources/views/public/sections/{{ $this->slug }}.blade.php</code>
            untuk dikustomisasi.
        </p>
    </div>
</div>
