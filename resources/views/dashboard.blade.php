<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <a href="{{ route('admin.pages.index') }}" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition duration-150">
                    <h3 class="text-lg font-medium text-gray-900">Pages</h3>
                    <p class="mt-1 text-sm text-gray-500">Manage pages, their translations, and section content.</p>
                </a>

                <a href="{{ route('admin.section-types.index') }}" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition duration-150">
                    <h3 class="text-lg font-medium text-gray-900">Section Types</h3>
                    <p class="mt-1 text-sm text-gray-500">Define reusable section structures with editable fields.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
