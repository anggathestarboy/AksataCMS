<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Section Type</h1>
            <a href="{{ route('admin.section-types.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Back to list</a>
        </div>

        @include('livewire.admin.section-types.partials.form')

        <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Frontend Template</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Setiap section type memiliki file template Blade yang mengontrol tampilannya di halaman publik.
                    File dibuat otomatis saat section type dibuat dan bisa diedit bebas untuk menyesuaikan tampilan.
                </p>

                <div class="mt-4 rounded-md bg-gray-50 border border-gray-200 px-4 py-3 font-mono text-xs text-gray-700">
                    resources/views/public/sections/{{ $sectionType->slug }}.blade.php
                </div>

                <div class="mt-3 flex items-center gap-3">
                    @if (\Illuminate\Support\Facades\View::exists('public.sections.' . $sectionType->slug))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Template exists</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Template missing</span>
                    @endif

                    <button type="button" x-data @click="if (confirm('Regenerate the template file? This will overwrite any customizations you made to it.')) $wire.regenerateTemplate()"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Regenerate Template
                    </button>
                </div>

                @if (session('template_status'))
                    <div class="mt-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                        {{ session('template_status') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
