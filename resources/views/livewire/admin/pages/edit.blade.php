<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Page</h1>
            <div class="flex items-center gap-4">
                @if ($page->status === 'published' && $page->url() !== null)
                    <a href="{{ $page->url() }}" target="_blank" rel="noopener" class="text-sm font-medium text-emerald-600 hover:text-emerald-900">View Page →</a>
                @endif
                <a href="{{ route('admin.pages.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Back to list</a>
            </div>
        </div>

        @include('livewire.admin.pages.partials.page-form')

        <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Sections</h3>
                <p class="mt-1 text-sm text-gray-500">Order the sections that make up this page. Each section holds translated content.</p>

                @error('newSectionTypeId')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-center">
                    <select wire:model="newSectionTypeId"
                        class="block w-full sm:w-72 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Add a section…</option>
                        @foreach ($sectionTypes as $sectionType)
                            <option value="{{ $sectionType->id }}">{{ $sectionType->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="addSection"
                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Add Section
                    </button>
                </div>

                @if ($sections->isEmpty())
                    <div class="mt-4 rounded-md bg-gray-50 border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                        No sections yet. Choose a section type above to add one.
                    </div>
                @else
                    <div class="mt-4 overflow-hidden border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section Type</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($sections as $section)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $section->sectionType->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" wire:click="moveSection({{ $section->id }}, 'up')" title="Move up" class="text-gray-400 hover:text-gray-700">↑</button>
                                            <button type="button" wire:click="moveSection({{ $section->id }}, 'down')" title="Move down" class="ms-1 text-gray-400 hover:text-gray-700">↓</button>
                                            <a href="{{ route('admin.pages.sections.edit', [$page, $section]) }}" class="ms-3 text-indigo-600 hover:text-indigo-900">Edit Content</a>
                                            <button type="button" x-data @click="if (confirm('Remove this section?')) $wire.deleteSection({{ $section->id }})"
                                                class="ms-3 text-red-600 hover:text-red-900">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
